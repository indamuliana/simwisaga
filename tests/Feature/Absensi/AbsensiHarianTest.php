<?php

namespace Tests\Feature\Absensi;

use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Absensi\Livewire\AbsensiHarianIndex;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AbsensiHarianTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $tahunAjaran;
    protected $semester;
    protected $rombelA;
    protected $rombelB;
    protected $siswa1;
    protected $siswa2;
    protected $siswa3;

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

        // Urutan NIS: Siswa 2 (1001), Siswa 1 (1002), Siswa 3 (1003)
        $this->siswa1 = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '1002',
            'nisn' => '001002',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'tanggal_lahir' => '2006-01-01',
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Andi Wijaya',
            'nipd' => '1001',
            'nisn' => '001001',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelB->id,
            'tanggal_lahir' => '2006-01-01',
        ]);

        $this->siswa3 = Siswa::create([
            'nama' => 'Citra Lestari',
            'nipd' => '1003',
            'nisn' => '001003',
            'jk' => 'P',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'tanggal_lahir' => '2006-01-01',
        ]);
    }

    public function test_default_halaman_langsung_memuat_seluruh_siswa_urut_nipd(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->assertSet('rombel_id', '')
            ->assertSee('Semua Rombel (Seluruh Siswa)')
            ->assertSee('X PPLG 1')
            ->assertSee('XI PPLG 1')
            ->assertSeeHtmlInOrder(['Andi Wijaya', 'Budi Santoso', 'Citra Lestari']);
    }

    public function test_pencarian_cepat_menyaring_siswa(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set('search', 'Citra')
            ->assertSee('Citra Lestari')
            ->assertDontSee('Andi Wijaya')
            ->assertDontSee('Budi Santoso')
            ->set('search', '1001') // NIPD Andi
            ->assertSee('Andi Wijaya')
            ->assertDontSee('Citra Lestari');
    }

    public function test_tandai_semua_hadir_tidak_menimpa_status_yang_sudah_diisi(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            // Andi diisi Sakit terlebih dahulu
            ->set("absensiData.{$this->siswa2->id}.status", StatusAbsensi::SAKIT->value)
            ->call('tandaiSemuaHadir')
            // Andi tetap Sakit
            ->assertSet("absensiData.{$this->siswa2->id}.status", StatusAbsensi::SAKIT->value)
            // Budi dan Citra yang tadinya kosong menjadi Hadir
            ->assertSet("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->assertSet("absensiData.{$this->siswa3->id}.status", StatusAbsensi::HADIR->value);
    }

    public function test_dapat_menyimpan_absensi_seluruh_siswa_tanpa_memilih_rombel(): void
    {
        $today = date('Y-m-d');

        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set('rombel_id', '')
            ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->set("absensiData.{$this->siswa2->id}.status", StatusAbsensi::SAKIT->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Absensi harian berhasil disimpan.');

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'status' => 'Hadir',
        ]);

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombelB->id,
            'status' => 'Sakit',
        ]);

        $record1 = AbsensiHarian::where('siswa_id', $this->siswa1->id)->first();
        $this->assertNotNull($record1);
        $this->assertEquals($today, $record1->tanggal->format('Y-m-d'));
    }
}
