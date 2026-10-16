<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\PenilaianInput;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\NilaiImporter;
use App\Modules\Penilaian\Services\NilaiTemplateExporter;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PenilaianImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected PenugasanMengajar $pmGanjil;
    protected PenugasanMengajar $pmGenap;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected Rombel $rombel1;
    protected Rombel $rombel2;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected TujuanPembelajaran $tp1;
    protected TujuanPembelajaran $tp2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_import_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruUser->assignRole('Super Admin');

        $ta = TahunAjaran::first();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->first();
        $this->semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->first();

        $jurusan = Jurusan::first();
        $this->rombel1 = Rombel::where('tahun_ajaran_id', $ta->id)->first();
        if (!$this->rombel1) {
            $this->rombel1 = Rombel::create([
                'tahun_ajaran_id' => $ta->id,
                'jurusan_id' => $jurusan->id,
                'tingkat' => 'X',
                'nomor_urut' => 91,
                'nama' => 'X HTL 1',
                'kapasitas' => 36,
            ]);
        }

        $this->rombel2 = Rombel::where('tahun_ajaran_id', $ta->id)->where('id', '!=', $this->rombel1->id)->first();
        if (!$this->rombel2) {
            $this->rombel2 = Rombel::create([
                'tahun_ajaran_id' => $ta->id,
                'jurusan_id' => $jurusan->id,
                'tingkat' => 'X',
                'nomor_urut' => 92,
                'nama' => 'X HTL 2',
                'kapasitas' => 36,
            ]);
        }

        $mapel = Mapel::first();
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Guru Pengampu Import',
            'nip' => '199501012020011003',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->pmGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 3,
        ]);

        $this->pmGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 3,
        ]);

        // Siswa 1 di Rombel 1
        $this->siswa1 = Siswa::create([
            'nama' => 'Ahmad Santoso',
            'nipd' => '10001001',
            'nisn' => '0011223344',
            'rombel_id' => $this->rombel1->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);
        RombelSiswa::create([
            'tahun_ajaran_id' => $ta->id,
            'rombel_id' => $this->rombel1->id,
            'siswa_id' => $this->siswa1->id,
        ]);

        // Siswa 2 di Rombel 2 (Multi-Rombel target)
        $this->siswa2 = Siswa::create([
            'nama' => 'Budi Permana',
            'nipd' => '10002002',
            'nisn' => '0011223355',
            'rombel_id' => $this->rombel2->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);
        RombelSiswa::create([
            'tahun_ajaran_id' => $ta->id,
            'rombel_id' => $this->rombel2->id,
            'siswa_id' => $this->siswa2->id,
        ]);

        // Buat TP yang dinaungi AH 1 pada Semester Ganjil
        $this->tp1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Memahami konsep dasar akuntansi keuangan.',
            'alokasi_ah' => 'AH 1',
        ]);

        $this->tp2 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 2',
            'deskripsi' => 'Menerapkan pencatatan jurnal umum.',
            'alokasi_ah' => 'AH 1',
        ]);
    }

    /**
     * Helper untuk membuat file Excel sementara
     */
    protected function createTemporaryExcelFile(array $headers, array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tulis title info di baris 1-3
        $sheet->setCellValue('A1', 'TEMPLATE TEST');
        $sheet->setCellValue('A2', 'INFO');

        // Tulis header di baris 5
        foreach ($headers as $colIdx => $h) {
            $sheet->setCellValueExplicit([$colIdx + 1, 5], $h, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

        // Tulis rows di baris 6 dst
        foreach ($rows as $rIdx => $row) {
            $rowNum = 6 + $rIdx;
            foreach ($row as $colIdx => $val) {
                $sheet->setCellValueExplicit([$colIdx + 1, $rowNum], (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'test_nilai_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpPath);

        return $tmpPath;
    }

    public function test_template_exporter_menghasilkan_data_siswa_dan_kolom_terpilih(): void
    {
        $exporter = new NilaiTemplateExporter(
            $this->pmGanjil,
            ['AH 1', 'STS 1', 'SAS'],
            'current'
        );

        $siswas = $exporter->getSiswas();
        $this->assertCount(1, $siswas);
        $this->assertEquals($this->siswa1->id, $siswas->first()->id);

        $spreadsheet = $exporter->buildSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Periksa header kolom
        $this->assertEquals('No', $sheet->getCell('A5')->getValue());
        $this->assertEquals('NIS', $sheet->getCell('B5')->getValue());
        $this->assertEquals('Nama Siswa', $sheet->getCell('C5')->getValue());
        $this->assertEquals('Rombel', $sheet->getCell('D5')->getValue());
        $this->assertEquals('AH 1', $sheet->getCell('E5')->getValue());
        $this->assertEquals('STS 1', $sheet->getCell('F5')->getValue());
        $this->assertEquals('SAS', $sheet->getCell('G5')->getValue());

        // Periksa data baris pertama
        $this->assertEquals('10001001', $sheet->getCell('B6')->getValue());
        $this->assertEquals('Ahmad Santoso', $sheet->getCell('C6')->getValue());
    }

    public function test_importer_memvalidasi_dan_menyimpan_nilai_ah_serta_sinkron_ke_tp(): void
    {
        $filePath = $this->createTemporaryExcelFile(
            ['No', 'NIS', 'Nama Siswa', 'Rombel', 'AH 1', 'STS 1', 'SAS'],
            [
                ['1', '10001001', 'Ahmad Santoso', 'X HTL 1', '85', '88', '90'],
            ]
        );

        $importer = new NilaiImporter();
        $parsed = $importer->validateAndParse($filePath, $this->pmGanjil, ['AH 1', 'STS 1', 'SAS']);

        $this->assertTrue($parsed['success']);
        $this->assertEquals(1, $parsed['valid_count']);
        $this->assertEquals(0, $parsed['error_count']);
        $this->assertEquals(85, $parsed['valid_rows'][0]['scores']['AH 1']);

        // Eksekusi commit import
        $res = $importer->commitImport($parsed['valid_rows'], $this->pmGanjil);
        $this->assertEquals(1, $res['saved_students']);

        // 1. Cek tabel NilaiAsesmenHarian
        $this->assertDatabaseHas('nilai_asesmen_harians', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 85,
        ]);

        // 2. Cek sinkronisasi ke NilaiFormatif untuk TP 1 dan TP 2
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 85,
        ]);
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp2->id,
            'nilai' => 85,
        ]);

        // 3. Cek tabel NilaiSumatif
        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 88,
            'sas' => 90,
        ]);

        @unlink($filePath);
    }

    public function test_importer_mendukung_multi_rombel_secara_otomatis_via_nis(): void
    {
        // Berkas Excel berisi siswa dari Rombel 1 dan Rombel 2 sekaligus
        $filePath = $this->createTemporaryExcelFile(
            ['No', 'NIS', 'Nama Siswa', 'Rombel', 'AH 1', 'STS 1'],
            [
                ['1', '10001001', 'Ahmad Santoso', 'X HTL 1', '80', '82'],
                ['2', '10002002', 'Budi Permana', 'X HTL 2', '92', '95'],
            ]
        );

        $importer = new NilaiImporter();
        $parsed = $importer->validateAndParse($filePath, $this->pmGanjil);

        $this->assertTrue($parsed['success']);
        $this->assertEquals(2, $parsed['valid_count']);

        $res = $importer->commitImport($parsed['valid_rows'], $this->pmGanjil);
        $this->assertEquals(2, $res['saved_students']);
        $this->assertEquals(2, $res['affected_rombels_count']);

        // Siswa 2 (Rombel 2) otomatis tersimpan ke penugasan mengajar rombel 2
        $pmRombel2 = PenugasanMengajar::where('rombel_id', $this->rombel2->id)
            ->where('mapel_id', $this->pmGanjil->mapel_id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $this->assertNotNull($pmRombel2);
        $this->assertDatabaseHas('nilai_asesmen_harians', [
            'siswa_id' => $this->siswa2->id,
            'penugasan_mengajar_id' => $pmRombel2->id,
            'kode_ah' => 'AH 1',
            'nilai' => 92,
        ]);

        @unlink($filePath);
    }

    public function test_template_exporter_menghasilkan_kolom_tp_dan_nh_sesuai_penugasan(): void
    {
        $cols = NilaiTemplateExporter::getColumnsByScope($this->pmGanjil, 'periode_aktif', '9M1');
        $this->assertEquals(['TP 1', 'TP 2', 'NH 9M1', 'STS 1'], $cols);

        $colsGanjil = NilaiTemplateExporter::getColumnsByScope($this->pmGanjil, 'ganjil');
        $this->assertEquals(['TP 1', 'TP 2', 'NH 9M1', 'STS 1', 'NH 9M2', 'SAS'], $colsGanjil);

        $exporter = new NilaiTemplateExporter($this->pmGanjil, $cols);
        $spreadsheet = $exporter->buildSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertEquals('TP 1', $sheet->getCell('E5')->getValue());
        $this->assertEquals('TP 2', $sheet->getCell('F5')->getValue());
        $this->assertEquals('NH 9M1', $sheet->getCell('G5')->getValue());
        $this->assertEquals('STS 1', $sheet->getCell('H5')->getValue());

        // Verifikasi keberadaan comment informatif
        $this->assertTrue($sheet->getComment('E5')->getText()->getPlainText() !== '');
        $this->assertStringContainsString('Memahami konsep dasar', $sheet->getComment('E5')->getText()->getPlainText());
    }

    public function test_importer_memvalidasi_dan_menyimpan_nilai_langsung_per_tp_dan_nh(): void
    {
        $filePath = $this->createTemporaryExcelFile(
            ['No', 'NIS', 'Nama Siswa', 'Rombel', 'TP 1', 'TP 2', 'NH 9M1', 'STS 1'],
            [
                ['1', '10001001', 'Ahmad Santoso', 'X HTL 1', '85', '90', '95', '88'],
            ]
        );

        $importer = new NilaiImporter();
        $parsed = $importer->validateAndParse($filePath, $this->pmGanjil, ['TP 1', 'TP 2', 'NH 9M1', 'STS 1']);

        $this->assertTrue($parsed['success']);
        $this->assertEquals(1, $parsed['valid_count']);
        $this->assertEquals(0, $parsed['error_count']);
        $this->assertEquals(['TP 1', 'TP 2', 'NH 9M1', 'STS 1'], $parsed['detected_columns']);
        $this->assertEquals(85, $parsed['valid_rows'][0]['scores']['TP 1']);
        $this->assertEquals(90, $parsed['valid_rows'][0]['scores']['TP 2']);
        $this->assertEquals(95, $parsed['valid_rows'][0]['scores']['NH 9M1']);
        $this->assertEquals(88, $parsed['valid_rows'][0]['scores']['STS 1']);

        $res = $importer->commitImport($parsed['valid_rows'], $this->pmGanjil);
        $this->assertEquals(1, $res['saved_students']);

        // Verifikasi NilaiFormatif tersimpan langsung per TP
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 85,
        ]);
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp2->id,
            'nilai' => 90,
        ]);

        // Verifikasi NilaiHarian tersimpan untuk 9M1
        $this->assertDatabaseHas('nilai_harians', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'target_periode' => '9M1',
            'nilai' => 95,
        ]);

        // Verifikasi NilaiSumatif STS 1 tersimpan
        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 88,
        ]);

        @unlink($filePath);
    }

    public function test_importer_mengenali_berbagai_format_header_tp_dan_variasi_penamaan(): void
    {
        $filePath = $this->createTemporaryExcelFile(
            ['No', 'NIS', 'Nama Siswa', 'Rombel', 'TP 1: Memahami dasar', 'TP 2 - Jurnal', 'Harian', 'STS'],
            [
                ['1', '10001001', 'Ahmad Santoso', 'X HTL 1', '79', '84', '92', '80'],
            ]
        );

        $importer = new NilaiImporter();
        $parsed = $importer->validateAndParse($filePath, $this->pmGanjil);

        $this->assertTrue($parsed['success']);
        $this->assertEquals(1, $parsed['valid_count']);
        $this->assertContains('TP 1', $parsed['detected_columns']);
        $this->assertContains('TP 2', $parsed['detected_columns']);
        $this->assertContains('STS 1', $parsed['detected_columns']);

        $res = $importer->commitImport($parsed['valid_rows'], $this->pmGanjil);
        $this->assertEquals(1, $res['saved_students']);

        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 79,
        ]);
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp2->id,
            'nilai' => 84,
        ]);

        @unlink($filePath);
    }

    public function test_livewire_alur_import_modal_buka_tutup_preset_dan_preview(): void
    {
        $this->actingAs($this->guruUser);

        $filePath = $this->createTemporaryExcelFile(
            ['No', 'NIS', 'Nama Siswa', 'Rombel', 'TP 1', 'NH 9M1', 'STS 1'],
            [
                ['1', '10001001', 'Ahmad Santoso', 'X HTL 1', '78', '95', '85'],
            ]
        );

        $uploadedFile = UploadedFile::fake()->createWithContent('nilai_siswa.xlsx', file_get_contents($filePath));

        Livewire::withQueryParams(['tab' => 'formatif'])
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->assertSee('Import Nilai Excel')
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->assertSet('importStep', 'select')
            // Coba preset ganjil
            ->call('setImportPreset', 'ganjil')
            ->assertSet('importSelectedColumns', ['TP 1', 'TP 2', 'NH 9M1', 'STS 1', 'NH 9M2', 'SAS'])
            // Coba preset periode aktif (9M1)
            ->call('setImportPreset', 'periode_aktif')
            ->assertSet('importSelectedColumns', ['TP 1', 'TP 2', 'NH 9M1', 'STS 1'])
            // Upload & preview
            ->set('importFile', $uploadedFile)
            ->call('uploadAndPreview')
            ->assertSet('importStep', 'preview')
            ->assertSee('Total Baris Siswa')
            ->assertSee('Siswa Siap Diimpor')
            // Eksekusi import
            ->call('executeImport')
            ->assertHasNoErrors()
            ->assertSet('showImportModal', false)
            ->assertSee('Berhasil mengimpor nilai untuk');

        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 78,
        ]);

        $this->assertDatabaseHas('nilai_harians', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'target_periode' => '9M1',
            'nilai' => 95,
        ]);

        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 85,
        ]);

        @unlink($filePath);
    }
}
