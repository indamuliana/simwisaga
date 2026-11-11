<?php

namespace Tests\Feature\Core;

use App\Core\Modules\ModuleRegistry;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarThreeTierMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_menu_for_returns_three_tier_structure(): void
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->create();
        $user->assignRole(User::SUPER_ADMIN_ROLE);

        $registry = app(ModuleRegistry::class);
        $menu = $registry->menuFor($user);

        $this->assertNotEmpty($menu);

        $groupLabels = collect($menu)->pluck('label')->all();

        // Verifikasi kelompok utama ada sesuai permintaan user
        $this->assertContains('ANALISIS', $groupLabels);
        $this->assertContains('KURIKULUM', $groupLabels);
        $this->assertContains('KESISWAAN', $groupLabels);
        $this->assertContains('PENGATURAN', $groupLabels);

        $groupsByKey = collect($menu)->keyBy('label');

        // 1. Verifikasi kelompok ANALISIS
        $analisisModules = collect($groupsByKey['ANALISIS']['modules'])->pluck('name')->all();
        $this->assertContains('AnalitikSpmb', $analisisModules);
        $this->assertContains('DashboardData', $analisisModules);

        // 2. Verifikasi kelompok KURIKULUM
        $kurikulumModules = collect($groupsByKey['KURIKULUM']['modules'])->pluck('name')->all();
        $this->assertContains('KalenderPendidikan', $kurikulumModules);
        $this->assertContains('Penilaian', $kurikulumModules);
        $this->assertContains('SeniBudaya', $kurikulumModules);
        $this->assertContains('Ekstrakurikuler', $kurikulumModules);
        $this->assertContains('Rapor', $kurikulumModules);

        // 3. Verifikasi kelompok KESISWAAN
        $kesiswaanModules = collect($groupsByKey['KESISWAAN']['modules'])->pluck('name')->all();
        $this->assertContains('Kesiswaan', $kesiswaanModules);
        $this->assertContains('Alumni', $kesiswaanModules);
        $this->assertContains('Absensi', $kesiswaanModules);
        $this->assertContains('Prestasi', $kesiswaanModules);

        // 4. Verifikasi kelompok PENGATURAN
        $pengaturanModules = collect($groupsByKey['PENGATURAN']['modules'])->pluck('name')->all();
        $this->assertContains('Akademik', $pengaturanModules);
        $this->assertContains('AdministrasiWeb', $pengaturanModules);
        $this->assertContains('Sistem', $pengaturanModules);
        $this->assertContains('Panduan', $pengaturanModules);

        // Verifikasi struktur setiap modul memiliki submodul (items)
        foreach ($groupsByKey as $group) {
            $this->assertArrayHasKey('modules', $group);
            $this->assertArrayHasKey('items', $group); // backwards compatibility
            foreach ($group['modules'] as $module) {
                $this->assertArrayHasKey('name', $module);
                $this->assertArrayHasKey('label', $module);
                $this->assertArrayHasKey('items', $module);
                $this->assertNotEmpty($module['items']);
            }
        }
    }

    public function test_sidebar_blade_renders_three_tier_accordion(): void
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->create();
        $user->assignRole(User::SUPER_ADMIN_ROLE);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();

        // Memastikan Alpine state untuk accordion 3-tier tersedia di view
        $response->assertSee('openGroup', false);
        $response->assertSee('openModule', false);

        // Memastikan kelompok utama ter-render
        $response->assertSee('ANALISIS');
        $response->assertSee('KURIKULUM');
        $response->assertSee('KESISWAAN');
        $response->assertSee('PENGATURAN');

        // Memastikan kotak pencarian menu sebelum dashboard ter-render
        $response->assertSee('placeholder="Cari menu..."', false);
        $response->assertSee('searchQuery', false);
        $response->assertSee('allMenus', false);

        // Memastikan modul tingkat 2 tidak lagi memakai bullet dot
        $response->assertDontSee('w-1.5 h-1.5 rounded-full', false);
    }
}
