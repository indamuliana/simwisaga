<?php

namespace Tests\Feature\Sistem;

use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Livewire\HakAkses;
use App\Modules\Sistem\Livewire\Pengguna;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HakAksesDanPenggunaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $guruRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_super',
            'must_change_password' => false,
        ]);
        $this->admin->assignRole(User::SUPER_ADMIN_ROLE);

        $this->guruRole = Role::findOrCreate('Guru', 'web');
    }

    public function test_matriks_hak_akses_renders_and_supports_custom_actions_and_bulk_toggles(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(HakAkses::class)
            ->assertOk()
            ->assertSee('Matriks Hak Akses')
            ->assertSee('Aksi Khusus');

        // Select Guru role
        $component->call('selectRole', (string) $this->guruRole->id);

        // Test Select All for Role
        $component->call('selectAllForRole');
        $this->assertNotEmpty($component->get('rolePermissions'));

        // Save
        $component->call('save');
        $this->guruRole->refresh();
        $this->assertGreaterThan(0, $this->guruRole->permissions()->count());

        // Test Clear All
        $component->call('clearAllForRole');
        $this->assertEmpty($component->get('rolePermissions'));
        $component->call('save');
        $this->guruRole->refresh();
        $this->assertEquals(0, $this->guruRole->permissions()->count());
    }

    public function test_pengguna_can_be_created_and_linked_to_pegawai(): void
    {
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Budi Santoso, S.Kom.',
            'nip' => '198501012010011005',
            'jenis_kelamin' => 'L',
            'jenis_gtk' => 'guru',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(Pengguna::class)
            ->call('create')
            ->set('profileType', 'pegawai')
            ->set('selectedPegawaiId', $pegawai->id)
            ->set('name', 'Budi Santoso, S.Kom.')
            ->set('username', '198501012010011005')
            ->set('email', 'budi@wikrama.sch.id')
            ->set('password', 'secret123')
            ->set('selectedRoles', ['Guru'])
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('username', '198501012010011005')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Guru'));

        $pegawai->refresh();
        $this->assertEquals($user->id, $pegawai->user_id);
        $this->assertEquals('Pegawai', $user->linked_profile['label']);
    }

    public function test_reset_password_resets_to_default_and_enables_must_change_password(): void
    {
        $user = User::factory()->create([
            'username' => 'guru_test',
            'password' => Hash::make('oldpassword'),
            'must_change_password' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(Pengguna::class)
            ->call('resetPassword', $user->id);

        $user->refresh();
        $this->assertTrue(Hash::check('wikrama123', $user->password));
        $this->assertTrue($user->must_change_password);
    }

    public function test_delete_user_safely_unlinks_pegawai_and_protects_self(): void
    {
        $targetUser = User::factory()->create(['username' => 'guru_to_delete']);
        $pegawai = Pegawai::create([
            'user_id' => $targetUser->id,
            'nama_lengkap' => 'Guru Dihapus',
            'jenis_kelamin' => 'L',
            'jenis_gtk' => 'guru',
            'status_kepegawaian' => 'honor',
            'is_active' => true,
        ]);

        // Attempt deleting self -> should be blocked
        Livewire::actingAs($this->admin)
            ->test(Pengguna::class)
            ->call('deleteUser', $this->admin->id)
            ->assertSee('Anda tidak dapat menghapus akun Anda sendiri');

        $this->assertNotNull(User::find($this->admin->id));

        // Delete target user -> should succeed and unlink pegawai
        Livewire::actingAs($this->admin)
            ->test(Pengguna::class)
            ->call('deleteUser', $targetUser->id);

        $this->assertNull(User::find($targetUser->id));
        $pegawai->refresh();
        $this->assertNull($pegawai->user_id);
    }
}
