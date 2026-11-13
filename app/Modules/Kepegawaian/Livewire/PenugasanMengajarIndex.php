<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Penugasan Mengajar')]
class PenugasanMengajarIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;

    public ?int $penugasanId = null;

    // Filters
    public ?int $filterTahunAjaran = null;

    public ?string $filterSemester = null; // null = semua, 'ganjil', 'genap'

    public ?int $filterPegawai = null;

    public ?int $filterRombel = null;

    // Form fields for Create (Bulk / Multi-Rombel)
    public ?int $tahun_ajaran_id = null;

    public string $periode_mode = 'full_year'; // 'full_year', 'ganjil', 'genap'

    public ?int $pegawai_id = null;

    public ?int $mapel_id = null;

    public array $selectedRombels = [];

    public array $rombelJpMap = [];

    public ?string $keterangan = null;

    // Backward-compatibility properties for legacy tests & single selection
    public ?int $rombel_id = null;

    public ?int $semester_id = null;

    public int $jp = 2;

    // Form fields for Single Edit
    public ?int $edit_semester_id = null;

    public ?int $edit_rombel_id = null;

    public int $edit_jp = 2;

    // Smart Delete Modal
    public bool $showDeleteModal = false;

    public ?int $deleteTargetId = null;

    public ?int $partnerPenugasanId = null;

    public string $deleteTargetInfo = '';

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaran = $currentTa?->id;
        $this->tahun_ajaran_id = $currentTa?->id;
    }

    public function updatedMapelId(): void
    {
        $this->rombelJpMap = [];
        $this->syncCurriculumJp();
        $this->checkCurriculumJp();
    }

    public function updatedTahunAjaranId(): void
    {
        $this->rombelJpMap = [];
        $this->syncCurriculumJp();
        $this->checkCurriculumJp();
    }

    public function updatedSelectedRombels(): void
    {
        $this->syncCurriculumJp();
    }

    public function updatedRombelId($value): void
    {
        if ($value) {
            $this->selectedRombels = array_unique(array_merge($this->selectedRombels, [$value]));
        }
        $this->checkCurriculumJp();
    }

    public function updatedSemesterId($value): void
    {
        if ($value) {
            $sem = Semester::find($value);
            if ($sem) {
                $this->tahun_ajaran_id = $sem->tahun_ajaran_id;
                $this->periode_mode = $sem->semester;
            }
        }
        $this->checkCurriculumJp();
    }

    public function checkCurriculumJp(): void
    {
        if ($this->rombel_id && $this->mapel_id) {
            $rombel = Rombel::find($this->rombel_id);
            $semester = $this->semester_id ? Semester::find($this->semester_id) : null;
            if ($rombel) {
                $sk = StrukturKurikulum::where('tahun_ajaran_id', $rombel->tahun_ajaran_id)
                    ->where('jurusan_id', $rombel->jurusan_id)
                    ->where('tingkat', $rombel->tingkat)
                    ->where('mapel_id', $this->mapel_id)
                    ->first();
                if ($sk) {
                    $semesterJp = $semester ? $sk->getJpForSemester($semester->semester) : null;
                    $this->jp = $semesterJp ?: $sk->jp_per_minggu;
                    $this->rombelJpMap[$this->rombel_id] = $this->jp;
                }
            }
        }
    }

    public function toggleTingkat(string $tingkat): void
    {
        $rombelIdsInTingkat = Rombel::where('tingkat', $tingkat)->pluck('id')->toArray();
        $currentSelectedInTingkat = array_intersect($this->selectedRombels, $rombelIdsInTingkat);

        if (count($currentSelectedInTingkat) === count($rombelIdsInTingkat)) {
            // Semua rombel pada tingkat ini sudah terpilih -> batalkan pilihan
            $this->selectedRombels = array_values(array_diff($this->selectedRombels, $rombelIdsInTingkat));
        } else {
            // Belum semua terpilih -> pilih semua rombel pada tingkat ini
            $this->selectedRombels = array_values(array_unique(array_merge($this->selectedRombels, $rombelIdsInTingkat)));
        }

        $this->syncCurriculumJp();
    }

    public function syncCurriculumJp(): void
    {
        if (! $this->mapel_id || ! $this->tahun_ajaran_id) {
            return;
        }

        foreach ($this->selectedRombels as $rombelId) {
            if (! isset($this->rombelJpMap[$rombelId])) {
                $rombel = Rombel::find($rombelId);
                if ($rombel) {
                    $sk = StrukturKurikulum::where('tahun_ajaran_id', $this->tahun_ajaran_id)
                        ->where('jurusan_id', $rombel->jurusan_id)
                        ->where('tingkat', $rombel->tingkat)
                        ->where('mapel_id', $this->mapel_id)
                        ->first();
                    $this->rombelJpMap[$rombelId] = $sk?->jp_per_minggu ?: ($sk?->jp_ganjil ?: 2);
                } else {
                    $this->rombelJpMap[$rombelId] = 2;
                }
            }
        }
    }

    public function create(): void
    {
        $this->guard('kepegawaian.penugasan.tambah');
        $this->reset(['penugasanId', 'keterangan', 'selectedRombels', 'rombelJpMap']);
        $this->tahun_ajaran_id = $this->filterTahunAjaran ?? AcademicContext::currentTahunAjaran()?->id;
        $this->periode_mode = 'full_year';

        $firstGuru = Pegawai::where('jenis_gtk', 'guru')->where('is_active', true)->first();
        $this->pegawai_id = $this->filterPegawai ?? $firstGuru?->id;

        $firstMapel = Mapel::where('is_active', true)->first();
        $this->mapel_id = $firstMapel?->id;

        if ($this->filterRombel) {
            $this->selectedRombels = [$this->filterRombel];
            $this->syncCurriculumJp();
        }

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('kepegawaian.penugasan.edit');
        $p = PenugasanMengajar::findOrFail($id);
        $this->penugasanId = $p->id;
        $this->edit_semester_id = $p->semester_id;
        $this->pegawai_id = $p->pegawai_id;
        $this->edit_rombel_id = $p->rombel_id;
        $this->mapel_id = $p->mapel_id;
        $this->edit_jp = $p->jp;
        $this->keterangan = $p->keterangan;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->penugasanId) {
            $this->guard('kepegawaian.penugasan.edit');
            $this->validate([
                'edit_semester_id' => ['required', 'exists:semesters,id'],
                'pegawai_id' => ['required', 'exists:pegawais,id'],
                'edit_rombel_id' => ['required', 'exists:rombels,id'],
                'mapel_id' => ['required', 'exists:mapels,id'],
                'edit_jp' => ['required', 'integer', 'min:1', 'max:200'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ]);

            $p = PenugasanMengajar::findOrFail($this->penugasanId);
            $p->update([
                'semester_id' => $this->edit_semester_id,
                'pegawai_id' => $this->pegawai_id,
                'rombel_id' => $this->edit_rombel_id,
                'mapel_id' => $this->mapel_id,
                'jp' => $this->edit_jp,
                'keterangan' => $this->keterangan ?: null,
            ]);

            $this->showModal = false;
            session()->flash('status', 'Penugasan mengajar berhasil diperbarui.');
            return;
        }

        $this->guard('kepegawaian.penugasan.tambah');
        $this->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'periode_mode' => ['required', 'in:full_year,ganjil,genap'],
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'mapel_id' => ['required', 'exists:mapels,id'],
            'selectedRombels' => ['required', 'array', 'min:1'],
            'selectedRombels.*' => ['exists:rombels,id'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'selectedRombels.required' => 'Pilih minimal satu rombel kelas.',
            'selectedRombels.min' => 'Pilih minimal satu rombel kelas.',
        ]);

        $semestersQuery = Semester::where('tahun_ajaran_id', $this->tahun_ajaran_id);
        if ($this->periode_mode === 'ganjil') {
            $semestersQuery->where('semester', 'ganjil');
        } elseif ($this->periode_mode === 'genap') {
            $semestersQuery->where('semester', 'genap');
        }
        $targetSemesters = $semestersQuery->get();

        if ($targetSemesters->isEmpty()) {
            $this->addError('periode_mode', 'Data semester untuk tahun ajaran terpilih tidak ditemukan.');
            return;
        }

        DB::transaction(function () use ($targetSemesters) {
            foreach ($this->selectedRombels as $rombelId) {
                $rombel = Rombel::find($rombelId);
                foreach ($targetSemesters as $sem) {
                    $jp = isset($this->rombelJpMap[$rombelId]) ? (int) $this->rombelJpMap[$rombelId] : null;
                    if (! $jp || $jp < 1) {
                        $sk = StrukturKurikulum::where('tahun_ajaran_id', $this->tahun_ajaran_id)
                            ->where('jurusan_id', $rombel?->jurusan_id)
                            ->where('tingkat', $rombel?->tingkat)
                            ->where('mapel_id', $this->mapel_id)
                            ->first();
                        $jp = $sk ? $sk->getJpForSemester($sem->semester) : 2;
                        if (! $jp) {
                            $jp = $sk?->jp_per_minggu ?: 2;
                        }
                    }

                    PenugasanMengajar::updateOrCreate(
                        [
                            'semester_id' => $sem->id,
                            'pegawai_id' => $this->pegawai_id,
                            'rombel_id' => $rombelId,
                            'mapel_id' => $this->mapel_id,
                        ],
                        [
                            'jp' => $jp,
                            'keterangan' => $this->keterangan ?: null,
                        ]
                    );
                }
            }
        });

        $totalEntries = count($this->selectedRombels) * $targetSemesters->count();
        $this->showModal = false;
        session()->flash('status', "Berhasil menyimpan {$totalEntries} alokasi penugasan mengajar (".count($this->selectedRombels)." rombel x {$targetSemesters->count()} semester).");
    }

    public function promptDelete(int $id): void
    {
        $this->guard('kepegawaian.penugasan.hapus');
        $p = PenugasanMengajar::with(['semester.tahunAjaran', 'pegawai', 'mapel', 'rombel', 'nilaiAkhirs'])->findOrFail($id);

        if ($p->nilaiAkhirs->count() > 0) {
            session()->flash('error', "Penugasan {$p->mapel?->nama} di kelas {$p->rombel?->nama} tidak dapat dihapus karena sudah memiliki {$p->nilaiAkhirs->count()} data nilai siswa.");
            return;
        }

        $this->deleteTargetId = $p->id;
        $this->deleteTargetInfo = "{$p->pegawai?->nama_gelar} - {$p->mapel?->nama} ({$p->rombel?->nama})";

        $sem = $p->semester;
        $otherSem = Semester::where('tahun_ajaran_id', $sem->tahun_ajaran_id)
            ->where('id', '!=', $sem->id)
            ->first();

        $this->partnerPenugasanId = null;
        if ($otherSem) {
            $partner = PenugasanMengajar::where('semester_id', $otherSem->id)
                ->where('pegawai_id', $p->pegawai_id)
                ->where('mapel_id', $p->mapel_id)
                ->where('rombel_id', $p->rombel_id)
                ->first();
            if ($partner && $partner->nilaiAkhirs()->count() === 0) {
                $this->partnerPenugasanId = $partner->id;
            }
        }

        $this->showDeleteModal = true;
    }

    public function executeDelete(string $mode = 'single'): void
    {
        $this->guard('kepegawaian.penugasan.hapus');

        if (! $this->deleteTargetId) {
            $this->showDeleteModal = false;
            return;
        }

        $target = PenugasanMengajar::find($this->deleteTargetId);
        if ($target) {
            $target->delete();
        }

        if ($mode === 'full_year' && $this->partnerPenugasanId) {
            $partner = PenugasanMengajar::find($this->partnerPenugasanId);
            if ($partner) {
                $partner->delete();
            }
            session()->flash('status', 'Penugasan mengajar 1 tahun penuh (kedua semester) berhasil dihapus.');
        } else {
            session()->flash('status', 'Penugasan mengajar untuk semester terpilih berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->reset(['deleteTargetId', 'partnerPenugasanId', 'deleteTargetInfo']);
    }

    public function cetakRekap()
    {
        $this->guard('kepegawaian.penugasan.cetak');

        $ta = TahunAjaran::find($this->filterTahunAjaran) ?? AcademicContext::currentTahunAjaran();
        $query = PenugasanMengajar::with(['semester.tahunAjaran', 'pegawai', 'rombel', 'mapel']);

        if ($this->filterTahunAjaran) {
            $query->whereHas('semester', fn ($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran));
        }
        if ($this->filterSemester) {
            $query->whereHas('semester', fn ($q) => $q->where('semester', $this->filterSemester));
        }
        if ($this->filterPegawai) {
            $query->where('pegawai_id', $this->filterPegawai);
        }
        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        $assignments = $query->get()->groupBy('pegawai_id');

        $rows = '';
        $no = 1;
        $grandTotalJp = 0;

        foreach ($assignments as $pegawaiId => $items) {
            $pegawai = $items->first()->pegawai;
            $nama = $pegawai?->nama_gelar ?? 'Guru';
            $nip = $pegawai?->nip ?? '-';
            $totalJpGuru = $items->sum('jp');
            $grandTotalJp += $totalJpGuru;

            $mapelList = $items->map(fn ($it) => "{$it->mapel?->nama} ({$it->rombel?->nama} [{$it->semester?->semester}] - {$it->jp} JP)")->join('<br>');

            $rows .= "
                <tr>
                    <td style='text-align: center;'>{$no}</td>
                    <td><b>{$nama}</b><br><small style='color: #64748b;'>NIP: {$nip}</small></td>
                    <td>{$mapelList}</td>
                    <td style='text-align: center; font-weight: bold;'>{$totalJpGuru} JP</td>
                </tr>
            ";
            $no++;
        }

        $subtitle = 'Tahun Ajaran '.($ta?->nama ?? '');
        if ($this->filterSemester) {
            $subtitle .= ' Semester '.ucfirst($this->filterSemester);
        } else {
            $subtitle .= ' (Semua Semester)';
        }

        $html = "
            <table class='content-table'>
                <thead>
                    <tr>
                        <th style='width: 30px; text-align: center;'>No</th>
                        <th>Nama Guru & NIP</th>
                        <th>Daftar Mapel, Rombel & Semester</th>
                        <th style='width: 90px; text-align: center;'>Beban (JP)</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                    <tr style='background-color: #f8fafc; font-weight: bold;'>
                        <td colspan='3' style='text-align: right;'>TOTAL KESELURUHAN BEBAN JAM MENGAJAR:</td>
                        <td style='text-align: center;'>{$grandTotalJp} JP</td>
                    </tr>
                </tbody>
            </table>
        ";

        $pdf = DocumentEngine::createPdf(
            title: 'REKAPITULASI PEMBAGIAN TUGAS MENGAJAR GURU',
            content: $html,
            options: [
                'subtitle' => $subtitle,
                'template_kode' => 'leger_nilai',
            ]
        );

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'Rekap_Beban_Mengajar_Guru.pdf'
        );
    }

    public function render()
    {
        $query = PenugasanMengajar::with(['semester.tahunAjaran', 'pegawai', 'mapel', 'rombel']);

        if ($this->filterTahunAjaran) {
            $query->whereHas('semester', fn ($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran));
        }
        if ($this->filterSemester) {
            $query->whereHas('semester', fn ($q) => $q->where('semester', $this->filterSemester));
        }
        if ($this->filterPegawai) {
            $query->where('pegawai_id', $this->filterPegawai);
        }
        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        $penugasans = $query->orderBy('pegawai_id')->get();
        $totalJp = $penugasans->sum('jp');

        $tahunAjarans = TahunAjaran::with('semesters')->orderByDesc('id')->get();
        $semesters = Semester::with('tahunAjaran')->orderByDesc('tahun_ajaran_id')->get();
        $gurus = Pegawai::where('jenis_gtk', 'guru')->where('is_active', true)->orderBy('nama_lengkap')->get();
        
        $rombels = Rombel::with('jurusan')->orderBy('tingkat')->orderBy('nama')->get();
        $rombelsByTingkat = $rombels->groupBy('tingkat');

        $mapels = Mapel::with('kelompokMapel')
            ->leftJoin('kelompok_mapels', 'mapels.kelompok_mapel_id', '=', 'kelompok_mapels.id')
            ->select('mapels.*')
            ->where('mapels.is_active', true)
            ->orderBy('kelompok_mapels.urutan')
            ->orderBy('mapels.urutan')
            ->get();

        return view('kepegawaian::livewire.penugasan-mengajar-index', [
            'penugasans' => $penugasans,
            'totalJp' => $totalJp,
            'tahunAjarans' => $tahunAjarans,
            'semesters' => $semesters,
            'gurus' => $gurus,
            'rombels' => $rombels,
            'rombelsByTingkat' => $rombelsByTingkat,
            'mapels' => $mapels,
        ]);
    }
}
