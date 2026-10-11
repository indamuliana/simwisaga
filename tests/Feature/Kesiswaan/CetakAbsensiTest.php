<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Kesiswaan\Livewire\CetakAbsensi;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CetakAbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole(User::SUPER_ADMIN_ROLE);
    }

    public function test_halaman_cetak_absensi_memiliki_default_20_kolom_tanggal_dan_dapat_diubah(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CetakAbsensi::class)
            ->assertSet('jumlahKolom', 20)
            ->set('jumlahKolom', 25)
            ->assertSet('jumlahKolom', 25)
            ->set('jumlahKolom', 15)
            ->assertSet('jumlahKolom', 15);
    }
}
