<?php

namespace Tests\Feature\Absensi;

use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Absensi\Livewire\AbsensiMapelIndex;
use App\Modules\Absensi\Models\AbsensiMapel;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AbsensiMapelTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $tahunAjaran;
    protected $semester;
    protected $rombelA;
    protected $rombelB;
    protected $mapel1;
    protected $mapel2;
    protected $penugasan1;
    protected $penugasan2;
    protected $penugasan3;
    protected $siswaA1;
    protected $siswaA2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create(['kode' => 'PPLG', 'nama' => 'PPLG']);

        $this->rombelA = Rombel::create([
            'nama' => 'X PPLG 1',
            'tingkat' => 'X',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $this->rombelB = Rombel::create([
            'nama' => 'XI PPLG 1',
            'tingkat' => 'XI',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $this->mapel1 = Mapel::create(['kode' => 'INF', 'nama' => 'Informatika', 'kelompok' => 'umum']);
        $this->mapel2 = Mapel::create(['kode' => 'PBO', 'nama' => 'Pemrograman Berorientasi Objek', 'kelompok' => 'kejuruan']);

        $guru = Pegawai::create([
            'user_id' => $this->admin->id,
            'nama_lengkap' => 'Guru Pengajar, S.Kom.',
            'is_active' => true,
        ]);

        // Rombel A punya 2 penugasan (Mapel 1 & Mapel 2)
        $this->penugasan1 = PenugasanMengajar::create([
            'semester_id' => $this->semester->id,
            'pegawai_id' => $guru->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombelA->id,
        ]);

        $this->penugasan2 = PenugasanMengajar::create([
            'semester_id' => $this->semester->id,
            'pegawai_id' => $guru->id,
            'mapel_id' => $this->mapel2->id,
            'rombel_id' => $this->rombelA->id,
        ]);

        // Rombel B hanya punya 1 penugasan (Mapel 1)
        $this->penugasan3 = PenugasanMengajar::create([
            'semester_id' => $this->semester->id,
            'pegawai_id' => $guru->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombelB->id,
        ]);

        $this->siswaA1 = Siswa::create([
            'nama' => 'Aliando',
            'nipd' => '2001',
            'nisn' => '002001',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'tanggal_lahir' => '2006-01-01',
        ]);

        $this->siswaA2 = Siswa::create([
            'nama' => 'Bella Safira',
            'nipd' => '2002',
            'nisn' => '002002',
            'jk' => 'P',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'tanggal_lahir' => '2006-01-01',
        ]);
    }

    public function test_filter_bertingkat_dan_auto_select_pada_rombel_tunggal(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiMapelIndex::class)
            ->assertSee('1. Kelas / Rombel')
            ->assertSee('2. Mata Pelajaran')
            // Memastikan kedua rombel (X dan XI) muncul di dropdown untuk admin
            ->assertSeeHtml($this->rombelA->nama)
            ->assertSeeHtml($this->rombelB->nama)
            // Memilih Rombel B yang hanya memiliki 1 penugasan
            ->set('rombel_id', (string) $this->rombelB->id)
            // Harusnya auto-select penugasan3
            ->assertSet('penugasan_id', (string) $this->penugasan3->id);
    }

    public function test_filter_bertingkat_rombel_dengan_banyak_mapel(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiMapelIndex::class)
            // Memilih Rombel A yang memiliki 2 mapel
            ->set('rombel_id', (string) $this->rombelA->id)
            ->assertSet('penugasan_id', '')
            // Pilih salah satu penugasan
            ->set('penugasan_id', (string) $this->penugasan1->id)
            ->assertSee('Aliando')
            ->assertSee('Bella Safira');
    }

    public function test_pencarian_siswa_pada_absensi_mapel(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiMapelIndex::class)
            ->set('rombel_id', (string) $this->rombelA->id)
            ->set('penugasan_id', (string) $this->penugasan1->id)
            ->assertSee('Aliando')
            ->assertSee('Bella Safira')
            // Cari nama Bella
            ->set('search', 'Bella')
            ->assertSee('Bella Safira')
            ->assertDontSee('Aliando');
    }

    public function test_dapat_menyimpan_absensi_mapel(): void
    {
        $today = date('Y-m-d');

        Livewire::actingAs($this->admin)
            ->test(AbsensiMapelIndex::class)
            ->set('rombel_id', (string) $this->rombelA->id)
            ->set('penugasan_id', (string) $this->penugasan1->id)
            ->set("absensiMapelData.{$this->siswaA1->id}.status", StatusAbsensi::HADIR->value)
            ->set("absensiMapelData.{$this->siswaA2->id}.status", StatusAbsensi::SAKIT->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Absensi mapel berhasil disimpan.');

        $this->assertDatabaseHas('absensi_mapels', [
            'siswa_id' => $this->siswaA1->id,
            'penugasan_mengajar_id' => $this->penugasan1->id,
            'status' => 'Hadir',
        ]);

        $this->assertDatabaseHas('absensi_mapels', [
            'siswa_id' => $this->siswaA2->id,
            'penugasan_mengajar_id' => $this->penugasan1->id,
            'status' => 'Sakit',
        ]);
    }
}
