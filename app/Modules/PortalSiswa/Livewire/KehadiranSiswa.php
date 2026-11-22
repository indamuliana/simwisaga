<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Rekap Kehadiran Siswa')]
class KehadiranSiswa extends Component
{
    use GuardsPermission, WithPagination;

    public string $filterTingkat = 'semua'; // 'semua', 'X', 'XI', 'XII'

    public string $filterSemester = 'semua'; // 'semua' atau semester_id

    public function mount(): void
    {
        $this->guard('portal_siswa.kehadiran.lihat');

        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->filterSemester = (string) $activeSemester->id;
        }
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

        $rekapBulanan = [];
        $riwayatPresensi = collect();

        if ($siswa) {
            $siswa->loadMissing(['rombel', 'riwayatRombel.rombel']);

            // Base query untuk presensi siswa
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

            // Ambil seluruh data hasil filter untuk statistik dan rekap bulanan
            $allFiltered = (clone $baseQuery)->get();

            $isHadir = fn ($item) => in_array($item->status instanceof StatusAbsensi ? $item->status->value : $item->status, ['Hadir', 'H']);
            $isSakit = fn ($item) => in_array($item->status instanceof StatusAbsensi ? $item->status->value : $item->status, ['Sakit', 'S']);
            $isIzin  = fn ($item) => in_array($item->status instanceof StatusAbsensi ? $item->status->value : $item->status, ['Izin', 'I']);
            $isAlpa  = fn ($item) => in_array($item->status instanceof StatusAbsensi ? $item->status->value : $item->status, ['Alpa', 'A']);

            $stats['H'] = $allFiltered->filter($isHadir)->count();
            $stats['S'] = $allFiltered->filter($isSakit)->count();
            $stats['I'] = $allFiltered->filter($isIzin)->count();
            $stats['A'] = $allFiltered->filter($isAlpa)->count();
            $stats['total'] = $stats['H'] + $stats['S'] + $stats['I'] + $stats['A'];
            $stats['persen'] = $stats['total'] > 0 ? round(($stats['H'] / $stats['total']) * 100, 1) : 100;

            // Susun Matriks Rekap Bulanan
            $groupedByMonth = $allFiltered->sortBy('tanggal')->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            });

            foreach ($groupedByMonth as $ym => $items) {
                $dt = Carbon::createFromFormat('Y-m', $ym);
                $h = $items->filter($isHadir)->count();
                $s = $items->filter($isSakit)->count();
                $i = $items->filter($isIzin)->count();
                $a = $items->filter($isAlpa)->count();
                $tot = $h + $s + $i + $a;
                $pct = $tot > 0 ? round(($h / $tot) * 100, 1) : 100;

                $rekapBulanan[] = [
                    'periode' => $dt->translatedFormat('F Y'),
                    'total' => $tot,
                    'H' => $h,
                    'S' => $s,
                    'I' => $i,
                    'A' => $a,
                    'persen' => $pct,
                ];
            }

            // Log presensi dengan pagination
            $riwayatPresensi = $baseQuery->with(['semester.tahunAjaran', 'rombel'])
                ->orderByDesc('tanggal')
                ->paginate(15);
        }

        return view('portalsiswa::livewire.kehadiran-siswa', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'stats' => $stats,
            'rekapBulanan' => $rekapBulanan,
            'riwayatPresensi' => $riwayatPresensi,
        ]);
    }
}
