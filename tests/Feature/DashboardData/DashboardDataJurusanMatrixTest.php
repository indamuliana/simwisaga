<?php

namespace Tests\Feature\DashboardData;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\DashboardData\Livewire\Jurusan\Index as DashboardJurusanIndex;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardDataJurusanMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $taAktif;
    protected Program $progReg;
    protected Program $progUng;
    protected Jurusan $jurusanHtl;
    protected Jurusan $jurusanPplg;
    protected Rombel $rombelHtlX;
    protected Rombel $rombelHtlXI;
    protected Rombel $rombelPplgX;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_jurusan_matrix_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->taAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->progReg = Program::where('kode', 'REG')->firstOrFail();
        $this->progUng = Program::where('kode', 'UNG')->firstOrFail();
        $this->jurusanHtl = Jurusan::where('kode', 'HTL')->firstOrFail();
        $this->jurusanPplg = Jurusan::where('kode', 'PPLG')->firstOrFail();

        // Ambil rombel PPLG yang sudah di-seed
        $this->rombelPplgX = Rombel::where('nama', 'X PPLG 1')->firstOrFail();

        // Buat rombel HTL baru
        $this->rombelHtlX = Rombel::create([
            'tahun_ajaran_id' => $this->taAktif->id,
            'jurusan_id' => $this->jurusanHtl->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X HTL 1',
            'kapasitas' => 36,
        ]);
        $this->rombelHtlXI = Rombel::create([
            'tahun_ajaran_id' => $this->taAktif->id,
            'jurusan_id' => $this->jurusanHtl->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI HTL 1',
            'kapasitas' => 36,
        ]);

        // Siswa X HTL 1 (2 L Reg, 3 P Ung) -> Total 5
        $this->createSiswa($this->rombelHtlX, $this->progReg, 'L');
        $this->createSiswa($this->rombelHtlX, $this->progReg, 'L');
        $this->createSiswa($this->rombelHtlX, $this->progUng, 'P');
        $this->createSiswa($this->rombelHtlX, $this->progUng, 'P');
        $this->createSiswa($this->rombelHtlX, $this->progUng, 'P');

        // Siswa XI HTL 1 (1 L Ung, 1 P Reg) -> Total 2
        $this->createSiswa($this->rombelHtlXI, $this->progUng, 'L');
        $this->createSiswa($this->rombelHtlXI, $this->progReg, 'P');
        // Total HTL: 7 siswa (L: 3, P: 4, Reg: 3, Ung: 4, Tingkat X: 5, Tingkat XI: 2)

        // Siswa X PPLG 1 (4 L Ung) -> Total 4
        $this->createSiswa($this->rombelPplgX, $this->progUng, 'L');
        $this->createSiswa($this->rombelPplgX, $this->progUng, 'L');
        $this->createSiswa($this->rombelPplgX, $this->progUng, 'L');
        $this->createSiswa($this->rombelPplgX, $this->progUng, 'L');
        // Total PPLG: 4 siswa (L: 4, P: 0, Tingkat X: 4)
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

    public function test_matriks_jurusan_menghitung_agregasi_akurat(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardJurusanIndex::class)
            ->assertSee('Data Jurusan')
            ->assertSee('Program Reguler')
            ->assertSee('Program Unggulan')
            ->assertSee('Distribusi Tingkat')
            ->assertSee('Total Siswa')
            ->assertSee('HTL')
            ->assertSee('PPLG')
            ->assertSee('TOTAL KESELURUHAN')
            ->assertViewHas('grandTotal', function ($grandTotal) {
                // Total seluruh sekolah: 7 (HTL) + 4 (PPLG) = 11 siswa, total 4 rombel (HTL: 2, PPLG: 1, TJKT: 1)
                return $grandTotal['total_all'] === 11
                    && $grandTotal['total_l'] === 7
                    && $grandTotal['total_p'] === 4
                    && $grandTotal['rombel_count'] === 4
                    && $grandTotal['tingkat']['X'] === 9
                    && $grandTotal['tingkat']['XI'] === 2;
            })
            ->assertViewHas('jurusanRows', function ($jurusanRows) {
                $htl = collect($jurusanRows)->firstWhere('kode', 'HTL');
                $pplg = collect($jurusanRows)->firstWhere('kode', 'PPLG');

                $htlValid = $htl
                    && $htl['rombel_count'] === 2
                    && $htl['total_l'] === 3
                    && $htl['total_p'] === 4
                    && $htl['total_all'] === 7
                    && $htl['tingkat']['X'] === 5
                    && $htl['tingkat']['XI'] === 2
                    && $htl['programs'][$this->progReg->id]['subtotal'] === 3
                    && $htl['programs'][$this->progUng->id]['subtotal'] === 4;

                $pplgValid = $pplg
                    && $pplg['rombel_count'] === 1
                    && $pplg['total_l'] === 4
                    && $pplg['total_p'] === 0
                    && $pplg['total_all'] === 4
                    && $pplg['tingkat']['X'] === 4;

                return $htlValid && $pplgValid;
            });
    }

    public function test_toggle_expand_menampilkan_rincian_rombel_per_jurusan(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardJurusanIndex::class)
            ->assertDontSee('X HTL 1')
            ->call('toggleExpand', $this->jurusanHtl->id)
            ->assertSee('X HTL 1')
            ->assertSee('XI HTL 1')
            ->assertDontSee('X PPLG 1')
            ->call('toggleExpand', $this->jurusanHtl->id)
            ->assertDontSee('X HTL 1');
    }

    public function test_toggle_expand_all_dan_search(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardJurusanIndex::class)
            ->call('toggleExpandAll')
            ->assertSee('X HTL 1')
            ->assertSee('XI HTL 1')
            ->assertSee('X PPLG 1')
            ->set('search', 'PPLG')
            ->assertSee('PPLG')
            ->assertDontSee('HTL');
    }
}
