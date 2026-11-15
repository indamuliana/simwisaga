<?php

namespace App\Modules\DashboardData\Livewire\Jurusan;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Data Jurusan')]
class Index extends Component
{
    use GuardsPermission;

    public string $search = '';
    public array $expanded = [];
    public bool $expandAll = false;

    public function mount()
    {
        $this->guard('dashboard_data.jurusan.lihat');
    }

    public function toggleExpand(int $jurusanId): void
    {
        if (in_array($jurusanId, $this->expanded, true)) {
            $this->expanded = array_values(array_diff($this->expanded, [$jurusanId]));
            $this->expandAll = false;
        } else {
            $this->expanded[] = $jurusanId;
        }
    }

    public function toggleExpandAll(): void
    {
        if ($this->expandAll) {
            $this->expanded = [];
            $this->expandAll = false;
        } else {
            $this->expanded = Jurusan::where('is_active', true)->pluck('id')->toArray();
            $this->expandAll = true;
        }
    }

    public function render()
    {
        $currentTa = AcademicContext::currentTahunAjaran();

        // 1. Program aktif untuk kolom matriks
        $programs = Program::where('is_active', true)->orderBy('id')->get();

        // 2. Jurusan aktif (filter search jika ada)
        $jurusans = Jurusan::where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('nama')
            ->get();

        // 3. Query Rombel per Jurusan (sesuai TA aktif)
        $rombelQuery = Rombel::query()->whereIn('jurusan_id', $jurusans->pluck('id'));
        if ($currentTa) {
            $rombelQuery->where('tahun_ajaran_id', $currentTa->id);
        }
        $rombels = $rombelQuery->orderBy('tingkat')->orderBy('nama')->get();

        // 4. Query Agregasi Siswa Berdasarkan Rombel, Program, dan JK (1 query efisien)
        $siswaCounts = Siswa::query()
            ->selectRaw('siswas.rombel_id, siswas.program_id, siswas.jk, count(*) as total')
            ->where('siswas.status', 'aktif')
            ->where('siswas.is_active', true)
            ->whereIn('siswas.rombel_id', $rombels->pluck('id'))
            ->groupBy('siswas.rombel_id', 'siswas.program_id', 'siswas.jk')
            ->get();

        // 5. Grand Total Inisialisasi
        $grandTotal = [
            'rombel_count' => 0,
            'programs' => [],
            'tingkat' => ['X' => 0, 'XI' => 0, 'XII' => 0],
            'total_l' => 0,
            'total_p' => 0,
            'total_all' => 0,
        ];

        foreach ($programs as $p) {
            $grandTotal['programs'][$p->id] = ['l' => 0, 'p' => 0, 'subtotal' => 0];
        }

        // 6. Susun Data Per Jurusan dan Rombel
        $jurusanRows = [];
        foreach ($jurusans as $j) {
            $jRombels = $rombels->where('jurusan_id', $j->id);
            $jRombelIds = $jRombels->pluck('id');
            $jCounts = $siswaCounts->whereIn('rombel_id', $jRombelIds);

            // Program breakdown
            $progData = [];
            foreach ($programs as $p) {
                $l = (int) $jCounts->where('program_id', $p->id)->where('jk', 'L')->sum('total');
                $p_count = (int) $jCounts->where('program_id', $p->id)->where('jk', 'P')->sum('total');
                $sub = $l + $p_count;

                $progData[$p->id] = ['l' => $l, 'p' => $p_count, 'subtotal' => $sub];

                $grandTotal['programs'][$p->id]['l'] += $l;
                $grandTotal['programs'][$p->id]['p'] += $p_count;
                $grandTotal['programs'][$p->id]['subtotal'] += $sub;
            }

            // Tingkat breakdown (X, XI, XII)
            $tingkatCounts = ['X' => 0, 'XI' => 0, 'XII' => 0];
            foreach (['X', 'XI', 'XII'] as $t) {
                $tRombelIds = $jRombels->where('tingkat', $t)->pluck('id');
                $tTotal = (int) $jCounts->whereIn('rombel_id', $tRombelIds)->sum('total');
                $tingkatCounts[$t] = $tTotal;
                $grandTotal['tingkat'][$t] += $tTotal;
            }

            // Total JK
            $totL = (int) $jCounts->where('jk', 'L')->sum('total');
            $totP = (int) $jCounts->where('jk', 'P')->sum('total');
            $totAll = $totL + $totP;

            $grandTotal['rombel_count'] += $jRombels->count();
            $grandTotal['total_l'] += $totL;
            $grandTotal['total_p'] += $totP;
            $grandTotal['total_all'] += $totAll;

            // Rombel sub-rows (untuk expand/collapse drill-down)
            $rombelRows = [];
            foreach ($jRombels as $r) {
                $rCounts = $jCounts->where('rombel_id', $r->id);
                $rProg = [];
                foreach ($programs as $p) {
                    $rl = (int) $rCounts->where('program_id', $p->id)->where('jk', 'L')->sum('total');
                    $rp = (int) $rCounts->where('program_id', $p->id)->where('jk', 'P')->sum('total');
                    $rProg[$p->id] = ['l' => $rl, 'p' => $rp, 'subtotal' => $rl + $rp];
                }
                $rlTotal = (int) $rCounts->where('jk', 'L')->sum('total');
                $rpTotal = (int) $rCounts->where('jk', 'P')->sum('total');

                $rombelRows[] = [
                    'id' => $r->id,
                    'nama' => $r->nama,
                    'tingkat' => $r->tingkat,
                    'programs' => $rProg,
                    'total_l' => $rlTotal,
                    'total_p' => $rpTotal,
                    'total_all' => $rlTotal + $rpTotal,
                ];
            }

            $jurusanRows[] = [
                'id' => $j->id,
                'kode' => $j->kode,
                'nama' => $j->nama,
                'rombel_count' => $jRombels->count(),
                'programs' => $progData,
                'tingkat' => $tingkatCounts,
                'total_l' => $totL,
                'total_p' => $totP,
                'total_all' => $totAll,
                'rombels' => $rombelRows,
            ];
        }

        $rasioL = $grandTotal['total_all'] > 0
            ? round(($grandTotal['total_l'] / $grandTotal['total_all']) * 100, 1)
            : 0;
        $rasioP = $grandTotal['total_all'] > 0
            ? round(($grandTotal['total_p'] / $grandTotal['total_all']) * 100, 1)
            : 0;

        return view('dashboarddata::livewire.jurusan.index', [
            'jurusanRows' => $jurusanRows,
            'programs' => $programs,
            'grandTotal' => $grandTotal,
            'rasioL' => $rasioL,
            'rasioP' => $rasioP,
            'currentTa' => $currentTa,
        ]);
    }
}
