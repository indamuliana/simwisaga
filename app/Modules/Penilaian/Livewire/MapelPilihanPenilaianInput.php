<?php

namespace App\Modules\Penilaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanHarian;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use App\Modules\Penilaian\Services\MapelPilihanScoreService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Pengolahan Nilai Mapel Pilihan')]
class MapelPilihanPenilaianInput extends Component
{
    use GuardsPermission, WithFileUploads;

    public int $mapel_pilihan_id;
    public MapelPilihan $mapelPilihan;
    public $siswas = [];
    public $allTps = [];
    public $semesters = [];

    #[Url(as: 'tab')]
    public string $activeTab = 'formatif'; // 'tp', 'formatif', 'rekap'

    // Form TP State
    public ?int $tp_semester_id = null;
    public string $tp_kode = '';
    public string $tp_lingkup_materi = '';
    public string $tp_deskripsi = '';
    public string $tp_alokasi_ah = 'AH 1';
    public string $tp_target_periode = '9M1';
    public int $tp_urutan = 1;
    public ?int $edit_tp_id = null;

    // View Mode & Semester selection
    public string $tabelTahunMode = 'semua'; // 'semua', 'ganjil', 'genap'
    public string $activePeriode = '9M1';
    public string $rekapPeriode = '9M1';
    public ?int $activeNilaiSemesterId = null;
    public ?int $semGanjilId = null;
    public ?int $semGenapId = null;

    // Grades State
    public array $tpScores = []; // [siswa_id => [tp_id => score]]
    public array $nhData = []; // [siswa_id => [target_periode => score]] (default 100)
    public array $sumatifData = []; // [siswa_id => [target_periode => score]]
    public array $ahData = []; // [siswa_id => ['AH 1' => val, ...]] (compatibility)
    public array $sumatifDataGanjil = []; // [siswa_id => ['sts' => val, 'sas' => val]]
    public array $sumatifDataGenap = []; // [siswa_id => ['sts' => val, 'sat' => val]]
    public array $rekapData = []; // [siswa_id => NilaiMapelPilihanAkhir]

    // Modal Edit Deskripsi 9M State
    public bool $showModalDeskripsi9M = false;
    public ?int $selectedSiswaId9M = null;
    public ?string $selectedSiswaNama9M = null;
    public string $selectedPeriodKey9M = '9M1';
    public string $tempDeskripsiManual9M = '';
    public string $autoDeskripsiInfo9M = '';

    // Import Excel State
    public bool $showImportModal = false;
    public $importFile = null;

    public function mount(int $id): void
    {
        $this->guard('penilaian.lihat');

        $this->mapel_pilihan_id = $id;
        $this->mapelPilihan = MapelPilihan::with(['guru', 'guruPendamping', 'tahunAjaran'])->findOrFail($id);

        // Validasi Hak Akses Guru
        $user = auth()->user();
        $isSuper = $user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
        $pegawai = Pegawai::where('user_id', $user?->id)->first();

        if (!$isSuper && !$user->can('akademik.mapel.edit')) {
            if ($pegawai && $this->mapelPilihan->guru_id !== $pegawai->id && $this->mapelPilihan->guru_pendamping_id !== $pegawai->id) {
                abort(403, 'Anda bukan guru pengampu atau pendamping untuk Mapel Pilihan ini.');
            }
        }

        $validTabs = ['tp', 'formatif', 'rekap'];
        if (request()->has('tab') && in_array(request()->query('tab'), $validTabs)) {
            $this->activeTab = request()->query('tab');
        }

        $this->loadSemesters();
        $this->loadSiswa();
        $this->loadAllTP();

        $currSemObj = Semester::find($this->activeNilaiSemesterId);
        $isGenap = $currSemObj && str_contains(strtolower($currSemObj->semester), 'genap');
        $defaultP = $isGenap ? '9M3' : '9M1';
        $this->activePeriode = request()->query('periode', $defaultP);
        $this->rekapPeriode = $this->activePeriode;
        $this->tp_target_periode = $this->activePeriode;

        if (in_array($this->activePeriode, ['9M3', '9M4']) && $this->semGenapId) {
            $this->activeNilaiSemesterId = $this->semGenapId;
        } elseif (in_array($this->activePeriode, ['9M1', '9M2']) && $this->semGanjilId) {
            $this->activeNilaiSemesterId = $this->semGanjilId;
        }

        $this->loadNilai();
    }

    public function loadSemesters(): void
    {
        $taId = $this->mapelPilihan->tahun_ajaran_id ?? AcademicContext::currentTahunAjaran()?->id;

        $dbSemesters = Semester::where('tahun_ajaran_id', $taId)
            ->orderBy('semester')
            ->get();

        $this->semesters = [];
        foreach ($dbSemesters as $sem) {
            $isGenap = str_contains(strtolower($sem->semester), 'genap');
            if ($isGenap) {
                $this->semGenapId = $sem->id;
            } else {
                $this->semGanjilId = $sem->id;
            }

            $this->semesters[] = [
                'id' => $sem->id,
                'nama' => $sem->nama,
                'semester' => $sem->semester,
                'is_genap' => $isGenap,
            ];
        }

        $currSem = AcademicContext::currentSemester();
        $this->activeNilaiSemesterId = $currSem?->id ?? ($this->semGanjilId ?? $dbSemesters->first()?->id);
        $this->tp_semester_id = $this->activeNilaiSemesterId;
    }

    public function loadSiswa(): void
    {
        $anggotas = MapelPilihanAnggota::with(['siswa.rombel'])
            ->where('mapel_pilihan_id', $this->mapel_pilihan_id)
            ->where('status', 'aktif')
            ->get();

        $this->siswas = $anggotas->map(fn($a) => $a->siswa)->filter()->sortBy('nama')->values()->all();
    }

    public function loadAllTP(): void
    {
        $this->allTps = MapelPilihanTp::where('mapel_pilihan_id', $this->mapel_pilihan_id)
            ->orderBy('semester_id')
            ->orderBy('urutan')
            ->get();
    }

    public function loadNilai(): void
    {
        $siswaIds = collect($this->siswas)->pluck('id')->all();

        // 1. Formatif TP & AH
        $this->tpScores = [];
        $this->ahData = [];
        $formatifs = NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $this->mapel_pilihan_id)
            ->whereIn('siswa_id', $siswaIds)
            ->get();

        $allAhCodes = ['AH 1', 'AH 2', 'AH 3', 'AH 4', 'AH 5', 'AH 6', 'AH 7', 'AH 8'];

        // 2. Nilai Harian (NH)
        $this->nhData = [];
        $nhRecords = NilaiMapelPilihanHarian::where('mapel_pilihan_id', $this->mapel_pilihan_id)
            ->whereIn('siswa_id', $siswaIds)
            ->get();

        // 3. Sumatif
        $this->sumatifData = [];
        $this->sumatifDataGanjil = [];
        $this->sumatifDataGenap = [];
        $sumatifs = NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $this->mapel_pilihan_id)
            ->whereIn('siswa_id', $siswaIds)
            ->get();

        $sumGanjil = $this->semGanjilId ? $sumatifs->where('semester_id', $this->semGanjilId)->keyBy('siswa_id') : collect();
        $sumGenap = $this->semGenapId ? $sumatifs->where('semester_id', $this->semGenapId)->keyBy('siswa_id') : collect();

        foreach ($this->siswas as $siswa) {
            $sId = $siswa->id;
            $this->tpScores[$sId] = [];
            $sFormatifs = $formatifs->where('siswa_id', $sId);

            foreach ($this->allTps as $tp) {
                $f = $sFormatifs->firstWhere('mapel_pilihan_tp_id', $tp->id);
                if (!$f && $tp->kode_ah) {
                    $f = $sFormatifs->firstWhere('kode_ah', $tp->kode_ah);
                }
                $this->tpScores[$sId][$tp->id] = ($f && $f->nilai !== null) ? (float) $f->nilai : null;
            }

            $this->ahData[$sId] = [];
            foreach ($allAhCodes as $code) {
                $val = $sFormatifs->firstWhere('kode_ah', $code)?->nilai;
                $this->ahData[$sId][$code] = ($val !== null && $val !== '') ? (float) $val : null;
            }

            // NH
            $sNhs = $nhRecords->where('siswa_id', $sId)->keyBy('target_periode');
            $this->nhData[$sId] = [
                '9M1' => $sNhs->get('9M1')?->nilai !== null ? (float) $sNhs->get('9M1')->nilai : 100.0,
                '9M2' => $sNhs->get('9M2')?->nilai !== null ? (float) $sNhs->get('9M2')->nilai : 100.0,
                '9M3' => $sNhs->get('9M3')?->nilai !== null ? (float) $sNhs->get('9M3')->nilai : 100.0,
                '9M4' => $sNhs->get('9M4')?->nilai !== null ? (float) $sNhs->get('9M4')->nilai : 100.0,
            ];

            // Sumatif
            $gRow = $sumGanjil->get($sId);
            $eRow = $sumGenap->get($sId);

            $this->sumatifDataGanjil[$sId] = [
                'sts' => $gRow?->sts_1 !== null ? (float) $gRow->sts_1 : null,
                'sas' => $gRow?->sas !== null ? (float) $gRow->sas : null,
            ];

            $this->sumatifDataGenap[$sId] = [
                'sts' => $eRow?->sts_2 !== null ? (float) $eRow->sts_2 : null,
                'sas' => $eRow?->sat !== null ? (float) $eRow->sat : null,
            ];

            $this->sumatifData[$sId] = [
                '9M1' => $gRow?->sts_1 !== null ? (float) $gRow->sts_1 : null,
                '9M2' => $gRow?->sas !== null ? (float) $gRow->sas : null,
                '9M3' => $eRow?->sts_2 !== null ? (float) $eRow->sts_2 : null,
                '9M4' => $eRow?->sat !== null ? (float) $eRow->sat : null,
            ];
        }

        // 4. Load Rekap Akhir untuk semester aktif
        $this->loadRekapForActiveSemester();
    }

    public function switchPeriode(string $periode): void
    {
        $this->activePeriode = $periode;
        $this->rekapPeriode = $periode;
        $this->tp_target_periode = $periode;
        if (in_array($periode, ['9M1', '9M2']) && $this->semGanjilId) {
            $this->activeNilaiSemesterId = $this->semGanjilId;
            $this->loadRekapForActiveSemester();
        } elseif (in_array($periode, ['9M3', '9M4']) && $this->semGenapId) {
            $this->activeNilaiSemesterId = $this->semGenapId;
            $this->loadRekapForActiveSemester();
        }
    }

    public function switchRekapPeriode(string $mode): void
    {
        $this->rekapPeriode = $mode;
        if (in_array($mode, ['9M1', '9M2']) && $this->semGanjilId) {
            $this->activeNilaiSemesterId = $this->semGanjilId;
            $this->loadRekapForActiveSemester();
        } elseif (in_array($mode, ['9M3', '9M4']) && $this->semGenapId) {
            $this->activeNilaiSemesterId = $this->semGenapId;
            $this->loadRekapForActiveSemester();
        }
    }

    public function getTpsForPeriode(string $periode)
    {
        return collect($this->allTps)->filter(function ($tp) use ($periode) {
            if (!empty($tp->target_periode)) {
                return $tp->target_periode === $periode;
            }
            $isGenap = in_array($periode, ['9M3', '9M4']);
            $targetSemId = $isGenap ? $this->semGenapId : $this->semGanjilId;
            if ($targetSemId && $tp->semester_id == $targetSemId) {
                return $periode === ($isGenap ? '9M3' : '9M1');
            }
            return false;
        })->sortBy('urutan')->values();
    }

    public function getActiveTpsProperty()
    {
        return $this->getTpsForPeriode($this->activePeriode);
    }

    public function getLabelSumatif(string $periode): string
    {
        return match ($periode) {
            '9M1' => 'STS 1',
            '9M2' => 'SAS',
            '9M3' => 'STS 2',
            '9M4' => 'SAT',
            default => 'STS',
        };
    }

    public function loadRekapForActiveSemester(): void
    {
        if (!$this->activeNilaiSemesterId) return;

        $akhirs = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $this->mapel_pilihan_id)
            ->where('semester_id', $this->activeNilaiSemesterId)
            ->get()
            ->keyBy('siswa_id');

        $this->rekapData = [];
        foreach ($this->siswas as $siswa) {
            $this->rekapData[$siswa->id] = $akhirs->get($siswa->id);
        }
    }

    public function updatedActiveNilaiSemesterId(): void
    {
        $this->loadRekapForActiveSemester();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function saveTp(): void
    {
        $this->guard('penilaian.tambah');

        $this->validate([
            'tp_semester_id' => 'required|exists:semesters,id',
            'tp_kode' => 'required|string|max:20',
            'tp_deskripsi' => 'required|string',
            'tp_alokasi_ah' => 'required|string',
            'tp_target_periode' => 'required|string',
            'tp_urutan' => 'required|integer|min:1',
        ]);

        if ($this->edit_tp_id) {
            $tp = MapelPilihanTp::where('mapel_pilihan_id', $this->mapel_pilihan_id)->findOrFail($this->edit_tp_id);
            $tp->update([
                'semester_id' => $this->tp_semester_id,
                'kode_tp' => $this->tp_kode,
                'lingkup_materi' => $this->tp_lingkup_materi,
                'deskripsi_tp' => $this->tp_deskripsi,
                'kode_ah' => $this->tp_alokasi_ah,
                'target_periode' => $this->tp_target_periode,
                'urutan' => $this->tp_urutan,
            ]);
            session()->flash('status', 'Tujuan Pembelajaran berhasil diperbarui.');
        } else {
            MapelPilihanTp::create([
                'mapel_pilihan_id' => $this->mapel_pilihan_id,
                'semester_id' => $this->tp_semester_id,
                'kode_tp' => $this->tp_kode,
                'lingkup_materi' => $this->tp_lingkup_materi,
                'deskripsi_tp' => $this->tp_deskripsi,
                'kode_ah' => $this->tp_alokasi_ah,
                'target_periode' => $this->tp_target_periode,
                'urutan' => $this->tp_urutan,
            ]);
            session()->flash('status', 'Tujuan Pembelajaran berhasil ditambahkan.');
        }

        $this->resetTpForm();
        $this->loadAllTP();

        // Recalculate Rekap jika semester sesuai
        $sem = Semester::find($this->tp_semester_id);
        if ($sem) {
            app(MapelPilihanScoreService::class)->calculateAndSaveRekap($this->mapelPilihan, $sem);
            $this->loadRekapForActiveSemester();
        }
    }

    public function editTp(int $id): void
    {
        $tp = MapelPilihanTp::where('mapel_pilihan_id', $this->mapel_pilihan_id)->findOrFail($id);
        $this->edit_tp_id = $tp->id;
        $this->tp_semester_id = $tp->semester_id;
        $this->tp_kode = $tp->kode_tp;
        $this->tp_lingkup_materi = $tp->lingkup_materi ?? '';
        $this->tp_deskripsi = $tp->deskripsi_tp;
        $this->tp_alokasi_ah = $tp->kode_ah ?? 'AH 1';
        $this->tp_target_periode = $tp->target_periode ?? '9M1';
        $this->tp_urutan = $tp->urutan ?? 1;
    }

    public function resetTpForm(): void
    {
        $this->edit_tp_id = null;
        $this->tp_kode = '';
        $this->tp_lingkup_materi = '';
        $this->tp_deskripsi = '';
        $this->tp_alokasi_ah = 'AH 1';
        $this->tp_target_periode = '9M1';
        $this->tp_urutan = count($this->allTps) + 1;
    }

    public function deleteTp(int $id): void
    {
        $this->guard('penilaian.hapus');

        $tp = MapelPilihanTp::where('mapel_pilihan_id', $this->mapel_pilihan_id)->findOrFail($id);
        $semId = $tp->semester_id;
        $tp->delete();

        $this->loadAllTP();
        $sem = Semester::find($semId);
        if ($sem) {
            app(MapelPilihanScoreService::class)->calculateAndSaveRekap($this->mapelPilihan, $sem);
            $this->loadRekapForActiveSemester();
        }

        session()->flash('status', 'Tujuan Pembelajaran berhasil dihapus.');
    }

    public function saveNilai(): void
    {
        $this->guard('penilaian.tambah');

        // Fallback synchronization from legacy ahData / sumatifDataGanjil / sumatifDataGenap
        foreach ($this->siswas as $siswa) {
            $sId = $siswa->id;
            foreach ($this->allTps as $tp) {
                if ((!isset($this->tpScores[$sId][$tp->id]) || $this->tpScores[$sId][$tp->id] === null || $this->tpScores[$sId][$tp->id] === '')
                    && isset($this->ahData[$sId][$tp->kode_ah]) && $this->ahData[$sId][$tp->kode_ah] !== null && $this->ahData[$sId][$tp->kode_ah] !== '') {
                    $this->tpScores[$sId][$tp->id] = $this->ahData[$sId][$tp->kode_ah];
                }
            }
            if (!isset($this->nhData[$sId])) {
                $this->nhData[$sId] = ['9M1' => 100.0, '9M2' => 100.0, '9M3' => 100.0, '9M4' => 100.0];
            }
            if ((!isset($this->sumatifData[$sId]['9M1']) || $this->sumatifData[$sId]['9M1'] === null) && isset($this->sumatifDataGanjil[$sId]['sts']) && $this->sumatifDataGanjil[$sId]['sts'] !== null && $this->sumatifDataGanjil[$sId]['sts'] !== '') {
                $this->sumatifData[$sId]['9M1'] = $this->sumatifDataGanjil[$sId]['sts'];
            }
            if ((!isset($this->sumatifData[$sId]['9M2']) || $this->sumatifData[$sId]['9M2'] === null) && isset($this->sumatifDataGanjil[$sId]['sas']) && $this->sumatifDataGanjil[$sId]['sas'] !== null && $this->sumatifDataGanjil[$sId]['sas'] !== '') {
                $this->sumatifData[$sId]['9M2'] = $this->sumatifDataGanjil[$sId]['sas'];
            }
            if ((!isset($this->sumatifData[$sId]['9M3']) || $this->sumatifData[$sId]['9M3'] === null) && isset($this->sumatifDataGenap[$sId]['sts']) && $this->sumatifDataGenap[$sId]['sts'] !== null && $this->sumatifDataGenap[$sId]['sts'] !== '') {
                $this->sumatifData[$sId]['9M3'] = $this->sumatifDataGenap[$sId]['sts'];
            }
            if ((!isset($this->sumatifData[$sId]['9M4']) || $this->sumatifData[$sId]['9M4'] === null) && isset($this->sumatifDataGenap[$sId]['sas']) && $this->sumatifDataGenap[$sId]['sas'] !== null && $this->sumatifDataGenap[$sId]['sas'] !== '') {
                $this->sumatifData[$sId]['9M4'] = $this->sumatifDataGenap[$sId]['sas'];
            }
        }

        $this->validate([
            'tpScores.*.*' => 'nullable|numeric|min:0|max:100',
            'nhData.*.*' => 'nullable|numeric|min:0|max:100',
            'sumatifData.*.*' => 'nullable|numeric|min:0|max:100',
            'ahData.*.*' => 'nullable|numeric|min:0|max:100',
        ], [
            'tpScores.*.*.numeric' => 'Nilai TP harus berupa angka.',
            'tpScores.*.*.min' => 'Nilai TP minimal 0.',
            'tpScores.*.*.max' => 'Nilai TP maksimal 100.',
            'nhData.*.*.numeric' => 'Nilai Harian (NH) harus berupa angka.',
            'nhData.*.*.min' => 'Nilai Harian (NH) minimal 0.',
            'nhData.*.*.max' => 'Nilai Harian (NH) maksimal 100.',
            'sumatifData.*.*.numeric' => 'Nilai STS/SAS/SAT harus berupa angka.',
            'sumatifData.*.*.min' => 'Nilai STS/SAS/SAT minimal 0.',
            'sumatifData.*.*.max' => 'Nilai STS/SAS/SAT maksimal 100.',
        ]);

        $scoreService = app(MapelPilihanScoreService::class);

        // 1. Simpan TP Formatif
        foreach ($this->tpScores as $siswaId => $scores) {
            foreach ($scores as $tpId => $val) {
                $tp = collect($this->allTps)->firstWhere('id', (int)$tpId);
                if (!$tp) continue;

                if ($val !== null && $val !== '' && is_numeric($val)) {
                    NilaiMapelPilihanFormatif::updateOrCreate(
                        [
                            'mapel_pilihan_id' => $this->mapel_pilihan_id,
                            'siswa_id' => $siswaId,
                            'mapel_pilihan_tp_id' => $tp->id,
                        ],
                        [
                            'semester_id' => $tp->semester_id,
                            'kode_ah' => $tp->kode_ah,
                            'nilai' => (float)$val,
                        ]
                    );
                } else {
                    NilaiMapelPilihanFormatif::where('mapel_pilihan_id', $this->mapel_pilihan_id)
                        ->where('siswa_id', $siswaId)
                        ->where('mapel_pilihan_tp_id', $tp->id)
                        ->delete();
                }
            }
        }

        // 2. Simpan Nilai Harian (NH)
        foreach ($this->nhData as $siswaId => $pScores) {
            foreach ($pScores as $periode => $val) {
                $nhVal = ($val !== null && $val !== '' && is_numeric($val)) ? (float)$val : 100.0;
                NilaiMapelPilihanHarian::updateOrCreate(
                    [
                        'mapel_pilihan_id' => $this->mapel_pilihan_id,
                        'siswa_id' => $siswaId,
                        'target_periode' => $periode,
                    ],
                    ['nilai' => $nhVal]
                );
            }
        }

        // 3. Simpan Sumatif
        foreach ($this->sumatifData as $siswaId => $pScores) {
            if ($this->semGanjilId) {
                $sts1 = isset($pScores['9M1']) && is_numeric($pScores['9M1']) ? (float)$pScores['9M1'] : null;
                $sas = isset($pScores['9M2']) && is_numeric($pScores['9M2']) ? (float)$pScores['9M2'] : null;
                if ($sts1 !== null || $sas !== null) {
                    NilaiMapelPilihanSumatif::updateOrCreate(
                        ['mapel_pilihan_id' => $this->mapel_pilihan_id, 'siswa_id' => $siswaId, 'semester_id' => $this->semGanjilId],
                        ['sts_1' => $sts1, 'sas' => $sas]
                    );
                } else {
                    NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $this->mapel_pilihan_id)->where('siswa_id', $siswaId)->where('semester_id', $this->semGanjilId)->delete();
                }
            }

            if ($this->semGenapId) {
                $sts2 = isset($pScores['9M3']) && is_numeric($pScores['9M3']) ? (float)$pScores['9M3'] : null;
                $sat = isset($pScores['9M4']) && is_numeric($pScores['9M4']) ? (float)$pScores['9M4'] : null;
                if ($sts2 !== null || $sat !== null) {
                    NilaiMapelPilihanSumatif::updateOrCreate(
                        ['mapel_pilihan_id' => $this->mapel_pilihan_id, 'siswa_id' => $siswaId, 'semester_id' => $this->semGenapId],
                        ['sts_2' => $sts2, 'sat' => $sat]
                    );
                } else {
                    NilaiMapelPilihanSumatif::where('mapel_pilihan_id', $this->mapel_pilihan_id)->where('siswa_id', $siswaId)->where('semester_id', $this->semGenapId)->delete();
                }
            }
        }

        // 4. Hitung Ulang Rekap
        if ($this->semGanjilId) {
            $semG = Semester::find($this->semGanjilId);
            if ($semG) $scoreService->calculateAndSaveRekap($this->mapelPilihan, $semG);
        }
        if ($this->semGenapId) {
            $semE = Semester::find($this->semGenapId);
            if ($semE) $scoreService->calculateAndSaveRekap($this->mapelPilihan, $semE);
        }

        $this->loadRekapForActiveSemester();
        session()->flash('status', 'Seluruh Nilai TP, Nilai Harian (NH), dan Sumatif berhasil disimpan.');
    }

    public function hitungUlangRekap(): void
    {
        $sem = Semester::find($this->activeNilaiSemesterId);
        if ($sem) {
            app(MapelPilihanScoreService::class)->calculateAndSaveRekap($this->mapelPilihan, $sem);
            $this->loadRekapForActiveSemester();
            session()->flash('status', 'Rekapitulasi nilai berhasil dihitung ulang.');
        }
    }

    public function openModalDeskripsi9M(int $siswaId, string $periodKey): void
    {
        $this->selectedSiswaId9M = $siswaId;
        $this->selectedPeriodKey9M = $periodKey;

        $siswa = collect($this->siswas)->firstWhere('id', $siswaId);
        $this->selectedSiswaNama9M = $siswa?->nama ?? 'Siswa';

        $rekap = $this->rekapData[$siswaId] ?? null;
        $block = $rekap?->data_9m[$periodKey] ?? [];

        $this->tempDeskripsiManual9M = $block['deskripsi_manual'] ?? '';
        $this->autoDeskripsiInfo9M = $block['deskripsi_tertinggi'] ?? ($block['status'] ?? '-');

        $this->showModalDeskripsi9M = true;
    }

    public function saveDeskripsi9M(): void
    {
        $this->guard('penilaian.edit');

        if (!$this->selectedSiswaId9M || !$this->activeNilaiSemesterId) return;

        $na = NilaiMapelPilihanAkhir::firstOrNew([
            'mapel_pilihan_id' => $this->mapel_pilihan_id,
            'siswa_id' => $this->selectedSiswaId9M,
            'semester_id' => $this->activeNilaiSemesterId,
        ]);

        $data9m = $na->data_9m ?? [];
        if (!isset($data9m[$this->selectedPeriodKey9M])) {
            $data9m[$this->selectedPeriodKey9M] = [];
        }
        $data9m[$this->selectedPeriodKey9M]['deskripsi_manual'] = trim($this->tempDeskripsiManual9M) ?: null;

        $na->data_9m = $data9m;
        $na->deskripsi_manual = trim($this->tempDeskripsiManual9M) ?: null;
        if (!$na->exists) {
            $na->status = 'belum_lengkap';
        }
        $na->save();

        $this->showModalDeskripsi9M = false;
        $this->loadRekapForActiveSemester();
        session()->flash('status', 'Catatan deskripsi rapor berhasil disimpan.');
    }

    public function exportTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai Mapil');

        // Header Title
        $sheet->setCellValue('A1', 'FORMAT IMPORT NILAI MAPEL PILIHAN');
        $sheet->setCellValue('A2', 'Mapel Pilihan: ' . $this->mapelPilihan->nama . ' (' . $this->mapelPilihan->kode . ')');
        $sheet->setCellValue('A3', 'KKM: ' . ($this->mapelPilihan->kkm_default ?? 75));

        // Table Columns
        $columns = [
            'No', 'NISN', 'Nama Siswa', 'Rombel',
            'AH 1', 'AH 2', 'STS 1', 'AH 3', 'AH 4', 'SAS',
            'AH 5', 'AH 6', 'STS 2', 'AH 7', 'AH 8', 'SAT'
        ];

        $colChar = 'A';
        foreach ($columns as $col) {
            $sheet->setCellValue($colChar . '5', $col);
            $sheet->getColumnDimension($colChar)->setAutoSize(true);
            $colChar++;
        }

        $sheet->getStyle('A5:P5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rowNum = 6;
        $no = 1;
        foreach ($this->siswas as $siswa) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValueExplicit('B' . $rowNum, $siswa->nisn ?: $siswa->nipd ?: '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowNum, $siswa->nama);
            $sheet->setCellValue('D' . $rowNum, $siswa->rombel?->nama ?? '-');

            // Formatif values
            $sheet->setCellValue('E' . $rowNum, $this->ahData[$siswa->id]['AH 1'] ?? '');
            $sheet->setCellValue('F' . $rowNum, $this->ahData[$siswa->id]['AH 2'] ?? '');
            $sheet->setCellValue('G' . $rowNum, $this->sumatifDataGanjil[$siswa->id]['sts'] ?? '');
            $sheet->setCellValue('H' . $rowNum, $this->ahData[$siswa->id]['AH 3'] ?? '');
            $sheet->setCellValue('I' . $rowNum, $this->ahData[$siswa->id]['AH 4'] ?? '');
            $sheet->setCellValue('J' . $rowNum, $this->sumatifDataGanjil[$siswa->id]['sas'] ?? '');

            $sheet->setCellValue('K' . $rowNum, $this->ahData[$siswa->id]['AH 5'] ?? '');
            $sheet->setCellValue('L' . $rowNum, $this->ahData[$siswa->id]['AH 6'] ?? '');
            $sheet->setCellValue('M' . $rowNum, $this->sumatifDataGenap[$siswa->id]['sts'] ?? '');
            $sheet->setCellValue('N' . $rowNum, $this->ahData[$siswa->id]['AH 7'] ?? '');
            $sheet->setCellValue('O' . $rowNum, $this->ahData[$siswa->id]['AH 8'] ?? '');
            $sheet->setCellValue('P' . $rowNum, $this->sumatifDataGenap[$siswa->id]['sas'] ?? '');

            $rowNum++;
        }

        $cleanKode = preg_replace('/[^A-Za-z0-9_-]/', '_', $this->mapelPilihan->kode);
        $fileName = 'Format_Nilai_Mapil_' . $cleanKode . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }

    public function importNilai(): void
    {
        $this->guard('penilaian.tambah');

        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $filePath = $this->importFile->getRealPath();
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Cari header row
        $headerRowIndex = null;
        foreach ($rows as $idx => $r) {
            if (isset($r[1]) && (str_contains(strtoupper($r[1]), 'NISN') || str_contains(strtoupper($r[2] ?? ''), 'NAMA'))) {
                $headerRowIndex = $idx;
                break;
            }
        }

        if ($headerRowIndex === null) {
            session()->flash('error', 'Format file tidak sesuai. Header NISN/Nama tidak ditemukan.');
            return;
        }

        $importedCount = 0;
        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $r = $rows[$i];
            $nisn = trim((string) ($r[1] ?? ''));
            $nama = trim((string) ($r[2] ?? ''));

            if (!$nisn && !$nama) continue;

            $siswa = collect($this->siswas)->first(function ($s) use ($nisn, $nama) {
                if ($nisn && ($s->nisn === $nisn || $s->nipd === $nisn)) return true;
                return strtolower($s->nama) === strtolower($nama);
            });

            if (!$siswa) continue;

            $sId = $siswa->id;

            // Formatif AH 1..8
            $ahCols = [
                'AH 1' => $r[4] ?? null,
                'AH 2' => $r[5] ?? null,
                'AH 3' => $r[7] ?? null,
                'AH 4' => $r[8] ?? null,
                'AH 5' => $r[10] ?? null,
                'AH 6' => $r[11] ?? null,
                'AH 7' => $r[13] ?? null,
                'AH 8' => $r[14] ?? null,
            ];

            foreach ($ahCols as $ahCode => $val) {
                if ($val !== null && $val !== '' && is_numeric($val)) {
                    $this->ahData[$sId][$ahCode] = (float) $val;
                }
            }

            // Sumatifs
            if (isset($r[6]) && is_numeric($r[6])) $this->sumatifDataGanjil[$sId]['sts'] = (float) $r[6];
            if (isset($r[9]) && is_numeric($r[9])) $this->sumatifDataGanjil[$sId]['sas'] = (float) $r[9];
            if (isset($r[12]) && is_numeric($r[12])) $this->sumatifDataGenap[$sId]['sts'] = (float) $r[12];
            if (isset($r[15]) && is_numeric($r[15])) $this->sumatifDataGenap[$sId]['sas'] = (float) $r[15];

            $importedCount++;
        }

        $this->saveNilai();
        $this->showImportModal = false;
        $this->reset('importFile');

        session()->flash('status', "Berhasil mengimpor nilai untuk {$importedCount} siswa.");
    }

    public function render()
    {
        $kkm = (float) ($this->mapelPilihan->kkm_default ?? 75);
        $currSemObj = Semester::find($this->activeNilaiSemesterId);
        $isGenap = $currSemObj && str_contains(strtolower($currSemObj->semester), 'genap');

        return view('penilaian::livewire.mapel-pilihan-penilaian-input', [
            'kkm' => $kkm,
            'isGenap' => $isGenap,
            'currSemObj' => $currSemObj,
        ]);
    }
}
