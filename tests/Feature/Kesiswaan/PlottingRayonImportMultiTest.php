<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kesiswaan\Livewire\PlottingRayonMassal;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\PlottingRayonExcelService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PlottingRayonImportMultiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rayon $rayon1;
    protected Rayon $rayon2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kesiswaan_import_multi',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->rayon1 = Rayon::firstOrCreate(['nama' => 'Alik 1'], ['wilayah_cakupan' => 'Alik']);
        $this->rayon2 = Rayon::firstOrCreate(['nama' => 'Bale 1'], ['wilayah_cakupan' => 'Bale']);
    }

    private function createExcelFile(array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Multi Rayon');

        foreach ($rows as $rowIndex => $rowData) {
            foreach ($rowData as $colIndex => $val) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValueExplicit($colLetter . ($rowIndex + 1), (string) $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'test_multi_rayon_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    public function test_service_import_multi_rayon_successfully_allocates_and_handles_partial_failures(): void
    {
        $siswa1 = Siswa::create([
            'nama' => 'Siswa Alik Satu',
            'nipd' => '1001',
            'nisn' => '0011001',
            'status' => 'aktif',
            'rayon_id' => null,
        ]);

        $siswa2 = Siswa::create([
            'nama' => 'Siswa Bale Satu',
            'nipd' => '1002',
            'nisn' => '0011002',
            'status' => 'aktif',
            'rayon_id' => null,
        ]);

        $siswa3 = Siswa::create([
            'nama' => 'Siswa Invalid Rayon',
            'nipd' => '1003',
            'nisn' => '0011003',
            'status' => 'aktif',
            'rayon_id' => null,
        ]);

        $rows = [
            ['NIS', 'Kode / Nama Rayon', 'Nama Siswa'],
            ['1001', 'ALIK 1', 'Siswa Alik Satu'], // Case-insensitive match on Rayon
            ['0011002', 'bale 1', 'Siswa Bale Satu'], // Match via NISN
            ['9999', 'Alik 1', 'Siswa Ghost'], // Unmatched NIS
            ['1003', 'Rayon Antah Berantah', 'Siswa Invalid Rayon'], // Invalid Rayon
            ['', 'Alik 1', 'Empty NIS'], // Empty NIS skipped
        ];

        $filePath = $this->createExcelFile($rows);

        $service = new PlottingRayonExcelService();
        $result = $service->importAndPlotMultiRayon($filePath);

        @unlink($filePath);

        $this->assertEquals('multi', $result['mode']);
        $this->assertEquals(2, $result['updated_count']);
        $this->assertEquals(['Alik 1' => 1, 'Bale 1' => 1], $result['per_rayon_counts']);
        $this->assertEquals(['9999'], $result['unmatched_nis']);
        $this->assertCount(1, $result['invalid_rayons']);
        $this->assertEquals('Rayon Antah Berantah', $result['invalid_rayons'][0]['rayon']);

        $this->assertEquals($this->rayon1->id, $siswa1->fresh()->rayon_id);
        $this->assertEquals($this->rayon2->id, $siswa2->fresh()->rayon_id);
        $this->assertNull($siswa3->fresh()->rayon_id);

        $this->assertDatabaseHas('rayon_siswa', [
            'siswa_id' => $siswa1->id,
            'rayon_id' => $this->rayon1->id,
            'alasan_pindah' => 'Import Multi-Rayon Excel',
        ]);
        $this->assertDatabaseHas('rayon_siswa', [
            'siswa_id' => $siswa2->id,
            'rayon_id' => $this->rayon2->id,
            'alasan_pindah' => 'Import Multi-Rayon Excel',
        ]);
    }

    public function test_livewire_can_switch_to_multi_mode_and_import_excel(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Budi Multi',
            'nipd' => '2001',
            'nisn' => '0022001',
            'status' => 'aktif',
            'rayon_id' => null,
        ]);

        $rows = [
            ['NIS', 'Kode / Nama Rayon', 'Nama Siswa'],
            ['2001', 'Alik 1', 'Budi Multi'],
        ];

        $tempPath = $this->createExcelFile($rows);
        $uploadedFile = UploadedFile::fake()->createWithContent('multi_test.xlsx', file_get_contents($tempPath));

        Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->call('openImportModal')
            ->set('importMode', 'multi')
            ->set('fileExcel', $uploadedFile)
            ->call('importExcel')
            ->assertHasNoErrors()
            ->assertSee('Hasil Import Multi-Rayon')
            ->assertSee('Alik 1');

        $this->assertEquals($this->rayon1->id, $siswa->fresh()->rayon_id);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_livewire_download_template_multi_rayon(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->set('importMode', 'multi');

        $responseKosong = $component->call('downloadTemplate', 'kosong');
        $this->assertNotNull($responseKosong);

        $responsePrefill = $component->call('downloadTemplate', 'prefill');
        $this->assertNotNull($responsePrefill);
    }

    public function test_single_rayon_import_mode_still_works_as_expected(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Joko Single',
            'nipd' => '3001',
            'nisn' => '0033001',
            'status' => 'aktif',
            'rayon_id' => null,
        ]);

        $rows = [
            ['NIS'],
            ['3001'],
        ];

        $tempPath = $this->createExcelFile($rows);
        $uploadedFile = UploadedFile::fake()->createWithContent('single_test.xlsx', file_get_contents($tempPath));

        Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->set('importMode', 'single')
            ->set('importTargetRayonId', $this->rayon2->id)
            ->set('fileExcel', $uploadedFile)
            ->call('importExcel')
            ->assertHasNoErrors()
            ->assertSee('Bale 1');

        $this->assertEquals($this->rayon2->id, $siswa->fresh()->rayon_id);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }
}
