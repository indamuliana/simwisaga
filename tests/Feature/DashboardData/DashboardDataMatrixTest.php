<?php

namespace Tests\Feature\DashboardData;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\DashboardData\Livewire\Index as DashboardIndex;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardDataMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $taAktif;
    protected Program $progReg;
    protected Program $progUng;
    protected Jurusan $jurusanHtl;
    protected Jurusan $jurusanPplg;
    protected Rombel $rombelX;
    protected Rombel $rombelXI;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_matrix_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->taAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->progReg = Program::where('kode', 'REG')->firstOrFail();
        $this->progUng = Program::where('kode', 'UNG')->firstOrFail();
        $this->jurusanHtl = Jurusan::where('kode', 'HTL')->firstOrFail();
        $this->jurusanPplg = Jurusan::where('kode', 'PPLG')->firstOrFail();

        // Siapkan Rombel Tingkat X
        $this->rombelX = Rombel::create([
            'tahun_ajaran_id' => $this->taAktif->id,
            'jurusan_id' => $this->jurusanHtl->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X HTL 1',
            'kapasitas' => 36,
        ]);

        // Siapkan Rombel Tingkat XI
        $this->rombelXI = Rombel::create([
            'tahun_ajaran_id' => $this->taAktif->id,
            'jurusan_id' => $this->jurusanPplg->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI PPLG 1',
            'kapasitas' => 36,
        ]);

        // Siswa di Rombel X HTL 1:
        // 2 Siswa L Reguler
        $this->createSiswa($this->rombelX, $this->progReg, 'L');
        $this->createSiswa($this->rombelX, $this->progReg, 'L');
        // 3 Siswa P Reguler
        $this->createSiswa($this->rombelX, $this->progReg, 'P');
        $this->createSiswa($this->rombelX, $this->progReg, 'P');
        $this->createSiswa($this->rombelX, $this->progReg, 'P');
        // 4 Siswa L Unggulan
        $this->createSiswa($this->rombelX, $this->progUng, 'L');
        $this->createSiswa($this->rombelX, $this->progUng, 'L');
        $this->createSiswa($this->rombelX, $this->progUng, 'L');
        $this->createSiswa($this->rombelX, $this->progUng, 'L');
        // 1 Siswa P Unggulan
        $this->createSiswa($this->rombelX, $this->progUng, 'P');
        // Total Rombel X HTL 1: 10 siswa (L: 6, P: 4)

        // Siswa di Rombel XI PPLG 1:
        // 1 Siswa L Reguler
        $this->createSiswa($this->rombelXI, $this->progReg, 'L');
        // 2 Siswa P Unggulan
        $this->createSiswa($this->rombelXI, $this->progUng, 'P');
        $this->createSiswa($this->rombelXI, $this->progUng, 'P');
        // Total Rombel XI PPLG 1: 3 siswa (L: 1, P: 2)
    }

    private function createSiswa(Rombel $rombel, Program $program, string $jk): Siswa
    {
        return Siswa::create([
            'rombel_id' => $rombel->id,
            'program_id' => $program->id,
            'nama' => 'Siswa ' . uniqid(),
            'nipd' => (string) rand(100000, 999999),
            'nisn' => (string) rand(1000000000, 9999999999),
            'jk' => $jk,
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    public function test_matriks_demografi_menghitung_agregasi_dengan_akurat(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardIndex::class)
            ->assertSet('tab', 'overview')
            ->assertSee('Matriks Demografi Siswa')
            ->assertSee('Program Reguler')
            ->assertSee('Program Unggulan')
            ->assertSee('Total Rombel')
            ->assertSee('X HTL 1')
            ->assertSee('XI PPLG 1')
            ->assertSee('Subtotal Tingkat X')
            ->assertSee('Subtotal Tingkat XI')
            ->assertSee('TOTAL KESELURUHAN')
            ->assertViewHas('grandTotal', function ($grandTotal) {
                // Total seluruh sekolah: 10 + 3 = 13 siswa
                return $grandTotal['total_all'] === 13
                    && $grandTotal['total_l'] === 7
                    && $grandTotal['total_p'] === 6;
            })
            ->assertViewHas('matrix', function ($matrix) {
                $subtotalX = $matrix['X']['subtotal'];
                $rowX = $matrix['X']['rows'][0];

                // Verifikasi data baris X HTL 1
                $rombelXValid = $rowX['nama'] === 'X HTL 1'
                    && $rowX['programs'][$this->progReg->id]['l'] === 2
                    && $rowX['programs'][$this->progReg->id]['p'] === 3
                    && $rowX['programs'][$this->progReg->id]['subtotal'] === 5
                    && $rowX['programs'][$this->progUng->id]['l'] === 4
                    && $rowX['programs'][$this->progUng->id]['p'] === 1
                    && $rowX['programs'][$this->progUng->id]['subtotal'] === 5
                    && $rowX['total_l'] === 6
                    && $rowX['total_p'] === 4
                    && $rowX['total_all'] === 10;

                // Verifikasi subtotal tingkat X
                $subtotalXValid = $subtotalX['total_all'] === 10
                    && $subtotalX['total_l'] === 6
                    && $subtotalX['total_p'] === 4;

                return $rombelXValid && $subtotalXValid;
            });
    }

    public function test_filter_tingkat_hanya_menampilkan_tingkat_yang_dipilih(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardIndex::class)
            ->set('filterTingkat', 'X')
            ->assertSee('X HTL 1')
            ->assertDontSee('XI PPLG 1')
            ->assertViewHas('grandTotal', function ($grandTotal) {
                return $grandTotal['total_all'] === 10;
            })
            ->call('resetFilters')
            ->assertSee('X HTL 1')
            ->assertSee('XI PPLG 1')
            ->assertViewHas('grandTotal', function ($grandTotal) {
                return $grandTotal['total_all'] === 13;
            });
    }

    public function test_filter_jurusan_hanya_menampilkan_jurusan_yang_dipilih(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardIndex::class)
            ->set('filterJurusan', (string) $this->jurusanPplg->id)
            ->assertSee('XI PPLG 1')
            ->assertDontSee('X HTL 1')
            ->assertViewHas('grandTotal', function ($grandTotal) {
                return $grandTotal['total_all'] === 3;
            });
    }
}
