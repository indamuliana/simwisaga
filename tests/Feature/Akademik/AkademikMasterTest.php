<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AkademikMasterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_tahun_ajaran_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.tahun-ajaran'))
            ->assertOk()
            ->assertSee('2024/2025')
            ->assertSee('Semester Ganjil');
    }

    public function test_halaman_jurusan_tampil_dengan_4_jurusan_resmi(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.jurusan'))
            ->assertOk()
            ->assertSee('PPLG')
            ->assertSee('TJKT')
            ->assertSee('PMN')
            ->assertSee('HTL');
    }

    public function test_halaman_rombel_tampil_dengan_format_standar(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.rombel'))
            ->assertOk()
            ->assertSee('X PPLG 1')
            ->assertSee('X TJKT 1');
    }

    public function test_rombel_nama_generator_bekerja_sesuai_format_resmi(): void
    {
        $nama = Rombel::generateNama('XI', 'PPLG', 2);
        $this->assertEquals('XI PPLG 2', $nama);
    }

    public function test_halaman_rayon_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.rayon'))
            ->assertOk()
            ->assertSee('Bales 1')
            ->assertSee('Tarkid 1');
    }

    public function test_halaman_mapel_tampil_dengan_4_kategori(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.mapel'))
            ->assertOk()
            ->assertSee('PAIBP')
            ->assertSee('DDK-PPLG')
            ->assertSee('PLH')
            ->assertSee('P5');
    }

    public function test_halaman_struktur_kurikulum_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.kurikulum'))
            ->assertOk()
            ->assertSee('Struktur Kurikulum');
    }

    public function test_academic_context_dapat_mengambil_semester_dan_ta_aktif(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->assertNotNull($currentTa);
        $this->assertEquals('2024/2025', $currentTa->nama);

        $currentSem = AcademicContext::currentSemester();
        $this->assertNotNull($currentSem);
        $this->assertEquals('ganjil', $currentSem->semester);
    }
}
