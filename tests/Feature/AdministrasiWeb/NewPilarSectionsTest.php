<?php

namespace Tests\Feature\AdministrasiWeb;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewPilarSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_four_new_pilar_sections_and_sekolah_teladan(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // 1. Section: Tiada pendidikan bermutu, tanpa pendidikan karakter
        $response->assertSee('Tiada pendidikan bermutu, tanpa pendidikan karakter');
        $response->assertSee('pilar-lulusan.jpg');
        $response->assertSee('pilar-kurikulum.png');
        $response->assertSee('Teaching Factory');

        // 2. Section: Tiada pendidikan karakter, tanpa budaya sekolah
        $response->assertSee('Tiada pendidikan karakter, tanpa budaya sekolah');
        $response->assertSee('Telkom University');
        $response->assertSee('Institut Pertanian Bogor (IPB)');

        // 3. Section: Tiada masyarakat pembelajar sekolah, tanpa pemimpin perubahan
        $response->assertSee('Tiada masyarakat pembelajar sekolah, tanpa pemimpin perubahan sekolah');
        $response->assertSee('Program Matrikulasi');
        $response->assertSee('Moving Class Dinamis');
        $response->assertSee('Sistem Belajar Bertiga');

        // 4. Section: Sekolah Teladan Nasional
        $response->assertSee('Sekolah Teladan Nasional');
        $response->assertSee('Pangan Jajan Anak Sehat Regional Barat');
        $response->assertSee('WORLDDIDAC ASIA 2025');
        $response->assertSee('Huawei ICT Academy');
        $response->assertSee('EC-Council Academia Partner');
    }
}
