<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Livewire\PlottingRayonMassal;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlottingRayonResetTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rayon $rayon1;
    protected Rayon $rayon2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kesiswaan_reset',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->rayon1 = Rayon::firstOrCreate(['nama' => 'Rayon Cibatu 1'], ['wilayah_cakupan' => 'Cibatu Utara']);
        $this->rayon2 = Rayon::firstOrCreate(['nama' => 'Rayon Cibatu 2'], ['wilayah_cakupan' => 'Cibatu Selatan']);
    }

    protected function createSiswaWithRayon(string $nama, string $nisn, ?int $rayonId = null, string $status = 'aktif'): Siswa
    {
        $siswa = Siswa::create([
            'nama' => $nama,
            'nipd' => 'NIPD-' . rand(10000, 99999),
            'nisn' => $nisn,
            'jk' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-01-01',
            'status' => $status,
            'rayon_id' => $rayonId,
            'kecamatan' => 'Cibatu',
            'kelurahan' => 'Karyamukti',
        ]);

        if ($rayonId) {
            RayonSiswa::create([
                'siswa_id' => $siswa->id,
                'rayon_id' => $rayonId,
                'tgl_mulai' => now()->subMonths(3),
            ]);
        }

        return $siswa;
    }

    public function test_halaman_plotting_rayon_memiliki_tombol_reset_plotting_rayon(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.plotting-rayon'))
            ->assertOk()
            ->assertSee('Reset Plotting Rayon');
    }

    public function test_reset_selected_rayon_mengosongkan_siswa_terpilih(): void
    {
        $s1 = $this->createSiswaWithRayon('Siswa A', '0011111111', $this->rayon1->id);
        $s2 = $this->createSiswaWithRayon('Siswa B', '0022222222', $this->rayon2->id);
        $s3 = $this->createSiswaWithRayon('Siswa C', '0033333333', $this->rayon1->id);

        Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->set('selectedSiswaIds', [(string) $s1->id, (string) $s2->id])
            ->call('resetSelectedRayon')
            ->assertSee('Berhasil!');

        $this->assertNull($s1->fresh()->rayon_id);
        $this->assertNull($s2->fresh()->rayon_id);
        $this->assertEquals($this->rayon1->id, $s3->fresh()->rayon_id);

        // Check audit trail closed
        $this->assertNotNull(RayonSiswa::where('siswa_id', $s1->id)->first()->tgl_selesai);
        $this->assertNotNull(RayonSiswa::where('siswa_id', $s2->id)->first()->tgl_selesai);
    }

    public function test_execute_reset_rayon_mode_kosongkan_lingkup_terpilih(): void
    {
        $s1 = $this->createSiswaWithRayon('Siswa X', '0044444444', $this->rayon1->id);

        Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->set('selectedSiswaIds', [(string) $s1->id])
            ->call('openResetModal')
            ->assertSet('showResetModal', true)
            ->assertSet('resetScope', 'terpilih')
            ->set('resetMode', 'kosongkan')
            ->call('executeResetRayon')
            ->assertSet('showResetModal', false)
            ->assertSee('Sukses!');

        $this->assertNull($s1->fresh()->rayon_id);
    }

    public function test_execute_reset_rayon_mode_kosongkan_lingkup_semua_memerlukan_konfirmasi_kata_RESET(): void
    {
        $s1 = $this->createSiswaWithRayon('Siswa 1', '0055555551', $this->rayon1->id);
        $s2 = $this->createSiswaWithRayon('Siswa 2', '0055555552', $this->rayon2->id);

        $test = Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->call('openResetModal')
            ->set('resetScope', 'semua')
            ->set('resetMode', 'kosongkan')
            ->set('resetConfirmText', 'SALAH')
            ->call('executeResetRayon')
            ->assertHasErrors(['resetConfirmText']);

        $this->assertNotNull($s1->fresh()->rayon_id);
        $this->assertNotNull($s2->fresh()->rayon_id);

        // Berikan konfirmasi yang benar
        $test->set('resetConfirmText', 'RESET')
            ->call('executeResetRayon')
            ->assertHasNoErrors()
            ->assertSee('Sukses!');

        $this->assertNull($s1->fresh()->rayon_id);
        $this->assertNull($s2->fresh()->rayon_id);
    }

    public function test_execute_reset_rayon_mode_acak_mendistribusikan_ke_seluruh_rayon(): void
    {
        $s1 = $this->createSiswaWithRayon('Siswa Acak 1', '0066666661', null);
        $s2 = $this->createSiswaWithRayon('Siswa Acak 2', '0066666662', null);
        $s3 = $this->createSiswaWithRayon('Siswa Acak 3', '0066666663', null);
        $s4 = $this->createSiswaWithRayon('Siswa Acak 4', '0066666664', null);

        Livewire::actingAs($this->admin)
            ->test(PlottingRayonMassal::class)
            ->call('openResetModal')
            ->set('resetScope', 'semua')
            ->set('resetMode', 'acak')
            ->set('resetConfirmText', 'RESET')
            ->call('executeResetRayon')
            ->assertHasNoErrors()
            ->assertSee('Sukses!');

        $this->assertNotNull($s1->fresh()->rayon_id);
        $this->assertNotNull($s2->fresh()->rayon_id);
        $this->assertNotNull($s3->fresh()->rayon_id);
        $this->assertNotNull($s4->fresh()->rayon_id);

        $validRayonIds = Rayon::pluck('id')->toArray();
        $this->assertContains($s1->fresh()->rayon_id, $validRayonIds);
        $this->assertContains($s2->fresh()->rayon_id, $validRayonIds);

        // Check new audit log created
        $this->assertDatabaseHas('rayon_siswa', [
            'siswa_id' => $s1->id,
            'rayon_id' => $s1->fresh()->rayon_id,
        ]);
    }
}
