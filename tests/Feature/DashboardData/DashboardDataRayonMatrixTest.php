<?php

namespace Tests\Feature\DashboardData;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\DashboardData\Livewire\Rayon\Index as RayonIndex;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardDataRayonMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rayon $rayonA;
    protected Rayon $rayonB;
    protected Jurusan $jurusanHtl;
    protected Jurusan $jurusanPplg;
    protected Rombel $rombelHtl;
    protected Rombel $rombelPplg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_rayon_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $pembimbingUser = User::factory()->create([
            'name' => 'Pak Pembimbing Hebat',
            'username' => 'pembimbing_hebat',
        ]);

        $this->rayonA = Rayon::create([
            'nama' => 'Rayon Cibatu 1',
            'pembimbing_id' => $pembimbingUser->id,
        ]);

        $this->rayonB = Rayon::create([
            'nama' => 'Rayon Tarogong 1',
            'pembimbing_id' => null,
        ]);

        $this->jurusanHtl = Jurusan::where('kode', 'HTL')->firstOrFail();
        $this->jurusanPplg = Jurusan::where('kode', 'PPLG')->firstOrFail();

        $ta = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $program = Program::where('is_active', true)->first();

        // Cari atau buat Rombel
        $this->rombelHtl = Rombel::where('nama', 'X HTL 1')->first() ?? Rombel::create([
            'nama' => 'X HTL 1',
            'tingkat' => 'X',
            'jurusan_id' => $this->jurusanHtl->id,
            'tahun_ajaran_id' => $ta->id,
            'nomor_urut' => 1,
            'kapasitas' => 36,
        ]);

        $this->rombelPplg = Rombel::where('nama', 'XI PPLG 1')->first() ?? Rombel::create([
            'nama' => 'XI PPLG 1',
            'tingkat' => 'XI',
            'jurusan_id' => $this->jurusanPplg->id,
            'tahun_ajaran_id' => $ta->id,
            'nomor_urut' => 2,
            'kapasitas' => 36,
        ]);

        // Buat data siswa teruji
        // Di Rayon A: 2 Laki-laki di X HTL 1, 1 Perempuan di XI PPLG 1
        Siswa::create([
            'nama' => 'Siswa L1 Rayon A',
            'nis' => '10001',
            'nisn' => '0000000001',
            'nipd' => '10001',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
            'rayon_id' => $this->rayonA->id,
            'rombel_id' => $this->rombelHtl->id,
            'program_id' => $program?->id,
        ]);

        Siswa::create([
            'nama' => 'Siswa L2 Rayon A',
            'nis' => '10002',
            'nisn' => '0000000002',
            'nipd' => '10002',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
            'rayon_id' => $this->rayonA->id,
            'rombel_id' => $this->rombelHtl->id,
            'program_id' => $program?->id,
        ]);

        Siswa::create([
            'nama' => 'Siswa P1 Rayon A',
            'nis' => '10003',
            'nisn' => '0000000003',
            'nipd' => '10003',
            'jk' => 'P',
            'status' => 'aktif',
            'is_active' => true,
            'rayon_id' => $this->rayonA->id,
            'rombel_id' => $this->rombelPplg->id,
            'program_id' => $program?->id,
        ]);

        // 1 Siswa tanpa rayon dan tanpa rombel (menguji edge case siswa belum punya rombel/jurusan)
        Siswa::create([
            'nama' => 'Siswa Tanpa Rayon',
            'nis' => '10099',
            'nisn' => '0000000099',
            'nipd' => '10099',
            'jk' => 'P',
            'status' => 'aktif',
            'is_active' => true,
            'rayon_id' => null,
            'rombel_id' => null,
            'program_id' => null,
        ]);
    }

    public function test_rayon_tab_renders_matrix_table(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard-data.index', ['tab' => 'rayon']));
        $response->assertOk();
        $response->assertSee('Data Rayon');
        $response->assertSee('Rayon Cibatu 1');
        $response->assertSee('Pak Pembimbing Hebat');
        $response->assertSee('Total Rayon');
    }

    public function test_livewire_rayon_component_aggregation_and_expand(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RayonIndex::class)
            ->assertSee('Rayon Cibatu 1')
            ->assertSee('Pak Pembimbing Hebat')
            ->assertSee('(Tanpa Rayon)')
            // Test toggle expand individual rayon
            ->call('toggleRayon', (string) $this->rayonA->id)
            ->assertSet('expandedRayons', [(string) $this->rayonA->id])
            ->assertSee('X HTL 1')
            ->assertSee('XI PPLG 1')
            // Test toggle collapse
            ->call('toggleRayon', (string) $this->rayonA->id)
            ->assertSet('expandedRayons', []);
    }

    public function test_filter_tingkat_filters_matrix_data(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RayonIndex::class)
            ->set('filterTingkat', 'X')
            ->call('toggleRayon', (string) $this->rayonA->id)
            ->assertSee('X HTL 1')
            ->assertDontSee('XI PPLG 1');
    }

    public function test_search_by_rayon_and_pembimbing(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RayonIndex::class)
            // Cari nama rayon
            ->set('search', 'Cibatu')
            ->assertSee('Rayon Cibatu 1')
            ->assertDontSee('Rayon Tarogong 1')
            // Cari nama pembimbing
            ->set('search', 'Hebat')
            ->assertSee('Rayon Cibatu 1')
            ->assertDontSee('Rayon Tarogong 1')
            // Reset filter
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('filterTingkat', '')
            ->assertSee('Rayon Tarogong 1');
    }

    public function test_drilldown_student_detail_for_rayon_and_tanpa_rayon(): void
    {
        // Drill-down ke Siswa Rayon Cibatu 1
        $responseA = $this->actingAs($this->admin)->get(route('dashboard-data.rayon.students', $this->rayonA->id));
        $responseA->assertOk();
        $responseA->assertSee('Rayon: Rayon Cibatu 1');
        $responseA->assertSee('Siswa L1 Rayon A');
        $responseA->assertSee('Siswa P1 Rayon A');

        // Drill-down ke Siswa Tanpa Rayon
        $responseTanpa = $this->actingAs($this->admin)->get(route('dashboard-data.rayon.students', 'tanpa-rayon'));
        $responseTanpa->assertOk();
        $responseTanpa->assertSee('Rayon: (Tanpa Rayon)');
        $responseTanpa->assertSee('Siswa Tanpa Rayon');
    }
}
