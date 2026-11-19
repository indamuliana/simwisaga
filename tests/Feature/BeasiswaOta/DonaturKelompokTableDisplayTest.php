<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\BeasiswaOta\Livewire\DonaturKelompokIndex;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KelompokAnggota;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DonaturKelompokTableDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_test_display',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_table_renders_target_and_commitment_monthly_and_semester(): void
    {
        $this->actingAs($this->admin);

        // Buat kelompok dengan target semester Rp 900.000 (Rp 150.000/bln)
        $kelompok = Kelompok::create([
            'kode_kelompok' => 'KEL-DISP',
            'nama_kelompok' => 'Kelompok Uji Display',
            'target_nominal' => 900000,
            'is_aktif' => true,
        ]);

        // Buat 2 donatur anggota @ Rp 100.000/bln -> Total komitmen Rp 200.000/bln (Rp 1.200.000/smt)
        $d1 = Donatur::create(['nama' => 'Donatur 1', 'tipe' => 'rutin', 'is_aktif' => true]);
        $d2 = Donatur::create(['nama' => 'Donatur 2', 'tipe' => 'rutin', 'is_aktif' => true]);

        KelompokAnggota::create(['kelompok_id' => $kelompok->id, 'donatur_id' => $d1->id, 'nominal_komitmen' => 100000]);
        KelompokAnggota::create(['kelompok_id' => $kelompok->id, 'donatur_id' => $d2->id, 'nominal_komitmen' => 100000]);

        Livewire::test(DonaturKelompokIndex::class)
            // Target per bulan Rp 150.000 dan semester Rp 900.000
            ->assertSee('Rp 150.000')
            ->assertSee('/bln')
            ->assertSee('Semester: Rp 900.000')
            // Komitmen per bulan Rp 200.000 dan semester Rp 1.200.000
            ->assertSee('Rp 200.000')
            ->assertSee('Semester: Rp 1.200.000')
            // Surplus Rp 50.000/bln (+Rp 300.000/smt)
            ->assertSee('+Rp 50.000/bln')
            ->assertSee('+Rp 300.000/smt')
            ->assertSee('Terpenuhi');
    }
}
