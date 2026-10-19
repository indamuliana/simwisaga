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

    public function test_master_role_bisa_dibuat_diedit_dan_dihapus(): void
    {
        $this->actingAs($this->admin);

        \Livewire\Livewire::test(\App\Modules\Sistem\Livewire\HakAkses::class)
            ->call('openCreateRole')
            ->set('roleName', 'Pengawas Ujian')
            ->call('saveRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', ['name' => 'Pengawas Ujian']);

        $role = \Spatie\Permission\Models\Role::findByName('Pengawas Ujian');

        \Livewire\Livewire::test(\App\Modules\Sistem\Livewire\HakAkses::class)
            ->call('openEditRole', $role->id)
            ->set('roleName', 'Pengawas Mutu')
            ->call('saveRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', ['name' => 'Pengawas Mutu']);

        \Livewire\Livewire::test(\App\Modules\Sistem\Livewire\HakAkses::class)
            ->call('deleteRole', $role->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('roles', ['name' => 'Pengawas Mutu']);
    }

    public function test_master_role_super_admin_tidak_bisa_dihapus_atau_diubah(): void
    {
        $this->actingAs($this->admin);
        $superAdminRole = \Spatie\Permission\Models\Role::findByName(User::SUPER_ADMIN_ROLE);

        \Livewire\Livewire::test(\App\Modules\Sistem\Livewire\HakAkses::class)
            ->call('deleteRole', $superAdminRole->id);

        $this->assertDatabaseHas('roles', ['name' => User::SUPER_ADMIN_ROLE]);
    }

    public function test_tab_pengguna_berfungsi_dan_dapat_berpindah(): void
    {
        $this->actingAs($this->admin);

        \Livewire\Livewire::test(\App\Modules\Sistem\Livewire\Pengguna::class)
            ->assertSet('activeTab', 'staff')
            ->call('setTab', 'siswa')
            ->assertSet('activeTab', 'siswa')
            ->call('setTab', 'ortu')
            ->assertSet('activeTab', 'ortu')
            ->call('setTab', 'semua')
            ->assertSet('activeTab', 'semua');
    }

    public function test_modal_ganti_password_mandiri_berfungsi(): void
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('password_lama_123'),
            'must_change_password' => false,
        ]);

        $this->actingAs($user);

        // Uji coba password lama salah
        \Livewire\Livewire::test(\App\Core\Livewire\Auth\ModalChangePassword::class)
            ->set('current_password', 'password_salah')
            ->set('password', 'password_baru_456')
            ->set('password_confirmation', 'password_baru_456')
            ->call('save')
            ->assertHasErrors(['current_password']);

        // Uji coba berhasil
        \Livewire\Livewire::test(\App\Core\Livewire\Auth\ModalChangePassword::class)
            ->set('current_password', 'password_lama_123')
            ->set('password', 'password_baru_456')
            ->set('password_confirmation', 'password_baru_456')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isOpen', false);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password_baru_456', $user->fresh()->password));
    }
}
