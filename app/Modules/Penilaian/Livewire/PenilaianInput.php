<?php

namespace App\Modules\Penilaian\Livewire;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\NilaiImporter;
use App\Modules\Penilaian\Services\NilaiTemplateExporter;
use App\Modules\Penilaian\Services\PenilaianScoreCalculator;
use App\Modules\Penilaian\Services\TujuanPembelajaranCopier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class PenilaianInput extends Component
{
    use WithFileUploads;

    public $penugasan_id;
    public $penugasan;
    public $siswas = [];
    public $tps = []; // TPs for the currently active semester being assessed
    public $allTps = []; // All TPs across Ganjil & Genap for this Mapel & Rombel
    public $semesters = []; // [ ['id' => ..., 'semester' => ..., 'nama' => ..., 'is_current_penugasan' => ..., 'penugasan_id' => ...] ]

    // State untuk Modal Import Nilai
    public bool $showImportModal = false;
    public string $importStep = 'select'; // 'select' | 'preview'
    public array $importSelectedColumns = [];
    public string $importRombelScope = 'current'; // 'current' | 'all_assigned'
    public $importFile = null;
    public ?array $importPreviewData = null;
    public bool $isImporting = false;

    // State untuk Modal Salin TP ke Rombel Lain
    public bool $showCopyTpModal = false;
    public string $copyTpSemesterScope = 'all'; // 'all' | 'ganjil' | 'genap'
    public array $selectedTargetRombelIds = [];
    public array $availableTargetRombels = [];
    public bool $isCopyingTp = false;

    // Tab state: 'tp', 'formatif', 'rekap'
    #[Url(as: 'tab')]
    public $activeTab = 'tp';

    // State for Active 9M Periode
    #[Url(as: 'periode')]
    public string $activePeriode = '9M1';
    public string $rekapPeriode = '9M1';

    // State for TP Form (Tab 1)
    public $tp_semester_id = null;
    public ?string $tp_target_periode = '9M1';
    public string $tp_lingkup_materi = '';
    public int $tp_urutan = 1;
    public $tp_kode = '';
    public $tp_deskripsi = '';
    public $tp_alokasi_ah = '';
    public $edit_tp_id = null;

    // State for Active Assessment Semester in Tab 2 & 3
    public $activeNilaiSemesterId = null;
    public $activeNilaiPenugasanId = null;

    // View mode for Tab 2: 'semua' (1 Tahun Penuh), 'ganjil', 'genap'
    public $tabelTahunMode = 'semua';

    // Penugasan IDs for both semesters
    public $pmGanjilId = null;
    public $pmGenapId = null;
    public $semGanjilId = null;
    public $semGenapId = null;

    // Grades State
    public array $tpScores = []; // [siswa_id => [tp_id => score]]
    public array $nhData = []; // [siswa_id => [target_periode => score]] (default 100)

    // State for Input Asesmen Harian (AH 1 s.d AH 8) sepanjang tahun
    public $ahData = [];

    // State for Sumatif Ganjil (STS 1 & SAS) & Genap (STS 2 & SAT)
    public $sumatifDataGanjil = [];
    public $sumatifDataGenap = [];

    // State for Input Formatif (for compatibility & sync)
    public $formatifData = [];

    // State for Input Sumatif (for compatibility)
    public $sumatifData = [];
    public $initialSumatifData = [];

    // State for Rekap
    public $rekapData = [];

    // State for Modal Edit Deskripsi 9M
    public bool $showModalDeskripsi9M = false;
    public ?int $selectedSiswaId9M = null;
    public ?string $selectedSiswaNama9M = null;
    public string $selectedPeriodKey9M = ''; // '9M1', '9M2', '9M3', '9M4'
    public string $selectedPeriodLabel9M = '';
    public string $tempDeskripsiManual9M = '';
    public string $autoDeskripsiInfo9M = '';

    public function mount($penugasan_id)
    {
        $this->penugasan_id = $penugasan_id;
        $this->penugasan = PenugasanMengajar::with(['mapel', 'rombel', 'semester.tahunAjaran'])->findOrFail($penugasan_id);

        $sessionKey = 'penilaian_active_tab_' . $penugasan_id;
        if (request()->has('tab') && in_array(request()->query('tab'), ['tp', 'formatif', 'rekap'])) {
            $this->activeTab = request()->query('tab');
            session([$sessionKey => $this->activeTab]);
        } elseif (session()->has($sessionKey)) {
            $this->activeTab = session($sessionKey);
        } else {
            $this->activeTab = $this->activeTab ?: 'tp';
        }

        $this->loadSemestersAndPenugasans();

        $this->activeNilaiSemesterId = $this->penugasan->semester_id;
        $this->activeNilaiPenugasanId = $this->penugasan->id;
        $this->tp_semester_id = $this->penugasan->semester_id;
        $this->tp_alokasi_ah = $this->availableAhOptions[0] ?? 'AH 1';

        $isGenap = str_contains(strtolower($this->penugasan->semester?->semester ?? ''), 'genap');
        $defaultP = $isGenap ? '9M3' : '9M1';
        $this->activePeriode = request()->query('periode', $defaultP);
        $this->rekapPeriode = $this->activePeriode;
        $this->tp_target_periode = $this->activePeriode;

        $this->loadAllTP();
        $this->tp_urutan = ($this->allTps->max('urutan') ?? 0) + 1;
        $this->loadSiswa();
        $this->loadNilaiForActiveSemester();

        $this->importSelectedColumns = NilaiTemplateExporter::getColumnsByScope($this->penugasan, 'periode_aktif', $this->activePeriode);
    }

    public function loadSemestersAndPenugasans()
    {
        $taId = $this->penugasan->semester?->tahun_ajaran_id;
        $dbSemesters = Semester::where('tahun_ajaran_id', $taId)
            ->orderBy('semester')
            ->get();

        $this->semesters = [];
        foreach ($dbSemesters as $sem) {
            $pm = PenugasanMengajar::where('rombel_id', $this->penugasan->rombel_id)
                ->where('mapel_id', $this->penugasan->mapel_id)
                ->where('semester_id', $sem->id)
                ->first();

            // Auto-create penugasan untuk semester pasangan jika belum ada
            if (!$pm) {
                $pm = PenugasanMengajar::create([
                    'semester_id' => $sem->id,
                    'rombel_id' => $this->penugasan->rombel_id,
                    'mapel_id' => $this->penugasan->mapel_id,
                    'pegawai_id' => $this->penugasan->pegawai_id,
                    'jp' => $this->penugasan->jp,
                    'keterangan' => $this->penugasan->keterangan,
                ]);
            }

            if (strtolower($sem->semester) === 'genap') {
                $this->semGenapId = $sem->id;
                $this->pmGenapId = $pm->id;
            } else {
                $this->semGanjilId = $sem->id;
                $this->pmGanjilId = $pm->id;
            }

            $this->semesters[] = [
                'id' => $sem->id,
                'semester' => $sem->semester,
                'nama' => 'Semester ' . ucfirst($sem->semester),
                'is_current_penugasan' => ($sem->id === $this->penugasan->semester_id),
                'penugasan_id' => $pm->id,
            ];
        }
    }

    public function loadAllTP()
    {
        $allPmIds = collect($this->semesters)->pluck('penugasan_id')->all();

        $this->allTps = TujuanPembelajaran::with(['penugasanMengajar.semester'])
            ->withCount('nilaiFormatifs')
            ->whereIn('penugasan_mengajar_id', $allPmIds)
            ->get()
            ->sortBy(function ($tp) {
                $sem = strtolower($tp->penugasanMengajar?->semester?->semester ?? '');
                $semOrder = str_contains($sem, 'genap') ? 2 : 1;
                preg_match('/\d+/', $tp->kode, $matches);
                $num = isset($matches[0]) ? (int) $matches[0] : 0;
                return sprintf('%d-%05d-%05d', $semOrder, $num, $tp->id);
            })
            ->values();
    }

    public function loadSiswa()
    {
        $this->siswas = Siswa::inRombel($this->penugasan->rombel_id)->orderBy('nama')->get();
    }

    public function loadNilaiForActiveSemester()
    {
        // 1. Ambil TPs untuk penugasan semester yang sedang aktif dinilai
        $this->tps = TujuanPembelajaran::where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        // 2. Load TP Formatif & Nilai Harian (NH)
        $this->ahData = [];
        $this->formatifData = [];
        $this->tpScores = [];
        $this->nhData = [];

        $allAhs = ['AH 1', 'AH 2', 'AH 3', 'AH 4', 'AH 5', 'AH 6', 'AH 7', 'AH 8'];
        $allPmIds = array_values(array_filter([$this->pmGanjilId, $this->pmGenapId]));

        $ahRecords = NilaiAsesmenHarian::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('penugasan_mengajar_id', $allPmIds)
            ->whereIn('kode_ah', $allAhs)
            ->get();

        $nilaiFormatif = NilaiFormatif::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->allTps->pluck('id'))
            ->get();

        $nhRecords = NilaiHarian::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('penugasan_mengajar_id', $allPmIds)
            ->get();

        $mapping = $this->ahTpsMapping;

        foreach ($this->siswas as $siswa) {
            $this->tpScores[$siswa->id] = [];
            foreach ($this->allTps as $tp) {
                $n = $nilaiFormatif->where('siswa_id', $siswa->id)->where('tujuan_pembelajaran_id', $tp->id)->first();
                $this->formatifData[$siswa->id][$tp->id] = $n ? $n->nilai : null;
                $this->tpScores[$siswa->id][$tp->id] = $n ? (float) $n->nilai : null;
            }

            // Nilai Harian (default 100.0)
            $this->nhData[$siswa->id] = [];
            foreach (['9M1', '9M2', '9M3', '9M4'] as $p) {
                $isGenap = in_array($p, ['9M3', '9M4']);
                $targetPmId = $isGenap ? $this->pmGenapId : $this->pmGanjilId;
                $recNh = $nhRecords->where('siswa_id', $siswa->id)->where('penugasan_mengajar_id', $targetPmId)->where('target_periode', $p)->first();
                $this->nhData[$siswa->id][$p] = $recNh ? (float) $recNh->nilai : 100.00;
            }

            foreach ($allAhs as $idx => $ahCode) {
                $isGenap = $idx >= 4;
                $targetPmId = $isGenap ? $this->pmGenapId : $this->pmGanjilId;

                $rec = $ahRecords->where('siswa_id', $siswa->id)
                    ->where('penugasan_mengajar_id', $targetPmId)
                    ->where('kode_ah', $ahCode)
                    ->first();

                if ($rec !== null && $rec->nilai !== null) {
                    $this->ahData[$siswa->id][$ahCode] = $rec->nilai;
                } else {
                    $tpsInAh = $mapping[$ahCode] ?? collect();
                    $tpFirst = $tpsInAh->first();
                    if ($tpFirst) {
                        $nf = $nilaiFormatif->where('siswa_id', $siswa->id)->where('tujuan_pembelajaran_id', $tpFirst->id)->first();
                        $this->ahData[$siswa->id][$ahCode] = $nf ? $nf->nilai : null;
                    } else {
                        $this->ahData[$siswa->id][$ahCode] = null;
                    }
                }
            }
        }

        // 3. Load Sumatif Ganjil & Genap
        $this->sumatifDataGanjil = [];
        $this->sumatifDataGenap = [];
        $this->sumatifData = [];

        $sumatifAll = NilaiSumatif::whereIn('penugasan_mengajar_id', $allPmIds)->get();

        foreach ($this->siswas as $siswa) {
            $sGanjil = $sumatifAll->where('penugasan_mengajar_id', $this->pmGanjilId)->where('siswa_id', $siswa->id)->first();
            $sGenap = $sumatifAll->where('penugasan_mengajar_id', $this->pmGenapId)->where('siswa_id', $siswa->id)->first();

            $this->sumatifDataGanjil[$siswa->id] = [
                'sts' => $sGanjil ? $sGanjil->sts : null,
                'sas' => $sGanjil ? $sGanjil->sas : null,
            ];

            $this->sumatifDataGenap[$siswa->id] = [
                'sts' => $sGenap ? $sGenap->sts : null,
                'sas' => $sGenap ? $sGenap->sas : null,
            ];

            $this->sumatifData[$siswa->id] = [
                '9M1' => $sGanjil ? $sGanjil->sts : null,
                '9M2' => $sGanjil ? $sGanjil->sas : null,
                '9M3' => $sGenap ? $sGenap->sts : null,
                '9M4' => $sGenap ? $sGenap->sas : null,
            ];
        }

        $this->initialSumatifData = $this->sumatifData;

        // 4. Jika sedang di tab rekap, hitung otomatis
        if ($this->activeTab === 'rekap') {
            $this->calculateRekap();
        }
    }

    public function setTabelMode($mode)
    {
        $this->tabelTahunMode = $mode;
        if ($mode === 'genap' && $this->semGenapId) {
            $this->activeNilaiSemesterId = $this->semGenapId;
            $this->activeNilaiPenugasanId = $this->pmGenapId;
        } elseif ($mode === 'ganjil' && $this->semGanjilId) {
            $this->activeNilaiSemesterId = $this->semGanjilId;
            $this->activeNilaiPenugasanId = $this->pmGanjilId;
        }
    }

    public function updatedTpSemesterId($val)
    {
        $options = $this->availableAhOptions;
        $this->tp_alokasi_ah = $options[0] ?? '';
    }

    public function updatedSumatifData($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $siswaId = (int) $parts[0];
            $type = $parts[1]; // 'sts' atau 'sas' or '9M1'..'9M4'
            if ($type === '9M1') {
                $this->sumatifDataGanjil[$siswaId]['sts'] = $value;
            } elseif ($type === '9M2') {
                $this->sumatifDataGanjil[$siswaId]['sas'] = $value;
            } elseif ($type === '9M3') {
                $this->sumatifDataGenap[$siswaId]['sts'] = $value;
            } elseif ($type === '9M4') {
                $this->sumatifDataGenap[$siswaId]['sas'] = $value;
            } elseif ($type === 'sts' || $type === 'sas') {
                if ($this->activeNilaiPenugasanId == $this->pmGenapId) {
                    $this->sumatifDataGenap[$siswaId][$type] = $value;
                    $p = ($type === 'sts') ? '9M3' : '9M4';
                    $this->sumatifData[$siswaId][$p] = $value;
                } else {
                    $this->sumatifDataGanjil[$siswaId][$type] = $value;
                    $p = ($type === 'sts') ? '9M1' : '9M2';
                    $this->sumatifData[$siswaId][$p] = $value;
                }
            }
        }
    }

    public function updatedSumatifDataGanjil($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $siswaId = (int) $parts[0];
            $field = $parts[1]; // 'sts' or 'sas'
            $period = ($field === 'sts') ? '9M1' : '9M2';
            $this->sumatifData[$siswaId][$period] = $value;
        }
    }

    public function updatedSumatifDataGenap($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $siswaId = (int) $parts[0];
            $field = $parts[1]; // 'sts' or 'sas'
            $period = ($field === 'sts') ? '9M3' : '9M4';
            $this->sumatifData[$siswaId][$period] = $value;
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        session(['penilaian_active_tab_' . $this->penugasan_id => $tab]);
        if ($tab === 'rekap') {
            $this->calculateRekap();
        }
    }

    public function switchNilaiSemester($semesterId)
    {
        $target = collect($this->semesters)->firstWhere('id', (int) $semesterId);
        if ($target) {
            $this->activeNilaiSemesterId = $target['id'];
            $this->activeNilaiPenugasanId = $target['penugasan_id'];
            $isGenap = str_contains(strtolower($target['semester']), 'genap');
            $this->tabelTahunMode = $isGenap ? 'genap' : 'ganjil';
            $this->loadNilaiForActiveSemester();
        }
    }

    public function getSuggestedNextNumberProperty(): int
    {
        $maxNum = 0;
        foreach ($this->allTps as $tp) {
            if (preg_match('/\d+/', $tp->kode, $matches)) {
                $val = (int) $matches[0];
                if ($val > $maxNum) {
                    $maxNum = $val;
                }
            }
        }
        return $maxNum + 1;
    }

    public function getSuggestedNextKodeProperty(): string
    {
        return 'TP ' . $this->suggestedNextNumber;
    }

    public function getAvailableAhOptionsProperty(): array
    {
        $sem = collect($this->semesters)->firstWhere('id', (int) $this->tp_semester_id);
        $semName = strtolower($sem['semester'] ?? '');
        return str_contains($semName, 'genap')
            ? ['AH 5', 'AH 6', 'AH 7', 'AH 8']
            : ['AH 1', 'AH 2', 'AH 3', 'AH 4'];
    }

    public function getActiveAhListProperty(): array
    {
        $sem = collect($this->semesters)->firstWhere('id', (int) $this->activeNilaiSemesterId);
        $semName = strtolower($sem['semester'] ?? '');
        return str_contains($semName, 'genap')
            ? ['AH 5', 'AH 6', 'AH 7', 'AH 8']
            : ['AH 1', 'AH 2', 'AH 3', 'AH 4'];
    }

    public function getAhTpsMappingProperty(): array
    {
        $mapping = [];
        $allAhs = ['AH 1', 'AH 2', 'AH 3', 'AH 4', 'AH 5', 'AH 6', 'AH 7', 'AH 8'];
        $allTpsList = $this->allTps;

        foreach ($allAhs as $idx => $ah) {
            $isGenap = $idx >= 4;
            $targetPmId = $isGenap ? $this->pmGenapId : $this->pmGanjilId;
            $tpsInSem = $allTpsList->where('penugasan_mengajar_id', $targetPmId)->values();

            $mapping[$ah] = $tpsInSem->filter(function ($tp, $tpIdx) use ($ah, $idx) {
                if (!empty($tp->alokasi_ah)) {
                    return $tp->alokasi_ah === $ah;
                }
                // Fallback untuk data tanpa alokasi_ah eksplisit:
                if (preg_match('/\d+/', $tp->kode, $m)) {
                    $num = (int) $m[0];
                    preg_match('/\d+/', $ah, $mah);
                    $ahNum = isset($mah[0]) ? (int) $mah[0] : 0;
                    if ($num === $ahNum) {
                        return true;
                    }
                }
                $localIdx = $idx % 4;
                return $tpIdx === $localIdx;
            })->values();
        }

        return $mapping;
    }

    public function getLabelStsProperty(): string
    {
        $currentSem = collect($this->semesters)->firstWhere('id', (int) $this->activeNilaiSemesterId);
        $semName = strtolower($currentSem['semester'] ?? '');
        return str_contains($semName, 'genap') ? 'STS 2' : 'STS 1';
    }

    public function getLabelSasProperty(): string
    {
        $currentSem = collect($this->semesters)->firstWhere('id', (int) $this->activeNilaiSemesterId);
        $semName = strtolower($currentSem['semester'] ?? '');
        return str_contains($semName, 'genap') ? 'SAT' : 'SAS';
    }

    public function getKkmProperty(): float
    {
        return (float) ($this->penugasan?->mapel?->kkm_default ?? 75);
    }

    public function isBelowKkm($val): bool
    {
        return $val !== null && $val !== '' && is_numeric($val) && (float) $val < $this->kkm;
    }

    public function switchPeriode(string $periode): void
    {
        $this->activePeriode = $periode;
        $this->rekapPeriode = $periode;
        $this->tp_target_periode = $periode;
        if (in_array($periode, ['9M1', '9M2']) && $this->semGanjilId) {
            $this->switchNilaiSemester($this->semGanjilId);
        } elseif (in_array($periode, ['9M3', '9M4']) && $this->semGenapId) {
            $this->switchNilaiSemester($this->semGenapId);
        }
    }

    public function switchRekapPeriode(string $mode): void
    {
        $this->rekapPeriode = $mode;
        if (in_array($mode, ['9M1', '9M2']) && $this->semGanjilId) {
            $this->switchNilaiSemester($this->semGanjilId);
        } elseif (in_array($mode, ['9M3', '9M4']) && $this->semGenapId) {
            $this->switchNilaiSemester($this->semGenapId);
        }
    }

    public function getTpsForPeriode(string $periode)
    {
        $isGenap = in_array($periode, ['9M3', '9M4']);
        $targetPmId = $isGenap ? $this->pmGenapId : $this->pmGanjilId;

        return $this->allTps->filter(function ($tp) use ($periode, $targetPmId, $isGenap) {
            if (!empty($tp->target_periode)) {
                return $tp->target_periode === $periode;
            }
            if (!empty($tp->alokasi_ah)) {
                $derived = match ($tp->alokasi_ah) {
                    'AH 1', 'AH 2' => '9M1',
                    'AH 3', 'AH 4' => '9M2',
                    'AH 5', 'AH 6' => '9M3',
                    'AH 7', 'AH 8' => '9M4',
                    default => null,
                };
                if ($derived) {
                    return $derived === $periode;
                }
            }
            if ($targetPmId && $tp->penugasan_mengajar_id == $targetPmId) {
                $defaultP = $isGenap ? '9M3' : '9M1';
                return $periode === $defaultP;
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

    public function saveTP()
    {
        $this->validate([
            'tp_semester_id' => 'required',
            'tp_target_periode' => 'required|string|in:9M1,9M2,9M3,9M4',
            'tp_kode' => 'required|string|max:20',
            'tp_deskripsi' => 'required|string|max:255',
            'tp_lingkup_materi' => 'nullable|string|max:255',
            'tp_alokasi_ah' => 'required',
            'tp_urutan' => 'required|integer|min:1',
        ], [
            'tp_semester_id.required' => 'Pilih semester target untuk TP ini.',
            'tp_target_periode.required' => 'Pilih blok periode 9M (9M1, 9M2, 9M3, 9M4).',
            'tp_kode.required' => 'Kode TP wajib diisi (misal: TP 1, TP 2).',
            'tp_deskripsi.required' => 'Deskripsi TP wajib diisi.',
            'tp_alokasi_ah.required' => 'Alokasi sesi AH wajib dipilih.',
            'tp_urutan.required' => 'Urutan TP wajib diisi.',
        ]);

        $targetSem = collect($this->semesters)->firstWhere('id', (int) $this->tp_semester_id);
        $targetPmId = $targetSem ? $targetSem['penugasan_id'] : $this->penugasan_id;

        if ($this->edit_tp_id) {
            $tp = TujuanPembelajaran::findOrFail($this->edit_tp_id);
            $tp->update([
                'penugasan_mengajar_id' => $targetPmId,
                'target_periode' => $this->tp_target_periode,
                'lingkup_materi' => $this->tp_lingkup_materi,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
                'urutan' => $this->tp_urutan,
                'alokasi_ah' => $this->tp_alokasi_ah,
            ]);
            $this->edit_tp_id = null;
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil diperbarui.');
        } else {
            TujuanPembelajaran::create([
                'penugasan_mengajar_id' => $targetPmId,
                'target_periode' => $this->tp_target_periode,
                'lingkup_materi' => $this->tp_lingkup_materi,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
                'urutan' => $this->tp_urutan,
                'alokasi_ah' => $this->tp_alokasi_ah,
                'is_active' => true,
            ]);
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil ditambahkan.');
        }

        $this->tp_kode = '';
        $this->tp_deskripsi = '';
        $this->tp_lingkup_materi = '';
        $this->tp_alokasi_ah = $this->availableAhOptions[0] ?? 'AH 1';
        $this->loadAllTP();
        $this->tp_urutan = ($this->allTps->max('urutan') ?? 0) + 1;
        $this->loadNilaiForActiveSemester();
    }

    public function editTP($tp_id)
    {
        $tp = TujuanPembelajaran::with('penugasanMengajar')->findOrFail($tp_id);
        $this->edit_tp_id = $tp->id;
        $this->tp_semester_id = $tp->penugasanMengajar?->semester_id ?? $this->penugasan->semester_id;
        $this->tp_target_periode = $tp->target_periode ?: '9M1';
        $this->tp_lingkup_materi = $tp->lingkup_materi ?? '';
        $this->tp_urutan = $tp->urutan ?? 1;
        $this->tp_kode = $tp->kode;
        $this->tp_deskripsi = $tp->deskripsi;
        $this->tp_alokasi_ah = $tp->alokasi_ah ?: ($this->availableAhOptions[0] ?? 'AH 1');
    }

    public function cancelEditTP()
    {
        $this->edit_tp_id = null;
        $this->tp_kode = '';
        $this->tp_deskripsi = '';
        $this->tp_lingkup_materi = '';
        $this->tp_semester_id = $this->activeNilaiSemesterId ?? $this->penugasan->semester_id;
        $this->tp_target_periode = str_contains(strtolower($this->penugasan->semester?->semester ?? ''), 'genap') ? '9M3' : '9M1';
        $this->tp_urutan = ($this->allTps->max('urutan') ?? 0) + 1;
        $this->tp_alokasi_ah = $this->availableAhOptions[0] ?? 'AH 1';
    }

    public function hapusTP($tp_id)
    {
        $tp = TujuanPembelajaran::findOrFail($tp_id);
        NilaiFormatif::where('tujuan_pembelajaran_id', $tp->id)->delete();
        $tp->delete();

        $this->loadAllTP();
        $this->loadNilaiForActiveSemester();
        session()->flash('message_tp', 'Tujuan Pembelajaran berhasil dihapus.');
    }

    public function saveNilai()
    {
        $this->validate([
            'tpScores.*.*' => 'nullable|numeric|min:0|max:100',
            'formatifData.*.*' => 'nullable|numeric|min:0|max:100',
            'nhData.*.*' => 'nullable|numeric|min:0|max:100',
            'sumatifData.*.*' => 'nullable|numeric|min:0|max:100',
        ], [
            'tpScores.*.*.numeric' => 'Nilai formatif harus berupa angka.',
            'tpScores.*.*.min' => 'Nilai formatif tidak boleh kurang dari 0.',
            'tpScores.*.*.max' => 'Nilai formatif tidak boleh lebih dari 100.',
            'formatifData.*.*.numeric' => 'Nilai formatif harus berupa angka.',
            'formatifData.*.*.min' => 'Nilai formatif tidak boleh kurang dari 0.',
            'formatifData.*.*.max' => 'Nilai formatif tidak boleh lebih dari 100.',
            'nhData.*.*.numeric' => 'Nilai Harian (NH) harus berupa angka.',
            'nhData.*.*.min' => 'Nilai Harian (NH) minimal 0.',
            'nhData.*.*.max' => 'Nilai Harian (NH) maksimal 100.',
            'sumatifData.*.*.numeric' => 'Nilai STS/SAS harus berupa angka.',
            'sumatifData.*.*.min' => 'Nilai STS/SAS minimal 0.',
            'sumatifData.*.*.max' => 'Nilai STS/SAS maksimal 100.',
        ]);

        // Fallback synchronization from legacy formatifData and sumatifData
        foreach ($this->siswas as $siswa) {
            $sId = $siswa->id;
            if (!empty($this->formatifData[$sId])) {
                foreach ($this->formatifData[$sId] as $tpId => $val) {
                    if ((!isset($this->tpScores[$sId][$tpId]) || $this->tpScores[$sId][$tpId] === null || $this->tpScores[$sId][$tpId] === '') && $val !== null && $val !== '') {
                        $this->tpScores[$sId][$tpId] = $val;
                    }
                }
            }
            if (!isset($this->nhData[$sId])) {
                $this->nhData[$sId] = ['9M1' => 100.0, '9M2' => 100.0, '9M3' => 100.0, '9M4' => 100.0];
            }
            $isGenap = ($this->activeNilaiPenugasanId == $this->pmGenapId);
            if (isset($this->sumatifData[$sId]['sts']) && $this->sumatifData[$sId]['sts'] !== null && $this->sumatifData[$sId]['sts'] !== '') {
                $key = $isGenap ? '9M3' : '9M1';
                if (!isset($this->sumatifData[$sId][$key]) || $this->sumatifData[$sId][$key] === null) {
                    $this->sumatifData[$sId][$key] = $this->sumatifData[$sId]['sts'];
                }
            }
            if (isset($this->sumatifData[$sId]['sas']) && $this->sumatifData[$sId]['sas'] !== null && $this->sumatifData[$sId]['sas'] !== '') {
                $key = $isGenap ? '9M4' : '9M2';
                if (!isset($this->sumatifData[$sId][$key]) || $this->sumatifData[$sId][$key] === null) {
                    $this->sumatifData[$sId][$key] = $this->sumatifData[$sId]['sas'];
                }
            }
        }

        // 1. Simpan Nilai Formatif per TP
        foreach ($this->tpScores as $siswaId => $scores) {
            foreach ($scores as $tpId => $val) {
                if ($val !== null && $val !== '' && is_numeric($val)) {
                    NilaiFormatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tpId],
                        ['nilai' => (float)$val]
                    );
                    $this->formatifData[$siswaId][$tpId] = (float)$val;
                } else {
                    NilaiFormatif::where('siswa_id', $siswaId)->where('tujuan_pembelajaran_id', $tpId)->delete();
                    $this->formatifData[$siswaId][$tpId] = null;
                }
            }
        }

        $targetGanjilPmId = $this->pmGanjilId ?: ($this->penugasan->semester?->semester === 'ganjil' ? $this->penugasan_id : null);
        $targetGenapPmId = $this->pmGenapId ?: ($this->penugasan->semester?->semester === 'genap' ? $this->penugasan_id : null);
        if (!$targetGanjilPmId && !$targetGenapPmId) {
            $targetGanjilPmId = $this->penugasan_id;
        }

        // Simpan Asesmen Harian jika ahData diisi
        if (!empty($this->ahData)) {
            foreach ($this->ahData as $siswaId => $codes) {
                foreach ($codes as $kodeAh => $ahVal) {
                    if ($ahVal !== null && $ahVal !== '' && is_numeric($ahVal)) {
                        $isGenap = in_array($kodeAh, ['AH 5', 'AH 6', 'AH 7', 'AH 8']);
                        $targetPmId = $isGenap ? ($targetGenapPmId ?: $this->penugasan_id) : ($targetGanjilPmId ?: $this->penugasan_id);
                        NilaiAsesmenHarian::updateOrCreate(
                            ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $targetPmId, 'kode_ah' => $kodeAh],
                            ['nilai' => (float)$ahVal]
                        );
                        // Duplikasi nilai ke seluruh TP yang berafiliasi dengan AH ini
                        foreach ($this->allTps as $tp) {
                            if ($tp->alokasi_ah === $kodeAh) {
                                NilaiFormatif::updateOrCreate(
                                    ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tp->id],
                                    ['nilai' => (float)$ahVal]
                                );
                                $this->tpScores[$siswaId][$tp->id] = (float)$ahVal;
                                $this->formatifData[$siswaId][$tp->id] = (float)$ahVal;
                            }
                        }
                    }
                }
            }
        }

        // 2. Simpan Nilai Harian (NH)
        foreach ($this->nhData as $siswaId => $pScores) {
            foreach ($pScores as $periode => $val) {
                $isGenap = in_array($periode, ['9M3', '9M4']);
                $targetPmId = $isGenap ? ($targetGenapPmId ?: $this->penugasan_id) : ($targetGanjilPmId ?: $this->penugasan_id);

                $nhVal = ($val !== null && $val !== '' && is_numeric($val)) ? (float)$val : 100.0;
                NilaiHarian::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'penugasan_mengajar_id' => $targetPmId,
                        'target_periode' => $periode,
                    ],
                    ['nilai' => $nhVal]
                );
            }
        }

        // Sinkronisasi data sumatif dari sumatifDataGanjil dan sumatifDataGenap
        if (!empty($this->sumatifDataGanjil)) {
            foreach ($this->sumatifDataGanjil as $sId => $vals) {
                if (is_array($vals)) {
                    if (isset($vals['sts']) && $vals['sts'] !== null && $vals['sts'] !== '') {
                        $this->sumatifData[$sId]['9M1'] = $vals['sts'];
                    } elseif (array_key_exists('sts', $vals) && $vals['sts'] === '') {
                        $this->sumatifData[$sId]['9M1'] = '';
                    }
                    if (isset($vals['sas']) && $vals['sas'] !== null && $vals['sas'] !== '') {
                        $this->sumatifData[$sId]['9M2'] = $vals['sas'];
                    } elseif (array_key_exists('sas', $vals) && $vals['sas'] === '') {
                        $this->sumatifData[$sId]['9M2'] = '';
                    }
                }
            }
        }
        if (!empty($this->sumatifDataGenap)) {
            foreach ($this->sumatifDataGenap as $sId => $vals) {
                if (is_array($vals)) {
                    if (isset($vals['sts']) && $vals['sts'] !== null && $vals['sts'] !== '') {
                        $this->sumatifData[$sId]['9M3'] = $vals['sts'];
                    } elseif (array_key_exists('sts', $vals) && $vals['sts'] === '') {
                        $this->sumatifData[$sId]['9M3'] = '';
                    }
                    if (isset($vals['sas']) && $vals['sas'] !== null && $vals['sas'] !== '') {
                        $this->sumatifData[$sId]['9M4'] = $vals['sas'];
                    } elseif (array_key_exists('sas', $vals) && $vals['sas'] === '') {
                        $this->sumatifData[$sId]['9M4'] = '';
                    }
                }
            }
        }

        // 3. Simpan Nilai Sumatif (STS & SAS/SAT)
        foreach ($this->sumatifData as $siswaId => $pScores) {
            if ((isset($pScores['sts']) || isset($pScores['sas'])) && !isset($pScores['9M1']) && !isset($pScores['9M3'])) {
                $isGenapCurrent = ($this->activeNilaiPenugasanId == $this->pmGenapId);
                if ($isGenapCurrent) {
                    if (isset($pScores['sts'])) $pScores['9M3'] = $pScores['sts'];
                    if (isset($pScores['sas'])) $pScores['9M4'] = $pScores['sas'];
                } else {
                    if (isset($pScores['sts'])) $pScores['9M1'] = $pScores['sts'];
                    if (isset($pScores['sas'])) $pScores['9M2'] = $pScores['sas'];
                }
            }

            if ($targetGanjilPmId) {
                $rawSts1 = $pScores['9M1'] ?? null;
                $rawSas = $pScores['9M2'] ?? null;
                $sts1 = ($rawSts1 !== null && $rawSts1 !== '' && is_numeric($rawSts1)) ? (float)$rawSts1 : null;
                $sas = ($rawSas !== null && $rawSas !== '' && is_numeric($rawSas)) ? (float)$rawSas : null;

                if ($sts1 !== null || $sas !== null) {
                    NilaiSumatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $targetGanjilPmId],
                        ['sts' => $sts1, 'sas' => $sas]
                    );
                } else {
                    NilaiSumatif::where('siswa_id', $siswaId)->where('penugasan_mengajar_id', $targetGanjilPmId)->delete();
                }
            }

            if ($targetGenapPmId) {
                $rawSts2 = $pScores['9M3'] ?? null;
                $rawSat = $pScores['9M4'] ?? null;
                $sts2 = ($rawSts2 !== null && $rawSts2 !== '' && is_numeric($rawSts2)) ? (float)$rawSts2 : null;
                $sat = ($rawSat !== null && $rawSat !== '' && is_numeric($rawSat)) ? (float)$rawSat : null;

                if ($sts2 !== null || $sat !== null) {
                    NilaiSumatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $targetGenapPmId],
                        ['sts' => $sts2, 'sas' => $sat]
                    );
                } else {
                    NilaiSumatif::where('siswa_id', $siswaId)->where('penugasan_mengajar_id', $targetGenapPmId)->delete();
                }
            }
        }

        // 4. Hitung ulang rekap
        if ($targetGanjilPmId) $this->calculateRekap($targetGanjilPmId);
        if ($targetGenapPmId && $targetGenapPmId !== $targetGanjilPmId) $this->calculateRekap($targetGenapPmId);

        session()->flash('message_nilai', 'Seluruh Nilai Akademik (Formatif & Sumatif) berhasil disimpan.');
    }

    public function calculateRekap(?int $targetPenugasanId = null)
    {
        $penugasanId = $targetPenugasanId ?? $this->activeNilaiPenugasanId;
        if (!$penugasanId) return;

        $isGenap = ($penugasanId == $this->pmGenapId);
        $block1Key = $isGenap ? '9M3' : '9M1';
        $block2Key = $isGenap ? '9M4' : '9M2';

        $kkm = (float) ($this->penugasan->mapel->kkm_default ?? 75);

        $tpsBlock1 = $this->getTpsForPeriode($block1Key);
        $tpsBlock2 = $this->getTpsForPeriode($block2Key);

        $calc = app(PenilaianScoreCalculator::class);
        $settings = PengaturanPenilaian::getActive();

        $existingNas = NilaiAkhir::where('penugasan_mengajar_id', $penugasanId)
            ->whereIn('siswa_id', $this->siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $computedRekap = [];
        foreach ($this->siswas as $siswa) {
            $existingNa = $existingNas->get($siswa->id);
            $currentData9m = $existingNa?->data_9m ?? [];

            $sTpScores = $this->tpScores[$siswa->id] ?? [];

            $rawNh1 = $this->nhData[$siswa->id][$block1Key] ?? null;
            $rawNh2 = $this->nhData[$siswa->id][$block2Key] ?? null;
            $nh1 = ($rawNh1 !== null && $rawNh1 !== '' && is_numeric($rawNh1)) ? (float)$rawNh1 : 100.0;
            $nh2 = ($rawNh2 !== null && $rawNh2 !== '' && is_numeric($rawNh2)) ? (float)$rawNh2 : 100.0;

            $rawSum1 = $this->sumatifData[$siswa->id][$block1Key] ?? null;
            $rawSum2 = $this->sumatifData[$siswa->id][$block2Key] ?? null;
            $sum1 = ($rawSum1 !== null && $rawSum1 !== '' && is_numeric($rawSum1)) ? (float)$rawSum1 : null;
            $sum2 = ($rawSum2 !== null && $rawSum2 !== '' && is_numeric($rawSum2)) ? (float)$rawSum2 : null;

            // Evaluasi Blok 1
            $resBlock1 = $calc->calculate9MBlock(
                $sTpScores,
                $nh1,
                $sum1,
                $kkm,
                $tpsBlock1,
                $currentData9m[$block1Key]['deskripsi_manual'] ?? null,
                $settings
            );

            // Evaluasi Blok 2
            $resBlock2 = $calc->calculate9MBlock(
                $sTpScores,
                $nh2,
                $sum2,
                $kkm,
                $tpsBlock2,
                $currentData9m[$block2Key]['deskripsi_manual'] ?? null,
                $settings
            );

            $currentData9m[$block1Key] = $resBlock1;
            $currentData9m[$block2Key] = $resBlock2;

            // Semester Penuh
            $semResult = $calc->calculateSemester($resBlock1, $resBlock2, $kkm, $settings);

            $activeRataFormatif = ($resBlock1['rata_tp'] !== null && $resBlock2['rata_tp'] !== null)
                ? round(($resBlock1['rata_tp'] + $resBlock2['rata_tp']) / 2, 2)
                : ($resBlock1['rata_tp'] ?? $resBlock2['rata_tp']);

            $ahRecs = NilaiAsesmenHarian::where('siswa_id', $siswa->id)
                ->where('penugasan_mengajar_id', $penugasanId)
                ->whereNotNull('nilai')
                ->get();
            if ($ahRecs->isNotEmpty()) {
                $activeRataFormatif = round($ahRecs->avg('nilai'), 2);
            }

            $na = NilaiAkhir::updateOrCreate(
                ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $penugasanId],
                [
                    'rata_formatif' => $activeRataFormatif,
                    'rata_tp' => $resBlock2['rata_tp'] ?? $resBlock1['rata_tp'],
                    'nh' => $resBlock2['nh'] ?? $resBlock1['nh'],
                    'sumatif' => $resBlock2['sumatif'] ?? $resBlock1['sumatif'],
                    'sts' => $sum1 !== null ? (int) $sum1 : null,
                    'sas' => $sum2 !== null ? (int) $sum2 : null,
                    'nilai_akhir' => $semResult['nilai_akhir'],
                    'predikat' => $semResult['predikat'],
                    'status' => $semResult['status'],
                    'deskripsi_tertinggi' => $semResult['nilai_akhir'] === null && !empty($semResult['deskripsi']) ? $semResult['deskripsi'] : ($resBlock2['deskripsi_tertinggi'] ?: $resBlock1['deskripsi_tertinggi']),
                    'deskripsi_terendah' => $semResult['nilai_akhir'] === null ? '' : ($resBlock2['deskripsi_terendah'] ?: $resBlock1['deskripsi_terendah']),
                    'deskripsi_manual' => $resBlock2['deskripsi_manual'] ?: $resBlock1['deskripsi_manual'],
                    'data_9m' => $currentData9m,
                ]
            );

            $computedRekap[$siswa->id] = $na;
        }

        if ($penugasanId == $this->activeNilaiPenugasanId) {
            $this->rekapData = $computedRekap;
        }
    }

    public function openEditDeskripsi9M(int $siswaId, string $periodKey): void
    {
        $this->selectedSiswaId9M = $siswaId;
        $this->selectedPeriodKey9M = $periodKey;

        $siswa = $this->siswas->firstWhere('id', $siswaId);
        $this->selectedSiswaNama9M = $siswa?->nama ?? 'Siswa';

        $periodLabels = [
            '9M1' => '9 Minggu ke-1 (Periode STS 1)',
            '9M2' => '9 Minggu ke-2 (Periode SAS)',
            '9M3' => '9 Minggu ke-3 (Periode STS 2)',
            '9M4' => '9 Minggu ke-4 (Periode SAT)',
        ];
        $this->selectedPeriodLabel9M = $periodLabels[$periodKey] ?? $periodKey;

        $na = $this->rekapData[$siswaId] ?? NilaiAkhir::where('siswa_id', $siswaId)->where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)->first();
        $record9m = $na?->getRecordFor9M($periodKey);

        $this->tempDeskripsiManual9M = $record9m['deskripsi_manual'] ?? '';
        
        $autoDesc = [];
        if (!empty($record9m['deskripsi_tertinggi'])) {
            $autoDesc[] = $record9m['deskripsi_tertinggi'];
        }
        if (!empty($record9m['deskripsi_terendah'])) {
            $autoDesc[] = $record9m['deskripsi_terendah'];
        }
        $this->autoDeskripsiInfo9M = !empty($autoDesc) ? implode("\n", $autoDesc) : 'Belum ada deskripsi otomatis yang dihasilkan.';

        $this->showModalDeskripsi9M = true;
    }

    public function closeEditDeskripsi9M(): void
    {
        $this->showModalDeskripsi9M = false;
        $this->selectedSiswaId9M = null;
        $this->selectedPeriodKey9M = '';
        $this->tempDeskripsiManual9M = '';
        $this->autoDeskripsiInfo9M = '';
    }

    public function saveDeskripsi9M(): void
    {
        if (!$this->selectedSiswaId9M || !$this->selectedPeriodKey9M) {
            return;
        }

        $na = NilaiAkhir::where('siswa_id', $this->selectedSiswaId9M)
            ->where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)
            ->first();

        if ($na) {
            $data9m = $na->data_9m ?? [];
            if (!isset($data9m[$this->selectedPeriodKey9M])) {
                $data9m[$this->selectedPeriodKey9M] = [];
            }
            $cleanText = trim($this->tempDeskripsiManual9M);
            $data9m[$this->selectedPeriodKey9M]['deskripsi_manual'] = $cleanText !== '' ? $cleanText : null;
            $na->data_9m = $data9m;
            $na->save();

            $this->rekapData[$this->selectedSiswaId9M] = $na;
        }

        session()->flash('message_rekap', 'Deskripsi capaian periode ' . $this->selectedPeriodKey9M . ' berhasil diperbarui.');
        $this->closeEditDeskripsi9M();
    }

    public function resetToAutoDeskripsi9M(): void
    {
        $this->tempDeskripsiManual9M = '';
        $this->saveDeskripsi9M();
    }

    public function copyAutoToManual9M(): void
    {
        $this->tempDeskripsiManual9M = $this->autoDeskripsiInfo9M;
    }

    public function updateManualDeskripsi($siswaId, $text)
    {
        $na = NilaiAkhir::where('siswa_id', $siswaId)->where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)->first();
        if ($na) {
            $na->deskripsi_manual = $text;
            $na->save();
        }
    }

    public function openImportModal(): void
    {
        $this->importStep = 'select';
        $this->importFile = null;
        $this->importPreviewData = null;
        $this->isImporting = false;
        $this->showImportModal = true;

        if (empty($this->importSelectedColumns)) {
            $this->importSelectedColumns = NilaiTemplateExporter::getColumnsByScope($this->penugasan, 'periode_aktif', $this->activePeriode);
        }
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importPreviewData = null;
        $this->isImporting = false;
    }

    public function setImportPreset(string $preset): void
    {
        if ($preset === 'periode_aktif') {
            $this->importSelectedColumns = NilaiTemplateExporter::getColumnsByScope($this->penugasan, 'periode_aktif', $this->activePeriode);
        } elseif ($preset === 'ganjil') {
            $this->importSelectedColumns = NilaiTemplateExporter::getColumnsByScope($this->penugasan, 'ganjil');
        } elseif ($preset === 'genap') {
            $this->importSelectedColumns = NilaiTemplateExporter::getColumnsByScope($this->penugasan, 'genap');
        } elseif ($preset === 'semua') {
            $this->importSelectedColumns = NilaiTemplateExporter::getColumnsByScope($this->penugasan, 'semua');
        } elseif ($preset === 'legacy_ah') {
            $this->importSelectedColumns = NilaiTemplateExporter::ALL_COLUMNS;
        } elseif ($preset === 'reset') {
            $this->importSelectedColumns = [];
        }
    }

    public function downloadTemplate()
    {
        if (empty($this->importSelectedColumns)) {
            $this->addError('importSelectedColumns', 'Pilih minimal satu kolom nilai asesmen untuk membuat template.');
            return;
        }

        $exporter = new NilaiTemplateExporter(
            $this->penugasan,
            $this->importSelectedColumns,
            $this->importRombelScope
        );

        return $exporter->downloadResponse();
    }

    public function uploadAndPreview(): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'importFile.required' => 'Pilih berkas Excel (.xlsx / .xls) terlebih dahulu.',
            'importFile.mimes' => 'Format berkas harus berupa Excel (.xlsx atau .xls).',
            'importFile.max' => 'Ukuran berkas maksimal adalah 10 MB.',
        ]);

        $realPath = $this->importFile->getRealPath();
        $cleanTempPath = tempnam(sys_get_temp_dir(), 'siraga_import_') . '.xlsx';
        copy($realPath, $cleanTempPath);

        $importer = new NilaiImporter();

        $this->importPreviewData = $importer->validateAndParse(
            $cleanTempPath,
            $this->penugasan,
            $this->importSelectedColumns
        );

        @unlink($cleanTempPath);

        if (!$this->importPreviewData['success'] && empty($this->importPreviewData['valid_rows'])) {
            return;
        }

        $this->importStep = 'preview';
    }

    public function cancelPreview(): void
    {
        $this->importStep = 'select';
        $this->importFile = null;
        $this->importPreviewData = null;
    }

    public function executeImport(): void
    {
        if (empty($this->importPreviewData['valid_rows'])) {
            $this->addError('importFile', 'Tidak ada baris data nilai yang valid untuk disimpan.');
            return;
        }

        $this->isImporting = true;

        $importer = new NilaiImporter();
        $result = $importer->commitImport($this->importPreviewData['valid_rows'], $this->penugasan);

        // Muat ulang data nilai pada tabel input 1 tahun
        $this->loadNilaiForActiveSemester();
        $this->calculateRekap();

        $this->isImporting = false;
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importPreviewData = null;

        session()->flash('message_nilai', "Sukses! Berhasil mengimpor nilai untuk {$result['saved_students']} siswa pada {$result['affected_rombels_count']} rombel.");
    }

    public function openCopyTpModal(): void
    {
        if (empty($this->allTps) || count($this->allTps) === 0) {
            session()->flash('message_tp_error', 'Belum ada Tujuan Pembelajaran yang dapat disalin.');
            return;
        }

        $copier = app(TujuanPembelajaranCopier::class);
        $this->availableTargetRombels = $copier->getAvailableTargetRombels($this->penugasan)->toArray();
        $this->selectedTargetRombelIds = [];
        $this->copyTpSemesterScope = 'all';
        $this->showCopyTpModal = true;
    }

    public function closeCopyTpModal(): void
    {
        $this->showCopyTpModal = false;
        $this->selectedTargetRombelIds = [];
    }

    public function selectAllTargetRombels(): void
    {
        $this->selectedTargetRombelIds = collect($this->availableTargetRombels)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function deselectAllTargetRombels(): void
    {
        $this->selectedTargetRombelIds = [];
    }

    public function executeCopyTp(): void
    {
        if (empty($this->selectedTargetRombelIds)) {
            session()->flash('message_copy_tp_error', 'Pilih minimal satu rombel tujuan untuk disalin.');
            return;
        }

        $this->isCopyingTp = true;

        try {
            $copier = app(TujuanPembelajaranCopier::class);
            $result = $copier->copyTps(
                $this->penugasan,
                array_map('intval', $this->selectedTargetRombelIds),
                $this->copyTpSemesterScope
            );

            session()->flash(
                'message_tp',
                "Sukses! Berhasil menyalin {$result['source_tps_count']} TP ke {$result['rombels_count']} rombel ({$result['created_count']} baru dibuat, {$result['updated_count']} diperbarui)."
            );

            $this->showCopyTpModal = false;
            $this->selectedTargetRombelIds = [];
        } catch (\Throwable $e) {
            session()->flash('message_copy_tp_error', 'Gagal menyalin TP: ' . $e->getMessage());
        } finally {
            $this->isCopyingTp = false;
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('penilaian::livewire.penilaian-input');
    }
}
