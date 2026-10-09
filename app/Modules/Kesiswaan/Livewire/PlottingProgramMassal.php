<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\PlottingProgramExcelService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Plotting Program Siswa (Unggulan / Reguler)')]
class PlottingProgramMassal extends Component
{
    use GuardsPermission, WithPagination, WithFileUploads;

    // Filters
    public ?int $filterCurrentProgram = null;
    public ?int $filterRombel = null;
    public ?int $filterRayon = null;
    public string $search = '';

    // Selection
    public array $selectedSiswaIds = [];
    public bool $selectAll = false;

    // Target Program Manual
    public ?int $targetProgramId = null;

    // Import Excel Modal
    public bool $showImportModal = false;
    public ?int $importTargetProgramId = null;
    public $fileExcel = null;
    public ?array $importResult = null;

    public function updatedFilterCurrentProgram(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterRombel(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterRayon(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedSiswaIds = $this->getQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedSiswaIds = [];
        }
    }

    protected function getQuery()
    {
        $query = Siswa::query();

        if ($this->filterCurrentProgram) {
            $query->where('program_id', $this->filterCurrentProgram);
        }

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nipd', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    public function applyProgram(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk di-plot ke program.');
            return;
        }

        if (!$this->targetProgramId) {
            session()->flash('error', 'Silakan pilih Program tujuan.');
            return;
        }

        $program = Program::findOrFail($this->targetProgramId);
        $count = count($this->selectedSiswaIds);

        DB::transaction(function () use ($program) {
            Siswa::whereIn('id', $this->selectedSiswaIds)->update(['program_id' => $program->id]);
        });

        $this->reset(['selectedSiswaIds', 'selectAll']);
        session()->flash('status', "Berhasil! Sebanyak {$count} siswa berhasil dialokasikan ke Program {$program->nama}.");
    }

    public function openImportModal(): void
    {
        $this->guard('kesiswaan.siswa.edit');
        $this->reset(['fileExcel', 'importResult']);
        $this->importTargetProgramId = $this->targetProgramId;
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->reset(['fileExcel', 'importResult']);
        $this->showImportModal = false;
    }

    public function downloadTemplate(string $type = 'kosong')
    {
        $this->guard('kesiswaan.siswa.lihat');
        $service = new PlottingProgramExcelService();

        if ($type === 'prefill') {
            $siswas = $this->getQuery()->with(['rombel', 'rayon', 'program'])->orderBy('rombel_id')->orderBy('nama')->get();
            $spreadsheet = $service->generateTemplatePrefill($siswas);
            $filename = 'Template_Plotting_Siswa_Filtered_' . date('Ymd_His') . '.xlsx';
        } else {
            $spreadsheet = $service->generateTemplateKosong();
            $filename = 'Template_Plotting_Program_Kosong.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importExcel(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $this->validate([
            'importTargetProgramId' => ['required', 'exists:programs,id'],
            'fileExcel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'importTargetProgramId.required' => 'Pilih program tujuan terlebih dahulu.',
            'importTargetProgramId.exists' => 'Program tujuan tidak valid.',
            'fileExcel.required' => 'Unggah berkas Excel terlebih dahulu.',
            'fileExcel.mimes' => 'Berkas harus berformat .xlsx, .xls, atau .csv.',
        ]);

        $service = new PlottingProgramExcelService();
        $path = $this->fileExcel->getRealPath();

        $result = $service->importAndPlot($path, (int) $this->importTargetProgramId);
        $this->importResult = $result;

        $this->reset('fileExcel');

        if ($result['updated_count'] > 0) {
            session()->flash('status', "Berhasil! {$result['updated_count']} siswa dialokasikan ke Program {$result['program_nama']}.");
        }
    }

    public function render()
    {
        $query = $this->getQuery()->with(['rombel', 'rayon', 'program']);
        $siswas = $query->orderBy('rombel_id')->orderBy('nama')->paginate(25);

        $programs = Program::where('is_active', true)->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();

        return view('kesiswaan::livewire.plotting-program-massal', [
            'siswas' => $siswas,
            'programs' => $programs,
            'rombels' => $rombels,
            'rayons' => $rayons,
        ]);
    }
}
