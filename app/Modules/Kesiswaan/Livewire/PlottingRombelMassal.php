<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\PlottingRombelExcelService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Plotting Rombel Massal (Kelas)')]
class PlottingRombelMassal extends Component
{
    use GuardsPermission, WithPagination, WithFileUploads;

    // Filters
    public string $filterStatusRombel = 'belum_ada'; // 'belum_ada' atau 'semua'
    public string $filterTingkat = ''; // '', 'X', 'XI', 'XII'
    public ?int $filterJurusan = null;
    public ?int $filterRayon = null;
    public string $search = '';

    // Selection
    public array $selectedSiswaIds = [];
    public bool $selectAll = false;

    // Target Rombel Manual
    public ?int $targetRombelId = null;

    // Import Excel Modal
    public bool $showImportModal = false;
    public ?int $importTargetRombelId = null;
    public $fileExcel = null;
    public ?array $importResult = null;

    public function updatedFilterStatusRombel(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterTingkat(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterJurusan(): void
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

        if ($this->filterStatusRombel === 'belum_ada') {
            $query->whereNull('rombel_id');
        }

        if ($this->filterTingkat) {
            $query->whereHas('rombel', function ($q) {
                $q->where('tingkat', $this->filterTingkat);
            });
        }

        if ($this->filterJurusan) {
            $query->whereHas('rombel', function ($q) {
                $q->where('jurusan_id', $this->filterJurusan);
            });
        }

        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%")
                    ->orWhere('nipd', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    public function applyPlotting(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk dialokasikan ke rombel.');
            return;
        }

        if (!$this->targetRombelId) {
            session()->flash('error', 'Silakan pilih Rombel tujuan terlebih dahulu.');
            return;
        }

        $rombel = Rombel::findOrFail($this->targetRombelId);
        $ta = AcademicContext::currentTahunAjaran();
        $count = count($this->selectedSiswaIds);

        DB::transaction(function () use ($rombel, $ta) {
            foreach ($this->selectedSiswaIds as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if ($siswa) {
                    $siswa->update(['rombel_id' => $rombel->id]);

                    if ($ta) {
                        RombelSiswa::updateOrCreate(
                            ['tahun_ajaran_id' => $ta->id, 'siswa_id' => $siswa->id],
                            ['rombel_id' => $rombel->id]
                        );
                    }
                }
            }
        });

        $this->reset(['selectedSiswaIds', 'selectAll']);

        $totalTerisi = $rombel->siswas()->count();
        $overMsg = ($rombel->kapasitas && $totalTerisi > $rombel->kapasitas)
            ? " (Peringatan: Rombel kini melebihi kapasitas standar: {$totalTerisi}/{$rombel->kapasitas})"
            : "";

        session()->flash('status', "Berhasil! Sebanyak {$count} siswa berhasil dialokasikan ke {$rombel->nama}.{$overMsg}");
    }

    public function openImportModal(): void
    {
        $this->guard('kesiswaan.siswa.edit');
        $this->reset(['fileExcel', 'importResult']);
        $this->importTargetRombelId = $this->targetRombelId;
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
        $service = new PlottingRombelExcelService();

        if ($type === 'prefill') {
            $siswas = $this->getQuery()->with(['rombel', 'rayon', 'program'])->orderBy('nama')->get();
            $spreadsheet = $service->generateTemplatePrefill($siswas);
            $filename = 'Template_Plotting_Rombel_Filtered_' . date('Ymd_His') . '.xlsx';
        } else {
            $spreadsheet = $service->generateTemplateKosong();
            $filename = 'Template_Plotting_Rombel_Kosong.xlsx';
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
            'importTargetRombelId' => ['required', 'exists:rombels,id'],
            'fileExcel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'importTargetRombelId.required' => 'Pilih rombel tujuan terlebih dahulu.',
            'importTargetRombelId.exists' => 'Rombel tujuan tidak valid.',
            'fileExcel.required' => 'Unggah berkas Excel terlebih dahulu.',
            'fileExcel.mimes' => 'Berkas harus berformat .xlsx, .xls, atau .csv.',
        ]);

        $service = new PlottingRombelExcelService();
        $path = $this->fileExcel->getRealPath();

        $result = $service->importAndPlot($path, (int) $this->importTargetRombelId);
        $this->importResult = $result;

        $this->reset('fileExcel');

        if ($result['updated_count'] > 0) {
            $overMsg = $result['is_over_capacity']
                ? " (Peringatan: Rombel melebihi kapasitas standar: {$result['total_terisi']}/{$result['kapasitas']})"
                : "";
            session()->flash('status', "Berhasil! {$result['updated_count']} siswa dialokasikan ke Rombel {$result['rombel_nama']}.{$overMsg}");
        }
    }

    public function render()
    {
        $query = $this->getQuery()->with(['rombel.jurusan', 'rayon', 'program']);
        $siswas = $query->orderBy('nama')->paginate(25);

        $ta = AcademicContext::currentTahunAjaran();
        $rombels = Rombel::withCount('siswas')
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();

        return view('kesiswaan::livewire.plotting-rombel-massal', [
            'siswas' => $siswas,
            'rombels' => $rombels,
            'jurusans' => $jurusans,
            'rayons' => $rayons,
            'tahunAjaran' => $ta,
        ]);
    }
}
