<?php

namespace Tests\Feature\Kepegawaian;

use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GeneratePegawaiAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
    }

    public function test_generates_accounts_for_pegawai_without_user(): void
    {
        $guru = Pegawai::create([
            'nama_lengkap' => 'Agustian Hardi, S.Kom.,Gr.',
            'jenis_gtk' => 'Guru',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'email' => 'agustian@wisaga.sch.id',
            'is_active' => true,
        ]);

        $tendik = Pegawai::create([
            'nama_lengkap' => 'Abdul Latip Mubarok',
            'jenis_gtk' => 'Tendik',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'sty',
            'email' => 'latip@wisaga.sch.id',
            'is_active' => true,
        ]);

        $this->artisan('kepegawaian:generate-accounts')
            ->expectsOutputToContain('Berhasil membuat 2 akun baru')
            ->assertSuccessful();

        $guru->refresh();
        $tendik->refresh();

        $this->assertNotNull($guru->user_id);
        $this->assertNotNull($tendik->user_id);

        $guruUser = User::find($guru->user_id);
        $tendikUser = User::find($tendik->user_id);

        $this->assertEquals('agustian.hardi', $guruUser->username);
        $this->assertTrue($guruUser->hasRole('Guru'));
        $this->assertTrue($guruUser->must_change_password);
        $this->assertTrue(Hash::check('wisaga123', $guruUser->password));

        $this->assertEquals('abdul.latip', $tendikUser->username);
        $this->assertTrue($tendikUser->hasRole('Tata Usaha'));
        $this->assertTrue($tendikUser->must_change_password);
        $this->assertTrue(Hash::check('wisaga123', $tendikUser->password));
    }

    public function test_is_idempotent_and_skips_pegawai_already_having_account(): void
    {
        $existingUser = User::create([
            'name' => 'Existing Teacher',
            'username' => 'existing.teacher',
            'password' => Hash::make('secret123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $existingUser->id,
            'nama_lengkap' => 'Existing Teacher, S.Pd',
            'jenis_gtk' => 'Guru',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $this->artisan('kepegawaian:generate-accounts')
            ->expectsOutputToContain('Semua pegawai sudah memiliki akun login')
            ->assertSuccessful();

        $this->assertEquals(1, User::count());
    }

    public function test_handles_username_collision_by_adding_counter(): void
    {
        Pegawai::create([
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'jenis_gtk' => 'Guru',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        Pegawai::create([
            'nama_lengkap' => 'Budi Santoso, M.Pd',
            'jenis_gtk' => 'Guru',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $this->artisan('kepegawaian:generate-accounts')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['username' => 'budi.santoso']);
        $this->assertDatabaseHas('users', ['username' => 'budi.santoso2']);
    }

    public function test_dry_run_does_not_modify_database(): void
    {
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Budi Hartono',
            'jenis_gtk' => 'Guru',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $this->artisan('kepegawaian:generate-accounts --dry-run')
            ->expectsOutputToContain('Mode simulasi (dry-run)')
            ->assertSuccessful();

        $pegawai->refresh();
        $this->assertNull($pegawai->user_id);
        $this->assertEquals(0, User::count());
    }
}
