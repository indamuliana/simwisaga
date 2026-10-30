<?php

namespace App\Modules\AnalitikSpmb\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\AnalitikSpmb\Models\SpmbIgnoredDuplicate;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Services\SpmbDataCleansingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Perbaikan Data Asal SMP &bull; SPMB')]
class PerbaikanSmpIndex extends Component
{
    use GuardsPermission;
    use WithPagination;

    // Filter & Search
    public string $search = '';
    public string $filterMapping = ''; // '', 'mapped', 'unmapped'
    public int $perPage = 20;

    // Selection for Multi-Merge
    public array $selectedSchools = [];
    public bool $selectAll = false;

    // Smart Duplicate Suggestions
    public array $suggestions = [];

    // Merge Modal State
    public bool $showMergeModal = false;
    public array $mergeSourceNames = [];
    public string $mergeTargetName = '';
    public ?int $mergeTargetMasterId = null;
    public bool $optUpdateSiswa = true;
    public bool $optCreateAlias = true;

    // Quick Rename Modal State
    public bool $showRenameModal = false;
    public string $renameOldName = '';
    public string $renameNewName = '';
    public bool $renameOptUpdateSiswa = true;
    public bool $renameOptCreateAlias = true;

    // Quick Edit Master Modal State
    public bool $showEditMasterModal = false;
    public ?int $editMasterId = null;
    public string $editMasterNamaResmi = '';
    public string $editMasterNpsn = '';
    public string $editMasterCatatan = '';
    public string $editMasterTier = 'Rintisan';
    public string $editMasterJenis = 'SMP';
    public string $editMasterStatus = 'Negeri';

    // Modal Daftar Rekomendasi Diabaikan
    public bool $showIgnoredModal = false;

    // Drawer Detail Siswa
    public bool $showDrawer = false;
    public string $drawerSchoolName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterMapping' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.lihat');
        $this->refreshSuggestions();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedSchools = [];
        $this->selectAll = false;
    }

    public function updatingFilterMapping(): void
    {
        $this->resetPage();
        $this->selectedSchools = [];
        $this->selectAll = false;
    }

    public function refreshSuggestions(): void
    {
        $this->suggestions = app(SpmbDataCleansingService::class)->detectPotentialDuplicates();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $cleansingService = app(SpmbDataCleansingService::class);
            $items = $cleansingService->getUniqueSchoolStrings($this->search, $this->filterMapping);
            $this->selectedSchools = $items->pluck('raw_name')->toArray();
        } else {
            $this->selectedSchools = [];
        }
    }

    public function openDrawer(string $rawName): void
    {
        $this->drawerSchoolName = $rawName;
        $this->showDrawer = true;
    }

    public function closeDrawer(): void
    {
        $this->showDrawer = false;
        $this->drawerSchoolName = '';
    }

    public function openRenameModal(string $rawName): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        $this->renameOldName = $rawName;
        $this->renameNewName = $rawName;
        $this->renameOptUpdateSiswa = true;
        $this->renameOptCreateAlias = true;
        $this->showRenameModal = true;
    }

    public function closeRenameModal(): void
    {
        $this->showRenameModal = false;
        $this->renameOldName = '';
        $this->renameNewName = '';
    }

    public function executeRename(): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        $this->validate([
            'renameNewName' => 'required|string|min:3|max:150',
        ]);

        if (trim($this->renameOldName) === trim($this->renameNewName)) {
            $this->closeRenameModal();
            return;
        }

        $cleansingService = app(SpmbDataCleansingService::class);
        $result = $cleansingService->renameSchoolString(
            $this->renameOldName,
            $this->renameNewName,
            $this->renameOptUpdateSiswa,
            $this->renameOptCreateAlias
        );

        $this->closeRenameModal();
        $this->refreshSuggestions();
        session()->flash('success', "Berhasil mengubah '{$this->renameOldName}' menjadi '{$result['target_name']}'. {$result['affected_students']} data siswa diperbarui.");
    }

    public function openMergeFromSelection(): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        if (empty($this->selectedSchools)) {
            session()->flash('error', 'Pilih minimal 1 sekolah asal dari tabel untuk digabungkan.');
            return;
        }

        $this->mergeSourceNames = array_values($this->selectedSchools);
        $this->mergeTargetName = $this->mergeSourceNames[0] ?? '';
        $this->mergeTargetMasterId = null;
        $this->optUpdateSiswa = true;
        $this->optCreateAlias = true;
        $this->showMergeModal = true;
    }

    public function openMergeSingle(string $rawName): void
    {
        $this->selectedSchools = [$rawName];
        $this->openMergeFromSelection();
    }

    public function openMergeFromSuggestion(int $suggestionIndex): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        if (! isset($this->suggestions[$suggestionIndex])) {
            return;
        }

        $cluster = $this->suggestions[$suggestionIndex];
        $this->mergeSourceNames = array_column($cluster['items'], 'raw_name');
        $this->mergeTargetName = $cluster['suggested_target'];
        $this->mergeTargetMasterId = null;
        $this->optUpdateSiswa = true;
        $this->optCreateAlias = true;
        $this->showMergeModal = true;
    }

    public function updatedMergeTargetMasterId($value): void
    {
        if ($value) {
            $master = SpmbSekolahAsal::find($value);
            if ($master) {
                $this->mergeTargetName = $master->nama_resmi;
            }
        }
    }

    public function closeMergeModal(): void
    {
        $this->showMergeModal = false;
        $this->mergeSourceNames = [];
        $this->mergeTargetName = '';
        $this->mergeTargetMasterId = null;
    }

    public function executeMerge(): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        $this->validate([
            'mergeTargetName' => 'required|string|min:3|max:150',
            'mergeSourceNames' => 'required|array|min:1',
        ]);

        $cleansingService = app(SpmbDataCleansingService::class);
        $result = $cleansingService->mergeSchoolStrings(
            $this->mergeSourceNames,
            $this->mergeTargetName,
            $this->optUpdateSiswa,
            $this->optCreateAlias,
            $this->mergeTargetMasterId
        );

        $this->selectedSchools = [];
        $this->selectAll = false;
        $this->closeMergeModal();
        $this->refreshSuggestions();

        session()->flash('success', "Penggabungan berhasil! {$result['affected_students']} data siswa diperbarui ke '{$result['target_name']}'.");
    }

    public function openEditMasterModal(int $masterId): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        $master = SpmbSekolahAsal::findOrFail($masterId);
        $this->editMasterId = $master->id;
        $this->editMasterNamaResmi = $master->nama_resmi;
        $this->editMasterNpsn = $master->npsn ?? '';
        $this->editMasterCatatan = $master->catatan ?? '';
        $this->editMasterTier = $master->tier ?? 'Rintisan';
        $this->editMasterJenis = $master->jenis ?? 'SMP';
        $this->editMasterStatus = $master->status_sekolah ?? 'Negeri';
        $this->showEditMasterModal = true;
    }

    public function closeEditMasterModal(): void
    {
        $this->showEditMasterModal = false;
        $this->editMasterId = null;
        $this->editMasterNamaResmi = '';
        $this->editMasterNpsn = '';
        $this->editMasterCatatan = '';
    }

    public function saveEditMaster(): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        if (! $this->editMasterId) {
            return;
        }

        $this->validate([
            'editMasterNpsn' => 'nullable|string|max:20',
            'editMasterCatatan' => 'nullable|string|max:500',
        ]);

        $master = SpmbSekolahAsal::findOrFail($this->editMasterId);
        $master->update([
            'npsn' => trim($this->editMasterNpsn) ?: null,
            'catatan' => trim($this->editMasterCatatan) ?: null,
        ]);

        $this->closeEditMasterModal();
        session()->flash('success', "Data master '{$master->nama_resmi}' berhasil diperbarui (NPSN & Catatan tersimpan).");
    }

    public function markAsNotDuplicate(int $suggestionIndex): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        if (! isset($this->suggestions[$suggestionIndex])) {
            return;
        }

        $cluster = $this->suggestions[$suggestionIndex];
        $names = array_column($cluster['items'], 'raw_name');

        $cleansingService = app(SpmbDataCleansingService::class);
        $cleansingService->markClusterAsDifferent($names);

        $this->refreshSuggestions();
        session()->flash('success', 'Rekomendasi duplikat ditandai sebagai sekolah yang berbeda dan tidak akan disarankan lagi.');
    }

    public function excludeFromSuggestion(int $suggestionIndex, string $rawName): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        if (! isset($this->suggestions[$suggestionIndex])) {
            return;
        }

        $cluster = $this->suggestions[$suggestionIndex];
        $cleansingService = app(SpmbDataCleansingService::class);

        foreach ($cluster['items'] as $item) {
            if ($item['raw_name'] !== $rawName) {
                $cleansingService->markAsDifferent($rawName, $item['raw_name']);
            }
        }

        $this->refreshSuggestions();
        session()->flash('success', "Sekolah '{$rawName}' berhasil dikeluarkan dari rekomendasi kelompok ini.");
    }

    public function restoreIgnoredPair(int $id): void
    {
        $this->guard('analitik_spmb.perbaikan_smp.kelola');

        app(SpmbDataCleansingService::class)->unmarkDifferent($id);
        $this->refreshSuggestions();
        session()->flash('success', 'Tanda sekolah berbeda berhasil dibatalkan.');
    }

    public function render()
    {
        $cleansingService = app(SpmbDataCleansingService::class);
        $allUnique = $cleansingService->getUniqueSchoolStrings($this->search, $this->filterMapping);

        // Manual pagination untuk collection
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $allUnique->forPage($page, $this->perPage)->values(),
            $allUnique->count(),
            $this->perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Ambil daftar master sekolah untuk dropdown target merge
        $masterSekolahs = SpmbSekolahAsal::orderBy('nama_resmi')->get(['id', 'nama_resmi', 'tier']);

        // Data siswa untuk pratinjau modal merge
        $previewStudents = collect();
        if ($this->showMergeModal && ! empty($this->mergeSourceNames)) {
            $previewStudents = $cleansingService->getAffectedStudents($this->mergeSourceNames);
        }

        // Data siswa untuk drawer
        $drawerStudents = collect();
        if ($this->showDrawer && $this->drawerSchoolName) {
            $drawerStudents = $cleansingService->getAffectedStudents([$this->drawerSchoolName]);
        }

        // Statistik ringkas
        $totalVariasi = $allUnique->count();
        $totalSiswaTerdata = $allUnique->sum('total_siswa');
        $totalUnmapped = $allUnique->where('is_mapped', false)->count();
        $ignoredCount = SpmbIgnoredDuplicate::count();
        $ignoredPairs = $this->showIgnoredModal ? SpmbIgnoredDuplicate::latest()->get() : collect();

        return view('analitikspmb::livewire.perbaikan-smp-index', array_merge(get_object_vars($this), [
            'paginated' => $paginated,
            'masterSekolahs' => $masterSekolahs,
            'previewStudents' => $previewStudents,
            'drawerStudents' => $drawerStudents,
            'totalVariasi' => $totalVariasi,
            'totalSiswaTerdata' => $totalSiswaTerdata,
            'totalUnmapped' => $totalUnmapped,
            'ignoredCount' => $ignoredCount,
            'ignoredPairs' => $ignoredPairs,
        ]));
    }
}
