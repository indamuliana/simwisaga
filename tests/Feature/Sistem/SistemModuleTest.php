<?php

namespace Tests\Feature\Sistem;

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->admin = User::factory()->create([
            'must_change_password' => false,
        ]);
        $this->admin->assignRole(User::SUPER_ADMIN_ROLE);
    }

    public function test_dashboard_tampil_dengan_menu_sistem(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Identitas Sekolah');
        $response->assertSee('Hak Akses');
        $response->assertSee('Pengguna');
        $response->assertSee('Log Aktivitas');
    }

    public function test_halaman_identitas_sekolah_tampil(): void
    {
        $response = $this->actingAs($this->admin)->get('/sistem/sekolah');
        $response->assertOk();
        $response->assertSee('Identitas Sekolah');
        $response->assertSee('Kop surat');
    }

    public function test_halaman_pengguna_tampil(): void
    {
        $response = $this->actingAs($this->admin)->get('/sistem/pengguna');
        $response->assertOk();
        $response->assertSee('Pengelolaan Pengguna');
    }

    public function test_halaman_hak_akses_tampil(): void
    {
        $response = $this->actingAs($this->admin)->get('/sistem/hak-akses');
        $response->assertOk();
        $response->assertSee('Matriks Hak Akses');
    }

    public function test_halaman_log_aktivitas_tampil(): void
    {
        $response = $this->actingAs($this->admin)->get('/sistem/log-aktivitas');
        $response->assertOk();
        $response->assertSee('Log Aktivitas');
    }
}
