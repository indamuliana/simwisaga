<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Penilaian\Livewire\PengaturanPenilaianIndex;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PengaturanPenilaianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_pengaturan',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->guru = User::factory()->create([
            'username' => 'guru_biasa',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guru->assignRole('Guru');
    }

    public function test_halaman_pengaturan_penilaian_dapat_diakses_oleh_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/penilaian/pengaturan')
            ->assertOk()
            ->assertSee('Pengaturan Bobot & Predikat Penilaian');
    }

    public function test_guru_biasa_tidak_bisa_mengakses_halaman_pengaturan(): void
    {
        $this->actingAs($this->guru)
            ->get('/penilaian/pengaturan')
            ->assertForbidden();
    }

    public function test_admin_dapat_mengubah_bobot_dan_rentang_predikat_penilaian(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PengaturanPenilaianIndex::class)
            ->assertSet('bobot_tp', 50)
            ->assertSet('bobot_sts', 30)
            ->assertSet('bobot_nh', 20)
            ->set('bobot_tp', 40)
            ->set('bobot_sts', 35)
            ->set('bobot_nh', 25)
            ->set('intervals.0.min', 93)
            ->set('intervals.1.min', 85)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Pengaturan bobot dan skala predikat berhasil disimpan.');

        $setting = PengaturanPenilaian::getActive();
        $this->assertEquals(40, $setting->bobot_tp);
        $this->assertEquals(35, $setting->bobot_sts);
        $this->assertEquals(25, $setting->bobot_nh);
        $this->assertEquals(93, $setting->predikat_intervals[0]['min']);
        $this->assertEquals(85, $setting->predikat_intervals[1]['min']);
    }

    public function test_validasi_menolak_jika_total_bobot_tidak_sama_dengan_100(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PengaturanPenilaianIndex::class)
            ->set('bobot_tp', 60)
            ->set('bobot_sts', 30)
            ->set('bobot_nh', 20) // Total 110
            ->call('save')
            ->assertHasErrors(['bobot_total']);
    }

    public function test_admin_dapat_mereset_pengaturan_ke_default(): void
    {
        // Ubah dulu ke custom
        PengaturanPenilaian::create([
            'bobot_tp' => 40,
            'bobot_sts' => 40,
            'bobot_nh' => 20,
            'nh_default_value' => 100.0,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PengaturanPenilaianIndex::class)
            ->call('resetToDefault')
            ->assertHasNoErrors();

        $setting = PengaturanPenilaian::getActive();
        $this->assertEquals(50, $setting->bobot_tp);
        $this->assertEquals(30, $setting->bobot_sts);
        $this->assertEquals(20, $setting->bobot_nh);
    }
}
