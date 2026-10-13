<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Services\SeniBudayaService;
use App\Modules\SeniBudaya\Services\SeniBudayaExcelService;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;

#[Layout('components.layouts.app')]
#[Title('Keanggotaan Siswa Seni Budaya')]
class SeniBudayaAnggotaIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $filterCabangId = 'all';
    public $filterRombelId = 'all';
    public $filterTingkat = 'all';
    public string $search = '';

    // Modal Pendaftaran Manual
    public bool $showEnrollModal = false;
    public ?int $selectedSiswaId = null;
    public ?int $targetCabangId = null;

    // Modal Import Excel
    public bool $showImportModal = false;
    public $importFile = null;
    public ?array $importSummary = null;

    // Modal Pindah Cabang
    public bool $showMoveModal = false;
    public ?int $movingAnggotaId = null;
    public ?int $newCabangId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCabangId()
    {
        $this->resetPage();
    }

    public function updatingFilterRombelId()
    {
        $this->resetPage();
    }

    public function openEnrollModal()
    {
        $this->reset(['selectedSiswaId', 'targetCabangId']);
        $this->showEnrollModal = true;
    }

    public function saveEnrollment(SeniBudayaService $service)
    {
        $this->validate([
            'selectedSiswaId' => 'required|exists:siswas,id',
            'targetCabangId' => 'required|exists:seni_budayas,id',
        ], [
            'selectedSiswaId.required' => 'Pilih siswa terlebih dahulu.',
            'targetCabangId.required' => 'Pilih cabang Seni Budaya.',
        ]);

        $semester = Semester::where('is_active', true)->first();
        if (!$semester) {
            session()->flash('error', 'Tidak ada semester aktif.');
            return;
        }

        $res = $service->enrollStudent($this->selectedSiswaId, $this->targetCabangId, $semester->id);
        $this->showEnrollModal = false;
        session()->flash('message', $res['message']);
    }

    public function openMoveModal(int $anggotaId)
    {
        $anggota = SeniBudayaAnggota::findOrFail($anggotaId);
        $this->movingAnggotaId = $anggota->id;
        $this->newCabangId = $anggota->seni_budaya_id;
        $this->showMoveModal = true;
    }

    public function saveMove(SeniBudayaService $service)
    {
        $this->validate([
            'movingAnggotaId' => 'required|exists:seni_budaya_anggotas,id',
            'newCabangId' => 'required|exists:seni_budayas,id',
        ]);

        $anggota = SeniBudayaAnggota::findOrFail($this->movingAnggotaId);
        $res = $service->enrollStudent($anggota->siswa_id, $this->newCabangId, $anggota->semester_id);

        $this->showMoveModal = false;
        session()->flash('message', "Siswa berhasil dipindahkan ke cabang baru.");
    }

    public function removeMember(int $anggotaId)
    {
        $anggota = SeniBudayaAnggota::with('siswa')->findOrFail($anggotaId);
        $nama = $anggota->siswa?->nama ?? 'Siswa';
        $anggota->delete();
        session()->flash('message', "Pendaftaran '{$nama}' dari Seni Budaya berhasil dihapus.");
    }

    public function openImportModal()
    {
        $this->reset(['importFile', 'importSummary']);
        $this->showImportModal = true;
    }

    public function processImport(SeniBudayaExcelService $excelService)
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'importFile.required' => 'Silakan pilih file Excel template.',
            'importFile.mimes' => 'Format file harus berupa Excel (.xlsx atau .xls).',
        ]);

        $semester = Semester::where('is_active', true)->first();
        if (!$semester) {
            session()->flash('error', 'Tidak ada semester aktif.');
            return;
        }

        $path = $this->importFile->getRealPath();
        $this->importSummary = $excelService->importAnggota($path, $semester->id);

        if ($this->importSummary['success_count'] > 0 || $this->importSummary['updated_count'] > 0) {
            session()->flash('message', "Import selesai: {$this->importSummary['success_count']} baru, {$this->importSummary['updated_count']} diperbarui.");
        }
    }

    public function render()
    {
        $semester = Semester::where('is_active', true)->first();
        $semesterId = $semester?->id ?? 0;

        $query = SeniBudayaAnggota::with(['siswa.rombel', 'seniBudaya'])
            ->where('semester_id', $semesterId);

        if ($this->filterCabangId !== 'all') {
            $query->where('seni_budaya_id', $this->filterCabangId);
        }

        if ($this->filterRombelId !== 'all') {
            $query->whereHas('siswa', fn($q) => $q->where('rombel_id', $this->filterRombelId));
        }

        if ($this->filterTingkat !== 'all') {
            $query->whereHas('siswa.rombel', fn($q) => $q->where('tingkat', $this->filterTingkat));
        }

        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('nipd', 'like', '%' . $this->search . '%');
            });
        }

        $anggotas = $query->orderBy('seni_budaya_id')
            ->join('siswas', 'seni_budaya_anggotas.siswa_id', '=', 'siswas.id')
            ->orderBy('siswas.nama')
            ->select('seni_budaya_anggotas.*')
            ->paginate(25);

        $cabangs = SeniBudaya::where('is_active', true)->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        // Siswa aktif untuk modal pendaftaran manual
        $availableSiswas = Siswa::with('rombel')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->take(500)
            ->get();

        return view('senibudaya::livewire.anggota-index', [
            'anggotas' => $anggotas,
            'cabangs' => $cabangs,
            'rombels' => $rombels,
            'availableSiswas' => $availableSiswas,
            'semester' => $semester,
        ]);
    }
}
