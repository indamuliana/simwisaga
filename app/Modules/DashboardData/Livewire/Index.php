<?php

namespace App\Modules\DashboardData\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard Data')]
class Index extends Component
{
    use GuardsPermission;

    #[Url(as: 'tab')]
    public string $tab = 'overview';

    #[Url(as: 'tingkat')]
    public string $filterTingkat = '';

    #[Url(as: 'jurusan')]
    public string $filterJurusan = '';

    public function mount()
    {
        if (request()->filled('tab')) {
            $this->tab = request()->query('tab');
        }

        // Migrasi atau normalisasi parameter tab lama 'program' ke 'rayon'
        if ($this->tab === 'program') {
            $this->tab = 'rayon';
        }

        $allowedTabs = $this->getAllowedTabs();
        if (empty($allowedTabs)) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka Dashboard Data.');
        }

        if (!array_key_exists($this->tab, $allowedTabs)) {
            $this->tab = array_key_first($allowedTabs);
        }
    }

    public function setTab(string $tab): void
    {
        $allowedTabs = $this->getAllowedTabs();
        if (array_key_exists($tab, $allowedTabs)) {
            $this->tab = $tab;
        }
    }

    public function resetFilters(): void
    {
        $this->filterTingkat = '';
        $this->filterJurusan = '';
    }

    public function getAllowedTabs(): array
    {
        $user = auth()->user();
        $tabs = [];

        if (!$user) {
            return $tabs;
        }

        if ($user->can('dashboard_data.dashboard.lihat')) {
            $tabs['overview'] = [
                'label' => 'Ringkasan',
                'icon' => 'chart-bar',
                'badge' => null,
            ];
        }

        if ($user->can('dashboard_data.rayon.lihat') || $user->can('dashboard_data.program.lihat')) {
            $tabs['rayon'] = [
                'label' => 'Rayon',
                'icon' => 'map-pin',
                'badge' => Rayon::count(),
            ];
        }

        if ($user->can('dashboard_data.jurusan.lihat')) {
            $tabs['jurusan'] = [
                'label' => 'Jurusan',
                'icon' => 'building-office-2',
                'badge' => Jurusan::where('is_active', true)->count(),
            ];
        }

        if ($user->can('dashboard_data.rombel.lihat')) {
            $currentTa = AcademicContext::currentTahunAjaran();
            $rombelCount = $currentTa
                ? Rombel::where('tahun_ajaran_id', $currentTa->id)->count()
                : Rombel::count();

            $tabs['rombel'] = [
                'label' => 'Rombel',
                'icon' => 'user-group',
                'badge' => $rombelCount,
            ];
        }

        if ($user->can('dashboard_data.sekolah_asal.lihat')) {
            $tabs['sekolah-asal'] = [
                'label' => 'Sekolah Asal',
                'icon' => 'building-library',
                'badge' => null,
            ];
        }

        return $tabs;
    }

    public function render()
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $allowedTabs = $this->getAllowedTabs();

        // Data Ringkasan (On-demand: hanya di-query jika tab overview aktif)
        $overviewData = [
            'totalSiswaAktif' => 0,
            'totalJurusan' => 0,
            'totalRombel' => 0,
            'totalProgram' => 0,
            'totalRayon' => 0,
            'totalSekolahAsal' => 0,
            'jurusanStats' => collect(),
            'matrix' => [],
            'grandTotal' => [
                'programs' => [],
                'total_l' => 0,
                'total_p' => 0,
                'total_all' => 0,
            ],
            'programs' => collect(),
            'jurusans' => collect(),
            'rasioL' => 0,
            'rasioP' => 0,
            'filteredRombelCount' => 0,
        ];

        if ($this->tab === 'overview' && isset($allowedTabs['overview'])) {
            $totalSiswaAktif = Siswa::where('status', 'aktif')->where('is_active', true)->count();
            $totalJurusan = Jurusan::where('is_active', true)->count();
            $totalRombel = $currentTa
                ? Rombel::where('tahun_ajaran_id', $currentTa->id)->count()
                : Rombel::count();
            $totalProgram = Program::where('is_active', true)->count();
            $totalRayon = Rayon::count();
            $totalSekolahAsal = Siswa::select('sekolah_asal')
                ->whereNotNull('sekolah_asal')
                ->where('sekolah_asal', '!=', '')
                ->distinct()
                ->count();

            // Daftar Program Aktif untuk Kolom Matriks
            $programs = Program::where('is_active', true)->orderBy('id')->get();

            // Daftar Jurusan Aktif untuk Filter
            $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get();

            // Query Rombel untuk Matriks (Sesuai Tahun Ajaran Aktif dan Filter)
            $rombelQuery = Rombel::query()->with('jurusan');
            if ($currentTa) {
                $rombelQuery->where('tahun_ajaran_id', $currentTa->id);
            }
            if ($this->filterTingkat !== '') {
                $rombelQuery->where('tingkat', $this->filterTingkat);
            }
            if ($this->filterJurusan !== '') {
                $rombelQuery->where('jurusan_id', $this->filterJurusan);
            }
            $rombels = $rombelQuery->orderBy('tingkat')->orderBy('nama')->get();

            // Agregasi Siswa Berdasarkan Rombel, Program, dan Jenis Kelamin (1 query efisien)
            $siswaCounts = Siswa::query()
                ->selectRaw('siswas.rombel_id, siswas.program_id, siswas.jk, count(*) as total')
                ->where('siswas.status', 'aktif')
                ->where('siswas.is_active', true)
                ->whereNotNull('siswas.rombel_id')
                ->whereIn('siswas.rombel_id', $rombels->pluck('id'))
                ->groupBy('siswas.rombel_id', 'siswas.program_id', 'siswas.jk')
                ->get()
                ->groupBy('rombel_id');

            // Susun Matriks Demografi Siswa
            $matrix = [];
            $grandTotal = [
                'programs' => [],
                'total_l' => 0,
                'total_p' => 0,
                'total_all' => 0,
            ];

            foreach ($programs as $p) {
                $grandTotal['programs'][$p->id] = ['l' => 0, 'p' => 0, 'subtotal' => 0];
            }

            foreach ($rombels->groupBy('tingkat') as $tingkatKey => $rombelGroup) {
                $tingkatSubtotal = [
                    'programs' => [],
                    'total_l' => 0,
                    'total_p' => 0,
                    'total_all' => 0,
                ];

                foreach ($programs as $p) {
                    $tingkatSubtotal['programs'][$p->id] = ['l' => 0, 'p' => 0, 'subtotal' => 0];
                }

                $rombelRows = [];
                foreach ($rombelGroup as $rombel) {
                    $counts = $siswaCounts->get($rombel->id, collect());

                    $row = [
                        'id' => $rombel->id,
                        'nama' => $rombel->nama,
                        'tingkat' => $rombel->tingkat,
                        'jurusan_id' => $rombel->jurusan_id,
                        'jurusan_kode' => $rombel->jurusan?->kode ?? '-',
                        'jurusan_nama' => $rombel->jurusan?->nama ?? '-',
                        'programs' => [],
                        'total_l' => 0,
                        'total_p' => 0,
                        'total_all' => 0,
                    ];

                    foreach ($programs as $p) {
                        $l = (int) $counts->where('program_id', $p->id)->where('jk', 'L')->sum('total');
                        $p_count = (int) $counts->where('program_id', $p->id)->where('jk', 'P')->sum('total');
                        $sub = $l + $p_count;

                        $row['programs'][$p->id] = [
                            'l' => $l,
                            'p' => $p_count,
                            'subtotal' => $sub,
                        ];

                        $tingkatSubtotal['programs'][$p->id]['l'] += $l;
                        $tingkatSubtotal['programs'][$p->id]['p'] += $p_count;
                        $tingkatSubtotal['programs'][$p->id]['subtotal'] += $sub;

                        $grandTotal['programs'][$p->id]['l'] += $l;
                        $grandTotal['programs'][$p->id]['p'] += $p_count;
                        $grandTotal['programs'][$p->id]['subtotal'] += $sub;
                    }

                    $rombelL = (int) $counts->where('jk', 'L')->sum('total');
                    $rombelP = (int) $counts->where('jk', 'P')->sum('total');
                    $rombelAll = $rombelL + $rombelP;

                    $row['total_l'] = $rombelL;
                    $row['total_p'] = $rombelP;
                    $row['total_all'] = $rombelAll;

                    $tingkatSubtotal['total_l'] += $rombelL;
                    $tingkatSubtotal['total_p'] += $rombelP;
                    $tingkatSubtotal['total_all'] += $rombelAll;

                    $grandTotal['total_l'] += $rombelL;
                    $grandTotal['total_p'] += $rombelP;
                    $grandTotal['total_all'] += $rombelAll;

                    $rombelRows[] = $row;
                }

                $matrix[$tingkatKey] = [
                    'rows' => $rombelRows,
                    'subtotal' => $tingkatSubtotal,
                ];
            }

            $rasioL = $grandTotal['total_all'] > 0
                ? round(($grandTotal['total_l'] / $grandTotal['total_all']) * 100, 1)
                : 0;
            $rasioP = $grandTotal['total_all'] > 0
                ? round(($grandTotal['total_p'] / $grandTotal['total_all']) * 100, 1)
                : 0;

            // Jurusan stats (untuk referensi/kompatibilitas)
            $jurusanStats = Jurusan::withCount([
                'rombels as total_siswa' => function($query) use ($currentTa) {
                    if ($currentTa) {
                        $query->where('tahun_ajaran_id', $currentTa->id);
                    }
                    $query->join('siswas', 'rombels.id', '=', 'siswas.rombel_id')
                          ->where('siswas.is_active', true)
                          ->where('siswas.status', 'aktif');
                },
                'rombels as siswa_l' => function($query) use ($currentTa) {
                    if ($currentTa) {
                        $query->where('tahun_ajaran_id', $currentTa->id);
                    }
                    $query->join('siswas', 'rombels.id', '=', 'siswas.rombel_id')
                          ->where('siswas.is_active', true)
                          ->where('siswas.status', 'aktif')
                          ->where('siswas.jk', 'L');
                },
                'rombels as siswa_p' => function($query) use ($currentTa) {
                    if ($currentTa) {
                        $query->where('tahun_ajaran_id', $currentTa->id);
                    }
                    $query->join('siswas', 'rombels.id', '=', 'siswas.rombel_id')
                          ->where('siswas.is_active', true)
                          ->where('siswas.status', 'aktif')
                          ->where('siswas.jk', 'P');
                }
            ])->get();

            $overviewData = [
                'totalSiswaAktif' => $totalSiswaAktif,
                'totalJurusan' => $totalJurusan,
                'totalRombel' => $totalRombel,
                'totalProgram' => $totalProgram,
                'totalRayon' => $totalRayon,
                'totalSekolahAsal' => $totalSekolahAsal,
                'jurusanStats' => $jurusanStats,
                'matrix' => $matrix,
                'grandTotal' => $grandTotal,
                'programs' => $programs,
                'jurusans' => $jurusans,
                'rasioL' => $rasioL,
                'rasioP' => $rasioP,
                'filteredRombelCount' => $rombels->count(),
            ];
        }

        return view('dashboarddata::livewire.index', array_merge([
            'currentTa' => $currentTa,
            'allowedTabs' => $allowedTabs,
            'activeTab' => $this->tab,
        ], $overviewData));
    }
}
