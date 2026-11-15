<?php

namespace App\Modules\DashboardData\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
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
#[Title('Detil Siswa')]
class StudentDetail extends Component
{
    use GuardsPermission;
    use WithPagination;

    public string $type = '';
    public string $identifier = '';
    public string $entityName = '';
    public string $search = '';

    public function mount($id = null, $name = null)
    {
        $routeName = request()->route()->getName();
        
        if (str_contains($routeName, 'rayon')) {
            $user = auth()->user();
            if (!($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
                if (!($user && ($user->can('dashboard_data.rayon.lihat') || $user->can('dashboard_data.program.lihat') || $user->can('dashboard_data.dashboard.lihat')))) {
                    $this->guard('dashboard_data.rayon.lihat');
                }
            }
            $this->type = 'rayon';
            $this->identifier = (string) $id;
            if ($id === 'tanpa-rayon' || $id === 'tanpa_rayon' || $id === '0') {
                $this->entityName = 'Rayon: (Tanpa Rayon)';
            } else {
                $rayon = Rayon::findOrFail($id);
                $this->entityName = 'Rayon: ' . $rayon->nama;
            }
        } elseif (str_contains($routeName, 'program')) {
            $this->guard('dashboard_data.program.lihat');
            $this->type = 'program';
            $this->identifier = (string) $id;
            $program = Program::findOrFail($id);
            $this->entityName = 'Program: ' . $program->nama;
        } elseif (str_contains($routeName, 'jurusan')) {
            $this->guard('dashboard_data.jurusan.lihat');
            $this->type = 'jurusan';
            $this->identifier = (string) $id;
            $jurusan = Jurusan::findOrFail($id);
            $this->entityName = 'Jurusan: ' . $jurusan->nama;
        } elseif (str_contains($routeName, 'rombel')) {
            $this->guard('dashboard_data.rombel.lihat');
            $this->type = 'rombel';
            $this->identifier = (string) $id;
            $rombel = Rombel::findOrFail($id);
            $this->entityName = 'Rombel: ' . $rombel->nama;
        } elseif (str_contains($routeName, 'sekolah-asal')) {
            $this->guard('dashboard_data.sekolah_asal.lihat');
            $this->type = 'sekolah-asal';
            $this->identifier = urldecode($name);
            $this->entityName = 'Sekolah Asal: ' . $this->identifier;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Siswa::with(['rombel.jurusan', 'program', 'rayon'])->where('status', 'aktif');

        if ($this->type === 'rayon') {
            if ($this->identifier === 'tanpa-rayon' || $this->identifier === 'tanpa_rayon' || $this->identifier === '0') {
                $query->whereNull('rayon_id');
            } else {
                $query->where('rayon_id', $this->identifier);
            }
        } elseif ($this->type === 'program') {
            $query->where('program_id', $this->identifier);
        } elseif ($this->type === 'jurusan') {
            $query->whereHas('rombel', function ($q) {
                $q->where('jurusan_id', $this->identifier);
            });
        } elseif ($this->type === 'rombel') {
            $query->where('rombel_id', $this->identifier);
        } elseif ($this->type === 'sekolah-asal') {
            $query->where('sekolah_asal', $this->identifier);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%')
                  ->orWhere('nipd', 'like', '%' . $this->search . '%');
            });
        }

        $siswas = $query->paginate(20);

        // Additional stats for Sekolah Asal
        $schoolStats = null;
        if ($this->type === 'sekolah-asal') {
            $baseSchool = Siswa::leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
                ->leftJoin('jurusans', 'rombels.jurusan_id', '=', 'jurusans.id')
                ->where('siswas.sekolah_asal', $this->identifier)
                ->where('siswas.is_active', true);

            $totalSiswa = (clone $baseSchool)->count();
            $totalL = (clone $baseSchool)->where('siswas.jk', 'L')->count();
            $totalP = (clone $baseSchool)->where('siswas.jk', 'P')->count();

            $jurusanCounts = (clone $baseSchool)
                ->select('jurusans.kode', DB::raw('count(*) as count'))
                ->whereNotNull('jurusans.kode')
                ->groupBy('jurusans.kode')
                ->pluck('count', 'jurusans.kode')
                ->toArray();

            $schoolStats = [
                'total' => $totalSiswa,
                'l' => $totalL,
                'p' => $totalP,
                'jurusans' => $jurusanCounts,
            ];
        }

        return view('dashboarddata::livewire.student-detail', [
            'siswas' => $siswas,
            'schoolStats' => $schoolStats,
            'entityName' => $this->entityName,
            'identifier' => $this->identifier,
            'type' => $this->type,
            'search' => $this->search,
        ]);
    }
}
