<?php

namespace Tests\Feature\AdministrasiWeb;

use App\Models\User;
use App\Modules\AdministrasiWeb\Livewire\MenuIndex;
use App\Modules\AdministrasiWeb\Models\WebMenu;
use Livewire\Livewire;
use Tests\TestCase;

class LandingPageAccessTest extends TestCase
{
    /**
     * Test that guests can view the landing page without redirect.
     */
    public function test_guest_can_access_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Masuk Portal');
        $response->assertSee('Daftar SPMB');
        $response->assertSee('Agenda Kegiatan Sekolah Terdekat');
    }

    /**
     * Test that authenticated users are NOT forcibly redirected to dashboard
     * and can view the public landing page with a direct Dashboard button.
     */
    public function test_authenticated_user_can_access_landing_page(): void
    {
        $user = new User([
            'id' => 999,
            'name' => 'Staf Pengajar Wikrama',
            'email' => 'guru@wikrama.sch.id',
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->get('/');

        // Should NOT be redirected to /dashboard
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Staf Pengajar Wikrama');
        $response->assertDontSee('Location: http://localhost/dashboard', false);
    }

    /**
     * Test that the redesigned Agenda section contains event items.
     */
    public function test_agenda_section_renders_events(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="agenda"', false);
        $response->assertSee('Unduh Kalender Akademik (PDF)');
    }

    /**
     * Test that WebMenu automatically seeds standard menus and renders hierarchical dropdowns.
     */
    public function test_dynamic_menus_seed_and_render_dropdowns(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Header menus
        $response->assertSee('Profil');
        $response->assertSee('Jurusan');
        $response->assertSee('Kesiswaan');
        $response->assertSee('Informasi');
        $response->assertSee('Kontak');

        // Submenus under dropdowns
        $response->assertSee('Filosofi Pendidikan');
        $response->assertSee('PPLG');
        $response->assertSee('TJKT');
        $response->assertSee('Bisnis Digital');
        $response->assertSee('Perhotelan');
    }

    /**
     * Test that admin can manage hierarchical menus via Livewire MenuIndex.
     */
    public function test_admin_can_manage_hierarchical_menus(): void
    {
        $admin = new User([
            'id' => 1,
            'name' => 'Admin Web',
            'email' => 'admin@wikrama.sch.id',
            'role' => 'superadmin',
        ]);

        // Seed initial menus if not already seeded
        WebMenu::seedDefaultMenus();

        // Find or create parent
        $parent = WebMenu::where('lokasi', 'header')->whereNull('parent_id')->first();
        $this->assertNotNull($parent);

        // Test creating sub-menu under parent via Livewire
        Livewire::actingAs($admin)
            ->test(MenuIndex::class)
            ->call('openCreateModal', $parent->id)
            ->assertSet('parent_id', $parent->id)
            ->set('label', 'Program Unggulan Baru')
            ->set('url', '#program-unggulan')
            ->call('save')
            ->assertHasNoErrors();

        // Verify child menu is saved in DB
        $child = WebMenu::where('label', 'Program Unggulan Baru')->first();
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->parent_id);
    }
}
