<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Livewire\PenyaluranIndex;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KomponenBiaya;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenyaluranTargetWarningBannerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Semester $semester;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_banner_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->semester = Semester::firstOrFail();

        $this->siswa = Siswa::create([
            'nama' => 'Ahmad Abdul Aziz Test',
            'nisn' => '0091109539',
            'jk' => 'L',
            'is_active' => true,
        ]);
    }

    public function test_banner_amber_renders_when_komponen_less_than_target_kelompok()
    {
        $kelompok = Kelompok::create([
            'kode_kelompok' => 'KEL-03 test',
            'nama_kelompok' => 'test 3',
            'target_nominal' => 12000000,
            'is_aktif' => true,
        ]);

        $penerima = SiswaPenerima::create([
            'siswa_id' => $this->siswa->id,
            'kelompok_id' => $kelompok->id,
            'status' => 'aktif',
            'tanggal_mulai' => now(),
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'spp',
            'nama_komponen' => 'SPP Bulanan',
            'nominal_per_bulan' => 200000,
            'total_target_semester' => 1200000,
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'asrama',
            'nama_komponen' => 'Asrama Bulanan',
            'nominal_per_bulan' => 100000,
            'total_target_semester' => 600000,
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'dsp',
            'nama_komponen' => 'Cicilan DSP',
            'nominal_per_bulan' => null,
            'total_target_semester' => 200000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenyaluranIndex::class)
            ->set('rutinKelompokId', $kelompok->id)
            ->assertSee('Perhatian: Rincian Komponen Siswa Belum Mencapai Target Semester Kelompok')
            ->assertSee('Rp 2.000.000/semester')
            ->assertSee('Rp 12.000.000')
            ->assertSee('Rp 10.000.000')
            ->assertSee('Atur Komponen Siswa')
            ->assertSee(route('beasiswa-ota.penerima'));
    }

    public function test_banner_emerald_renders_when_komponen_matches_target_kelompok()
    {
        $kelompok = Kelompok::create([
            'kode_kelompok' => 'KEL-PAS',
            'nama_kelompok' => 'Kelompok Pas',
            'target_nominal' => 2000000,
            'is_aktif' => true,
        ]);

        $penerima = SiswaPenerima::create([
            'siswa_id' => $this->siswa->id,
            'kelompok_id' => $kelompok->id,
            'status' => 'aktif',
            'tanggal_mulai' => now(),
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'spp',
            'nama_komponen' => 'SPP Bulanan',
            'nominal_per_bulan' => 200000,
            'total_target_semester' => 1200000,
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'asrama',
            'nama_komponen' => 'Asrama Bulanan',
            'nominal_per_bulan' => 100000,
            'total_target_semester' => 600000,
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'dsp',
            'nama_komponen' => 'Cicilan DSP',
            'nominal_per_bulan' => null,
            'total_target_semester' => 200000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenyaluranIndex::class)
            ->set('rutinKelompokId', $kelompok->id)
            ->assertSee('Sinkron:')
            ->assertSee('telah sesuai 100% dengan Target Semester Kelompok');
    }

    public function test_banner_rose_renders_when_komponen_exceeds_target_kelompok()
    {
        $kelompok = Kelompok::create([
            'kode_kelompok' => 'KEL-LEBIH',
            'nama_kelompok' => 'Kelompok Kecil',
            'target_nominal' => 1000000,
            'is_aktif' => true,
        ]);

        $penerima = SiswaPenerima::create([
            'siswa_id' => $this->siswa->id,
            'kelompok_id' => $kelompok->id,
            'status' => 'aktif',
            'tanggal_mulai' => now(),
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $penerima->id,
            'jenis_komponen' => 'spp',
            'nama_komponen' => 'SPP Bulanan',
            'nominal_per_bulan' => 200000,
            'total_target_semester' => 1200000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenyaluranIndex::class)
            ->set('rutinKelompokId', $kelompok->id)
            ->assertSee('Perhatian: Komponen Biaya Siswa Melebihi Target Semester Kelompok')
            ->assertSee('Rp 200.000');
    }
}
