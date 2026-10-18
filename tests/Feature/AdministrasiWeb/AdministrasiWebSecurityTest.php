<?php

namespace Tests\Feature\AdministrasiWeb;

use App\Modules\AdministrasiWeb\Models\WebArticle;
use App\Modules\AdministrasiWeb\Models\WebPage;
use App\Modules\AdministrasiWeb\Support\WebSecurity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrasiWebSecurityTest extends TestCase
{

    public function test_admin_routes_require_authentication(): void
    {
        $routes = [
            '/web-admin/landing',
            '/web-admin/articles',
            '/web-admin/articles/create',
            '/web-admin/menus',
            '/web-admin/pages',
            '/web-admin/pages/create',
            '/web-admin/facilities',
            '/web-admin/testimonials',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_html_sanitizer_removes_dangerous_scripts_and_events(): void
    {
        $malicious = '<p>Halo Dunia</p><script>alert("hack")</script><a href="javascript:void(0)" onclick="stealCookies()">Link</a><img src="x" onerror="badCode()">';
        $cleaned = WebSecurity::cleanHtml($malicious);

        $this->assertStringNotContainsString('<script>', $cleaned);
        $this->assertStringNotContainsString('alert("hack")', $cleaned);
        $this->assertStringNotContainsString('onclick', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
        $this->assertStringNotContainsString('javascript:', $cleaned);
        $this->assertStringContainsString('<p>Halo Dunia</p>', $cleaned);
    }
}
