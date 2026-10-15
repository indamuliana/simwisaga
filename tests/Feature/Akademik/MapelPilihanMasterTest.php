<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\MapelPilihanIndex;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MapelPilihanMasterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Pegawai $guru;
    protected Rombel $rombel;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_mapil',
            'must_change_password' => false,
        ]);
        $this->admin->assignRole(User::SUPER_ADMIN_ROLE);

        $this->ta = TahunAjaran::create([
            'tahun' => '2026/2027',
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->guru = Pegawai::create([
            'nama_lengkap' => 'Guru Mapil Komputer',
            'nik' => '1234567890123456',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $this->rombel = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'XI',
            'nama' => 'PPLG XI-1',
            'is_active' => true,
        ]);

        $this->siswa1 = Siswa::create([
            'rombel_id' => $this->rombel->id,
            'nisn' => '0011223344',
            'nipd' => '1001',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'rombel_id' => $this->rombel->id,
            'nisn' => '0011223355',
            'nipd' => '1002',
            'nama' => 'Siti Rahma',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    public function test_can_view_mapel_pilihan_master_index(): void
    {
        Livewire::actingAs($this->admin)
            ->test(MapelPilihanIndex::class)
            ->assertOk()
            ->assertSee('Mata Pelajaran Pilihan (Mapil)');
    }

    public function test_can_create_and_edit_mapel_pilihan_with_multi_tingkat(): void
    {
        $comp = Livewire::actingAs($this->admin)
            ->test(MapelPilihanIndex::class)
            ->set('kode', 'MP-IOT')
            ->set('nama', 'Internet of Things Terapan')
            ->set('kkm_default', 80)
            ->set('jp', 4)
            ->set('tingkats', ['XI', 'XII'])
            ->set('guru_id', $this->guru->id)
            ->call('saveMapil')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapel_pilihans', [
            'kode' => 'MP-IOT',
            'nama' => 'Internet of Things Terapan',
            'kkm_default' => 80,
            'guru_id' => $this->guru->id,
        ]);

        $mapil = MapelPilihan::where('kode', 'MP-IOT')->first();
        $this->assertIsArray($mapil->tingkats);
        $this->assertContains('XI', $mapil->tingkats);
        $this->assertContains('XII', $mapil->tingkats);

        // Edit Mapil
        $comp->call('editMapil', $mapil->id)
            ->set('nama', 'Internet of Things Lanjut')
            ->call('saveMapil')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapel_pilihans', [
            'id' => $mapil->id,
            'nama' => 'Internet of Things Lanjut',
        ]);
    }

    public function test_can_enroll_multiple_students_and_one_student_can_take_multiple_mapil(): void
    {
        $mapilA = MapelPilihan::create([
            'tahun_ajaran_id' => $this->ta->id,
            'kode' => 'MP-AI',
            'nama' => 'Kecerdasan Buatan',
            'tingkats' => ['XI'],
            'kkm_default' => 75,
            'jp' => 4,
            'guru_id' => $this->guru->id,
            'is_active' => true,
        ]);

        $mapilB = MapelPilihan::create([
            'tahun_ajaran_id' => $this->ta->id,
            'kode' => 'MP-CYBER',
            'nama' => 'Keamanan Siber',
            'tingkats' => ['XI'],
            'kkm_default' => 75,
            'jp' => 4,
            'guru_id' => $this->guru->id,
            'is_active' => true,
        ]);

        // Daftarkan siswa1 ke Mapil A
        Livewire::actingAs($this->admin)
            ->test(MapelPilihanIndex::class)
            ->set('selectedMapilId', $mapilA->id)
            ->set('selectedCandidateIds', [(string) $this->siswa1->id, (string) $this->siswa2->id])
            ->call('addSelectedCandidates')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapel_pilihan_anggotas', [
            'mapel_pilihan_id' => $mapilA->id,
            'siswa_id' => $this->siswa1->id,
        ]);
        $this->assertDatabaseHas('mapel_pilihan_anggotas', [
            'mapel_pilihan_id' => $mapilA->id,
            'siswa_id' => $this->siswa2->id,
        ]);

        // Daftarkan siswa1 juga ke Mapil B (Multi-mapil per siswa)
        Livewire::actingAs($this->admin)
            ->test(MapelPilihanIndex::class)
            ->set('selectedMapilId', $mapilB->id)
            ->set('selectedCandidateIds', [(string) $this->siswa1->id])
            ->call('addSelectedCandidates')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapel_pilihan_anggotas', [
            'mapel_pilihan_id' => $mapilB->id,
            'siswa_id' => $this->siswa1->id,
        ]);

        // Siswa 1 kini memiliki 2 Mapil aktif secara independen
        $count = MapelPilihanAnggota::where('siswa_id', $this->siswa1->id)->count();
        $this->assertEquals(2, $count);

        // Hapus siswa2 dari Mapil A
        $anggota2 = MapelPilihanAnggota::where('mapel_pilihan_id', $mapilA->id)
            ->where('siswa_id', $this->siswa2->id)
            ->first();

        Livewire::actingAs($this->admin)
            ->test(MapelPilihanIndex::class)
            ->call('removeMember', $anggota2->id);

        $this->assertDatabaseMissing('mapel_pilihan_anggotas', [
            'id' => $anggota2->id,
        ]);
    }
}
