<?php

namespace Tests\Feature\Absensi;

use App\Models\User;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Enums\StatusAbsensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolesSeeder;

class AbsensiModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_absensi_harian_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('absensi.harian'))
            ->assertOk()
            ->assertSee('Absensi Harian');
    }

    public function test_halaman_absensi_mapel_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('absensi.mapel'))
            ->assertOk()
            ->assertSee('Absensi Mata Pelajaran');
    }

    public function test_halaman_rekap_absensi_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('absensi.rekap'))
            ->assertOk()
            ->assertSee('Rekapitulasi Absensi');
    }

    public function test_absensi_harian_model_dapat_menyimpan_data(): void
    {
        $tahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true
        ]);
        $semester = Semester::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 'ganjil',
            'is_active' => true
        ]);
        
        $jurusan = \App\Modules\Akademik\Models\Jurusan::create([
            'kode' => 'RPL', 'nama' => 'RPL'
        ]);
        $rombel = Rombel::create([
            'nama' => 'X RPL 1',
            'tingkat' => 'X',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $tahunAjaran->id
        ]);
        $siswa = Siswa::create([
            'nama' => 'Siswa Test',
            'nipd' => '12345',
            'nisn' => '0012345',
            'jk' => 'L',
            'tanggal_lahir' => '2006-01-01',
            'rombel_id' => $rombel->id
        ]);

        $absensi = AbsensiHarian::create([
            'tanggal' => now()->format('Y-m-d'),
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'semester_id' => $semester->id,
            'status' => StatusAbsensi::ALPA->value,
            'keterangan' => 'Sering bolos',
            'diinput_oleh' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('absensi_harians', [
            'id' => $absensi->id,
            'status' => 'Alpa',
            'keterangan' => 'Sering bolos'
        ]);
        
        $this->assertInstanceOf(StatusAbsensi::class, $absensi->status);
        $this->assertFalse($absensi->status->isHadirStatistik());
    }
}
