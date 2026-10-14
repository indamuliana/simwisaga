<?php

namespace App\Modules\Penilaian\Livewire;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\NilaiImporter;
use App\Modules\Penilaian\Services\NilaiTemplateExporter;
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
    public array $importSelectedColumns = [
        'AH 1', 'AH 2', 'STS 1', 'AH 3', 'AH 4', 'SAS',
        'AH 5', 'AH 6', 'STS 2', 'AH 7', 'AH 8', 'SAT',
    ];
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

    // State for TP Form (Tab 1)
    public $tp_semester_id = null;
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
        }

        $this->loadSemestersAndPenugasans();

        $this->activeNilaiSemesterId = $this->penugasan->semester_id;
        $this->activeNilaiPenugasanId = $this->penugasan->id;
        $this->tp_semester_id = $this->penugasan->semester_id;
        $this->tp_alokasi_ah = $this->availableAhOptions[0] ?? 'AH 1';

        $this->loadAllTP();
        $this->loadSiswa();
        $this->loadNilaiForActiveSemester();
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
            ->orderBy('id')
            ->get();

        // 2. Load AH Data sepanjang tahun (AH 1 s.d AH 8)
        $this->ahData = [];
        $this->formatifData = [];
        $allAhs = ['AH 1', 'AH 2', 'AH 3', 'AH 4', 'AH 5', 'AH 6', 'AH 7', 'AH 8'];
        $allPmIds = array_values(array_filter([$this->pmGanjilId, $this->pmGenapId]));

        $ahRecords = NilaiAsesmenHarian::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('penugasan_mengajar_id', $allPmIds)
            ->whereIn('kode_ah', $allAhs)
            ->get();

        $nilaiFormatif = NilaiFormatif::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->allTps->pluck('id'))
            ->get();

        $mapping = $this->ahTpsMapping;

        foreach ($this->siswas as $siswa) {
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
                    // Fallback jika sudah ada nilai_formatif untuk TP yang teralokasi di AH ini
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

            // Sync formatifData state untuk seluruh TP
            foreach ($this->allTps as $tp) {
                $n = $nilaiFormatif->where('siswa_id', $siswa->id)->where('tujuan_pembelajaran_id', $tp->id)->first();
                $this->formatifData[$siswa->id][$tp->id] = $n ? $n->nilai : null;
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

            // Untuk kompatibilitas backward: sinkronkan ke sumatifData
            $activeS = ($this->activeNilaiPenugasanId == $this->pmGenapId) ? $sGenap : $sGanjil;
            $this->sumatifData[$siswa->id] = [
                'sts' => $activeS ? $activeS->sts : null,
                'sas' => $activeS ? $activeS->sas : null,
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
            $type = $parts[1]; // 'sts' atau 'sas'
            if ($this->activeNilaiPenugasanId == $this->pmGenapId) {
                $this->sumatifDataGenap[$siswaId][$type] = $value;
            } else {
                $this->sumatifDataGanjil[$siswaId][$type] = $value;
            }
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

    public function saveTP()
    {
        $this->validate([
            'tp_semester_id' => 'required',
            'tp_kode' => 'required|string|max:20',
            'tp_deskripsi' => 'required|string|max:255',
            'tp_alokasi_ah' => 'required|string|max:20',
        ], [
            'tp_semester_id.required' => 'Pilih semester target untuk TP / CP ini.',
            'tp_kode.required' => 'Kode TP / CP wajib diisi (misal: TP 1, TP 4, atau CP 1).',
            'tp_deskripsi.required' => 'Deskripsi TP / CP wajib diisi.',
            'tp_alokasi_ah.required' => 'Alokasi Asesmen Harian (AH) wajib dipilih.',
        ]);

        $targetSem = collect($this->semesters)->firstWhere('id', (int) $this->tp_semester_id);
        $targetPmId = $targetSem ? $targetSem['penugasan_id'] : $this->penugasan_id;

        if ($this->edit_tp_id) {
            $tp = TujuanPembelajaran::findOrFail($this->edit_tp_id);
            $tp->update([
                'penugasan_mengajar_id' => $targetPmId,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
                'alokasi_ah' => $this->tp_alokasi_ah,
            ]);
            $this->edit_tp_id = null;
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil diperbarui.');
        } else {
            TujuanPembelajaran::create([
                'penugasan_mengajar_id' => $targetPmId,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
                'alokasi_ah' => $this->tp_alokasi_ah,
                'is_active' => true,
            ]);
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil ditambahkan.');
        }

        $this->tp_kode = '';
        $this->tp_deskripsi = '';
        $this->tp_alokasi_ah = $this->availableAhOptions[0] ?? 'AH 1';
        $this->loadAllTP();
        $this->loadNilaiForActiveSemester();
    }

    public function editTP($tp_id)
    {
        $tp = TujuanPembelajaran::with('penugasanMengajar')->findOrFail($tp_id);
        $this->edit_tp_id = $tp->id;
        $this->tp_semester_id = $tp->penugasanMengajar?->semester_id ?? $this->penugasan->semester_id;
        $this->tp_kode = $tp->kode;
        $this->tp_deskripsi = $tp->deskripsi;
        $this->tp_alokasi_ah = $tp->alokasi_ah ?: ($this->availableAhOptions[0] ?? 'AH 1');
    }

    public function cancelEditTP()
    {
        $this->edit_tp_id = null;
        $this->tp_kode = '';
        $this->tp_deskripsi = '';
        $this->tp_semester_id = $this->activeNilaiSemesterId ?? $this->penugasan->semester_id;
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
            'ahData.*.*' => 'nullable|numeric|min:0|max:100',
            'formatifData.*.*' => 'nullable|numeric|min:0|max:100',
            'sumatifData.*.sts' => 'nullable|numeric|min:0|max:100',
            'sumatifData.*.sas' => 'nullable|numeric|min:0|max:100',
            'sumatifDataGanjil.*.sts' => 'nullable|numeric|min:0|max:100',
            'sumatifDataGanjil.*.sas' => 'nullable|numeric|min:0|max:100',
            'sumatifDataGenap.*.sts' => 'nullable|numeric|min:0|max:100',
            'sumatifDataGenap.*.sas' => 'nullable|numeric|min:0|max:100',
        ], [
            'ahData.*.*.numeric' => 'Nilai asesmen harian (AH) harus berupa angka.',
            'ahData.*.*.min' => 'Nilai asesmen harian (AH) tidak boleh kurang dari 0.',
            'ahData.*.*.max' => 'Nilai asesmen harian (AH) tidak boleh lebih dari 100.',
            'formatifData.*.*.numeric' => 'Nilai formatif harus berupa angka.',
            'formatifData.*.*.min' => 'Nilai formatif tidak boleh kurang dari 0.',
            'formatifData.*.*.max' => 'Nilai formatif tidak boleh lebih dari 100.',
            'sumatifData.*.sts.numeric' => 'Nilai STS harus berupa angka.',
            'sumatifData.*.sts.min' => 'Nilai STS tidak boleh kurang dari 0.',
            'sumatifData.*.sts.max' => 'Nilai STS tidak boleh lebih dari 100.',
            'sumatifData.*.sas.numeric' => 'Nilai SAS harus berupa angka.',
            'sumatifData.*.sas.min' => 'Nilai SAS tidak boleh kurang dari 0.',
            'sumatifData.*.sas.max' => 'Nilai SAS tidak boleh lebih dari 100.',
        ]);

        $mapping = $this->ahTpsMapping;

        // Sinkronisasi data sumatif hanya jika sumatifData diubah secara eksplisit (misal dari pemanggilan tes lama)
        foreach ($this->sumatifData as $sId => $vals) {
            $initialSts = $this->initialSumatifData[$sId]['sts'] ?? null;
            $initialSas = $this->initialSumatifData[$sId]['sas'] ?? null;
            $currentSts = $vals['sts'] ?? null;
            $currentSas = $vals['sas'] ?? null;

            $isStsChanged = ($currentSts !== $initialSts);
            $isSasChanged = ($currentSas !== $initialSas);

            if ($isStsChanged || $isSasChanged) {
                if ($this->activeNilaiPenugasanId == $this->pmGenapId) {
                    if ($isStsChanged) $this->sumatifDataGenap[$sId]['sts'] = $currentSts;
                    if ($isSasChanged) $this->sumatifDataGenap[$sId]['sas'] = $currentSas;
                } else {
                    if ($isStsChanged) $this->sumatifDataGanjil[$sId]['sts'] = $currentSts;
                    if ($isSasChanged) $this->sumatifDataGanjil[$sId]['sas'] = $currentSas;
                }
            }
        }

        // 1. Simpan Nilai Asesmen Harian (AH 1 s.d AH 8) sepanjang tahun & Sinkronisasi ke seluruh TP binaannya
        $allAhs = ['AH 1', 'AH 2', 'AH 3', 'AH 4', 'AH 5', 'AH 6', 'AH 7', 'AH 8'];
        foreach ($this->ahData as $siswaId => $scores) {
            foreach ($scores as $ahCode => $score) {
                $idx = array_search($ahCode, $allAhs);
                $isGenap = ($idx !== false && $idx >= 4);
                $targetPmId = $isGenap ? $this->pmGenapId : $this->pmGanjilId;
                if (!$targetPmId) continue;

                $tpsInAh = $mapping[$ahCode] ?? collect();

                // Jika score AH kosong, periksa apakah ada formatifData yang diisi langsung untuk TP dalam AH ini
                if ($score === null || $score === '') {
                    foreach ($tpsInAh as $tp) {
                        if (isset($this->formatifData[$siswaId][$tp->id]) && $this->formatifData[$siswaId][$tp->id] !== null && $this->formatifData[$siswaId][$tp->id] !== '') {
                            $score = $this->formatifData[$siswaId][$tp->id];
                            $this->ahData[$siswaId][$ahCode] = $score;
                            break;
                        }
                    }
                }

                if ($score !== null && $score !== '') {
                    NilaiAsesmenHarian::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'penugasan_mengajar_id' => $targetPmId,
                            'kode_ah' => $ahCode,
                        ],
                        ['nilai' => $score]
                    );

                    // Sinkronisasi nilai yang sama persis ke setiap TP di bawah naungan AH ini
                    foreach ($tpsInAh as $tp) {
                        NilaiFormatif::updateOrCreate(
                            ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tp->id],
                            ['nilai' => $score]
                        );
                        $this->formatifData[$siswaId][$tp->id] = $score;
                    }
                } else {
                    NilaiAsesmenHarian::where('siswa_id', $siswaId)
                        ->where('penugasan_mengajar_id', $targetPmId)
                        ->where('kode_ah', $ahCode)
                        ->delete();

                    foreach ($tpsInAh as $tp) {
                        // Hanya hapus jika formatifData juga tidak memiliki nilai
                        if (!isset($this->formatifData[$siswaId][$tp->id]) || $this->formatifData[$siswaId][$tp->id] === null || $this->formatifData[$siswaId][$tp->id] === '') {
                            NilaiFormatif::where('siswa_id', $siswaId)
                                ->where('tujuan_pembelajaran_id', $tp->id)
                                ->delete();
                            $this->formatifData[$siswaId][$tp->id] = null;
                        }
                    }
                }
            }
        }

        // 2. Simpan Nilai Formatif jika ada perubahan langsung (dukungan kompatibilitas)
        foreach ($this->formatifData as $siswaId => $tpScores) {
            foreach ($tpScores as $tpId => $score) {
                if ($score !== null && $score !== '') {
                    NilaiFormatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tpId],
                        ['nilai' => $score]
                    );
                }
            }
        }

        // 3. Simpan Sumatif Ganjil (STS 1 & SAS)
        if ($this->pmGanjilId) {
            foreach ($this->sumatifDataGanjil as $siswaId => $data) {
                $sts = ($data['sts'] === '' || $data['sts'] === null) ? null : (float) $data['sts'];
                $sas = ($data['sas'] === '' || $data['sas'] === null) ? null : (float) $data['sas'];

                if ($sts !== null || $sas !== null) {
                    NilaiSumatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $this->pmGanjilId],
                        ['sts' => $sts, 'sas' => $sas]
                    );
                } else {
                    NilaiSumatif::where('siswa_id', $siswaId)
                        ->where('penugasan_mengajar_id', $this->pmGanjilId)
                        ->delete();
                }
            }
            $this->calculateRekap($this->pmGanjilId);
        }

        // 4. Simpan Sumatif Genap (STS 2 & SAT)
        if ($this->pmGenapId) {
            foreach ($this->sumatifDataGenap as $siswaId => $data) {
                $sts = ($data['sts'] === '' || $data['sts'] === null) ? null : (float) $data['sts'];
                $sas = ($data['sas'] === '' || $data['sas'] === null) ? null : (float) $data['sas'];

                if ($sts !== null || $sas !== null) {
                    NilaiSumatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $this->pmGenapId],
                        ['sts' => $sts, 'sas' => $sas]
                    );
                } else {
                    NilaiSumatif::where('siswa_id', $siswaId)
                        ->where('penugasan_mengajar_id', $this->pmGenapId)
                        ->delete();
                }
            }
            $this->calculateRekap($this->pmGenapId);
        }

        // Sinkronkan properti sumatifData untuk backward-compatibility dengan semester aktif
        $activeSumatif = ($this->activeNilaiPenugasanId == $this->pmGenapId) ? $this->sumatifDataGenap : $this->sumatifDataGanjil;
        $this->sumatifData = $activeSumatif;
        $this->initialSumatifData = $activeSumatif;

        session()->flash('message_nilai', 'Seluruh Nilai Akademik (Formatif & Sumatif) berhasil disimpan.');
    }

    public function calculateRekap(?int $targetPenugasanId = null)
    {
        $penugasanId = $targetPenugasanId ?? $this->activeNilaiPenugasanId;
        if (!$penugasanId) return;

        $isGenap = ($penugasanId == $this->pmGenapId);
        $block1Key = $isGenap ? '9M3' : '9M1';
        $block2Key = $isGenap ? '9M4' : '9M2';

        $ahBlock1 = $isGenap ? ['AH 5', 'AH 6'] : ['AH 1', 'AH 2'];
        $ahBlock2 = $isGenap ? ['AH 7', 'AH 8'] : ['AH 3', 'AH 4'];

        $sumatifSource = $isGenap ? $this->sumatifDataGenap : $this->sumatifDataGanjil;

        $ahMapping = $this->ahTpsMapping;
        $tpsBlock1 = collect();
        foreach ($ahBlock1 as $ah) {
            $tpsBlock1 = $tpsBlock1->merge($ahMapping[$ah] ?? collect());
        }
        $tpsBlock2 = collect();
        foreach ($ahBlock2 as $ah) {
            $tpsBlock2 = $tpsBlock2->merge($ahMapping[$ah] ?? collect());
        }

        $kkm = $this->penugasan->mapel->kkm_default ?? 75;

        // Ambil data nilai akhir yang sudah ada (untuk menjaga deskripsi_manual dan data_9m yang lama)
        $existingNas = NilaiAkhir::where('penugasan_mengajar_id', $penugasanId)
            ->whereIn('siswa_id', $this->siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $computedRekap = [];
        foreach ($this->siswas as $siswa) {
            $existingNa = $existingNas->get($siswa->id);
            $currentData9m = $existingNa?->data_9m ?? [];

            // 1. Evaluasi Blok 1 (9M1 atau 9M3)
            $resBlock1 = $this->evaluate9MBlock(
                $siswa->id,
                $ahBlock1,
                $tpsBlock1,
                $sumatifSource[$siswa->id]['sts'] ?? null,
                $kkm,
                $currentData9m[$block1Key]['deskripsi_manual'] ?? null
            );

            // 2. Evaluasi Blok 2 (9M2 atau 9M4)
            $resBlock2 = $this->evaluate9MBlock(
                $siswa->id,
                $ahBlock2,
                $tpsBlock2,
                $sumatifSource[$siswa->id]['sas'] ?? null,
                $kkm,
                $currentData9m[$block2Key]['deskripsi_manual'] ?? null
            );

            $currentData9m[$block1Key] = $resBlock1;
            $currentData9m[$block2Key] = $resBlock2;

            // Hitung nilai akhir semester (backward compatibility)
            $allAhThisSem = array_merge($ahBlock1, $ahBlock2);
            $activeAhThisSem = [];
            foreach ($allAhThisSem as $ah) {
                if (($ahMapping[$ah] ?? collect())->isNotEmpty()) {
                    $activeAhThisSem[] = $ah;
                }
            }

            $filledAhs = [];
            $semHasEmpty = false;
            $semHasBelowKkm = false;

            if (!empty($activeAhThisSem)) {
                foreach ($activeAhThisSem as $ah) {
                    $val = $this->ahData[$siswa->id][$ah] ?? null;
                    if ($val === null || $val === '' || !is_numeric($val)) {
                        $semHasEmpty = true;
                    } else {
                        $num = (float) $val;
                        $filledAhs[] = $num;
                        if ($num < $kkm) {
                            $semHasBelowKkm = true;
                        }
                    }
                }
            } else {
                $semHasEmpty = true;
            }

            $stsVal = isset($sumatifSource[$siswa->id]['sts']) && is_numeric($sumatifSource[$siswa->id]['sts']) ? (float) $sumatifSource[$siswa->id]['sts'] : null;
            $sasVal = isset($sumatifSource[$siswa->id]['sas']) && is_numeric($sumatifSource[$siswa->id]['sas']) ? (float) $sumatifSource[$siswa->id]['sas'] : null;

            if ($stsVal === null || $sasVal === null) {
                $semHasEmpty = true;
            } else {
                if ($stsVal < $kkm || $sasVal < $kkm) {
                    $semHasBelowKkm = true;
                }
            }

            $rataFormatifSem = !empty($filledAhs) ? round(array_sum($filledAhs) / count($filledAhs), 2) : null;

            if ($semHasEmpty || $semHasBelowKkm) {
                $nilaiAkhirSem = null;
            } else {
                $nilaiAkhirSem = round(($rataFormatifSem * 0.5) + (($stsVal ?? 0) * 0.25) + (($sasVal ?? 0) * 0.25), 2);
            }

            // Deskripsi semester penuh
            if (($resBlock2['status'] ?? '') === 'belum_lengkap' || ($resBlock1['status'] ?? '') === 'belum_lengkap') {
                $semDescTinggi = 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.';
                $semDescRendah = '';
            } elseif (($resBlock2['status'] ?? '') === 'belum_kompeten' || ($resBlock1['status'] ?? '') === 'belum_kompeten') {
                $semDescTinggi = 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.';
                $semDescRendah = '';
            } else {
                $semDescTinggi = $resBlock2['deskripsi_tertinggi'] ?: $resBlock1['deskripsi_tertinggi'];
                $semDescRendah = $resBlock2['deskripsi_terendah'] ?: $resBlock1['deskripsi_terendah'];
            }

            // Simpan ke database
            $na = NilaiAkhir::updateOrCreate(
                ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $penugasanId],
                [
                    'rata_formatif' => $rataFormatifSem,
                    'sts' => $stsVal,
                    'sas' => $sasVal,
                    'nilai_akhir' => $nilaiAkhirSem,
                    'deskripsi_tertinggi' => $semDescTinggi,
                    'deskripsi_terendah' => $semDescRendah,
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

    /**
     * Evaluasi satu blok 9 minggu untuk satu siswa
     */
    protected function evaluate9MBlock(
        int $siswaId,
        array $ahCodes,
        \Illuminate\Support\Collection $tpsInBlock,
        $sumatifVal,
        float $kkm,
        ?string $existingManualOverride = null
    ): array {
        $ahMapping = $this->ahTpsMapping;

        // 1. Identifikasi AH aktif pada blok ini (hanya AH yang memiliki alokasi TP)
        $activeAhCodes = [];
        foreach ($ahCodes as $ah) {
            $tpsForAh = $ahMapping[$ah] ?? collect();
            if ($tpsForAh->isNotEmpty()) {
                $activeAhCodes[] = $ah;
            }
        }

        // Kumpulkan nilai AH aktif & periksa kondisi kosong / di bawah KKM
        $ahScores = [];
        $hasEmptyAh = false;
        $hasBelowKkm = false;

        if (!empty($activeAhCodes)) {
            foreach ($activeAhCodes as $ah) {
                $score = $this->ahData[$siswaId][$ah] ?? null;
                // Fallback cek formatifData jika ahData belum disinkronkan
                if ($score === null || $score === '') {
                    $tpsForAh = $ahMapping[$ah] ?? collect();
                    foreach ($tpsForAh as $tp) {
                        if (isset($this->formatifData[$siswaId][$tp->id]) && $this->formatifData[$siswaId][$tp->id] !== null && $this->formatifData[$siswaId][$tp->id] !== '') {
                            $score = $this->formatifData[$siswaId][$tp->id];
                            break;
                        }
                    }
                }

                if ($score === null || $score === '' || !is_numeric($score)) {
                    $hasEmptyAh = true;
                } else {
                    $numScore = (float) $score;
                    $ahScores[] = $numScore;
                    if ($numScore < $kkm) {
                        $hasBelowKkm = true;
                    }
                }
            }
        }

        // 2. Evaluasi Sumatif (STS atau SAS atau SAT)
        $sumatif = null;
        $hasSumatif = false;
        if ($sumatifVal !== null && $sumatifVal !== '' && is_numeric($sumatifVal)) {
            $sumatif = (float) $sumatifVal;
            $hasSumatif = true;
            if ($sumatif < $kkm) {
                $hasBelowKkm = true;
            }
        }

        // Kasus: Blok sama sekali belum diisi nilai apapun (belum dimulai atau belum ada asesmen)
        $hasAnyScoreEntered = !empty($ahScores) || $hasSumatif;
        if (!$hasAnyScoreEntered) {
            return [
                'status' => 'belum_ada_nilai',
                'rata_formatif' => null,
                'sumatif' => null,
                'nilai_akhir' => null,
                'deskripsi_tertinggi' => '',
                'deskripsi_terendah' => '',
                'deskripsi_manual' => $existingManualOverride ?: null,
            ];
        }

        $rataFormatif = !empty($ahScores) ? round(array_sum($ahScores) / count($ahScores), 2) : null;

        // 3. Tentukan Status, NA Blok, dan Deskripsi
        // Jika ada salah satu nilai kosong (AH ada yang belum diisi atau sumatif belum diisi)
        $hasEmptyScore = $hasEmptyAh || !$hasSumatif || (empty($activeAhCodes) && $tpsInBlock->isNotEmpty());

        $nilaiAkhir = null;
        $status = 'kompeten';
        $descTinggi = '';
        $descRendah = '';

        if ($hasEmptyScore) {
            $status = 'belum_lengkap';
            $descTinggi = 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.';
            $descRendah = '';
            $nilaiAkhir = null;
        } elseif ($hasBelowKkm) {
            $status = 'belum_kompeten';
            $descTinggi = 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.';
            $descRendah = '';
            $nilaiAkhir = null;
        } else {
            $status = 'kompeten';
            if ($rataFormatif !== null && $sumatif !== null) {
                $nilaiAkhir = round(($rataFormatif * 0.5) + ($sumatif * 0.5), 2);
            } elseif ($rataFormatif !== null) {
                $nilaiAkhir = $rataFormatif;
            } elseif ($sumatif !== null) {
                $nilaiAkhir = $sumatif;
            }

            // Evaluasi Deskripsi TP normal untuk siswa yang kompeten
            $tpScores = [];
            foreach ($tpsInBlock as $tp) {
                $score = $this->formatifData[$siswaId][$tp->id] ?? null;
                if (($score === null || $score === '') && !empty($tp->alokasi_ah)) {
                    $score = $this->ahData[$siswaId][$tp->alokasi_ah] ?? null;
                }
                if ($score !== null && $score !== '' && is_numeric($score)) {
                    $tpScores[$tp->id] = (float) $score;
                }
            }

            if (!empty($tpScores)) {
                $countTps = count($tpScores);

                if ($countTps === 1) {
                    // KASUS 1: Hanya ada 1 TP dalam periode 9M (tetap hasilkan 2 baris)
                    $onlyTpId = array_key_first($tpScores);
                    $onlyScore = $tpScores[$onlyTpId];
                    $onlyTp = $tpsInBlock->firstWhere('id', $onlyTpId);
                    $tpDesc = lcfirst(rtrim($onlyTp->deskripsi ?? '', '.'));

                    // Baris 1: Capaian penguasaan
                    if ($onlyScore >= 85) {
                        $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $tpDesc . '.';
                    } elseif ($onlyScore >= $kkm) {
                        $descTinggi = 'Menunjukkan penguasaan yang baik dalam ' . $tpDesc . '.';
                    } else {
                        $descTinggi = 'Cukup menguasai dalam ' . $tpDesc . '.';
                    }

                    // Baris 2: Pemantapan / Bimbingan berkelanjutan
                    if ($onlyScore >= $kkm) {
                        $descRendah = 'Perlu pemantapan lebih lanjut dalam ' . $tpDesc . ' secara berkelanjutan.';
                    } else {
                        $descRendah = 'Perlu bimbingan lebih lanjut dalam ' . $tpDesc . '.';
                    }
                } else {
                    // KASUS 2: Ada 2 atau lebih TP
                    $maxScore = max($tpScores);
                    $minScore = min($tpScores);

                    if ($maxScore == $minScore) {
                        // Semua TP bernilai identik
                        $tpIds = array_keys($tpScores);
                        $firstTpId = $tpIds[0];
                        $otherTpIds = array_slice($tpIds, 1);

                        $firstTp = $tpsInBlock->firstWhere('id', $firstTpId);
                        $firstDesc = lcfirst(rtrim($firstTp->deskripsi ?? '', '.'));

                        if ($maxScore >= 85) {
                            $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $firstDesc . '.';
                        } elseif ($maxScore >= $kkm) {
                            $descTinggi = 'Menunjukkan penguasaan yang baik dalam ' . $firstDesc . '.';
                        } else {
                            $descTinggi = 'Cukup menguasai dalam ' . $firstDesc . '.';
                        }

                        $otherTps = $tpsInBlock->whereIn('id', $otherTpIds);
                        $tpOtherDesc = $otherTps->pluck('deskripsi')->map(fn($d) => lcfirst(rtrim($d, '.')))->all();
                        $joinedOtherDesc = count($tpOtherDesc) > 1 ? implode(' dan ', $tpOtherDesc) : ($tpOtherDesc[0] ?? '');

                        if ($minScore >= $kkm) {
                            $descRendah = 'Perlu pemantapan dalam ' . $joinedOtherDesc . '.';
                        } else {
                            $descRendah = 'Perlu bimbingan dalam ' . $joinedOtherDesc . '.';
                        }
                    } else {
                        // Terdapat perbedaan nilai tertinggi dan terendah
                        $highestTpIds = array_keys(array_filter($tpScores, fn($s) => $s == $maxScore));
                        $lowestTpIds = array_keys(array_filter($tpScores, fn($s) => $s == $minScore && !in_array($s, $highestTpIds)));

                        if (empty($lowestTpIds)) {
                            $lowestTpIds = array_keys(array_filter($tpScores, fn($s) => $s == $minScore));
                        }

                        // Baris 1: TP yang unggul
                        $highestTps = $tpsInBlock->whereIn('id', $highestTpIds);
                        $tpDescTinggi = $highestTps->pluck('deskripsi')->map(fn($d) => lcfirst(rtrim($d, '.')))->all();
                        $joinedDescTinggi = count($tpDescTinggi) > 1 ? implode(' serta ', $tpDescTinggi) : ($tpDescTinggi[0] ?? '');

                        if ($maxScore >= 85) {
                            $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $joinedDescTinggi . '.';
                        } elseif ($maxScore >= $kkm) {
                            $descTinggi = 'Menunjukkan penguasaan yang baik dalam ' . $joinedDescTinggi . '.';
                        } else {
                            $descTinggi = 'Cukup menguasai dalam ' . $joinedDescTinggi . '.';
                        }

                        // Baris 2: TP yang rendah
                        $lowestTps = $tpsInBlock->whereIn('id', $lowestTpIds);
                        $tpDescRendah = $lowestTps->pluck('deskripsi')->map(fn($d) => lcfirst(rtrim($d, '.')))->all();
                        $joinedDescRendah = count($tpDescRendah) > 1 ? implode(' dan ', $tpDescRendah) : ($tpDescRendah[0] ?? '');

                        if ($minScore >= $kkm) {
                            $descRendah = 'Perlu pemantapan dalam ' . $joinedDescRendah . '.';
                        } else {
                            $descRendah = 'Perlu bimbingan dalam ' . $joinedDescRendah . '.';
                        }
                    }
                }
            }
        }

        return [
            'status' => $status,
            'rata_formatif' => $rataFormatif,
            'sumatif' => $sumatif,
            'nilai_akhir' => $nilaiAkhir,
            'deskripsi_tertinggi' => $descTinggi,
            'deskripsi_terendah' => $descRendah,
            'deskripsi_manual' => $existingManualOverride ?: null,
        ];
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
        if ($preset === 'semua') {
            $this->importSelectedColumns = NilaiTemplateExporter::ALL_COLUMNS;
        } elseif ($preset === 'ganjil') {
            $this->importSelectedColumns = ['AH 1', 'AH 2', 'STS 1', 'AH 3', 'AH 4', 'SAS'];
        } elseif ($preset === 'genap') {
            $this->importSelectedColumns = ['AH 5', 'AH 6', 'STS 2', 'AH 7', 'AH 8', 'SAT'];
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
