<?php

namespace App\Modules\Penilaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Penilaian Mapel Pilihan (Mapil)')]
class MapelPilihanPenilaianIndex extends Component
{
    use GuardsPermission;

    public string $search = '';
    public string $filterTingkat = 'semua';

    public function mount(): void
    {
        $this->guard('penilaian.lihat');
    }

    public function render()
    {
        $ta = AcademicContext::currentTahunAjaran();
        $sem = AcademicContext::currentSemester();
        $user = auth()->user();
        $pegawai = Pegawai::where('user_id', $user?->id)->first();
        $isSuper = $user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();

        $query = MapelPilihan::with(['guru', 'guruPendamping', 'anggotas.siswa'])
            ->where('is_active', true);

        if ($ta) {
            $query->where('tahun_ajaran_id', $ta->id);
        }

        // Jika bukan superadmin / kurikulum, batasi ke Mapil yang diampu atau didampingi
        if (!$isSuper && $pegawai && !$user->can('akademik.mapel.edit')) {
            $query->where(function ($q) use ($pegawai) {
                $q->where('guru_id', $pegawai->id)
                  ->orWhere('guru_pendamping_id', $pegawai->id);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('kode', 'like', '%' . $this->search . '%');
            });
        }

        $allMapils = $query->orderBy('nama')->get();

        if ($this->filterTingkat !== 'semua') {
            $allMapils = $allMapils->filter(function ($m) {
                $t = $m->tingkats ?? [];
                return in_array($this->filterTingkat, (array) $t);
            });
        }

        // Hitung metrik kelengkapan per Mapil
        $mapilStats = [];
        $semId = $sem?->id;
        $isGenap = $sem && str_contains(strtolower($sem->semester), 'genap');

        foreach ($allMapils as $m) {
            $totalSiswa = $m->anggotas->where('status', 'aktif')->count();
            $totalTp = $semId ? MapelPilihanTp::where('mapel_pilihan_id', $m->id)->where('semester_id', $semId)->count() : 0;

            // Formatif check
            $ahKeys = $isGenap ? ['AH 5', 'AH 6', 'AH 7', 'AH 8'] : ['AH 1', 'AH 2', 'AH 3', 'AH 4'];
            $activeAhs = $semId ? MapelPilihanTp::where('mapel_pilihan_id', $m->id)
                ->where('semester_id', $semId)
                ->pluck('kode_ah')
                ->filter()
                ->unique()
                ->values()
                ->all() : [];

            $formatifCount = 0;
            if ($semId && $totalSiswa > 0 && !empty($activeAhs)) {
                $formatifCount = NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $m->id)
                    ->where('semester_id', $semId)
                    ->whereIn('kode_ah', $activeAhs)
                    ->whereNotNull('nilai')
                    ->count();
            }

            $expectedFormatif = $totalSiswa * count($activeAhs);
            $persenFormatif = $expectedFormatif > 0 ? min(100, round(($formatifCount / $expectedFormatif) * 100)) : 0;

            // Sumatif check
            $sumatifFilled = 0;
            if ($semId && $totalSiswa > 0) {
                $querySum = NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $m->id)
                    ->where('semester_id', $semId);
                if ($isGenap) {
                    $querySum->whereNotNull('sts_2')->whereNotNull('sat');
                } else {
                    $querySum->whereNotNull('sts_1')->whereNotNull('sas');
                }
                $sumatifFilled = $querySum->count();
            }
            $persenSumatif = $totalSiswa > 0 ? min(100, round(($sumatifFilled / $totalSiswa) * 100)) : 0;

            // Rekap tuntas
            $rekapTuntas = 0;
            if ($semId && $totalSiswa > 0) {
                $rekapTuntas = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $m->id)
                    ->where('semester_id', $semId)
                    ->where('status', 'kompeten')
                    ->whereNotNull('nilai_akhir')
                    ->count();
            }

            $mapilStats[$m->id] = [
                'total_siswa' => $totalSiswa,
                'total_tp' => $totalTp,
                'persen_formatif' => $persenFormatif,
                'persen_sumatif' => $persenSumatif,
                'rekap_tuntas' => $rekapTuntas,
            ];
        }

        return view('penilaian::livewire.mapel-pilihan-penilaian-index', [
            'mapils' => $allMapils,
            'mapilStats' => $mapilStats,
            'currentTa' => $ta,
            'currentSem' => $sem,
        ]);
    }
}
