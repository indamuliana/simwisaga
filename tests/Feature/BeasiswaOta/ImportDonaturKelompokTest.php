<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\BeasiswaOta\Livewire\DonaturKelompokIndex;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KelompokAnggota;
use App\Modules\BeasiswaOta\Services\OtaExcelService;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportDonaturKelompokTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kelompok $kelompokA;
    protected Kelompok $kelompokB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_ota_tester',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->kelompokA = Kelompok::create([
            'kode_kelompok' => 'KLP-A',
            'nama_kelompok' => 'Kelompok Peduli A',
            'target_nominal' => 2000000,
        ]);

        $this->kelompokB = Kelompok::create([
            'kode_kelompok' => 'KLP-B',
            'nama_kelompok' => 'Kelompok Peduli B',
            'target_nominal' => 2000000,
        ]);
    }

    /**
     * Helper membuat file Excel sementara dengan urutan kolom template donatur:
     * A: Nama Donatur (*Wajib)
     * B: Tipe Donatur (rutin/insidental)
     * C: Nomor Telepon / WA
     * D: Email
     * E: Alamat
     * F: Keterangan
     * G: Nominal Komitmen Iuran (Opsional)
     */
    protected function createExcelFile(array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'Nama Donatur (*Wajib)',
            'Tipe Donatur (rutin/insidental)',
            'Nomor Telepon / WA',
            'Email',
            'Alamat',
            'Keterangan',
            'Nominal Komitmen Iuran (Opsional)',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Rows
        $sheet->fromArray($rows, null, 'A2');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_ota_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    public function test_template_donaturs_memiliki_header_kolom_g()
    {
        $spreadsheet = OtaExcelService::templateDonaturs();
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertEquals('Nama Donatur (*Wajib)', $sheet->getCell('A1')->getValue());
        $this->assertEquals('Nominal Komitmen Iuran (Opsional)', $sheet->getCell('G1')->getValue());
    }

    public function test_import_donatur_tanpa_kelompok_tujuan_hanya_masuk_master_donatur()
    {
        $filePath = $this->createExcelFile([
            ['Budi Santoso', 'insidental', '081234567890', 'budi@test.com', 'Garut', 'Donatur lepas', ''],
            ['Siti Rahma', 'rutin', '081298765432', 'siti@test.com', 'Bandung', 'Donatur tetap', ''],
        ]);

        $res = OtaExcelService::importDonaturs($filePath, null);

        $this->assertEquals(2, $res['imported']);
        $this->assertEquals(0, $res['kelompok_assigned']);

        $this->assertDatabaseHas('ota_donaturs', ['nama' => 'Budi Santoso', 'tipe' => 'insidental']);
        $this->assertDatabaseHas('ota_donaturs', ['nama' => 'Siti Rahma', 'tipe' => 'rutin']);
        $this->assertEquals(0, KelompokAnggota::count());

        @unlink($filePath);
    }

    public function test_import_donatur_dengan_target_kelompok_otomatis_masuk_anggota_dan_set_rutin()
    {
        $filePath = $this->createExcelFile([
            ['Ahmad Fauzi', 'insidental', '0811223344', 'ahmad@test.com', 'Garut', 'Karyawan', ''], // tipe insidental di excel akan di-override jadi rutin
            ['Dewi Sartika', 'rutin', '0811556677', 'dewi@test.com', 'Garut', 'Pengusaha', '250000'], // kolom G spesifik 250rb
        ]);

        $res = OtaExcelService::importDonaturs($filePath, $this->kelompokA->id, 150000);

        $this->assertEquals(2, $res['imported']);
        $this->assertEquals(2, $res['kelompok_assigned']);
        $this->assertEquals(0, $res['kelompok_skipped']);

        // Donatur 1 menggunakan nominal komitmen default modal (150000) dan tipenya otomatis diset 'rutin'
        $ahmad = Donatur::where('nama', 'Ahmad Fauzi')->firstOrFail();
        $this->assertEquals('rutin', $ahmad->tipe);
        $this->assertDatabaseHas('ota_kelompok_anggotas', [
            'kelompok_id' => $this->kelompokA->id,
            'donatur_id' => $ahmad->id,
            'nominal_komitmen' => 150000,
        ]);

        // Donatur 2 menggunakan nominal komitmen dari kolom G Excel (250000)
        $dewi = Donatur::where('nama', 'Dewi Sartika')->firstOrFail();
        $this->assertEquals('rutin', $dewi->tipe);
        $this->assertDatabaseHas('ota_kelompok_anggotas', [
            'kelompok_id' => $this->kelompokA->id,
            'donatur_id' => $dewi->id,
            'nominal_komitmen' => 250000,
        ]);

        @unlink($filePath);
    }

    public function test_import_donatur_lewati_jika_sudah_terdaftar_di_kelompok_yang_sama()
    {
        // Daftarkan donatur terlebih dahulu di kelompokA
        $donaturExisting = Donatur::create([
            'nama' => 'Hendra Setiawan',
            'tipe' => 'rutin',
            'nomor_telepon' => '081333444555',
            'is_aktif' => true,
        ]);

        KelompokAnggota::create([
            'kelompok_id' => $this->kelompokA->id,
            'donatur_id' => $donaturExisting->id,
            'nominal_komitmen' => 200000,
            'status' => 'aktif',
        ]);

        $filePath = $this->createExcelFile([
            ['Hendra Setiawan', 'rutin', '081333444555', 'hendra@test.com', 'Jakarta', '', '300000'],
            ['Donatur Baru', 'rutin', '081999888777', 'baru@test.com', 'Garut', '', '150000'],
        ]);

        $res = OtaExcelService::importDonaturs($filePath, $this->kelompokA->id, 150000);

        $this->assertEquals(1, $res['imported']); // Donatur Baru
        $this->assertEquals(1, $res['updated']);  // Hendra di-update datanya
        $this->assertEquals(1, $res['kelompok_assigned']); // hanya Donatur Baru
        $this->assertEquals(1, $res['kelompok_skipped']); // Hendra dilewati karena sudah ada

        // Keanggotaan Hendra tidak berubah nominal komitmennya (tetap 200000)
        $this->assertDatabaseHas('ota_kelompok_anggotas', [
            'kelompok_id' => $this->kelompokA->id,
            'donatur_id' => $donaturExisting->id,
            'nominal_komitmen' => 200000,
        ]);

        @unlink($filePath);
    }

    public function test_livewire_open_import_donatur_modal_and_import()
    {
        $this->actingAs($this->admin);

        $filePath = $this->createExcelFile([
            ['Zainal Abidin', 'rutin', '082211445566', 'zainal@test.com', 'Garut', '', ''],
        ]);

        $uploadedFile = UploadedFile::fake()->createWithContent('donatur_test.xlsx', file_get_contents($filePath));

        Livewire::test(DonaturKelompokIndex::class)
            ->call('openImportDonaturModal', $this->kelompokB->id)
            ->assertSet('showImportDonaturModal', true)
            ->assertSet('importTargetKelompokId', $this->kelompokB->id)
            ->assertSet('importNominalKomitmen', 150000)
            ->set('fileImportDonatur', $uploadedFile)
            ->call('importDonaturExcel')
            ->assertSet('showImportDonaturModal', false)
            ->assertHasNoErrors()
            ->assertSee('Import Donatur Selesai');

        $zainal = Donatur::where('nama', 'Zainal Abidin')->firstOrFail();
        $this->assertDatabaseHas('ota_kelompok_anggotas', [
            'kelompok_id' => $this->kelompokB->id,
            'donatur_id' => $zainal->id,
            'nominal_komitmen' => 150000,
        ]);

        @unlink($filePath);
    }
}
