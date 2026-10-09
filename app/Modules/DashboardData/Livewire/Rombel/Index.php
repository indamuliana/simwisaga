<?php

namespace App\Modules\DashboardData\Livewire\Rombel;

use App\Core\Concerns\GuardsPermission;
use App\Core\Livewire\Traits\WithSorting;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Rombel')]
class Index extends Component
{
    use GuardsPermission, WithPagination, WithSorting;

    public string $search = '';
    public ?int $filterTahunAjaran = null;

    public function mount()
    {
        $this->guard('dashboard_data.rombel.lihat');
        $this->sortField = 'nama';
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaran = $currentTa?->id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterTahunAjaran()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Rombel::with('jurusan', 'waliKelas', 'tahunAjaran')
            ->withCount([
                'siswas as total_siswa',
                'siswas as siswa_l' => function($query) {
                    $query->where('jk', 'L');
                },
                'siswas as siswa_p' => function($query) {
                    $query->where('jk', 'P');
                }
            ]);

        if ($this->filterTahunAjaran) {
            $query->where('tahun_ajaran_id', $this->filterTahunAjaran);
        }

        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('nama', 'like', '%'.$this->search.'%')
                    ->orWhereHas('jurusan', function($j) {
                        $j->where('nama', 'like', '%'.$this->search.'%');
                    });
            });
        });

        $rombels = $query->orderBy($this->sortField, $this->sortDirection)->paginate(15);
        $tahunAjarans = TahunAjaran::orderByDesc('tgl_mulai')->get();

        return view('dashboarddata::livewire.rombel.index', [
            'rombels' => $rombels,
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}
