<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Support\DataMasker;
use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\SiswaCustomExporter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
#[Title('Unduh Data Siswa Kustom')]
class SiswaCustomExport extends Component
{
    use GuardsPermission;

    // Filter Multi-Select
    public array $filterTingkat = [];
    public array $filterRombel = [];
    public array $filterRayon = [];
    public array $filterProgram = [];
    public array $filterStatus = ['aktif'];

    // Sorting
    public string $sortField = 'nama';
    public string $sortDirection = 'asc';

    // Kolom Terpilih & Pencarian
    public array $selectedColumns = [
        'nipd',
        'nisn',
        'nama',
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'tingkat',
        'rombel',
        'rayon',
        'status',
    ];

    public string $searchAvailable = '';
    public string $selectedCategory = '';

    public function mount(): void
    {
        $this->guard('kesiswaan.siswa.lihat');
    }

    public function addColumn(string $key): void
    {
        $available = SiswaCustomExporter::getAvailableColumns();
        if (isset($available[$key]) && ! in_array($key, $this->selectedColumns, true)) {
            $this->selectedColumns[] = $key;
        }
    }

    public function removeColumn(string $key): void
    {
        $this->selectedColumns = array_values(array_filter(
            $this->selectedColumns,
            fn ($item) => $item !== $key
        ));
    }

    public function moveColumnUp(string $key): void
    {
        $index = array_search($key, $this->selectedColumns, true);
        if ($index !== false && $index > 0) {
            $prev = $this->selectedColumns[$index - 1];
            $this->selectedColumns[$index - 1] = $key;
            $this->selectedColumns[$index] = $prev;
        }
    }

    public function moveColumnDown(string $key): void
    {
        $index = array_search($key, $this->selectedColumns, true);
        if ($index !== false && $index < count($this->selectedColumns) - 1) {
            $next = $this->selectedColumns[$index + 1];
            $this->selectedColumns[$index + 1] = $key;
            $this->selectedColumns[$index] = $next;
        }
    }

    public function applyPreset(string $presetKey): void
    {
        $presets = SiswaCustomExporter::getPresets();
        if (isset($presets[$presetKey])) {
            $this->selectedColumns = $presets[$presetKey]['columns'];
        }
    }

    public function clearColumns(): void
    {
        $this->selectedColumns = [];
    }

    public function selectAllFilter(string $filterType, array $values): void
    {
        $prop = 'filter' . ucfirst($filterType);
        if (property_exists($this, $prop)) {
            $this->{$prop} = array_map('strval', $values);
        }
    }

    public function clearFilter(string $filterType): void
    {
        $prop = 'filter' . ucfirst($filterType);
        if (property_exists($this, $prop)) {
            $this->{$prop} = [];
        }
    }

    protected function getFilteredQuery()
    {
        $query = Siswa::query()->with(['rombel', 'rayon', 'program', 'orangTuas']);

        if (! empty($this->filterTingkat)) {
            $query->whereHas('rombel', function ($q) {
                $q->whereIn('tingkat', $this->filterTingkat);
            });
        }

        if (! empty($this->filterRombel)) {
            $query->whereIn('rombel_id', $this->filterRombel);
        }

        if (! empty($this->filterRayon)) {
            $query->whereIn('rayon_id', $this->filterRayon);
        }

        if (! empty($this->filterProgram)) {
            $query->whereIn('program_id', $this->filterProgram);
        }

        if (! empty($this->filterStatus)) {
            $query->whereIn('status', $this->filterStatus);
        }

        if ($this->sortField === 'rombel') {
            $query->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
                ->orderBy('rombels.nama', $this->sortDirection)
                ->select('siswas.*');
        } elseif ($this->sortField === 'rayon') {
            $query->leftJoin('rayons', 'siswas.rayon_id', '=', 'rayons.id')
                ->orderBy('rayons.nama', $this->sortDirection)
                ->select('siswas.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query;
    }

    public function downloadExcel(): StreamedResponse
    {
        $this->guard('kesiswaan.siswa.lihat');

        if (empty($this->selectedColumns)) {
            session()->flash('error', 'Pilih minimal satu kolom sebelum mengunduh data.');
            return response()->streamDownload(fn () => null, 'empty.xlsx');
        }

        $siswas = $this->getFilteredQuery()->get();
        $exporter = new SiswaCustomExporter();
        $spreadsheet = $exporter->exportXlsx($siswas, $this->selectedColumns);

        $filename = 'Data_Siswa_Kustom_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadCsv(): StreamedResponse
    {
        $this->guard('kesiswaan.siswa.lihat');

        if (empty($this->selectedColumns)) {
            session()->flash('error', 'Pilih minimal satu kolom sebelum mengunduh data.');
            return response()->streamDownload(fn () => null, 'empty.csv');
        }

        $siswas = $this->getFilteredQuery()->get();
        $exporter = new SiswaCustomExporter();
        $csvContent = $exporter->exportCsv($siswas, $this->selectedColumns);

        $filename = 'Data_Siswa_Kustom_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function render()
    {
        $activeTa = AcademicContext::currentTahunAjaran();

        $allRombels = Rombel::query()
            ->when($activeTa, fn ($q) => $q->where('tahun_ajaran_id', $activeTa->id))
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        $allRayons = Rayon::orderBy('nama')->get();
        $allPrograms = Program::where('is_active', true)->orderBy('nama')->get();

        $availableColumns = SiswaCustomExporter::getAvailableColumns();

        // Filter available columns by category and search keyword
        $unselectedColumns = [];
        foreach ($availableColumns as $key => $col) {
            if (in_array($key, $this->selectedColumns, true)) {
                continue;
            }

            if ($this->selectedCategory !== '' && $col['category'] !== $this->selectedCategory) {
                continue;
            }

            if ($this->searchAvailable !== '') {
                $kw = strtolower($this->searchAvailable);
                if (! str_contains(strtolower($col['label']), $kw) && ! str_contains(strtolower($key), $kw)) {
                    continue;
                }
            }

            $unselectedColumns[$key] = $col;
        }

        // Preview data
        $query = $this->getFilteredQuery();
        $totalCount = (clone $query)->count();
        $previewSiswas = (clone $query)->take(5)->get();

        $sensitive = DataMasker::canViewSensitive();

        // Build preview matrix
        $previewData = [];
        foreach ($previewSiswas as $s) {
            $row = [];
            foreach ($this->selectedColumns as $colKey) {
                if (isset($availableColumns[$colKey])) {
                    $row[$colKey] = ($availableColumns[$colKey]['resolver'])($s, $sensitive);
                }
            }
            $previewData[] = $row;
        }

        $categories = array_values(array_unique(array_map(fn ($c) => $c['category'], $availableColumns)));

        return view('kesiswaan::livewire.siswa-custom-export', [
            'allRombels' => $allRombels,
            'allRayons' => $allRayons,
            'allPrograms' => $allPrograms,
            'availableColumns' => $availableColumns,
            'unselectedColumns' => $unselectedColumns,
            'categories' => $categories,
            'totalCount' => $totalCount,
            'previewData' => $previewData,
            'presets' => SiswaCustomExporter::getPresets(),
        ]);
    }
}
