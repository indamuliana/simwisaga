<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Livewire\SiswaCustomExport;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\SiswaCustomExporter;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiswaCustomExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rayon $rayon1;
    protected Rayon $rayon2;
    protected ?Rombel $rombelX = null;
    protected ?Rombel $rombelXI = null;
    protected Program $program1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_custom_export',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->rayon1 = Rayon::firstOrCreate(['nama' => 'Rayon Sukajadi 1'], ['wilayah_cakupan' => 'Sukajadi']);
        $this->rayon2 = Rayon::firstOrCreate(['nama' => 'Rayon Sukajadi 2'], ['wilayah_cakupan' => 'Sukajadi']);

        $this->rombelX = Rombel::where('tingkat', 'X')->first();
        if (! $this->rombelX) {
            $ta = \App\Modules\Akademik\Models\TahunAjaran::first();
            $jurusan = \App\Modules\Akademik\Models\Jurusan::first();
            $this->rombelX = Rombel::create([
                'nama' => 'X-PPLG-1',
                'tingkat' => 'X',
                'tahun_ajaran_id' => $ta->id,
                'jurusan_id' => $jurusan->id,
                'nomor_urut' => 99,
                'kapasitas' => 36,
            ]);
        }

        $this->rombelXI = Rombel::where('tingkat', 'XI')->first();
        if (! $this->rombelXI) {
            $ta = \App\Modules\Akademik\Models\TahunAjaran::first();
            $jurusan = \App\Modules\Akademik\Models\Jurusan::first();
            $this->rombelXI = Rombel::create([
                'nama' => 'XI-PPLG-1',
                'tingkat' => 'XI',
                'tahun_ajaran_id' => $ta->id,
                'jurusan_id' => $jurusan->id,
                'nomor_urut' => 98,
                'kapasitas' => 36,
            ]);
        }

        $this->program1 = Program::firstOrCreate(['nama' => 'Unggulan'], ['is_active' => true]);
    }

    public function test_page_can_be_accessed_by_authorized_user(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.download-custom'))
            ->assertOk()
            ->assertSee('Unduh Data Siswa Kustom');
    }

    public function test_unauthorized_user_is_forbidden(): void
    {
        $user = User::factory()->create([
            'username' => 'user_polos',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($user)
            ->get(route('kesiswaan.download-custom'))
            ->assertForbidden();
    }

    public function test_multi_select_filters_accurately_filter_students(): void
    {
        $siswa1 = Siswa::create([
            'nama' => 'Siswa Kelas X Aktif',
            'nipd' => '1101',
            'nisn' => '001101',
            'status' => 'aktif',
            'rombel_id' => $this->rombelX->id,
            'rayon_id' => $this->rayon1->id,
            'program_id' => $this->program1->id,
        ]);

        $siswa2 = Siswa::create([
            'nama' => 'Siswa Kelas XI Aktif',
            'nipd' => '1102',
            'nisn' => '001102',
            'status' => 'aktif',
            'rombel_id' => $this->rombelXI->id,
            'rayon_id' => $this->rayon2->id,
            'program_id' => $this->program1->id,
        ]);

        $siswa3 = Siswa::create([
            'nama' => 'Siswa Lulus',
            'nipd' => '1103',
            'nisn' => '001103',
            'status' => 'lulus',
            'rombel_id' => $this->rombelXI->id,
            'rayon_id' => $this->rayon1->id,
        ]);

        // Default: status aktif -> siswa1 dan siswa2
        Livewire::actingAs($this->admin)
            ->test(SiswaCustomExport::class)
            ->assertViewHas('totalCount', 2)
            // Filter hanya Tingkat X
            ->set('filterTingkat', ['X'])
            ->assertViewHas('totalCount', 1)
            // Filter Status termasuk Lulus
            ->set('filterTingkat', ['X', 'XI'])
            ->set('filterStatus', ['aktif', 'lulus'])
            ->assertViewHas('totalCount', 3)
            // Filter Rayon Sukajadi 1
            ->set('filterRayon', [(string) $this->rayon1->id])
            ->assertViewHas('totalCount', 2);
    }

    public function test_column_selection_manipulation_and_presets(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SiswaCustomExport::class)
            ->call('clearColumns')
            ->assertSet('selectedColumns', [])
            // Tambah kolom satu per satu
            ->call('addColumn', 'nama')
            ->call('addColumn', 'nipd')
            ->assertSet('selectedColumns', ['nama', 'nipd'])
            // Geser urutan
            ->call('moveColumnDown', 'nama')
            ->assertSet('selectedColumns', ['nipd', 'nama'])
            ->call('moveColumnUp', 'nama')
            ->assertSet('selectedColumns', ['nama', 'nipd'])
            // Hapus kolom
            ->call('removeColumn', 'nama')
            ->assertSet('selectedColumns', ['nipd'])
            // Terapkan preset Kontak & Alamat
            ->call('applyPreset', 'kontak_alamat')
            ->assertCount('selectedColumns', 13);
    }

    public function test_export_service_xlsx_and_csv_generation(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Ahmad Exporter',
            'nipd' => '099123',
            'nisn' => '0099887766',
            'nik' => '3201123456780001',
            'status' => 'aktif',
            'rombel_id' => $this->rombelX->id,
            'rayon_id' => $this->rayon1->id,
            'hp' => '081234567890',
            'alamat' => 'Jl. Pahlawan No. 12',
        ]);

        $exporter = new SiswaCustomExporter();
        $columns = ['nipd', 'nisn', 'nik', 'nama', 'hp', 'rombel', 'rayon', 'status'];

        // Test XLSX
        $spreadsheet = $exporter->exportXlsx(collect([$siswa]), $columns);
        $this->assertNotNull($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();
        $this->assertEquals('No', $sheet->getCell('A1')->getValue());
        $this->assertEquals('NIS / NIPD', $sheet->getCell('B1')->getValue());
        $this->assertEquals('Nama Lengkap', $sheet->getCell('E1')->getValue());
        $this->assertEquals('099123', $sheet->getCell('B2')->getValue());
        $this->assertEquals('Ahmad Exporter', $sheet->getCell('E2')->getValue());

        // Test CSV
        $csvContent = $exporter->exportCsv(collect([$siswa]), $columns);
        $this->assertStringContainsString('NIS / NIPD', $csvContent);
        $this->assertStringContainsString('Ahmad Exporter', $csvContent);
        $this->assertStringContainsString('081234567890', $csvContent);
    }

    public function test_livewire_download_excel_and_csv_actions(): void
    {
        Siswa::create([
            'nama' => 'Budi Tester',
            'nipd' => '8881',
            'nisn' => '8881001',
            'status' => 'aktif',
            'rombel_id' => $this->rombelX->id,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(SiswaCustomExport::class);

        $responseExcel = $component->call('downloadExcel');
        $this->assertNotNull($responseExcel);

        $responseCsv = $component->call('downloadCsv');
        $this->assertNotNull($responseCsv);
    }
}
