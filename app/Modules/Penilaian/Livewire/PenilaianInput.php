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
use Livewire\Attributes\Layout;
use Livewire\Component;

class PenilaianInput extends Component
{
    public $penugasan_id;
    public $penugasan;
    public $siswas = [];
    public $tps = []; // TPs for the currently active semester being assessed
    public $allTps = []; // All TPs across Ganjil & Genap for this Mapel & Rombel
    public $semesters = []; // [ ['id' => ..., 'semester' => ..., 'nama' => ..., 'is_current_penugasan' => ..., 'penugasan_id' => ...] ]

    // Tab state: 'tp', 'formatif', 'rekap'
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

    // State for Rekap
    public $rekapData = [];

    public function mount($penugasan_id)
    {
        $this->penugasan_id = $penugasan_id;
        $this->penugasan = PenugasanMengajar::with(['mapel', 'rombel', 'semester.tahunAjaran'])->findOrFail($penugasan_id);

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

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
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

        // Sinkronisasi data sumatif jika diubah melalui properti sumatifData (misal dari pemanggilan tes)
        foreach ($this->sumatifData as $sId => $vals) {
            if ($this->activeNilaiPenugasanId == $this->pmGenapId) {
                if (isset($vals['sts']) && $vals['sts'] !== null && $vals['sts'] !== '') {
                    $this->sumatifDataGenap[$sId]['sts'] = $vals['sts'];
                }
                if (isset($vals['sas']) && $vals['sas'] !== null && $vals['sas'] !== '') {
                    $this->sumatifDataGenap[$sId]['sas'] = $vals['sas'];
                }
            } else {
                if (isset($vals['sts']) && $vals['sts'] !== null && $vals['sts'] !== '') {
                    $this->sumatifDataGanjil[$sId]['sts'] = $vals['sts'];
                }
                if (isset($vals['sas']) && $vals['sas'] !== null && $vals['sas'] !== '') {
                    $this->sumatifDataGanjil[$sId]['sas'] = $vals['sas'];
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
                if ($data['sts'] !== null || $data['sas'] !== null) {
                    NilaiSumatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $this->pmGanjilId],
                        ['sts' => $data['sts'] === '' ? null : $data['sts'], 'sas' => $data['sas'] === '' ? null : $data['sas']]
                    );
                }
            }
        }

        // 4. Simpan Sumatif Genap (STS 2 & SAT)
        if ($this->pmGenapId) {
            foreach ($this->sumatifDataGenap as $siswaId => $data) {
                if ($data['sts'] !== null || $data['sas'] !== null) {
                    NilaiSumatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $this->pmGenapId],
                        ['sts' => $data['sts'] === '' ? null : $data['sts'], 'sas' => $data['sas'] === '' ? null : $data['sas']]
                    );
                }
            }
        }

        session()->flash('message_nilai', 'Seluruh Nilai Akademik (Formatif & Sumatif) berhasil disimpan.');
    }

    public function calculateRekap()
    {
        $this->rekapData = [];

        $formatifs = NilaiFormatif::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->tps->pluck('id'))
            ->get();

        $ahRecords = NilaiAsesmenHarian::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)
            ->get();

        $sumatifs = NilaiSumatif::where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)->get()->keyBy('siswa_id');

        foreach ($this->siswas as $siswa) {
            // Hitung rata-rata formatif dari nilai AH yang terisi
            $siswaAh = $ahRecords->where('siswa_id', $siswa->id)->whereNotNull('nilai');
            if ($siswaAh->isNotEmpty()) {
                $rataFormatif = round($siswaAh->avg('nilai'), 2);
            } else {
                $siswaFormatifs = $formatifs->where('siswa_id', $siswa->id)->whereNotNull('nilai');
                $rataFormatif = $siswaFormatifs->isNotEmpty() ? round($siswaFormatifs->avg('nilai'), 2) : 0;
            }

            // Tentukan deskripsi capaian tertinggi & terendah berbasis TP
            $siswaFormatifs = $formatifs->where('siswa_id', $siswa->id)->whereNotNull('nilai');
            $highestTpId = null;
            $lowestTpId = null;
            $maxScore = null;
            $minScore = null;

            foreach ($siswaFormatifs as $nf) {
                if ($maxScore === null || $nf->nilai > $maxScore) {
                    $maxScore = $nf->nilai;
                    $highestTpId = $nf->tujuan_pembelajaran_id;
                }
                if ($minScore === null || $nf->nilai < $minScore) {
                    $minScore = $nf->nilai;
                    $lowestTpId = $nf->tujuan_pembelajaran_id;
                }
            }

            $sts = $sumatifs[$siswa->id]->sts ?? 0;
            $sas = $sumatifs[$siswa->id]->sas ?? 0;

            // Formula: Formatif (AH) 50%, STS 25%, SAS 25%
            $nilaiAkhir = ($rataFormatif * 0.5) + ($sts * 0.25) + ($sas * 0.25);

            $descTinggi = '';
            $descRendah = '';

            if ($highestTpId) {
                $tp = $this->tps->firstWhere('id', $highestTpId);
                if ($tp) {
                    $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . lcfirst($tp->deskripsi) . '.';
                }
            }

            if ($lowestTpId && $lowestTpId !== $highestTpId) {
                $tp = $this->tps->firstWhere('id', $lowestTpId);
                if ($tp) {
                    $descRendah = 'Perlu bimbingan lebih lanjut dalam ' . lcfirst($tp->deskripsi) . '.';
                }
            }

            $na = NilaiAkhir::updateOrCreate(
                ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $this->activeNilaiPenugasanId],
                [
                    'rata_formatif' => $rataFormatif,
                    'sts' => $sts,
                    'sas' => $sas,
                    'nilai_akhir' => $nilaiAkhir,
                    'deskripsi_tertinggi' => $descTinggi,
                    'deskripsi_terendah' => $descRendah,
                ]
            );

            $this->rekapData[$siswa->id] = $na;
        }
    }

    public function updateManualDeskripsi($siswaId, $text)
    {
        $na = NilaiAkhir::where('siswa_id', $siswaId)->where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)->first();
        if ($na) {
            $na->deskripsi_manual = $text;
            $na->save();
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('penilaian::livewire.penilaian-input');
    }
}
