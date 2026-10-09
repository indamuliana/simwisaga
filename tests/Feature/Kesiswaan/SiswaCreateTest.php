<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Livewire\SiswaCreate;
use App\Modules\Kesiswaan\Models\Program;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiswaCreateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kesiswaan',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_tambah_siswa_dapat_diakses_oleh_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.siswa.create'))
            ->assertOk()
            ->assertSee('Tambah Siswa Baru');
    }

    public function test_admin_dapat_menyimpan_siswa_baru_dan_akun_user_terbuat(): void
    {
        $rombel = Rombel::first();
        $rayon = Rayon::first();
        $program = Program::where('kode', 'REG')->first();

        Livewire::actingAs($this->admin)
            ->test(SiswaCreate::class)
            ->set('nama', 'Muhammad Fajar')
            ->set('nipd', '12209999')
            ->set('nisn', '0081234567')
            ->set('jk', 'L')
            ->set('tempat_lahir', 'Garut')
            ->set('tanggal_lahir', '2008-05-15')
            ->set('rombel_id', $rombel?->id)
            ->set('rayon_id', $rayon?->id)
            ->set('program_id', $program?->id)
            ->set('email', 'fajar@gmail.com')
            ->set('ayah_nama', 'Asep Rohmat')
            ->set('ibu_nama', 'Siti Maryam')
            ->call('save', true)
            ->assertHasNoErrors()
            ->assertRedirect(route('kesiswaan.siswa'));

        // Cek database siswa
        $this->assertDatabaseHas('siswas', [
            'nama' => 'Muhammad Fajar',
            'nipd' => '12209999',
            'nisn' => '0081234567',
        ]);

        // Cek akun login siswa otomatis
        $this->assertDatabaseHas('users', [
            'username' => '12209999',
            'name' => 'Muhammad Fajar',
        ]);

        // Cek akun login ortu otomatis
        $this->assertDatabaseHas('users', [
            'username' => 'ortu_12209999',
        ]);

        // Cek data orang tua
        $this->assertDatabaseHas('siswa_orang_tuas', [
            'nama' => 'Asep Rohmat',
            'hubungan' => 'ayah',
        ]);
        $this->assertDatabaseHas('siswa_orang_tuas', [
            'nama' => 'Siti Maryam',
            'hubungan' => 'ibu',
        ]);
    }

    public function test_simpan_dan_tambah_lagi_mereset_formulir(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SiswaCreate::class)
            ->set('nama', 'Siswa Satu')
            ->set('nisn', '0089999001')
            ->set('jk', 'P')
            ->call('save', false)
            ->assertHasNoErrors()
            ->assertSet('nama', '')
            ->assertSet('nisn', '');

        $this->assertDatabaseHas('siswas', [
            'nama' => 'Siswa Satu',
            'nisn' => '0089999001',
        ]);
    }
}
