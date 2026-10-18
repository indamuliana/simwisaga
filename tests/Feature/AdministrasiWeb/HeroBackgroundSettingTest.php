<?php

namespace Tests\Feature\AdministrasiWeb;

use App\Models\User;
use App\Modules\AdministrasiWeb\Livewire\LandingSetting;
use App\Modules\AdministrasiWeb\Models\WebSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroBackgroundSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::findOrCreate('web.landing.edit', 'web');
    }

    public function test_resolve_image_url_handles_various_formats(): void
    {
        // 1. Default fallback when empty
        $url1 = WebSetting::resolveImageUrl('', 'images/web/campus-environment.jpg');
        $this->assertStringContainsString('images/web/campus-environment.jpg', $url1);

        // 2. Direct assets path
        $url2 = WebSetting::resolveImageUrl('images/web/campus-environment.jpg');
        $this->assertStringContainsString('images/web/campus-environment.jpg', $url2);

        // 3. Storage uploaded path
        $url3 = WebSetting::resolveImageUrl('web/hero/test.jpg');
        $this->assertStringContainsString('storage/web/hero/test.jpg', $url3);

        // 4. Absolute URL
        $url4 = WebSetting::resolveImageUrl('https://example.com/banner.jpg');
        $this->assertEquals('https://example.com/banner.jpg', $url4);
    }

    public function test_admin_can_upload_and_reset_hero_background_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->givePermissionTo('web.landing.edit');

        $file = UploadedFile::fake()->image('custom-hero.jpg', 1920, 1080);

        Livewire::actingAs($user)
            ->test(LandingSetting::class)
            ->set('activeTab', 'hero')
            ->set('hero_bg_file', $file)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Pengaturan landing page berhasil disimpan');

        $group = WebSetting::getGroup('hero');
        $this->assertNotNull($group['background_image'] ?? null);
        $this->assertStringStartsWith('web/hero/', $group['background_image']);
        Storage::disk('public')->assertExists($group['background_image']);

        // Now test reset to default
        Livewire::actingAs($user)
            ->test(LandingSetting::class)
            ->call('resetHeroBackground')
            ->assertHasNoErrors();

        $groupAfterReset = WebSetting::getGroup('hero');
        $this->assertEquals('images/web/campus-environment.jpg', $groupAfterReset['background_image']);
    }

    public function test_landing_page_renders_hero_section_with_dark_overlay_and_background(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('hero-bg-overlay');
        $response->assertSee('hero-bg-img');
        $response->assertSee('rgba(2, 6, 23, 0.70)', false);
        $response->assertSee('brightness(0.40)', false);
    }

    public function test_admin_can_adjust_hero_overlay_opacity_and_brightness(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('web.landing.edit');

        // Test custom sliders
        Livewire::actingAs($user)
            ->test(LandingSetting::class)
            ->set('activeTab', 'hero')
            ->set('hero.overlay_opacity', 65)
            ->set('hero.image_brightness', 45)
            ->call('save')
            ->assertHasNoErrors();

        $group = WebSetting::getGroup('hero');
        $this->assertEquals(65, $group['overlay_opacity']);
        $this->assertEquals(45, $group['image_brightness']);

        auth()->logout();
        $response = $this->get('/');
        $response->assertSee('rgba(2, 6, 23, 0.65)', false);
        $response->assertSee('brightness(0.45)', false);

        // Test preset button
        Livewire::actingAs($user)
            ->test(LandingSetting::class)
            ->call('setHeroPreset', 50, 60)
            ->call('save')
            ->assertHasNoErrors();

        $groupPreset = WebSetting::getGroup('hero');
        $this->assertEquals(50, $groupPreset['overlay_opacity']);
        $this->assertEquals(60, $groupPreset['image_brightness']);

        auth()->logout();
        $response2 = $this->get('/');
        $response2->assertSee('rgba(2, 6, 23, 0.50)', false);
        $response2->assertSee('brightness(0.60)', false);
    }
}
