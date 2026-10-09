<?php

namespace App\Modules\DashboardData\Livewire\Jurusan;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Jurusan')]
class Index extends Component
{
    use GuardsPermission;
    use WithPagination;

    public string $search = '';

    public function mount()
    {
        $this->guard('dashboard_data.jurusan.lihat');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $jurusans = Jurusan::withCount([
            'rombels as total_siswa' => function($query) {
                $query->join('siswas', 'rombels.id', '=', 'siswas.rombel_id');
            },
            'rombels as siswa_l' => function($query) {
                $query->join('siswas', 'rombels.id', '=', 'siswas.rombel_id')->where('siswas.jk', 'L');
            },
            'rombels as siswa_p' => function($query) {
                $query->join('siswas', 'rombels.id', '=', 'siswas.rombel_id')->where('siswas.jk', 'P');
            }
        ])
        ->when($this->search, function ($query) {
            $query->where('nama', 'like', '%'.$this->search.'%')
                  ->orWhere('kode', 'like', '%'.$this->search.'%');
        })
        ->paginate(10);

        return view('dashboarddata::livewire.jurusan.index', [
            'jurusans' => $jurusans,
        ]);
    }
}
