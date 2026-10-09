<?php

namespace App\Modules\DashboardData\Livewire\Program;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Models\Program;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Program')]
class Index extends Component
{
    use GuardsPermission;
    use WithPagination;

    public string $search = '';

    public function mount()
    {
        $this->guard('dashboard_data.program.lihat');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $programs = Program::withCount([
            'siswas as total_siswa',
            'siswas as siswa_l' => function($query) {
                $query->where('jk', 'L');
            },
            'siswas as siswa_p' => function($query) {
                $query->where('jk', 'P');
            }
        ])
        ->when($this->search, function ($query) {
            $query->where('nama', 'like', '%'.$this->search.'%')
                  ->orWhere('kode', 'like', '%'.$this->search.'%');
        })
        ->paginate(10);

        return view('dashboarddata::livewire.program.index', [
            'programs' => $programs,
        ]);
    }
}
