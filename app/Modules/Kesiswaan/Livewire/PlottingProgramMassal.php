<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Plotting Program Siswa (Unggulan / Reguler)')]
class PlottingProgramMassal extends Component
{
    use GuardsPermission, WithPagination;

    // Filters
    public ?int $filterCurrentProgram = null;
    public ?int $filterRombel = null;
    public ?int $filterRayon = null;
    public string $search = '';

    // Selection
    public array $selectedSiswaIds = [];
    public bool $selectAll = false;

    // Target Program
    public ?int $targetProgramId = null;

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
