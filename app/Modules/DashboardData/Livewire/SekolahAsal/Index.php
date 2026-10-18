<?php

namespace App\Modules\DashboardData\Livewire\SekolahAsal;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
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
    public string $filterTingkat = '';
    public string $filterJurusan = '';
    public string $sortBy = 'total_desc';
    public bool $showTopModal = false;

    public function mount()
    {
        $this->guard('dashboard_data.sekolah_asal.lihat');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTingkat()
    {
        $this->resetPage();
    }

    public function updatingFilterJurusan()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterTingkat', 'filterJurusan', 'sortBy']);
        $this->resetPage();
    }

    public function openTopModal()
    {
        $this->showTopModal = true;
    }

    public function closeTopModal()
    {
        $this->showTopModal = false;
    }

    public function render()
    {
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get();

        $selects = [
            'siswas.sekolah_asal',
            DB::raw('count(*) as total_siswa'),
            DB::raw('sum(case when siswas.jk = "L" then 1 else 0 end) as siswa_l'),
            DB::raw('sum(case when siswas.jk = "P" then 1 else 0 end) as siswa_p'),
        ];

        foreach ($jurusans as $j) {
            $selects[] = DB::raw("sum(case when jurusans.id = {$j->id} then 1 else 0 end) as jurusan_{$j->id}");
        }

        $baseQuery = Siswa::leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
            ->leftJoin('jurusans', 'rombels.jurusan_id', '=', 'jurusans.id')
            ->whereNotNull('siswas.sekolah_asal')
            ->where('siswas.sekolah_asal', '!=', '')
            ->where('siswas.is_active', true);

        if ($this->search) {
            $baseQuery->where('siswas.sekolah_asal', 'like', '%' . trim($this->search) . '%');
        }

        if ($this->filterTingkat) {
            $baseQuery->where('rombels.tingkat', $this->filterTingkat);
        }

        if ($this->filterJurusan) {
            $baseQuery->where('rombels.jurusan_id', $this->filterJurusan);
        }

        // Summary metrics
        $totalSiswa = (clone $baseQuery)->count();
        $totalSekolah = (clone $baseQuery)->distinct('siswas.sekolah_asal')->count('siswas.sekolah_asal');
        $avgPerSekolah = $totalSekolah > 0 ? round($totalSiswa / $totalSekolah, 1) : 0;
        $topSekolah = (clone $baseQuery)
            ->select('siswas.sekolah_asal', DB::raw('count(*) as c'))
            ->groupBy('siswas.sekolah_asal')
            ->orderByDesc('c')
            ->first();

        // Table Query
        $tableQuery = (clone $baseQuery)->select($selects)->groupBy('siswas.sekolah_asal');

        switch ($this->sortBy) {
            case 'total_asc':
                $tableQuery->orderBy('total_siswa', 'asc')->orderBy('siswas.sekolah_asal', 'asc');
                break;
            case 'name_asc':
                $tableQuery->orderBy('siswas.sekolah_asal', 'asc');
                break;
            case 'name_desc':
                $tableQuery->orderBy('siswas.sekolah_asal', 'desc');
                break;
            case 'total_desc':
            default:
                $tableQuery->orderByDesc('total_siswa')->orderBy('siswas.sekolah_asal', 'asc');
                break;
        }

        $sekolahAsals = $tableQuery->paginate(15);

        // Top 10 Leaderboard
        $top10List = (clone $baseQuery)
            ->select($selects)
            ->groupBy('siswas.sekolah_asal')
            ->orderByDesc('total_siswa')
            ->limit(10)
            ->get()
            ->map(function ($item, $idx) use ($totalSiswa, $jurusans) {
                $dominantName = '-';
                $dominantCount = 0;
                foreach ($jurusans as $j) {
                    $count = (int) ($item->{'jurusan_' . $j->id} ?? 0);
                    if ($count > $dominantCount) {
                        $dominantCount = $count;
                        $dominantName = $j->kode;
                    }
                }

                $pct = $totalSiswa > 0 ? round(($item->total_siswa / $totalSiswa) * 100, 1) : 0;

                return [
                    'rank' => $idx + 1,
                    'nama' => $item->sekolah_asal,
                    'total' => $item->total_siswa,
                    'siswa_l' => $item->siswa_l,
                    'siswa_p' => $item->siswa_p,
                    'percentage' => $pct,
                    'dominant' => $dominantName !== '-' ? "{$dominantName} ({$dominantCount})" : '-',
                ];
            });

        return view('dashboarddata::livewire.sekolah-asal.index', [
            'sekolahAsals' => $sekolahAsals,
            'jurusans' => $jurusans,
            'totalSiswa' => $totalSiswa,
            'totalSekolah' => $totalSekolah,
            'avgPerSekolah' => $avgPerSekolah,
            'topSekolah' => $topSekolah,
            'top10List' => $top10List,
            'search' => $this->search,
            'filterTingkat' => $this->filterTingkat,
            'filterJurusan' => $this->filterJurusan,
            'sortBy' => $this->sortBy,
            'showTopModal' => $this->showTopModal,
        ]);
    }
}
