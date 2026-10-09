<?php

namespace Tests\Feature\Kepegawaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepegawaianModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_pegawai',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_data_pegawai_tampil(): void
    {
        Pegawai::create([
            'nama_lengkap' => 'Beni Iskandar',
            'gelar_belakang' => 'S.Kom.',
            'nip' => '198501012010011001',
            'jenis_gtk' => 'guru',
            'status_kepegawaian' => 'gty',
            'jenis_kelamin' => 'L',
        ]);

        $this->actingAs($this->admin)
            ->get(route('kepegawaian.pegawai'))
            ->assertOk()
            ->assertSee('Beni Iskandar')
            ->assertSee('S.Kom.');
    }

    public function test_halaman_penugasan_mengajar_tampil(): void
    {
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Rina Marlina',
            'gelar_belakang' => 'M.Pd.',
            'jenis_gtk' => 'guru',
            'status_kepegawaian' => 'gty',
            'jenis_kelamin' => 'P',
        ]);

        $sem = Semester::where('is_active', true)->first();
        $rombel = Rombel::first();
        $mapel = Mapel::first();

        PenugasanMengajar::create([
            'semester_id' => $sem->id,
            'pegawai_id' => $pegawai->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $mapel->id,
            'jp' => 4,
        ]);

        $this->actingAs($this->admin)
            ->get(route('kepegawaian.penugasan'))
            ->assertOk()
            ->assertSee('Rina Marlina')
            ->assertSee('4 JP');
    }

    public function test_pegawai_nama_gelar_berfungsi(): void
    {
        $p = new Pegawai([
            'nama_lengkap' => 'Asep Gunawan',
            'gelar_depan' => 'Drs.',
            'gelar_belakang' => 'M.M.',
        ]);

        $this->assertEquals('Drs. Asep Gunawan, M.M.', $p->nama_gelar);
    }
}
