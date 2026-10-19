<?php

namespace Tests\Feature\Core;

use App\Core\Livewire\Auth\Login;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_tampil(): void
    {
        $this->get('/login')->assertOk()->assertSee('SIRAGA');
    }

    public function test_halaman_depan_landing_page_tampil(): void
    {
        $this->get('/')->assertOk()->assertSee('SMK Wikrama 1 Garut')->assertSee('Kompetensi Keahlian');
    }

    public function test_guest_diarahkan_ke_login_saat_akses_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_login_berhasil_dengan_username(): void
    {
        User::factory()->create(['username' => '0063473065', 'password' => 'Rahasia123']);

        Livewire::test(Login::class)
            ->set('username', '0063473065')
            ->set('password', 'Rahasia123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create(['username' => 'budi', 'password' => 'Rahasia123']);

        Livewire::test(Login::class)
            ->set('username', 'budi')
            ->set('password', 'salah')
            ->call('login')
            ->assertHasErrors('username');

        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        User::factory()->create(['username' => 'nonaktif', 'password' => 'Rahasia123', 'is_active' => false]);

        Livewire::test(Login::class)
            ->set('username', 'nonaktif')
            ->set('password', 'Rahasia123')
            ->call('login')
            ->assertHasErrors('username');

        $this->assertGuest();
    }

    public function test_login_dibatasi_setelah_5_kali_gagal(): void
    {
        User::factory()->create(['username' => 'budi', 'password' => 'Rahasia123']);

        $component = Livewire::test(Login::class)->set('username', 'budi')->set('password', 'salah');
        for ($i = 0; $i < 5; $i++) {
            $component->call('login');
        }

        // Percobaan ke-6 ditolak walau password benar.
        $component->set('password', 'Rahasia123')->call('login')->assertHasErrors('username');
        $this->assertGuest();
    }

    public function test_password_awal_wajib_diganti(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('password.change'));
        $this->actingAs($user)->get('/ganti-password')->assertOk();
    }

    public function test_ganti_password_menghapus_flag(): void
    {
        $user = User::factory()->create(['password' => 'Lama12345', 'must_change_password' => true]);

        Livewire::actingAs($user)
            ->test(\App\Core\Livewire\Auth\ChangePassword::class)
            ->set('current_password', 'Lama12345')
            ->set('password', 'Baru12345')
            ->set('password_confirmation', 'Baru12345')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse($user->fresh()->must_change_password);
    }

    public function test_super_admin_lolos_semua_gate(): void
    {
        $this->seed(RolesSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(User::SUPER_ADMIN_ROLE);
        $biasa = User::factory()->create();

        $this->assertTrue($admin->can('apa.saja.lihat'));
        $this->assertFalse($biasa->can('apa.saja.lihat'));
    }

    public function test_seeder_membuat_roles_dan_super_admin(): void
    {
        $this->seed(RolesSeeder::class);

        $this->assertDatabaseCount('roles', count(RolesSeeder::ROLES));
        $this->assertDatabaseHas('roles', ['name' => 'Koordinator Alumni']);
        $this->assertDatabaseHas('roles', ['name' => 'Koordinator Sarpras']);
    }
}
