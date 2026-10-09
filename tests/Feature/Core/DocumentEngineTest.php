<?php

namespace Tests\Feature\Core;

use App\Core\Services\DocumentEngine;
use App\Core\Support\DataMasker;
use App\Models\User;
use App\Modules\Sistem\Models\DocumentTemplate;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_doc',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->operator = User::factory()->create([
            'username' => 'operator_doc',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->operator->assignRole('Operator');

        $this->guru = User::factory()->create([
            'username' => 'guru_doc',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guru->assignRole('Guru');
    }

    public function test_data_masker_sensor_nik_kk_nisn_untuk_user_biasa(): void
    {
        $nik = '3205020807060003';
        $nisn = '0063473065';

        // Untuk guru (user biasa), data disensor
        $maskedNik = DataMasker::maskNik($nik, $this->guru);
        $maskedNisn = DataMasker::maskNisn($nisn, $this->guru);

        $this->assertNotEquals($nik, $maskedNik);
        $this->assertStringContainsString('****', $maskedNik);
        $this->assertEquals('3205********0003', $maskedNik);

        $this->assertNotEquals($nisn, $maskedNisn);
        $this->assertEquals('006*****65', $maskedNisn);
    }

    public function test_data_masker_menampilkan_data_asli_untuk_operator_dan_admin(): void
    {
        $nik = '3205020807060003';
        $nisn = '0063473065';

        $this->assertEquals($nik, DataMasker::maskNik($nik, $this->operator));
        $this->assertEquals($nisn, DataMasker::maskNisn($nisn, $this->operator));
        $this->assertEquals($nik, DataMasker::maskNik($nik, $this->admin));
    }

    public function test_document_engine_menghasilkan_pdf_valid(): void
    {
        $pdf = DocumentEngine::createPdf(
            title: 'DAFTAR SISWA KELAS X PPLG 1',
            content: '<p>Tabel uji siswa</p>',
            options: [
                'template_kode' => 'daftar_siswa',
                'context' => [
                    'wali_kelas' => 'Beni Iskandar, S.Kom.',
                ],
            ]
        );

        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_halaman_template_dokumen_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('sistem.template'))
            ->assertOk()
            ->assertSee('Template Dokumen')
            ->assertSee('rekap_absensi_rombel')
            ->assertSee('surat_peringatan');
    }
}
