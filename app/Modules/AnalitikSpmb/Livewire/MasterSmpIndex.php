<?php

namespace App\Modules\AnalitikSpmb\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Services\SpmbDataCleansingService;
use App\Modules\AnalitikSpmb\Services\SpmbMasterSmpImportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Master Data SMP')]
class MasterSmpIndex extends Component
{
    use GuardsPermission, WithFileUploads, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $filterProvinsi = '';

    #[Url(history: true)]
    public string $filterKabupaten = '';

    #[Url(history: true)]
    public string $filterKecamatan = '';

    #[Url(history: true)]
    public string $filterStatus = '';

    #[Url(history: true)]
    public string $filterBentuk = '';

    #[Url(history: true)]
    public string $filterPenetrasi = ''; // all, terjaring, belum

    public int $perPage = 25;

    // Modal Import State
    public bool $showImportModal = false;
    public string $inputProvinsi = 'Jawa Barat';
    public $importFile = null;
    public ?array $importResult = null;

    protected function rules(): array
    {
        return [
            'inputProvinsi' => 'required|string|min:3|max:100',
            'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:25600',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProvinsi(): void
    {
        $this->filterKabupaten = '';
        $this->filterKecamatan = '';
        $this->resetPage();
    }

    public function updatedFilterKabupaten(): void
    {
        $this->filterKecamatan = '';
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterBentuk(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPenetrasi(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterProvinsi',
            'filterKabupaten',
            'filterKecamatan',
            'filterStatus',
            'filterBentuk',
            'filterPenetrasi',
        ]);
        $this->resetPage();
    }

    public function openImportModal(): void
    {
        $this->guard('analitik_spmb.master_smp.import');
        $this->showImportModal = true;
        $this->importResult = null;
        $this->importFile = null;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importResult = null;
    }

    public function processImport(): void
    {
        $this->guard('analitik_spmb.master_smp.import');
        $this->validate();

        $path = $this->importFile->getRealPath();
        $ext = $this->importFile->getClientOriginalExtension();

        $importer = app(SpmbMasterSmpImportService::class);
        $result = $importer->import($path, $this->inputProvinsi, $ext);

        $this->importResult = $result;
        session()->flash('success', "Import {$result['provinsi']} berhasil! {$result['inserted']} sekolah baru ditambahkan, {$result['updated']} diperbarui.");
    }

    public function downloadTemplate(string $format = 'csv')
    {
        $this->guard('analitik_spmb.master_smp.import');
        $importer = app(SpmbMasterSmpImportService::class);

        if ($format === 'xlsx') {
            $spreadsheet = $importer->generateTemplateSpreadsheet();
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 'Template_Master_SMP.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $csv = $importer->generateTemplateCsv();
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'Template_Master_SMP.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Daftarkan sekolah master resmi menjadi Master Sekolah Asal SPMB lokal
     */
    public function jadikanSekolahAsal(int $masterSmpId): void
    {
        $this->guard('analitik_spmb.master_smp.kelola');

        $service = app(SpmbDataCleansingService::class);
        $sekolahAsal = $service->linkMasterSmpToSekolahAsal($masterSmpId);

        session()->flash('success', "Sekolah '{$sekolahAsal->nama_resmi}' berhasil ditambahkan ke daftar Asal SMP lokal!");
    }

    public function render()
    {
        $this->guard('analitik_spmb.master_smp.lihat');

        $query = SpmbMasterSmp::query()->with('sekolahAsal');

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', $s)
                    ->orWhere('npsn', 'like', $s)
                    ->orWhere('kecamatan', 'like', $s)
                    ->orWhere('alamat', 'like', $s);
            });
        }

        if ($this->filterProvinsi) {
            $query->where('provinsi', $this->filterProvinsi);
        }

        if ($this->filterKabupaten) {
            $query->where('kabupaten', $this->filterKabupaten);
        }

        if ($this->filterKecamatan) {
            $query->where('kecamatan', $this->filterKecamatan);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterBentuk) {
            $query->where('bentuk', $this->filterBentuk);
        }

        if ($this->filterPenetrasi === 'terjaring') {
            $query->has('sekolahAsal');
        } elseif ($this->filterPenetrasi === 'belum') {
            $query->doesntHave('sekolahAsal');
        }

        $paginated = $query->orderBy('kabupaten')->orderBy('nama')->paginate($this->perPage);

        // Statistik
        $totalMaster = SpmbMasterSmp::count();
        $totalProvinsi = SpmbMasterSmp::distinct('provinsi')->count('provinsi');
        $totalTerjaring = SpmbMasterSmp::has('sekolahAsal')->count();
        $totalPeluang = max(0, $totalMaster - $totalTerjaring);

        // Dropdown data
        $provinsiList = SpmbMasterSmp::distinct()->whereNotNull('provinsi')->orderBy('provinsi')->pluck('provinsi');

        $kabupatenList = SpmbMasterSmp::query()
            ->when($this->filterProvinsi, fn ($q) => $q->where('provinsi', $this->filterProvinsi))
            ->distinct()
            ->whereNotNull('kabupaten')
            ->orderBy('kabupaten')
            ->pluck('kabupaten');

        $kecamatanList = SpmbMasterSmp::query()
            ->when($this->filterKabupaten, fn ($q) => $q->where('kabupaten', $this->filterKabupaten))
            ->distinct()
            ->whereNotNull('kecamatan')
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $bentukList = SpmbMasterSmp::distinct()->whereNotNull('bentuk')->orderBy('bentuk')->pluck('bentuk');

        return view('analitikspmb::livewire.master-smp-index', [
            'paginated' => $paginated,
            'totalMaster' => $totalMaster,
            'totalProvinsi' => $totalProvinsi,
            'totalTerjaring' => $totalTerjaring,
            'totalPeluang' => $totalPeluang,
            'provinsiList' => $provinsiList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'bentukList' => $bentukList,
        ]);
    }
}
