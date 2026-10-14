<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\CustomKelompokIndex;
use App\Modules\Akademik\Models\CustomKelompok;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Services\CustomKelompokExcelService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomKelompokTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_custom_kelompok',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->tahunAjaran = TahunAjaran::where('is_active', true)->first()
            ?? TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
    }

    public function test_halaman_custom_kelompok_terproteksi_auth(): void
    {
        $this->get(route('akademik.custom-kelompok'))
            ->assertRedirect(route('login'));
    }

    public function test_halaman_custom_kelompok_bisa_diakses_oleh_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.custom-kelompok'))
            ->assertOk()
            ->assertSee('Custom Kelompok')
            ->assertSee('Tab 1: Seluruh Siswa & Tag', false)
            ->assertSee('Tab 2: Kelola Kelompok')
            ->assertSee('Tab 3: Pengelompokan Siswa');
    }

    public function test_crud_kelompok_master(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->set('activeTab', 'kelompok')
            ->call('createKelompok')
            ->set('nama', 'Tim Robotika')
            ->set('kode', 'ROBO-01')
            ->set('kategori', 'Minat/Bakat')
            ->set('keterangan', 'Kelompok persiapan lomba robotika')
            ->set('warna', '#10b981')
            ->set('kapasitas', 15)
            ->call('saveKelompok')
            ->assertHasNoErrors()
            ->assertSee('Tim Robotika');

        $this->assertDatabaseHas('custom_kelompoks', [
            'nama' => 'Tim Robotika',
            'kode' => 'ROBO-01',
            'kategori' => 'Minat/Bakat',
            'kapasitas' => 15,
        ]);

        $kelompok = CustomKelompok::where('nama', 'Tim Robotika')->first();

        // Test Edit
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->call('editKelompok', $kelompok->id)
            ->set('nama', 'Tim Robotika & IoT')
            ->call('saveKelompok')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('custom_kelompoks', [
            'id' => $kelompok->id,
            'nama' => 'Tim Robotika & IoT',
        ]);

        // Test Hapus
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->call('confirmDeleteKelompok', $kelompok->id)
            ->call('deleteKelompok')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('custom_kelompoks', [
            'id' => $kelompok->id,
        ]);
    }

    public function test_pendaftaran_siswa_manual_dan_multi_kelompok(): void
    {
        $kelompokA = CustomKelompok::create([
            'nama' => 'Kelompok Olimpiade Sains',
            'kode' => 'OSN',
            'warna' => '#3b82f6',
        ]);

        $kelompokB = CustomKelompok::create([
            'nama' => 'Kelompok Remedial Fisika',
            'kode' => 'REM-FIS',
            'warna' => '#ef4444',
        ]);

        $siswa1 = Siswa::create([
            'nama' => 'Ahmad Fulan',
            'nipd' => '12001',
            'nisn' => '0012001',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $siswa2 = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '12002',
            'nisn' => '0012002',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        // Daftarkan siswa1 dan siswa2 ke kelompokA
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->set('selectedKelompokId', $kelompokA->id)
            ->set('selectedSiswaIds', [$siswa1->id, $siswa2->id])
            ->set('catatanPerSiswa', 'Anggota Tim')
            ->call('saveManualSiswa')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('custom_kelompok_siswas', [
            'custom_kelompok_id' => $kelompokA->id,
            'siswa_id' => $siswa1->id,
            'keterangan' => 'Anggota Tim',
        ]);
        $this->assertDatabaseHas('custom_kelompok_siswas', [
            'custom_kelompok_id' => $kelompokA->id,
            'siswa_id' => $siswa2->id,
        ]);

        // Verifikasi 1 siswa bisa memiliki lebih dari 1 kelompok: Daftarkan siswa1 juga ke kelompokB
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->set('selectedKelompokId', $kelompokB->id)
            ->set('selectedSiswaIds', [$siswa1->id])
            ->set('catatanPerSiswa', 'Remedial Bab 2')
            ->call('saveManualSiswa')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('custom_kelompok_siswas', [
            'custom_kelompok_id' => $kelompokB->id,
            'siswa_id' => $siswa1->id,
            'keterangan' => 'Remedial Bab 2',
        ]);

        $this->assertEquals(2, $siswa1->fresh()->customKelompoks()->count());
        $this->assertEquals(1, $siswa2->fresh()->customKelompoks()->count());

        // Test ubah keterangan siswa di kelompok
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->set('selectedKelompokId', $kelompokA->id)
            ->call('editKeteranganSiswa', $siswa1->id)
            ->set('editingKeterangan', 'Ketua Tim')
            ->call('saveKeteranganSiswa')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('custom_kelompok_siswas', [
            'custom_kelompok_id' => $kelompokA->id,
            'siswa_id' => $siswa1->id,
            'keterangan' => 'Ketua Tim',
        ]);

        // Test keluarkan siswa dari kelompok
        Livewire::actingAs($this->admin)
            ->test(CustomKelompokIndex::class)
            ->set('selectedKelompokId', $kelompokA->id)
            ->call('removeSiswa', $siswa2->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('custom_kelompok_siswas', [
            'custom_kelompok_id' => $kelompokA->id,
            'siswa_id' => $siswa2->id,
        ]);
    }

    public function test_excel_service_template_export_dan_cetak(): void
    {
        $service = new CustomKelompokExcelService();
        $kelompok = CustomKelompok::create([
            'nama' => 'Kelompok Bahasa Inggris',
            'kode' => 'ENG-01',
            'kategori' => 'Peminatan',
            'warna' => '#8b5cf6',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Citra Lestari',
            'nipd' => '12003',
            'nisn' => '0012003',
            'jk' => 'P',
            'status' => 'aktif',
            'is_active' => true,
        ]);
        $kelompok->siswas()->attach($siswa->id, ['keterangan' => 'Speaker']);

        // 1. Template
        $template = $service->downloadTemplate($kelompok);
        $this->assertNotNull($template);
        $this->assertEquals('Template Import', $template->getActiveSheet()->getTitle());

        // 2. Export Anggota
        $exportAnggota = $service->exportMembers($kelompok);
        $this->assertNotNull($exportAnggota);
        $this->assertEquals('Anggota Kelompok', $exportAnggota->getActiveSheet()->getTitle());

        // 3. Export Siswa Tags
        $exportTags = $service->exportSiswaTags(Siswa::with('customKelompoks')->get());
        $this->assertNotNull($exportTags);
        $this->assertEquals('Siswa dan Tag Kelompok', $exportTags->getActiveSheet()->getTitle());

        // 4. Export Attendance Sheet
        $attendanceSheet = $service->exportAttendanceSheet($kelompok, [
            'jumlahKolom' => 16,
            'tahunPelajaran' => '2026/2027',
            'mapel' => 'English Debate',
            'namaGuru' => 'John Doe, S.Pd.',
        ]);
        $this->assertNotNull($attendanceSheet);
        $this->assertEquals('Daftar Hadir', $attendanceSheet->getActiveSheet()->getTitle());
    }
}
