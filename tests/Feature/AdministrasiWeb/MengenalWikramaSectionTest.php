<?php

namespace Tests\Feature\AdministrasiWeb;

use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MengenalWikramaSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
    }

    public function test_landing_page_renders_new_mengenal_section_layout(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        // 1. Verifikasi Section & Id
        $response->assertSee('id="mengenal"', false);

        // 2. Verifikasi Headline & Tagline
        $response->assertSee('Mengenal Lebih Dekat');
        $response->assertSee('Menemukan Potensi Sejati');
        $response->assertSee('Menginspirasi');

        // 3. Verifikasi Elemen Collage Organik
        $response->assertSee('mengenal-dashed-ring');
        $response->assertSee('25+ Tahun');
        $response->assertSee('Pengabdian & Mutu Vokasi Unggul');

        // 4. Verifikasi 2 Poin Keunggulan Utama
        $response->assertSee('Kurikulum Vokasi & Kompetensi Keahlian');
        $response->assertSee('Daya Saing & Karakter Santri');

        // 5. Verifikasi CTA & Hotline SPMB WhatsApp
        $response->assertSee('Jelajahi Ekosistem');
        $response->assertSee('+62 813-2331-4430');
        $response->assertSee('https://wa.me/6281323314430', false);
    }
}
