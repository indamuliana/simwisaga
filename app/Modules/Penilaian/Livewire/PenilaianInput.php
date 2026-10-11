<?php

namespace App\Modules\Penilaian\Livewire;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
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
    public $edit_tp_id = null;

    // State for Active Assessment Semester in Tab 2 & 3
    public $activeNilaiSemesterId = null;
    public $activeNilaiPenugasanId = null;

    // State for Input Formatif
    public $formatifData = [];

    // State for Input Sumatif
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

        // 2. Load Formatif
        $this->formatifData = [];
        $nilai = NilaiFormatif::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->tps->pluck('id'))
            ->get();

        foreach ($this->siswas as $siswa) {
            foreach ($this->tps as $tp) {
                $n = $nilai->where('siswa_id', $siswa->id)->where('tujuan_pembelajaran_id', $tp->id)->first();
                $this->formatifData[$siswa->id][$tp->id] = $n ? $n->nilai : null;
            }
        }

        // 3. Load Sumatif
        $this->sumatifData = [];
        $sumatif = NilaiSumatif::where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)
            ->get()
            ->keyBy('siswa_id');

        foreach ($this->siswas as $siswa) {
            $n = $sumatif[$siswa->id] ?? null;
            $this->sumatifData[$siswa->id] = [
                'sts' => $n ? $n->sts : null,
                'sas' => $n ? $n->sas : null,
            ];
        }

        // 4. Jika sedang di tab rekap, hitung otomatis
        if ($this->activeTab === 'rekap') {
            $this->calculateRekap();
        }
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

    public function saveTP()
    {
        $this->validate([
            'tp_semester_id' => 'required',
            'tp_kode' => 'required|string|max:20',
            'tp_deskripsi' => 'required|string|max:255',
        ], [
            'tp_semester_id.required' => 'Pilih semester target untuk TP / CP ini.',
            'tp_kode.required' => 'Kode TP / CP wajib diisi (misal: TP 1, TP 4, atau CP 1).',
            'tp_deskripsi.required' => 'Deskripsi TP / CP wajib diisi.',
        ]);

        $targetSem = collect($this->semesters)->firstWhere('id', (int) $this->tp_semester_id);
        $targetPmId = $targetSem ? $targetSem['penugasan_id'] : $this->penugasan_id;

        if ($this->edit_tp_id) {
            $tp = TujuanPembelajaran::findOrFail($this->edit_tp_id);
            $tp->update([
                'penugasan_mengajar_id' => $targetPmId,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
            ]);
            $this->edit_tp_id = null;
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil diperbarui.');
        } else {
            TujuanPembelajaran::create([
                'penugasan_mengajar_id' => $targetPmId,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
                'is_active' => true,
            ]);
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil ditambahkan.');
        }

        $this->tp_kode = '';
        $this->tp_deskripsi = '';
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
    }

    public function cancelEditTP()
    {
        $this->edit_tp_id = null;
        $this->tp_kode = '';
        $this->tp_deskripsi = '';
        $this->tp_semester_id = $this->activeNilaiSemesterId ?? $this->penugasan->semester_id;
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
            'formatifData.*.*' => 'nullable|numeric|min:0|max:100',
            'sumatifData.*.sts' => 'nullable|numeric|min:0|max:100',
            'sumatifData.*.sas' => 'nullable|numeric|min:0|max:100',
        ], [
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

        // Simpan Formatif
        foreach ($this->formatifData as $siswaId => $tpScores) {
            foreach ($tpScores as $tpId => $score) {
                if ($score !== null && $score !== '') {
                    NilaiFormatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tpId],
                        ['nilai' => $score]
                    );
                } else {
                    NilaiFormatif::where('siswa_id', $siswaId)->where('tujuan_pembelajaran_id', $tpId)->delete();
                }
            }
        }

        // Simpan Sumatif
        foreach ($this->sumatifData as $siswaId => $data) {
            if ($data['sts'] !== null || $data['sas'] !== null) {
                NilaiSumatif::updateOrCreate(
                    ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $this->activeNilaiPenugasanId],
                    ['sts' => $data['sts'] === '' ? null : $data['sts'], 'sas' => $data['sas'] === '' ? null : $data['sas']]
                );
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

        $sumatifs = NilaiSumatif::where('penugasan_mengajar_id', $this->activeNilaiPenugasanId)->get()->keyBy('siswa_id');

        foreach ($this->siswas as $siswa) {
            $siswaFormatifs = $formatifs->where('siswa_id', $siswa->id);
            $rataFormatif = $siswaFormatifs->avg('nilai') ?? 0;

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

            // Formula: Formatif 50%, STS 25%, SAS 25%
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
