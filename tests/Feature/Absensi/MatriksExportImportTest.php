<?php

namespace Tests\Feature\Absensi;

use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Absensi\Livewire\RekapAbsensiIndex;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class MatriksExportImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rombel $rombel;
    protected Semester $semester;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $this->rombel = Rombel::create([
            'nama' => 'X PPLG 1',
            'tingkat' => 'X',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $this->siswa1 = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '12600001',
            'nisn' => '0012600001',
            'jk' => 'L',
            'status' => 'aktif',
            'tanggal_lahir' => '2006-01-01',
            'rombel_id' => $this->rombel->id,
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Siti Aminah',
            'nipd' => '12600002',
            'nisn' => '0012600002',
            'jk' => 'P',
            'status' => 'aktif',
            'tanggal_lahir' => '2006-02-02',
            'rombel_id' => $this->rombel->id,
        ]);
    }

    public function test_can_export_matriks_excel(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(RekapAbsensiIndex::class)
            ->set('tgl_mulai', '2026-11-02')
            ->set('tgl_selesai', '2026-11-06')
            ->set('rombelId', $this->rombel->id)
            ->call('exportMatriksExcel')
            ->assertFileDownloaded();
    }

    public function test_can_download_template(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(RekapAbsensiIndex::class)
            ->set('tgl_mulai', '2026-11-02')
            ->set('tgl_selesai', '2026-11-06')
            ->set('rombelId', $this->rombel->id)
            ->call('downloadTemplate')
            ->assertFileDownloaded();
    }

    public function test_can_open_import_modal_and_preview_uploaded_csv(): void
    {
        $this->actingAs($this->admin);

        // Buat file CSV matriks tiruan
        $csvContent = implode("\n", [
            '1,2,3,4,5,6,7,8,9',
            ',,,,,,,,',
            'No,NIS,ROMBEL,L/P,Rayon,Pembimbing,Nama Murid,2026-11-02,2026-11-03,S,I,A',
            ',,,,,,,,,,,',
            '1,12600001,X PPLG 1,L,Rayon 1,Guru 1,Budi Santoso,,S,1,0,0',
            '2,12600002,X PPLG 1,P,Rayon 1,Guru 1,Siti Aminah,I,A,0,1,1',
            '3,99999999,X PPLG 1,L,Rayon 1,Guru 1,Siswa Fiktif,S,,1,0,0',
        ]);

        $file = UploadedFile::fake()->createWithContent('absensi_matriks.csv', $csvContent);

        Livewire::test(RekapAbsensiIndex::class)
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->set('file', $file)
            ->assertSet('previewData.matched_students_count', 2)
            ->assertSet('previewData.total_dates', 2)
            ->assertSet('previewData.total_records', 4)
            ->call('confirmImport')
            ->assertSet('showImportModal', false)
            ->assertSee('Sukses!');

        // Pastikan record tersimpan di database
        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'tanggal' => '2026-11-02',
            'status' => StatusAbsensi::HADIR->value,
        ]);

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'tanggal' => '2026-11-03',
            'status' => StatusAbsensi::SAKIT->value,
        ]);

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa2->id,
            'tanggal' => '2026-11-02',
            'status' => StatusAbsensi::IZIN->value,
        ]);

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa2->id,
            'tanggal' => '2026-11-03',
            'status' => StatusAbsensi::ALPA->value,
        ]);
    }

    public function test_user_without_permission_cannot_open_import_modal(): void
    {
        $nonAdmin = User::factory()->create();
        $this->actingAs($nonAdmin);

        Livewire::test(RekapAbsensiIndex::class)
            ->call('openImportModal')
            ->assertForbidden();
    }

    public function test_can_open_reset_modal_and_calculate_count(): void
    {
        $this->actingAs($this->admin);

        // Buat record absensi sampel
        AbsensiHarian::create([
            'tanggal' => '2026-11-02',
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombel->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);

        AbsensiHarian::create([
            'tanggal' => '2026-11-03',
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombel->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::SAKIT->value,
        ]);

        Livewire::test(RekapAbsensiIndex::class)
            ->set('tgl_mulai', '2026-11-01')
            ->set('tgl_selesai', '2026-11-05')
            ->call('openResetModal')
            ->assertSet('showResetModal', true)
            ->assertSee('2 record');
    }

    public function test_execute_reset_requires_confirmation_text(): void
    {
        $this->actingAs($this->admin);

        AbsensiHarian::create([
            'tanggal' => '2026-11-02',
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombel->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);

        Livewire::test(RekapAbsensiIndex::class)
            ->call('openResetModal')
            ->set('resetConfirmationText', 'SALAH')
            ->call('executeResetAbsensi')
            ->assertSet('showResetModal', true)
            ->assertSet('resetErrorMessage', 'Ketik kata "RESET" dengan tepat untuk mengonfirmasi penghapusan data.');

        $this->assertEquals(1, AbsensiHarian::count());
    }

    public function test_execute_reset_deletes_records_successfully(): void
    {
        $this->actingAs($this->admin);

        AbsensiHarian::create([
            'tanggal' => '2026-11-02',
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombel->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);

        AbsensiHarian::create([
            'tanggal' => '2026-11-03',
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombel->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::SAKIT->value,
        ]);

        Livewire::test(RekapAbsensiIndex::class)
            ->set('tgl_mulai', '2026-11-01')
            ->set('tgl_selesai', '2026-11-05')
            ->call('openResetModal')
            ->set('resetConfirmationText', 'RESET')
            ->call('executeResetAbsensi')
            ->assertSet('showResetModal', false)
            ->assertSee('Sukses! Berhasil mereset 2 data absensi.');

        $this->assertEquals(0, AbsensiHarian::count());
    }

    public function test_user_without_permission_cannot_open_reset_modal(): void
    {
        $nonAdmin = User::factory()->create();
        $this->actingAs($nonAdmin);

        Livewire::test(RekapAbsensiIndex::class)
            ->call('openResetModal')
            ->assertForbidden();
    }
}
