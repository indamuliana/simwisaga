<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Penugasan Mengajar')]
class PenugasanMengajarIndex extends Component
{
    use GuardsPermission, WithPagination;

    // View Mode Tabs: 'guru' (Rekap Beban per Guru) | 'rinci' (Daftar Rinci Flat Table)
    public string $activeTab = 'guru';

    public bool $showModal = false;

    public ?int $penugasanId = null;

    // Filters & Search
    public ?int $filterTahunAjaran = null;

    public ?string $filterSemester = null; // null = semua, 'ganjil', 'genap'

    public ?int $filterPegawai = null;

    public ?int $filterRombel = null;

    public ?int $filterJurusan = null;

    public ?string $filterTingkat = null; // null, 'X', 'XI', 'XII'

    public string $search = '';

    // Sorting
    public string $sortGuruBy = 'nama'; // 'nama', 'jp_desc', 'jp_asc'

    public string $sortRinciBy = 'pegawai'; // 'pegawai', 'rombel', 'mapel', 'jp_desc'

    // Pagination
    public int $perPage = -1; // -1 = semua data, 25, 50

    // Accordion state in Tab Guru
    public array $expandedGurus = [];

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

    // Multi Select Properties for Bulk Deletion
    public array $selectedPenugasanIds = [];

    public bool $showBulkDeleteModal = false;

    public int $bulkDeleteSafeCount = 0;

    public int $bulkDeleteSkippedCount = 0;

    public function mount(): void
    {
        $this->guard('akademik.penugasan.lihat');
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaran = request()->query('filterTahunAjaran') ? (int) request()->query('filterTahunAjaran') : $currentTa?->id;

        $currentSem = AcademicContext::currentSemester();
        $defaultSemester = $currentSem?->semester ?? 'ganjil';
        $this->filterSemester = request()->has('filterSemester') ? (request()->query('filterSemester') ?: null) : $defaultSemester;

        $this->filterRombel = request()->query('filterRombel') ? (int) request()->query('filterRombel') : null;
        $this->filterPegawai = request()->query('filterPegawai') ? (int) request()->query('filterPegawai') : null;
        $this->filterJurusan = request()->query('filterJurusan') ? (int) request()->query('filterJurusan') : null;
        $this->filterTingkat = request()->query('filterTingkat') ? (string) request()->query('filterTingkat') : null;
        if (request()->query('perPage')) {
            $this->perPage = (int) request()->query('perPage');
        }
        if (request()->query('tab') && in_array(request()->query('tab'), ['guru', 'rinci'], true)) {
            $this->activeTab = request()->query('tab');
        }
        $this->tahun_ajaran_id = $this->filterTahunAjaran;
    }

    public function hasActiveFilters(): bool
    {
        $currentSem = AcademicContext::currentSemester();
        $defaultSemester = $currentSem?->semester ?? 'ganjil';

        return ! empty($this->search)
            || $this->filterSemester !== $defaultSemester
            || ! empty($this->filterPegawai)
            || ! empty($this->filterRombel)
            || ! empty($this->filterJurusan)
            || ! empty($this->filterTingkat)
            || $this->perPage !== -1;
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['guru', 'rinci'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function toggleExpandGuru(int $pegawaiId): void
    {
        if (in_array($pegawaiId, $this->expandedGurus, true)) {
            $this->expandedGurus = array_values(array_diff($this->expandedGurus, [$pegawaiId]));
        } else {
            $this->expandedGurus[] = $pegawaiId;
        }
    }

    public function expandAllGurus(array $allIds): void
    {
        $this->expandedGurus = array_map('intval', $allIds);
    }

    public function collapseAllGurus(): void
    {
        $this->expandedGurus = [];
    }

    public function resetFilters(): void
    {
        $currentSem = AcademicContext::currentSemester();
        $this->search = '';
        $this->filterSemester = $currentSem?->semester ?? 'ganjil';
        $this->filterPegawai = null;
        $this->filterRombel = null;
        $this->filterJurusan = null;
        $this->filterTingkat = null;
        $this->perPage = -1;
        $this->sortGuruBy = 'nama';
        $this->sortRinciBy = 'pegawai';
        $this->resetPage();
        $this->selectedPenugasanIds = [];
    }

    public function createForGuru(int $pegawaiId): void
    {
        $this->create();
        $this->pegawai_id = $pegawaiId;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedPenugasanIds = [];
    }

    public function updatedFilterJurusan(): void
    {
        $this->filterRombel = null;
        $this->resetPage();
        $this->selectedPenugasanIds = [];
    }

    public function updatedFilterTingkat(): void
    {
        $this->filterRombel = null;
        $this->resetPage();
        $this->selectedPenugasanIds = [];
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSortGuruBy(): void
    {
        $this->resetPage();
    }

    public function updatedSortRinciBy(): void
    {
        $this->resetPage();
    }

    public function updatedMapelId(): void
    {
        $this->pruneSelectedRombels();
        $this->syncCurriculumJp();
        $this->checkCurriculumJp();
    }

    public function updatedTahunAjaranId(): void
    {
        $this->pruneSelectedRombels();
        $this->syncCurriculumJp();
        $this->checkCurriculumJp();
    }

    public function updatedPeriodeMode(): void
    {
        $this->pruneSelectedRombels();
        $this->syncCurriculumJp();
    }

    public function updatedSelectedRombels(): void
    {
        $this->syncCurriculumJp();
    }

    public function updatedFilterTahunAjaran(): void
    {
        $this->selectedPenugasanIds = [];
    }

    public function updatedFilterSemester(): void
    {
        $this->selectedPenugasanIds = [];
    }

    public function updatedFilterPegawai(): void
    {
        $this->selectedPenugasanIds = [];
    }

    public function updatedFilterRombel(): void
    {
        $this->selectedPenugasanIds = [];
    }

    public function cancelSelection(): void
    {
        $this->selectedPenugasanIds = [];
    }

    public function updatedEditSemesterId(): void
    {
        $this->syncEditCurriculumJp();
    }

    public function updatedEditRombelId(): void
    {
        $this->syncEditCurriculumJp();
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

    /**
     * Memeriksa apakah mapel aktif di Struktur Kurikulum pada tahun ajaran terpilih.
     */
    public function isMapelInCurriculum(): bool
    {
        if (! $this->tahun_ajaran_id || ! $this->mapel_id) {
            return false;
        }

        return StrukturKurikulum::where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('mapel_id', $this->mapel_id)
            ->exists();
    }

    /**
     * Mengambil daftar rombel yang sah sesuai alokasi Struktur Kurikulum
     * untuk tahun ajaran, mapel, dan periode mode yang sedang dipilih.
     */
    public function getAllowedRombels(): \Illuminate\Support\Collection
    {
        if (! $this->tahun_ajaran_id || ! $this->mapel_id) {
            return collect();
        }

        $skQuery = StrukturKurikulum::where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('mapel_id', $this->mapel_id);

        if ($this->periode_mode === 'ganjil') {
            $skQuery->where('jp_ganjil', '>', 0);
        } elseif ($this->periode_mode === 'genap') {
            $skQuery->where('jp_genap', '>', 0);
        } else {
            // full_year: aktif jika memiliki JP di salah satu atau kedua semester
            $skQuery->where(function ($q) {
                $q->where('jp_ganjil', '>', 0)
                    ->orWhere('jp_genap', '>', 0)
                    ->orWhere('jp_per_minggu', '>', 0);
            });
        }

        $activeSkList = $skQuery->get();

        if ($activeSkList->isEmpty()) {
            return collect();
        }

        return Rombel::with('jurusan')
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where(function ($q) use ($activeSkList) {
                foreach ($activeSkList as $sk) {
                    $q->orWhere(function ($sub) use ($sk) {
                        $sub->where('tingkat', $sk->tingkat)
                            ->where('jurusan_id', $sk->jurusan_id);
                    });
                }
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    /**
     * Mengambil rombel yang diizinkan untuk modal Edit Penugasan Satuan.
     */
    public function getAllowedRombelsForEdit(): \Illuminate\Support\Collection
    {
        if (! $this->edit_semester_id || ! $this->mapel_id) {
            return collect();
        }

        $sem = Semester::find($this->edit_semester_id);
        if (! $sem) {
            return collect();
        }

        $skQuery = StrukturKurikulum::where('tahun_ajaran_id', $sem->tahun_ajaran_id)
            ->where('mapel_id', $this->mapel_id);

        if ($sem->semester === 'ganjil') {
            $skQuery->where('jp_ganjil', '>', 0);
        } elseif ($sem->semester === 'genap') {
            $skQuery->where('jp_genap', '>', 0);
        }

        $activeSkList = $skQuery->get();
        if ($activeSkList->isEmpty()) {
            return collect();
        }

        return Rombel::with('jurusan')
            ->where('tahun_ajaran_id', $sem->tahun_ajaran_id)
            ->where(function ($q) use ($activeSkList) {
                foreach ($activeSkList as $sk) {
                    $q->orWhere(function ($sub) use ($sk) {
                        $sub->where('tingkat', $sk->tingkat)
                            ->where('jurusan_id', $sk->jurusan_id);
                    });
                }
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    /**
     * Memeriksa apakah satu rombel sah di kurikulum untuk semester dan mapel tertentu.
     */
    public function isRombelAllowedForSemesterAndMapel(int $rombelId, int $semesterId, int $mapelId): bool
    {
        $rombel = Rombel::find($rombelId);
        $sem = Semester::find($semesterId);
        if (! $rombel || ! $sem) {
            return false;
        }

        $skQuery = StrukturKurikulum::where('tahun_ajaran_id', $sem->tahun_ajaran_id)
            ->where('mapel_id', $mapelId)
            ->where('tingkat', $rombel->tingkat)
            ->where('jurusan_id', $rombel->jurusan_id);

        if ($sem->semester === 'ganjil') {
            $skQuery->where('jp_ganjil', '>', 0);
        } elseif ($sem->semester === 'genap') {
            $skQuery->where('jp_genap', '>', 0);
        }

        return $skQuery->exists();
    }

    /**
     * Memangkas pilihan rombel yang sudah tidak lagi diizinkan oleh kurikulum.
     */
    public function pruneSelectedRombels(): void
    {
        $allowedIds = $this->getAllowedRombels()->pluck('id')->toArray();
        $this->selectedRombels = array_values(array_intersect($this->selectedRombels, $allowedIds));

        foreach (array_keys($this->rombelJpMap) as $rId) {
            if (! in_array($rId, $this->selectedRombels)) {
                unset($this->rombelJpMap[$rId]);
            }
        }
    }

    public function syncEditCurriculumJp(): void
    {
        if ($this->edit_rombel_id && $this->mapel_id && $this->edit_semester_id) {
            $rombel = Rombel::find($this->edit_rombel_id);
            $semester = Semester::find($this->edit_semester_id);
            if ($rombel && $semester) {
                $sk = StrukturKurikulum::where('tahun_ajaran_id', $semester->tahun_ajaran_id)
                    ->where('jurusan_id', $rombel->jurusan_id)
                    ->where('tingkat', $rombel->tingkat)
                    ->where('mapel_id', $this->mapel_id)
                    ->first();
                if ($sk) {
                    $jp = $sk->getJpForSemester($semester->semester);
                    $this->edit_jp = $jp ?: $sk->jp_per_minggu;
                }
            }
        }
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
        $allowedRombels = $this->getAllowedRombels();
        $rombelIdsInTingkat = $allowedRombels->where('tingkat', $tingkat)->pluck('id')->toArray();

        if (empty($rombelIdsInTingkat)) {
            return;
        }

        $currentSelectedInTingkat = array_intersect($this->selectedRombels, $rombelIdsInTingkat);

        if (count($currentSelectedInTingkat) === count($rombelIdsInTingkat)) {
            // Semua rombel valid pada tingkat ini sudah terpilih -> batalkan pilihan
            $this->selectedRombels = array_values(array_diff($this->selectedRombels, $rombelIdsInTingkat));
        } else {
            // Belum semua terpilih -> pilih semua rombel valid pada tingkat ini
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
        $this->guard('akademik.penugasan.tambah');
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
        $this->guard('akademik.penugasan.edit');
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
            $this->guard('akademik.penugasan.edit');
            $this->validate([
                'edit_semester_id' => ['required', 'exists:semesters,id'],
                'pegawai_id' => ['required', 'exists:pegawais,id'],
                'edit_rombel_id' => ['required', 'exists:rombels,id'],
                'mapel_id' => ['required', 'exists:mapels,id'],
                'edit_jp' => ['required', 'integer', 'min:1', 'max:200'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ]);

            $p = PenugasanMengajar::findOrFail($this->penugasanId);

            $editAllowed = $this->getAllowedRombelsForEdit();
            if (! $this->isRombelAllowedForSemesterAndMapel($this->edit_rombel_id, $this->edit_semester_id, $this->mapel_id)) {
                $rNama = Rombel::find($this->edit_rombel_id)?->nama ?? 'terpilih';
                $this->addError('edit_rombel_id', "Kelas {$rNama} tidak terdaftar dalam Struktur Kurikulum untuk mata pelajaran ini pada semester terpilih.");
                return;
            }

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

        $this->guard('akademik.penugasan.tambah');
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

        $allowedRombels = $this->getAllowedRombels();
        $allowedIds = $allowedRombels->pluck('id')->toArray();
        $invalidRombelIds = array_diff($this->selectedRombels, $allowedIds);

        if (! empty($invalidRombelIds)) {
            $invalidNames = Rombel::whereIn('id', $invalidRombelIds)->pluck('nama')->implode(', ');
            $this->addError('selectedRombels', "Kelas ({$invalidNames}) tidak terdaftar dalam Struktur Kurikulum untuk mata pelajaran ini pada tahun ajaran dan semester terpilih.");
            return;
        }

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
        $this->guard('akademik.penugasan.hapus');
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
        $this->guard('akademik.penugasan.hapus');

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

    public function toggleSelectAll(): void
    {
        $visibleIds = $this->getFilteredPenugasanQuery()->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        $currentSelected = array_map('intval', $this->selectedPenugasanIds);
        $intersect = array_intersect($currentSelected, $visibleIds);

        if (count($intersect) === count($visibleIds) && count($visibleIds) > 0) {
            $this->selectedPenugasanIds = array_values(array_diff($currentSelected, $visibleIds));
        } else {
            $this->selectedPenugasanIds = array_values(array_unique(array_merge($currentSelected, $visibleIds)));
        }
    }

    public function promptBulkDelete(): void
    {
        $this->guard('akademik.penugasan.hapus');

        if (empty($this->selectedPenugasanIds)) {
            session()->flash('error', 'Pilih minimal satu penugasan mengajar untuk dihapus.');
            return;
        }

        $targets = PenugasanMengajar::with('nilaiAkhirs')->whereIn('id', $this->selectedPenugasanIds)->get();
        $this->bulkDeleteSafeCount = $targets->filter(fn ($p) => $p->nilaiAkhirs->count() === 0)->count();
        $this->bulkDeleteSkippedCount = $targets->filter(fn ($p) => $p->nilaiAkhirs->count() > 0)->count();

        $this->showBulkDeleteModal = true;
    }

    public function executeBulkDelete(): void
    {
        $this->guard('akademik.penugasan.hapus');

        if (empty($this->selectedPenugasanIds)) {
            $this->showBulkDeleteModal = false;
            return;
        }

        $targets = PenugasanMengajar::with('nilaiAkhirs')->whereIn('id', $this->selectedPenugasanIds)->get();
        $safeIds = [];
        $skippedCount = 0;

        foreach ($targets as $p) {
            if ($p->nilaiAkhirs->count() > 0) {
                $skippedCount++;
            } else {
                $safeIds[] = $p->id;
            }
        }

        if (! empty($safeIds)) {
            PenugasanMengajar::whereIn('id', $safeIds)->delete();
        }

        $deletedCount = count($safeIds);
        $this->selectedPenugasanIds = [];
        $this->showBulkDeleteModal = false;

        $msg = "Berhasil menghapus {$deletedCount} penugasan mengajar.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} penugasan dilewati karena sudah memiliki data nilai siswa).";
        }

        session()->flash('status', $msg);
    }

    public function getFilteredPenugasanQuery()
    {
        $query = PenugasanMengajar::query();

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
        if ($this->filterJurusan) {
            $query->whereHas('rombel', fn ($q) => $q->where('jurusan_id', $this->filterJurusan));
        }
        if ($this->filterTingkat) {
            $query->whereHas('rombel', fn ($q) => $q->where('tingkat', $this->filterTingkat));
        }
        if (! empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('pegawai', function ($qp) use ($searchTerm) {
                    $qp->where('nama_lengkap', 'like', $searchTerm)
                        ->orWhere('nip', 'like', $searchTerm);
                })
                ->orWhereHas('mapel', function ($qm) use ($searchTerm) {
                    $qm->where('nama', 'like', $searchTerm)
                        ->orWhere('kode', 'like', $searchTerm);
                })
                ->orWhereHas('rombel', function ($qr) use ($searchTerm) {
                    $qr->where('nama', 'like', $searchTerm);
                });
            });
        }

        return $query;
    }

    public function cetakRekap()
    {
        $this->guard('akademik.penugasan.cetak');

        $ta = TahunAjaran::find($this->filterTahunAjaran) ?? AcademicContext::currentTahunAjaran();
        $query = $this->getFilteredPenugasanQuery()->with(['semester.tahunAjaran', 'pegawai', 'rombel', 'mapel']);

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

        $subtitle = 'Tahun Ajaran ' . ($ta?->nama ?? '');
        if ($this->filterSemester) {
            $subtitle .= ' Semester ' . ucfirst($this->filterSemester);
        } else {
            $subtitle .= ' (Semua Semester)';
        }

        if ($this->filterJurusan) {
            $jurusanModel = Jurusan::find($this->filterJurusan);
            if ($jurusanModel) {
                $subtitle .= ' | Jurusan ' . $jurusanModel->kode;
            }
        }
        if ($this->filterTingkat) {
            $subtitle .= ' | Tingkat ' . $this->filterTingkat;
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
        $allPenugasans = $this->getFilteredPenugasanQuery()
            ->with(['semester.tahunAjaran', 'pegawai', 'mapel.kelompokMapel', 'rombel.jurusan'])
            ->get();

        $totalJp = (int) $allPenugasans->sum('jp');
        $totalPenugasanCount = $allPenugasans->count();
        $totalGuruBertugasCount = $allPenugasans->pluck('pegawai_id')->filter()->unique()->count();

        // Deteksi Team Teaching (Mapel & Rombel sama di Semester yang sama)
        $teamTeachingGroups = $allPenugasans->groupBy(function ($item) {
            return $item->semester_id . '_' . $item->mapel_id . '_' . $item->rombel_id;
        });

        $teamTeachingItemCount = $allPenugasans->filter(function ($item) use ($teamTeachingGroups) {
            $key = $item->semester_id . '_' . $item->mapel_id . '_' . $item->rombel_id;
            return $teamTeachingGroups->get($key, collect())->count() > 1;
        })->count();

        // Batch Query Struktur Kurikulum Target JP
        $kurikulumMap = [];
        $taIds = $allPenugasans->pluck('semester.tahun_ajaran_id')->filter()->unique()->values();
        $jurusanIds = $allPenugasans->pluck('rombel.jurusan_id')->filter()->unique()->values();
        $tingkats = $allPenugasans->pluck('rombel.tingkat')->filter()->unique()->values();
        $mapelIds = $allPenugasans->pluck('mapel_id')->filter()->unique()->values();

        if ($taIds->isNotEmpty() && $mapelIds->isNotEmpty()) {
            $kurikulums = StrukturKurikulum::whereIn('tahun_ajaran_id', $taIds)
                ->whereIn('jurusan_id', $jurusanIds)
                ->whereIn('tingkat', $tingkats)
                ->whereIn('mapel_id', $mapelIds)
                ->get();

            foreach ($kurikulums as $k) {
                $kurikulumMap[$k->tahun_ajaran_id . '_' . $k->jurusan_id . '_' . $k->tingkat . '_' . $k->mapel_id] = [
                    'jp_ganjil' => (int) $k->jp_ganjil,
                    'jp_genap' => (int) $k->jp_genap,
                ];
            }
        }

        // --- TAB 1: DATA REKAP BEBAN PER GURU ---
        $groupedByGuru = $allPenugasans->groupBy('pegawai_id');
        $guruCards = [];

        foreach ($groupedByGuru as $pegawaiId => $assignments) {
            $pegawai = $assignments->first()->pegawai;
            if (! $pegawai) {
                continue;
            }
            $totalJpGuru = (int) $assignments->sum('jp');

            if ($totalJpGuru < 18) {
                $bebanStatus = 'kurang';
                $bebanLabel = 'Beban Kurang (< 18 JP)';
                $bebanBadgeClass = 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800';
            } elseif ($totalJpGuru <= 24) {
                $bebanStatus = 'ideal';
                $bebanLabel = 'Standar / Ideal (18-24 JP)';
                $bebanBadgeClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
            } else {
                $bebanStatus = 'lebih';
                $bebanLabel = 'Beban Lebih (> 24 JP)';
                $bebanBadgeClass = 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800';
            }

            $detailList = [];
            foreach ($assignments as $a) {
                $ttKey = $a->semester_id . '_' . $a->mapel_id . '_' . $a->rombel_id;
                $ttGroup = $teamTeachingGroups->get($ttKey, collect());
                $isTeam = $ttGroup->count() > 1;
                $partners = $isTeam ? $ttGroup->where('id', '!=', $a->id)->map(fn ($pt) => [
                    'id' => $pt->id,
                    'nama' => $pt->pegawai?->nama_gelar ?: ($pt->pegawai?->nama_lengkap ?: 'Guru Mitra'),
                    'jp' => (int) $pt->jp,
                ])->values()->toArray() : [];

                $kKey = $a->semester?->tahun_ajaran_id . '_' . $a->rombel?->jurusan_id . '_' . $a->rombel?->tingkat . '_' . $a->mapel_id;
                $semStr = $a->semester?->semester;
                $targetJp = isset($kurikulumMap[$kKey]) ? ($semStr === 'genap' ? $kurikulumMap[$kKey]['jp_genap'] : $kurikulumMap[$kKey]['jp_ganjil']) : null;

                $detailList[] = [
                    'id' => $a->id,
                    'mapel_id' => $a->mapel_id,
                    'mapel_nama' => $a->mapel?->nama,
                    'mapel_kode' => $a->mapel?->kode,
                    'kelompok' => $a->mapel?->kelompok_label,
                    'rombel_id' => $a->rombel_id,
                    'rombel_nama' => $a->rombel?->nama,
                    'jurusan_kode' => $a->rombel?->jurusan?->kode,
                    'tingkat' => $a->rombel?->tingkat,
                    'tahun_ajaran' => $a->semester?->tahunAjaran?->nama,
                    'semester_type' => $a->semester?->semester,
                    'jp' => (int) $a->jp,
                    'target_jp' => $targetJp,
                    'is_team' => $isTeam,
                    'team_total_jp' => (int) $ttGroup->sum('jp'),
                    'partners' => $partners,
                ];
            }

            $uniqueRombels = $assignments->pluck('rombel_id')->unique()->count();
            $uniqueMapels = $assignments->pluck('mapel_id')->unique()->count();
            $teamTeachingCount = collect($detailList)->where('is_team', true)->count();

            $guruCards[] = [
                'pegawai_id' => $pegawai->id,
                'nama' => $pegawai->nama_gelar ?: $pegawai->nama_lengkap,
                'nip' => $pegawai->nip ?: '-',
                'jenis_kelamin' => $pegawai->jenis_kelamin,
                'total_jp' => $totalJpGuru,
                'beban_status' => $bebanStatus,
                'beban_label' => $bebanLabel,
                'beban_badge_class' => $bebanBadgeClass,
                'unique_rombels' => $uniqueRombels,
                'unique_mapels' => $uniqueMapels,
                'team_teaching_count' => $teamTeachingCount,
                'assignments' => $detailList,
            ];
        }

        // Sorting Tab Guru
        if ($this->sortGuruBy === 'jp_desc') {
            usort($guruCards, fn ($a, $b) => $b['total_jp'] <=> $a['total_jp'] ?: strcasecmp($a['nama'], $b['nama']));
        } elseif ($this->sortGuruBy === 'jp_asc') {
            usort($guruCards, fn ($a, $b) => $a['total_jp'] <=> $b['total_jp'] ?: strcasecmp($a['nama'], $b['nama']));
        } else {
            usort($guruCards, fn ($a, $b) => strcasecmp($a['nama'], $b['nama']));
        }

        // Pagination Tab Guru
        $page = $this->getPage();
        $totalGuruCards = count($guruCards);
        $perPageVal = $this->perPage > 0 ? $this->perPage : max($totalGuruCards, 1);
        $slicedGuruCards = array_slice($guruCards, ($page - 1) * $perPageVal, $perPageVal);
        $paginatedGuruCards = new LengthAwarePaginator(
            $slicedGuruCards,
            $totalGuruCards,
            $perPageVal,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // --- TAB 2: DATA DAFTAR RINCI (FLAT TABLE) ---
        $enrichedPenugasans = $allPenugasans->map(function ($p) use ($teamTeachingGroups, $kurikulumMap, $groupedByGuru) {
            $ttKey = $p->semester_id . '_' . $p->mapel_id . '_' . $p->rombel_id;
            $ttGroup = $teamTeachingGroups->get($ttKey, collect());
            $isTeam = $ttGroup->count() > 1;
            $partners = $isTeam ? $ttGroup->where('id', '!=', $p->id)->map(fn ($pt) => [
                'id' => $pt->id,
                'nama' => $pt->pegawai?->nama_gelar ?: ($pt->pegawai?->nama_lengkap ?: 'Guru Mitra'),
                'jp' => (int) $pt->jp,
            ])->values()->toArray() : [];

            $kKey = $p->semester?->tahun_ajaran_id . '_' . $p->rombel?->jurusan_id . '_' . $p->rombel?->tingkat . '_' . $p->mapel_id;
            $semStr = $p->semester?->semester;
            $targetJp = isset($kurikulumMap[$kKey]) ? ($semStr === 'genap' ? $kurikulumMap[$kKey]['jp_genap'] : $kurikulumMap[$kKey]['jp_ganjil']) : null;

            $guruTotalJp = $groupedByGuru->has($p->pegawai_id) ? (int) $groupedByGuru->get($p->pegawai_id)->sum('jp') : (int) $p->jp;

            $p->is_team_teaching = $isTeam;
            $p->team_partners = $partners;
            $p->team_total_jp = (int) $ttGroup->sum('jp');
            $p->curriculum_target_jp = $targetJp;
            $p->guru_total_jp = $guruTotalJp;

            return $p;
        });

        // Sorting Tab Rinci
        if ($this->sortRinciBy === 'rombel') {
            $enrichedPenugasans = $enrichedPenugasans->sortBy(fn ($p) => $p->rombel?->nama);
        } elseif ($this->sortRinciBy === 'mapel') {
            $enrichedPenugasans = $enrichedPenugasans->sortBy(fn ($p) => $p->mapel?->nama);
        } elseif ($this->sortRinciBy === 'jp_desc') {
            $enrichedPenugasans = $enrichedPenugasans->sortByDesc('jp');
        } else {
            $enrichedPenugasans = $enrichedPenugasans->sortBy(fn ($p) => $p->pegawai?->nama_gelar ?: $p->pegawai?->nama_lengkap);
        }

        // Pagination Tab Rinci
        $totalPenugasan = $enrichedPenugasans->count();
        $rinciPerPageVal = $this->perPage > 0 ? $this->perPage : max($totalPenugasan, 1);
        $slicedPenugasans = $enrichedPenugasans->slice(($page - 1) * $rinciPerPageVal, $rinciPerPageVal)->values();
        $paginatedPenugasans = new LengthAwarePaginator(
            $slicedPenugasans,
            $totalPenugasan,
            $rinciPerPageVal,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $visibleIds = $slicedPenugasans->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        $currentSelected = array_map('intval', $this->selectedPenugasanIds);
        $isAllSelected = count($visibleIds) > 0 && count(array_intersect($currentSelected, $visibleIds)) === count($visibleIds);

        // Filter collections
        $tahunAjarans = TahunAjaran::with('semesters')->orderByDesc('id')->get();
        $semesters = Semester::with('tahunAjaran')->orderByDesc('tahun_ajaran_id')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get();
        $gurus = Pegawai::where('jenis_gtk', 'guru')->where('is_active', true)->orderBy('nama_lengkap')->get();

        $rombelQuery = Rombel::with('jurusan');
        if ($this->filterTahunAjaran) {
            $rombelQuery->where('tahun_ajaran_id', $this->filterTahunAjaran);
        }
        if ($this->filterJurusan) {
            $rombelQuery->where('jurusan_id', $this->filterJurusan);
        }
        if ($this->filterTingkat) {
            $rombelQuery->where('tingkat', $this->filterTingkat);
        }
        $rombels = $rombelQuery->orderBy('tingkat')->orderBy('nama')->get();

        $allowedRombels = $this->getAllowedRombels();
        $rombelsByTingkat = $allowedRombels->groupBy('tingkat');
        $isMapelInCurriculum = $this->isMapelInCurriculum();
        $hasMatchingRombels = $allowedRombels->isNotEmpty();
        $editAllowedRombels = $this->penugasanId ? $this->getAllowedRombelsForEdit() : collect();

        $mapels = Mapel::with('kelompokMapel')
            ->leftJoin('kelompok_mapels', 'mapels.kelompok_mapel_id', '=', 'kelompok_mapels.id')
            ->select('mapels.*')
            ->where('mapels.is_active', true)
            ->orderBy('kelompok_mapels.urutan')
            ->orderBy('mapels.urutan')
            ->get();

        return view('akademik::livewire.penugasan-mengajar-index', [
            'penugasans' => $paginatedPenugasans,
            'paginatedGuruCards' => $paginatedGuruCards,
            'totalJp' => $totalJp,
            'totalPenugasanCount' => $totalPenugasanCount,
            'totalGuruBertugasCount' => $totalGuruBertugasCount,
            'teamTeachingItemCount' => $teamTeachingItemCount,
            'tahunAjarans' => $tahunAjarans,
            'semesters' => $semesters,
            'jurusans' => $jurusans,
            'gurus' => $gurus,
            'rombels' => $rombels,
            'rombelsByTingkat' => $rombelsByTingkat,
            'isMapelInCurriculum' => $isMapelInCurriculum,
            'hasMatchingRombels' => $hasMatchingRombels,
            'editAllowedRombels' => $editAllowedRombels,
            'mapels' => $mapels,
            'isAllSelected' => $isAllSelected,
            'activeSemesterName' => AcademicContext::currentSemester()?->semester,
        ]);
    }
}
