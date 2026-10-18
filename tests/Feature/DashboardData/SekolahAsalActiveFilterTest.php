<?php

namespace Tests\Feature\DashboardData;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\DashboardData\Livewire\SekolahAsal\Index as SekolahAsalIndex;
use App\Modules\DashboardData\Livewire\StudentDetail;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SekolahAsalActiveFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->rombel = Rombel::firstOrFail();
    }

    public function test_sekolah_asal_hanya_menghitung_siswa_aktif(): void
    {
        // Siswa aktif dari SMPN 1
        Siswa::create([
            'nama' => 'Siswa Aktif',
            'nipd' => '12200001',
            'nisn' => '0011223344',
            'jk' => 'L',
            'sekolah_asal' => 'SMPN 1 Garut',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        // Siswa non-aktif/keluar dari SMPN 1
        Siswa::create([
            'nama' => 'Siswa Nonaktif',
            'nipd' => '12200002',
            'nisn' => '0011223345',
            'jk' => 'L',
            'sekolah_asal' => 'SMPN 1 Garut',
            'rombel_id' => $this->rombel->id,
            'status' => 'keluar',
            'is_active' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SekolahAsalIndex::class)
            ->assertViewHas('totalSiswa', 1)
            ->assertSee('SMPN 1 Garut');

        Livewire::actingAs($this->admin)
            ->test(StudentDetail::class, ['name' => urlencode('SMPN 1 Garut')])
            ->assertSee('Siswa Aktif')
            ->assertDontSee('Siswa Nonaktif');
    }
}
