<?php

namespace App\Modules\Kepesantrenan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kepesantrenan\Models\PesantrenHafalanSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenKamar;
use App\Modules\Kepesantrenan\Models\PesantrenSetoranHafalan;
use App\Modules\Kepesantrenan\Models\QuranSurah;
use App\Modules\Kepesantrenan\Services\HafalanRankService;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Rekap & Leaderboard Hafalan Santri')]
class RekapHafalanIndex extends Component
{
    use GuardsPermission;

    public $activeTab = 'leaderboard'; // 'leaderboard', 'rekap', 'persiswa'

    // Filter Leaderboard
    public $search = '';

    public $filterKamarId = null;

    public $filterRayonId = null;

    // State Tab Per Siswa
    public $selectedSiswaId = null;

    public $searchSiswaDetail = '';

    protected $rankService;

    public function boot(HafalanRankService $rankService): void
    {
        $this->rankService = $rankService;
    }

    public function mount(): void
    {
        $this->guard('pesantren.rekap.lihat');

        // Santri pertama untuk default tab per siswa
        $firstSiswa = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
            ->where('is_active', true)
            ->first();
        if ($firstSiswa) {
            $this->selectedSiswaId = $firstSiswa->id;
        }
    }

    public function selectDetailSiswa(int $id): void
    {
        $this->selectedSiswaId = $id;
        $this->activeTab = 'persiswa';
    }

    public function render()
    {
        $kamars = PesantrenKamar::orderBy('nama_kamar')->get();
        $rayons = Rayon::orderBy('nama')->get();

        // 1. Data Leaderboard
        $leaderboard = $this->rankService->getLeaderboard(
            $this->filterKamarId ? (int) $this->filterKamarId : null,
            $this->filterRayonId ? (int) $this->filterRayonId : null,
            $this->search ?: null,
            100
        );

        // 2. Data Rekap Keseluruhan, Kamar, Rayon
        $rekapData = $this->rankService->getRekapLaporan();

        // 3. Data Detail Per Siswa
        $selectedSiswa = null;
        $siswaStats = null;
        $surahDetails = collect();
        $riwayatSetoran = collect();

        if ($this->selectedSiswaId) {
            $selectedSiswa = Siswa::with(['rombel', 'rayon', 'pesantrenKamars.musrif', 'pesantrenHalaqohs.pengajar'])
                ->find($this->selectedSiswaId);

            if ($selectedSiswa) {
                $siswaStats = $this->rankService->getSiswaStats($selectedSiswa->id);

                // Ambil daftar surat dan ayat yang dihafal
                $hafalans = PesantrenHafalanSiswa::where('siswa_id', $selectedSiswa->id)
                    ->get(['surah_id', 'ayat_number'])
                    ->groupBy('surah_id');

                if ($hafalans->isNotEmpty()) {
                    $surahModels = QuranSurah::whereIn('nomor_surah', $hafalans->keys())->get()->keyBy('nomor_surah');

                    $surahDetails = $hafalans->map(function ($items, $surahId) use ($surahModels) {
                        $surah = $surahModels[$surahId] ?? null;
                        $ayatList = $items->pluck('ayat_number')->sort()->values();
                        $count = $ayatList->count();
                        $total = $surah?->jumlah_ayat ?? $count;
                        $isLengkap = $count >= $total;

                        return [
                            'surah' => $surah,
                            'ayat_count' => $count,
                            'total_ayat' => $total,
                            'persen' => round(($count / $total) * 100, 1),
                            'is_lengkap' => $isLengkap,
                            'ayat_range' => $ayatList->min().' - '.$ayatList->max(),
                        ];
                    })->sortBy(fn ($item) => $item['surah']?->nomor_surah ?? 999);
                }

                $riwayatSetoran = PesantrenSetoranHafalan::with(['surah', 'penguji'])
                    ->where('siswa_id', $selectedSiswa->id)
                    ->latest('tanggal')
                    ->latest('id')
                    ->take(20)
                    ->get();
            }
        }

        // List santri unggulan untuk picker detail
        $unggulans = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
            ->where('is_active', true)
            ->when($this->searchSiswaDetail, fn ($q) => $q->where('nama', 'like', "%{$this->searchSiswaDetail}%"))
            ->orderBy('nama')
            ->take(30)
            ->get();

        return view('kepesantrenan::livewire.rekap-hafalan-index', [
            'kamars' => $kamars,
            'rayons' => $rayons,
            'leaderboard' => $leaderboard,
            'rekapData' => $rekapData,
            'selectedSiswa' => $selectedSiswa,
            'siswaStats' => $siswaStats,
            'surahDetails' => $surahDetails,
            'riwayatSetoran' => $riwayatSetoran,
            'unggulans' => $unggulans,
        ]);
    }
}
