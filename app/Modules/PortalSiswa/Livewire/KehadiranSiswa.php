<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Kehadiran Siswa')]
class KehadiranSiswa extends Component
{
    use GuardsPermission, WithPagination;

    public string $filterTingkat = 'semua'; // 'semua', 'X', 'XI', 'XII'

    public string $filterSemester = 'semua'; // 'semua' atau semester_id

    public function mount(): void
    {
        $this->guard('portal_siswa.kehadiran.lihat');
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->siswa) {
            return $user->siswa;
        }

        return Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nipd', $user->username)
            ->first();
    }

    public function updatingFilterTingkat(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSemester(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $siswa = $this->getSiswa();

        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();

        $stats = [
            'total' => 0,
            'H' => 0,
            'S' => 0,
            'I' => 0,
            'A' => 0,
            'persen' => 100,
        ];

        $riwayatPresensi = collect();

        if ($siswa) {
            // Build base query
            $baseQuery = AbsensiHarian::where('siswa_id', $siswa->id);

            // Filter Semester jika dipilih
            if ($this->filterSemester !== 'semua') {
                $baseQuery->where('semester_id', (int) $this->filterSemester);
            }

            // Filter Tingkat jika dipilih
            if ($this->filterTingkat !== 'semua') {
                $baseQuery->whereHas('rombel', function ($q) {
                    $q->where('tingkat', $this->filterTingkat);
                });
            }

            // Hitung statistik berdasarkan filter
            $stats['H'] = (clone $baseQuery)->where('status', 'H')->count();
            $stats['S'] = (clone $baseQuery)->where('status', 'S')->count();
            $stats['I'] = (clone $baseQuery)->where('status', 'I')->count();
            $stats['A'] = (clone $baseQuery)->where('status', 'A')->count();
            $stats['total'] = $stats['H'] + $stats['S'] + $stats['I'] + $stats['A'];
            $stats['persen'] = $stats['total'] > 0 ? round(($stats['H'] / $stats['total']) * 100, 1) : 100;

            // Log presensi dengan pagination
            $riwayatPresensi = $baseQuery->with('semester')
                ->orderByDesc('tanggal')
                ->paginate(15);
        }

        return view('portalsiswa::livewire.kehadiran-siswa', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'stats' => $stats,
            'riwayatPresensi' => $riwayatPresensi,
        ]);
    }
}
