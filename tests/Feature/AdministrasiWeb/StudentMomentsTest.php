<?php

namespace Tests\Feature\AdministrasiWeb;

use App\Models\User;
use App\Modules\AdministrasiWeb\Livewire\LandingSetting;
use App\Modules\AdministrasiWeb\Models\WebSetting;
use Livewire\Livewire;
use Tests\TestCase;

class StudentMomentsTest extends TestCase
{
    /**
     * Test that landing page renders the JIS-style Dinamika Kehidupan Siswa single-row carousel.
     */
    public function test_landing_page_renders_student_moments_carousel(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="dinamika-siswa"', false);
        $response->assertSee('Dinamika Kehidupan Siswa');
        $response->assertSee('scrollPrev()', false);
        $response->assertSee('scrollNext()', false);
        $response->assertSee('rotate-45', false);
        $response->assertSee('snap-x', false);
        $response->assertSee('dinamika-fullwidth-wrapper', false);
        $response->assertSee('student-moment-card', false);
        $response->assertSee('width: 286px', false);
        $response->assertSee('height: 480px', false);
    }

    /**
     * Test that admin can access student moments tab and manage cards via Livewire LandingSetting.
     */
    public function test_admin_can_manage_student_moments_cards_via_landing_setting(): void
    {
        $admin = new User([
            'id' => 1,
            'name' => 'Admin Web',
            'email' => 'admin@wikrama.sch.id',
            'role' => 'superadmin',
        ]);

        $test = Livewire::actingAs($admin)
            ->test(LandingSetting::class)
            ->set('activeTab', 'student_moments')
            ->assertSee('Daftar Card Dinamika Siswa')
            ->call('openCreateCardModal')
            ->assertSet('showCardModal', true)
            ->set('cardForm.title', 'Kompetisi Robotika & AI Vokasi Nasional')
            ->set('cardForm.desc', 'Siswa jurusan PPLG dan TJKT menyabet predikat Juara Umum dalam ajang Inovasi Teknologi Nasional.')
            ->set('cardForm.url', '#robotika-ai')
            ->set('cardForm.cta_text', 'Pelajari Prestasi Ini')
            ->set('cardForm.target', '_self')
            ->set('cardForm.badge', 'Juara Umum AI')
            ->set('cardForm.is_active', true)
            ->call('saveCard')
            ->assertHasNoErrors()
            ->assertSet('showCardModal', false);

        // Verify the saved card in student_moments
        $moments = WebSetting::getGroup('student_moments');
        $this->assertNotEmpty($moments['items'] ?? []);

        $lastCard = end($moments['items']);
        $this->assertEquals('Kompetisi Robotika & AI Vokasi Nasional', $lastCard['title']);
        $this->assertEquals('Pelajari Prestasi Ini', $lastCard['cta_text']);
        $this->assertEquals('#robotika-ai', $lastCard['url']);

        // Test editing card
        $lastIdx = count($moments['items']) - 1;
        Livewire::actingAs($admin)
            ->test(LandingSetting::class)
            ->set('activeTab', 'student_moments')
            ->call('openEditCardModal', $lastIdx)
            ->assertSet('showCardModal', true)
            ->assertSet('cardForm.title', 'Kompetisi Robotika & AI Vokasi Nasional')
            ->set('cardForm.title', 'Kompetisi Robotika & AI Vokasi Nasional (Piala Emas)')
            ->call('saveCard')
            ->assertHasNoErrors();

        $updatedMoments = WebSetting::getGroup('student_moments');
        $this->assertEquals('Kompetisi Robotika & AI Vokasi Nasional (Piala Emas)', $updatedMoments['items'][$lastIdx]['title']);

        // Test toggle card active
        Livewire::actingAs($admin)
            ->test(LandingSetting::class)
            ->call('toggleCardActive', $lastIdx);

        $toggledMoments = WebSetting::getGroup('student_moments');
        $this->assertFalse($toggledMoments['items'][$lastIdx]['is_active']);

        // Test delete card
        Livewire::actingAs($admin)
            ->test(LandingSetting::class)
            ->call('deleteCard', $lastIdx);

        $afterDeleteMoments = WebSetting::getGroup('student_moments');
        $this->assertCount($lastIdx, $afterDeleteMoments['items']);
    }

    /**
     * Test that customized cards render on the public landing page with proper links and hover attributes.
     */
    public function test_landing_page_renders_customized_student_moment_cards(): void
    {
        WebSetting::setGroup('student_moments', [
            'tag' => 'Dinamika Kehidupan Siswa',
            'title' => 'Momen Prestasi Siswa Terpadu',
            'subtitle' => 'Eksplorasi kegiatan santri dan inovasi vokasi.',
            'items' => [
                [
                    'title' => 'Kunjungan Industri Google Indonesia',
                    'desc' => 'Studi lapangan dan mentorship eksklusif bagi siswa kejuruan teknologi.',
                    'image' => 'images/web/hero-pplg.jpg',
                    'url' => 'https://smkwikrama1garut.sch.id/kunjungan-google',
                    'cta_text' => 'Lihat Galeri Kunjungan',
                    'target' => '_blank',
                    'badge' => 'Google Cloud Mentorship',
                    'is_active' => true,
                ],
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Kunjungan Industri Google Indonesia');
        $response->assertSee('Studi lapangan dan mentorship eksklusif bagi siswa kejuruan teknologi.');
        $response->assertSee('Lihat Galeri Kunjungan');
        $response->assertSee('https://smkwikrama1garut.sch.id/kunjungan-google');
        $response->assertSee('Google Cloud Mentorship');
    }
}
