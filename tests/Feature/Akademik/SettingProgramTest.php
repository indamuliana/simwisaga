<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\ProgramIndex;
use App\Modules\Akademik\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingProgramTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_program',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_setting_program_dapat_diakses_oleh_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.program'))
            ->assertOk()
            ->assertSee('Setting Program Pendidikan')
            ->assertSee('Reguler')
            ->assertSee('Unggulan');
    }

    public function test_admin_dapat_menambah_program_baru(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProgramIndex::class)
            ->call('create')
            ->set('kode', 'TAHFIDZ')
            ->set('nama', 'Kelas Tahfidz Quran')
            ->set('deskripsi', 'Program khusus tahfidz dan keagamaan')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('programs', [
            'kode' => 'TAHFIDZ',
            'nama' => 'Kelas Tahfidz Quran',
            'is_active' => 1,
        ]);
    }

    public function test_admin_dapat_toggle_status_aktif_program(): void
    {
        $program = Program::where('kode', 'UNG')->first();
        $this->assertTrue($program->is_active);

        Livewire::actingAs($this->admin)
            ->test(ProgramIndex::class)
            ->call('toggleActive', $program->id);

        $this->assertFalse($program->fresh()->is_active);
    }

    public function test_program_tidak_dapat_dihapus_jika_memiliki_siswa(): void
    {
        $program = Program::where('kode', 'REG')->first();

        // Buat siswa terhubung ke program
        Siswa::create([
            'nama' => 'Siswa Test Program',
            'nisn' => '0098765432',
            'program_id' => $program->id,
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProgramIndex::class)
            ->call('delete', $program->id)
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
        ]);
    }

    public function test_program_dapat_dihapus_jika_belum_ada_siswa(): void
    {
        $program = Program::create([
            'kode' => 'TESTDEL',
            'nama' => 'Program Uji Hapus',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProgramIndex::class)
            ->call('delete', $program->id)
            ->assertSee('berhasil dihapus');

        $this->assertDatabaseMissing('programs', [
            'id' => $program->id,
        ]);
    }
}
