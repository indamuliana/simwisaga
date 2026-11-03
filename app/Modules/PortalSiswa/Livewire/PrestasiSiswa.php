<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Prestasi\Livewire\PrestasiMandiriSiswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Prestasi Siswa')]
class PrestasiSiswa extends PrestasiMandiriSiswa
{
    use GuardsPermission;

    public function mount()
    {
        $this->guard('portal_siswa.prestasi.lihat');
        parent::mount();
    }
}
