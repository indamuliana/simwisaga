<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('UPD & Seni Budaya')]
class UpdSeniSiswa extends Component
{
    use GuardsPermission;

    public function mount(): void
    {
        $this->guard('portal_siswa.upd_seni.lihat');
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

        if ($siswa) {
            $siswa->loadMissing([
                'seniBudayaAnggotas.seniBudaya.pembimbing',
                'seniBudayaAnggotas.semester.tahunAjaran',
                'ekstrakurikulerAnggotas.ekstrakurikuler.pembina',
                'ekstrakurikulerAnggotas.semester.tahunAjaran',
                'mapelPilihanAnggotas.mapelPilihan.guru',
                'mapelPilihanAnggotas.tahunAjaran',
            ]);
        }

        return view('portalsiswa::livewire.upd-seni-siswa', [
            'siswa' => $siswa,
        ]);
    }
}
