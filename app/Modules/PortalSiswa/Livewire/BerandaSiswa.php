<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\KalenderPendidikan\Models\KalenderAgenda;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\PortalSiswa\Models\PengajuanBeasiswa;
use App\Modules\PortalSiswa\Models\PengajuanPerbaikanDataSiswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Beranda Siswa')]
class BerandaSiswa extends Component
{
    use GuardsPermission;

    public function mount(): void
    {
        $this->guard('portal_siswa.agenda.lihat');
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

    public function render()
    {
        $siswa = $this->getSiswa();

        // Agenda Kegiatan Publik Terdekat
        $agendas = KalenderAgenda::with('kategori')
            ->where('status', KalenderAgenda::STATUS_PUBLIK)
            ->where('tgl_selesai', '>=', now()->toDateString())
            ->orderBy('tgl_mulai')
            ->orderBy('jam_mulai')
            ->take(6)
            ->get();

        // Statistik Presensi
        $absensiStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'persen' => 100];
        $pendingPerbaikan = 0;
        $pendingBeasiswa = 0;

        if ($siswa) {
            $siswa->loadMissing(['rombel.waliKelas', 'rayon.pembimbing', 'program']);

            $absensiStats['H'] = AbsensiHarian::where('siswa_id', $siswa->id)->where('status', 'H')->count();
            $absensiStats['S'] = AbsensiHarian::where('siswa_id', $siswa->id)->where('status', 'S')->count();
            $absensiStats['I'] = AbsensiHarian::where('siswa_id', $siswa->id)->where('status', 'I')->count();
            $absensiStats['A'] = AbsensiHarian::where('siswa_id', $siswa->id)->where('status', 'A')->count();
            $total = $absensiStats['H'] + $absensiStats['S'] + $absensiStats['I'] + $absensiStats['A'];
            $absensiStats['persen'] = $total > 0 ? round(($absensiStats['H'] / $total) * 100, 1) : 100;

            $pendingPerbaikan = PengajuanPerbaikanDataSiswa::where('siswa_id', $siswa->id)
                ->where('status', PengajuanPerbaikanDataSiswa::STATUS_MENUNGGU)
                ->count();

            $pendingBeasiswa = PengajuanBeasiswa::where('siswa_id', $siswa->id)
                ->where('status', PengajuanBeasiswa::STATUS_MENUNGGU)
                ->count();
        }

        return view('portalsiswa::livewire.beranda-siswa', [
            'siswa' => $siswa,
            'agendas' => $agendas,
            'absensiStats' => $absensiStats,
            'pendingPerbaikan' => $pendingPerbaikan,
            'pendingBeasiswa' => $pendingBeasiswa,
        ]);
    }
}
