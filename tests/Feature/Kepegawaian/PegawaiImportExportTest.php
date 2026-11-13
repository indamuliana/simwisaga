<?php

namespace Tests\Feature\Kepegawaian;

use App\Models\User;
use App\Modules\Kepegawaian\Livewire\PegawaiCreate;
use App\Modules\Kepegawaian\Livewire\PegawaiEdit;
use App\Modules\Kepegawaian\Livewire\PegawaiIndex;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Services\PegawaiExportService;
use App\Modules\Kepegawaian\Services\PegawaiImportService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\ImportPegawaiInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PegawaiImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_kepegawaian',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_seeder_imports_33_pegawai_and_maps_extended_data_properly(): void
    {
        // Buat akun user Inda terlebih dahulu agar tertaut otomatis
        $userInda = User::create([
            'name' => 'Inda Muliana, S.Kom.',
            'username' => 'inda.muliana',
            'email' => 'inda@wikrama.sch.id',
            'password' => bcrypt('password'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $userInda->assignRole('Guru');

        $this->seed(ImportPegawaiInitialSeeder::class);

        $this->assertDatabaseCount('pegawais', 33);

        // Verify user link untuk Inda
        $inda = Pegawai::where('nama_lengkap', 'like', '%Inda Muliana%')->first();
        $this->assertNotNull($inda);
        $this->assertEquals($userInda->id, $inda->user_id);
        $this->assertEquals('PRW2107170383', $inda->niy);
        $this->assertEquals('PRW2107170383', $inda->niy_label);

        // Verify Tasep Miftah Farid with empty NIY
        $tasep = Pegawai::where('nama_lengkap', 'like', '%Tasep Miftah Farid%')->first();
        $this->assertNotNull($tasep);
        $this->assertNull($tasep->niy);
        $this->assertEquals('-', $tasep->niy_label);

        // Verify NIK truncation warning accessor
        $nikTruncated = Pegawai::where('nik', 'like', '%0000')->first();
        if ($nikTruncated) {
            $this->assertTrue($nikTruncated->perlu_verifikasi_nik);
        }

        // Verify normal NIK does not trigger warning
        $validNikPegawai = Pegawai::whereNotNull('nik')
            ->where('nik', 'not like', '%0000')
            ->first();
        if ($validNikPegawai) {
            $this->assertFalse($validNikPegawai->perlu_verifikasi_nik);
        }
    }

    public function test_export_service_and_livewire_downloads(): void
    {
        $this->seed(ImportPegawaiInitialSeeder::class);

        $exportService = app(PegawaiExportService::class);

        // 1. Template CSV string
        $templateCsv = $exportService->downloadTemplateCsv();
        $this->assertIsString($templateCsv);
        $this->assertStringContainsString('No. Pegawai', $templateCsv);
        $this->assertStringContainsString('Fulan bin Fulan', $templateCsv);

        // 2. CSV Export string
        $csvContent = $exportService->exportCsv();
        $this->assertIsString($csvContent);
        $this->assertStringContainsString('PRW2107170383', $csvContent);

        // 3. XLSX Export file
        $xlsxPath = $exportService->exportXlsx();
        $this->assertFileExists($xlsxPath);
        @unlink($xlsxPath);

        // 4. Livewire Downloads
        Livewire::actingAs($this->admin)
            ->test(PegawaiIndex::class)
            ->call('downloadTemplateCsv')
            ->assertFileDownloaded('Template_Import_Pegawai_Wikrama.csv');

        Livewire::actingAs($this->admin)
            ->test(PegawaiIndex::class)
            ->call('exportCsv')
            ->assertFileDownloaded();

        Livewire::actingAs($this->admin)
            ->test(PegawaiIndex::class)
            ->call('exportXlsx')
            ->assertFileDownloaded();
    }

    public function test_livewire_pegawai_index_import_modal_and_actions(): void
    {
        $this->seed(ImportPegawaiInitialSeeder::class);

        Livewire::actingAs($this->admin)
            ->test(PegawaiIndex::class)
            ->assertSee('Tenaga Kependidikan')
            ->assertSee('Import Data')
            ->assertSee('Export Data')
            ->assertSee('PRW2107170383')
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->call('closeImportModal')
            ->assertSet('showImportModal', false);
    }

    public function test_import_service_can_parse_and_append_data(): void
    {
        $tempCsv = tempnam(sys_get_temp_dir(), 'test_gtk_') . '.csv';

        $output = fopen($tempCsv, 'w');
        fputcsv($output, PegawaiExportService::HEADERS);

        $sampleRow = array_fill(0, count(PegawaiExportService::HEADERS), '');
        $sampleRow[0] = 1; // No. Pegawai
        $sampleRow[1] = 'PRW.99.99'; // NIY
        $sampleRow[2] = 'Guru Testing Baru, S.Pd'; // Nama
        $sampleRow[3] = 'L'; // Jenis Kelamin
        $sampleRow[14] = 'GTY'; // Status Kepegawaian
        $sampleRow[15] = 'Tetap'; // Jenis Kepegawaian
        $sampleRow[16] = 'Bandung'; // Tempat Lahir
        $sampleRow[17] = '1985-05-15'; // Tanggal Lahir
        $sampleRow[22] = '2010-07-01'; // Tgl Pertama Kontrak
        $sampleRow[23] = 'SK-KONTRAK-99'; // No SK Kontrak Pertama
        $sampleRow[33] = 'Jl. Testing Garut'; // Alamat
        $sampleRow[34] = 'Pendidikan Fisika UNPAD'; // Pendidikan Terakhir
        $sampleRow[35] = 'S1'; // Jenjang
        $sampleRow[44] = 'Fisika Terapan'; // Mapel Diampu
        $sampleRow[45] = 'Wakasek'; // Jabatan Struktural
        $sampleRow[46] = '2 Tahun'; // Lama Kontrak Selanjutnya
        $sampleRow[47] = 'Rekomendasi naik gaji'; // Keterangan Perpanjang
        $sampleRow[48] = '07.00 - 16.00'; // Jam Masuk Kerja
        $sampleRow[65] = '2028-06-30'; // Habis Kontrak

        fputcsv($output, $sampleRow);
        fclose($output);

        $importService = app(PegawaiImportService::class);
        $result = $importService->importAppend($tempCsv);
        @unlink($tempCsv);

        $this->assertEquals(1, $result['inserted']);
        $this->assertEquals(0, $result['updated']);

        $pegawai = Pegawai::where('niy', 'PRW.99.99')->first();
        $this->assertNotNull($pegawai);
        $this->assertEquals('Guru Testing Baru, S.Pd', $pegawai->nama_lengkap);
        $this->assertEquals('Fisika Terapan', $pegawai->mapel_diampu);
        $this->assertEquals('S1', $pegawai->jenjang_pendidikan);
        $this->assertEquals('07.00 - 16.00', $pegawai->jam_kerja);
        $this->assertEquals('SK-KONTRAK-99', $pegawai->no_sk_kontrak_pertama);
        $this->assertEquals('2 Tahun', $pegawai->lama_kontrak_selanjutnya);
        $this->assertEquals('Rekomendasi naik gaji', $pegawai->keterangan_perpanjang);
        $this->assertEquals('2028-06-30', $pegawai->habis_kontrak->format('Y-m-d'));
    }

    public function test_livewire_pegawai_create_and_edit_with_extended_fields(): void
    {
        Storage::fake('public');

        $foto = UploadedFile::fake()->image('profil.jpg');

        // Create
        Livewire::actingAs($this->admin)
            ->test(PegawaiCreate::class)
            ->set('nama_lengkap', 'Asep Saepuloh')
            ->set('niy', 'PRW.01.99')
            ->set('nik', '3205011111900001')
            ->set('jenis_kelamin', 'L')
            ->set('jenis_gtk', 'Guru')
            ->set('status_kepegawaian', 'GTY')
            ->set('mapel_diampu', 'Bahasa Sunda')
            ->set('jam_kerja', '07:00 - 15:30')
            ->set('jenjang_pendidikan', 'S1')
            ->set('pendidikan_terakhir', 'S1 Pendidikan Bahasa Daerah')
            ->set('tgl_pertama_kontrak', '2019-01-01')
            ->set('no_sk_kontrak_pertama', 'SK-01-2019')
            ->set('habis_kontrak', '2027-12-31')
            ->set('lama_kontrak_selanjutnya', '1 Tahun')
            ->set('keterangan_perpanjang', 'Direkomendasikan')
            ->set('foto_1', $foto)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $pegawai = Pegawai::where('niy', 'PRW.01.99')->first();
        $this->assertNotNull($pegawai);
        $this->assertEquals('Asep Saepuloh', $pegawai->nama_lengkap);
        $this->assertEquals('Bahasa Sunda', $pegawai->mapel_diampu);
        $this->assertEquals('07:00 - 15:30', $pegawai->jam_kerja);
        $this->assertEquals('S1', $pegawai->jenjang_pendidikan);
        $this->assertEquals('2027-12-31', $pegawai->habis_kontrak->format('Y-m-d'));

        // Edit
        Livewire::actingAs($this->admin)
            ->test(PegawaiEdit::class, ['id' => $pegawai->id])
            ->assertSet('niy', 'PRW.01.99')
            ->assertSet('mapel_diampu', 'Bahasa Sunda')
            ->assertSet('jenjang_pendidikan', 'S1')
            ->set('mapel_diampu', 'Bahasa Sunda & Budaya')
            ->set('lama_kontrak_selanjutnya', '2 Tahun')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $pegawai->refresh();
        $this->assertEquals('Bahasa Sunda & Budaya', $pegawai->mapel_diampu);
        $this->assertEquals('2 Tahun', $pegawai->lama_kontrak_selanjutnya);
    }
}
