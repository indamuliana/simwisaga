<?php

namespace Tests\Feature\AdministrasiWeb;

use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporMutuSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
    }

    public function test_landing_page_renders_rapor_mutu_section_and_animated_progress_bars(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        // 1. Verifikasi Section & Id
        $response->assertSee('id="rapor-mutu"', false);
        $response->assertSee('Rapor Pendidikan Resmi Kemendikdasmen');
        $response->assertSee('Peringkat 20% Teratas Tingkat Nasional');

        // 2. Verifikasi Komponen Progress Bar & Metrik
        $response->assertSee('rapor-progress-track');
        $response->assertSee('rapor-progress-fill');
        $response->assertSee('rapor-counter-val');

        // 3. Verifikasi Metrik Spesifik
        $response->assertSee('Kompetensi Literasi Siswa');
        $response->assertSee('Penyerapan Lulusan Vokasi (BMW)');
        $response->assertSee('Indeks Karakter & Integritas Siswa');
        $response->assertSee('Kompetensi Numerasi & Logika');
        $response->assertSee('Kualitas Pembelajaran Guru');

        // 4. Verifikasi Data Target Animasi
        $response->assertSee('data-progress="98"', false);
        $response->assertSee('data-target="98"', false);
        $response->assertSee('Peringkat Atas Nasional');

        // 5. Verifikasi Penghargaan & Akreditasi
        $response->assertSee('BAN-PDM');
        $response->assertSee('Akreditasi A+');
        $response->assertSee('SMK Pusat Keunggulan');
    }
}
