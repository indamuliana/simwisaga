<?php

namespace App\Modules\Kepesantrenan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepesantrenan\Models\PesantrenHafalanSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenSetoranHafalan;
use App\Modules\Kepesantrenan\Models\QuranSurah;
use App\Modules\Kepesantrenan\Services\HafalanRankService;
use App\Modules\Kepesantrenan\Services\QuranHelper;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Input Kontrol Hafalan Qur\'an')]
class HafalanInput extends Component
{
    use GuardsPermission;

    // Pemilihan Santri (Autocomplete Input)
    public $searchSiswa = '';

    public $showSiswaDropdown = false;

    public $selectedSiswaId = null;

    // Mode Keamanan: Saklar Koreksi / Pembatalan Ayat
    public $modeKoreksi = false;

    // Navigasi Juz
    public $selectedJuz = 1;

    // State ayat terhafal dan ayat yang baru ditandai
    // Format map: [ 'surah_ayat' => true ]
    public $memorizedMap = [];

    public $tempSelectedMap = [];

    // Alat Bantu Pilih Rentang Cepat
    public $showRangeTool = false;

    public $rangeSurahId = null;

    public $rangeStart = 1;

    public $rangeEnd = 1;

    // Modal Simpan Setoran
    public $showSaveModal = false;

    public $setoranTanggal = '';

    public $setoranPegawaiId = null;

    public $setoranJenis = 'ziyadah';

    public $setoranPredikat = 'mumtaz';

    public $setoranCatatan = '';

    // Tab Tampilan (grid kontrol vs riwayat setoran)
    public $activeTab = 'lembar'; // 'lembar' atau 'riwayat'

    protected $rankService;

    public function boot(HafalanRankService $rankService): void
    {
        $this->rankService = $rankService;
    }

    public function mount(): void
    {
        $this->guard('pesantren.hafalan.lihat');

        $this->setoranTanggal = now()->toDateString();

        // Cari pegawai ID dari user yang sedang login jika ada
        $user = auth()->user();
        $pegawai = Pegawai::where('user_id', $user?->id)->first();
        $this->setoranPegawaiId = $pegawai?->id;
    }

    public function updatedSearchSiswa(): void
    {
        if (strlen(trim($this->searchSiswa)) > 0) {
            $this->showSiswaDropdown = true;
        }
    }

    public function selectSiswa(int $siswaId): void
    {
        $this->selectedSiswaId = $siswaId;
        $this->searchSiswa = '';
        $this->showSiswaDropdown = false;
        $this->tempSelectedMap = [];
        $this->loadMemorizedAyat();
    }

    public function clearSiswaSelection(): void
    {
        $this->selectedSiswaId = null;
        $this->searchSiswa = '';
        $this->showSiswaDropdown = false;
        $this->memorizedMap = [];
        $this->tempSelectedMap = [];
    }

    public function toggleModeKoreksi(): void
    {
        $this->modeKoreksi = ! $this->modeKoreksi;
    }

    public function loadMemorizedAyat(): void
    {
        if (! $this->selectedSiswaId) {
            $this->memorizedMap = [];

            return;
        }

        $records = PesantrenHafalanSiswa::where('siswa_id', $this->selectedSiswaId)
            ->get(['surah_id', 'ayat_number', 'predikat']);

        $map = [];
        foreach ($records as $r) {
            $map["{$r->surah_id}_{$r->ayat_number}"] = $r->predikat ?: 'mumtaz';
        }

        $this->memorizedMap = $map;
    }

    public function changeJuz(int $juz): void
    {
        $this->selectedJuz = max(1, min(30, $juz));
    }

    /**
     * Toggle status satu ayat dengan proteksi Mode Koreksi untuk ayat yang sudah tersimpan.
     */
    public function toggleAyat(int $surahId, int $ayatNumber): void
    {
        $this->guard('pesantren.hafalan.input');

        if (! $this->selectedSiswaId) {
            session()->flash('warning', 'Silakan cari dan pilih santri unggulan terlebih dahulu.');

            return;
        }

        $key = "{$surahId}_{$ayatNumber}";

        // Jika ayat SUDAH tersimpan di database:
        if (isset($this->memorizedMap[$key])) {
            // Proteksi: Jika Mode Koreksi TIDAK aktif, jangan hapus!
            if (! $this->modeKoreksi) {
                session()->flash('warning', "Ayat {$ayatNumber} sudah tersimpan. Aktifkan saklar 'Mode Koreksi' jika Anda berniat membatalkan hafalan ayat ini.");

                return;
            }

            // Jika Mode Koreksi AKTIF, baru boleh dihapus
            unset($this->memorizedMap[$key]);
            PesantrenHafalanSiswa::where('siswa_id', $this->selectedSiswaId)
                ->where('surah_id', $surahId)
                ->where('ayat_number', $ayatNumber)
                ->delete();

            session()->flash('info', "Hafalan Surat {$surahId} Ayat {$ayatNumber} berhasil dibatalkan.");

            return;
        }

        // Toggle di penampung sementara (sebelum klik simpan)
        if (isset($this->tempSelectedMap[$key])) {
            unset($this->tempSelectedMap[$key]);
        } else {
            $this->tempSelectedMap[$key] = [
                'surah_id' => $surahId,
                'ayat' => $ayatNumber,
            ];
        }
    }

    /**
     * Tandai rentang ayat sekaligus (misal ayat 1 sampai 25).
     */
    public function applyRange(): void
    {
        $this->guard('pesantren.hafalan.input');

        if (! $this->selectedSiswaId) {
            session()->flash('warning', 'Silakan cari dan pilih santri unggulan terlebih dahulu.');

            return;
        }

        if (! $this->rangeSurahId) {
            return;
        }

        $surah = QuranSurah::find($this->rangeSurahId);
        if (! $surah) {
            return;
        }

        $start = max(1, min($this->rangeStart, $surah->jumlah_ayat));
        $end = max($start, min($this->rangeEnd, $surah->jumlah_ayat));

        for ($a = $start; $a <= $end; $a++) {
            $key = "{$this->rangeSurahId}_{$a}";
            if (! isset($this->memorizedMap[$key])) {
                $this->tempSelectedMap[$key] = [
                    'surah_id' => $this->rangeSurahId,
                    'ayat' => $a,
                ];
            }
        }

        $this->showRangeTool = false;
        session()->flash('info', "Berhasil menandai {$surah->nama_latin} ayat {$start}-{$end}. Klik 'Simpan Progres' di bawah.");
    }

    public function openSaveModal(): void
    {
        $this->guard('pesantren.hafalan.input');

        if (! $this->selectedSiswaId) {
            session()->flash('warning', 'Silakan cari dan pilih santri unggulan terlebih dahulu.');

            return;
        }

        if (empty($this->tempSelectedMap)) {
            session()->flash('warning', 'Pilih minimal satu kotak ayat terlebih dahulu untuk disimpan.');

            return;
        }

        $this->showSaveModal = true;
    }

    /**
     * Simpan progres setoran santri ke database.
     */
    public function saveProgress(): void
    {
        $this->guard('pesantren.hafalan.input');
        if (! $this->selectedSiswaId || empty($this->tempSelectedMap)) {
            return;
        }

        $siswa = Siswa::findOrFail($this->selectedSiswaId);

        DB::transaction(function () use ($siswa) {
            $bySurah = [];
            foreach ($this->tempSelectedMap as $item) {
                $surahId = $item['surah_id'];
                $ayat = $item['ayat'];

                PesantrenHafalanSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'surah_id' => $surahId,
                        'ayat_number' => $ayat,
                    ],
                    [
                        'tanggal_hafal' => $this->setoranTanggal,
                        'status' => 'lancar',
                        'predikat' => $this->setoranPredikat,
                        'verified_by_id' => $this->setoranPegawaiId ?: null,
                    ]
                );

                $bySurah[$surahId][] = $ayat;
            }

            foreach ($bySurah as $surahId => $ayats) {
                sort($ayats);
                $minAyat = min($ayats);
                $maxAyat = max($ayats);
                $totalAyat = count($ayats);

                PesantrenSetoranHafalan::create([
                    'siswa_id' => $siswa->id,
                    'pegawai_id' => $this->setoranPegawaiId ?: null,
                    'tanggal' => $this->setoranTanggal,
                    'jenis' => $this->setoranJenis,
                    'surah_id' => $surahId,
                    'ayat_awal' => $minAyat,
                    'ayat_akhir' => $maxAyat,
                    'total_ayat' => $totalAyat,
                    'predikat' => $this->setoranPredikat,
                    'catatan' => $this->setoranCatatan ?: null,
                ]);
            }
        });

        $this->tempSelectedMap = [];
        $this->showSaveModal = false;
        $this->loadMemorizedAyat();
        $this->reset(['setoranCatatan']);

        session()->flash('message', "Alhamdulillah! Progres hafalan {$siswa->nama} berhasil disimpan.");
    }

    public function deleteSetoran(int $setoranId): void
    {
        $this->guard('pesantren.hafalan.hapus');
        PesantrenSetoranHafalan::findOrFail($setoranId)->delete();
        session()->flash('message', 'Catatan riwayat setoran berhasil dihapus.');
    }

    public function render()
    {
        // Hasil Autocomplete Santri Unggulan
        $autocompleteResults = collect();
        if ($this->showSiswaDropdown && strlen(trim($this->searchSiswa)) > 0) {
            $autocompleteResults = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('nama', 'like', "%{$this->searchSiswa}%")
                        ->orWhere('nisn', 'like', "%{$this->searchSiswa}%")
                        ->orWhere('nipd', 'like', "%{$this->searchSiswa}%");
                })
                ->with(['rombel', 'pesantrenKamars'])
                ->take(8)
                ->get();
        }

        $selectedSiswa = $this->selectedSiswaId
            ? Siswa::with(['rombel', 'rayon', 'pesantrenKamars'])->find($this->selectedSiswaId)
            : null;

        $stats = $selectedSiswa ? $this->rankService->getSiswaStats($selectedSiswa->id) : null;

        // Data bagian surat & ayat untuk Juz yang sedang aktif
        QuranHelper::ensureSurahsSeeded();
        $juzSections = QuranHelper::getJuzSections($this->selectedJuz);
        $surahIdsInJuz = collect($juzSections)->pluck('surah')->unique();
        $surahs = QuranSurah::whereIn('nomor_surah', $surahIdsInJuz)->get()->keyBy('nomor_surah');

        // Indikator progres per Juz untuk navigasi (1 s/d 30)
        $juzProgress = [];
        for ($j = 1; $j <= 30; $j++) {
            $sections = QuranHelper::getJuzSections($j);
            $totalAyatInJuz = 0;
            $memorizedInJuz = 0;
            foreach ($sections as $sec) {
                $totalSec = ($sec['end'] - $sec['start'] + 1);
                $totalAyatInJuz += $totalSec;
                for ($a = $sec['start']; $a <= $sec['end']; $a++) {
                    if (isset($this->memorizedMap["{$sec['surah']}_{$a}"])) {
                        $memorizedInJuz++;
                    }
                }
            }
            $juzProgress[$j] = [
                'total' => $totalAyatInJuz,
                'memorized' => $memorizedInJuz,
                'is_tuntas' => $totalAyatInJuz > 0 && ($memorizedInJuz >= $totalAyatInJuz),
                'has_progress' => $memorizedInJuz > 0,
            ];
        }

        // Riwayat setoran santri bersangkutan
        $setorans = $selectedSiswa
            ? PesantrenSetoranHafalan::with(['surah', 'penguji'])
                ->where('siswa_id', $selectedSiswa->id)
                ->latest('tanggal')
                ->latest('id')
                ->paginate(15)
            : collect();

        $pegawais = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();

        return view('kepesantrenan::livewire.hafalan-input', [
            'autocompleteResults' => $autocompleteResults,
            'selectedSiswa' => $selectedSiswa,
            'stats' => $stats,
            'juzSections' => $juzSections,
            'surahs' => $surahs,
            'juzProgress' => $juzProgress,
            'setorans' => $setorans,
            'pegawais' => $pegawais,
        ]);
    }
}
