<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Livewire\SiswaDetail;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiswaDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_detail_siswa',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $rombel = Rombel::first();
        $rayon = Rayon::first();
        $program = Program::first();

        $this->siswa = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '12345678',
            'nisn' => '0055123456',
            'nik' => '3205011234567890',
            'jk' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-05-10',
            'agama' => 'Islam',
            'alamat' => 'Jl. Merdeka No. 45',
            'rombel_id' => $rombel?->id,
            'rayon_id' => $rayon?->id,
            'program_id' => $program?->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    public function test_halaman_detail_siswa_dapat_diakses_oleh_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.siswa.show', $this->siswa->id))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('12345678')
            ->assertSee('Biodata & Orang Tua');
    }

    public function test_navigasi_tab_detail_siswa_berfungsi(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SiswaDetail::class, ['id' => $this->siswa->id])
            ->assertSet('activeTab', 'biodata')
            ->assertSee('Identitas Diri & Kependudukan')
            ->call('setTab', 'pilihan')
            ->assertSet('activeTab', 'pilihan')
            ->assertSee('Seni Budaya yang Dipilih')
            ->assertSee('Ekstrakurikuler & Unit Pengembangan Diri (UPD)')
            ->call('setTab', 'prestasi')
            ->assertSet('activeTab', 'prestasi')
            ->assertSee('Daftar Prestasi & Penghargaan Siswa')
            ->assertSee('Beasiswa Orang Tua Asuh (OTA)')
            ->call('setTab', 'riwayat')
            ->assertSet('activeTab', 'riwayat')
            ->assertSee('Statistik Kehadiran Harian Siswa')
            ->assertSee('Riwayat Perjalanan Kelas (Rombel)');
    }

    public function test_user_tanpa_hak_akses_ditolak(): void
    {
        $regularUser = User::factory()->create([
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($regularUser)
            ->get(route('kesiswaan.siswa.show', $this->siswa->id))
            ->assertForbidden();
    }
}
