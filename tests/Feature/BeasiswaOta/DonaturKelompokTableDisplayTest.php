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

    public function test_donatur_table_renders_header_keterangan_and_data(): void
    {
        $this->actingAs($this->admin);

        $d = Donatur::create([
            'nama' => 'Bapak Irfan Fauzi',
            'tipe' => 'rutin',
            'nomor_telepon' => '081399887766',
            'keterangan' => 'Donatur Tetap Yayasan Peduli',
            'is_aktif' => true,
        ]);

        Livewire::test(DonaturKelompokIndex::class)
            ->set('activeTab', 'donatur')
            ->assertSee('Keterangan')
            ->assertSee('Donatur Tetap Yayasan Peduli')
            ->set('searchDonatur', 'Yayasan Peduli')
            ->assertSee('Bapak Irfan Fauzi')
            ->set('searchDonatur', 'TidakDitemukan123')
            ->assertDontSee('Bapak Irfan Fauzi');
    }

    public function test_anggota_modal_table_renders_keterangan(): void
    {
        $this->actingAs($this->admin);

        $kelompok = Kelompok::create([
            'kode_kelompok' => 'KEL-MODAL-TEST',
            'nama_kelompok' => 'Kelompok Modal Test',
            'target_nominal' => 1000000,
            'is_aktif' => true,
        ]);

        $d = Donatur::create([
            'nama' => 'Ibu Siti Aminah',
            'tipe' => 'rutin',
            'nomor_telepon' => '081233445566',
            'keterangan' => 'Wali Santri Angkatan 2024',
            'is_aktif' => true,
        ]);

        KelompokAnggota::create([
            'kelompok_id' => $kelompok->id,
            'donatur_id' => $d->id,
            'nominal_komitmen' => 150000,
        ]);

        Livewire::test(DonaturKelompokIndex::class)
            ->call('manageAnggota', $kelompok->id)
            ->assertSee('Keterangan')
            ->assertSee('Wali Santri Angkatan 2024');
    }
}
