<?php

namespace App\Modules\DashboardData\Livewire;

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
#[Title('Dashboard Data')]
class Index extends Component
{
    use GuardsPermission;

    public function mount()
    {
        $this->guard('dashboard_data.dashboard.lihat');
    }

    public function render()
    {
        $currentTa = AcademicContext::currentTahunAjaran();

        $totalSiswaAktif = Siswa::where('status', 'aktif')->where('is_active', true)->count();
        $totalJurusan = Jurusan::where('is_active', true)->count();
        $totalRombel = $currentTa
            ? Rombel::where('tahun_ajaran_id', $currentTa->id)->count()
            : Rombel::count();
        $totalProgram = Program::where('is_active', true)->count();
        $totalSekolahAsal = Siswa::select('sekolah_asal')->whereNotNull('sekolah_asal')->where('sekolah_asal', '!=', '')->distinct()->count();

        // Rekap Jurusan untuk Tahun Ajaran Aktif
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

        return view('dashboarddata::livewire.index', [
            'totalSiswaAktif' => $totalSiswaAktif,
            'totalJurusan' => $totalJurusan,
            'totalRombel' => $totalRombel,
            'totalProgram' => $totalProgram,
            'totalSekolahAsal' => $totalSekolahAsal,
            'jurusanStats' => $jurusanStats,
            'currentTa' => $currentTa,
        ]);
    }
}
