<?php

namespace App\Modules\DashboardData\Livewire\SekolahAsal;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Sekolah Asal')]
class Index extends Component
{
    use GuardsPermission;
    use WithPagination;

    public string $search = '';

    public function mount()
    {
        $this->guard('dashboard_data.sekolah_asal.lihat');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sekolahAsals = Siswa::select(
                'sekolah_asal', 
                DB::raw('count(*) as total_siswa'), 
                DB::raw('sum(case when jk = "L" then 1 else 0 end) as siswa_l'), 
                DB::raw('sum(case when jk = "P" then 1 else 0 end) as siswa_p')
            )
            ->whereNotNull('sekolah_asal')
            ->where('sekolah_asal', '!=', '')
            ->when($this->search, function ($query) {
                $query->where('sekolah_asal', 'like', '%'.$this->search.'%');
            })
            ->groupBy('sekolah_asal')
            ->orderBy('sekolah_asal')
            ->paginate(15);

        return view('dashboarddata::livewire.sekolah-asal.index', [
            'sekolahAsals' => $sekolahAsals,
        ]);
    }
}
