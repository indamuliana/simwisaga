<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\AnalitikSpmb\Models\MasterWilayah;
use App\Modules\Kesiswaan\Livewire\PemetaanAlamatSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\PemetaanAlamatService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PemetaanAlamatSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rayon $rayon;
    protected ?Rombel $rombel = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_pemetaan_alamat',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->rayon = Rayon::firstOrCreate(['nama' => 'Rayon Sukajadi 1'], ['wilayah_cakupan' => 'Sukajadi']);
        $this->rombel = Rombel::first();

        // Seed Master Wilayah pengujian
        MasterWilayah::firstOrCreate(['kode' => '32'], ['nama' => 'Jawa Barat', 'level' => 1, 'parent_kode' => null]);
        MasterWilayah::firstOrCreate(['kode' => '32.05'], ['nama' => 'Kab. Garut', 'level' => 2, 'parent_kode' => '32']);
        MasterWilayah::firstOrCreate(['kode' => '32.05.01'], ['nama' => 'Tarogong Kaler', 'level' => 3, 'parent_kode' => '32.05']);
        MasterWilayah::firstOrCreate(['kode' => '32.05.01.2001'], ['nama' => 'Pananjung', 'level' => 4, 'parent_kode' => '32.05.01']);
        MasterWilayah::firstOrCreate(['kode' => '32.05.15'], ['nama' => 'Karangpawitan', 'level' => 3, 'parent_kode' => '32.05']);
        MasterWilayah::firstOrCreate(['kode' => '32.05.15.2001'], ['nama' => 'Suci Kaler', 'level' => 4, 'parent_kode' => '32.05.15']);

        MasterWilayah::firstOrCreate(['kode' => '32.71'], ['nama' => 'Kota Bogor', 'level' => 2, 'parent_kode' => '32']);
        MasterWilayah::firstOrCreate(['kode' => '32.71.05'], ['nama' => 'Bogor Utara', 'level' => 3, 'parent_kode' => '32.71']);
        MasterWilayah::firstOrCreate(['kode' => '32.71.05.1001'], ['nama' => 'Tegal Gundil', 'level' => 4, 'parent_kode' => '32.71.05']);

        MasterWilayah::firstOrCreate(['kode' => '32.73'], ['nama' => 'Kota Bandung', 'level' => 2, 'parent_kode' => '32']);
    }

    public function test_page_can_be_accessed_by_authorized_user(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.pemetaan-alamat'))
            ->assertOk()
            ->assertSee('Pemetaan')
            ->assertSee('Standarisasi Alamat Siswa');
    }

    public function test_unauthorized_user_is_forbidden(): void
    {
        $user = User::factory()->create([
            'username' => 'user_biasa',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($user)
            ->get(route('kesiswaan.pemetaan-alamat'))
            ->assertForbidden();
    }

    public function test_service_detects_dirty_prefix_and_suggests_cleanup(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Teks Kotor',
            'nipd' => '7001',
            'nisn' => '7001001',
            'status' => 'aktif',
            'kelurahan' => 'Desa/Kel. Pananjung',
            'kecamatan' => 'Kec. Tarogong Kaler',
            'kabupaten_kota' => 'Garut',
            'provinsi' => 'Jawa Barat',
        ]);

        $service = new PemetaanAlamatService();
        $analysis = $service->analyzeSiswa($siswa);

        $this->assertEquals('teks_kotor', $analysis['status']);
        $this->assertTrue($analysis['is_anomali']);
        $this->assertEquals('Pananjung', $analysis['suggested']['kelurahan']);
        $this->assertEquals('Tarogong Kaler', $analysis['suggested']['kecamatan']);
        $this->assertEquals('Kab. Garut', $analysis['suggested']['kabupaten_kota']);
    }

    public function test_service_detects_kabupaten_mismatch_and_corrects_it(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Mismatch Karangpawitan',
            'nipd' => '7002',
            'nisn' => '7002001',
            'status' => 'aktif',
            'kelurahan' => 'Suci Kaler',
            'kecamatan' => 'Karangpawitan',
            'kabupaten_kota' => 'Kota Bandung', // Mismatch! Seharusnya Kab. Garut
            'provinsi' => 'Jawa Barat',
        ]);

        $service = new PemetaanAlamatService();
        $analysis = $service->analyzeSiswa($siswa);

        $this->assertEquals('kabupaten_tidak_sinkron', $analysis['status']);
        $this->assertTrue($analysis['is_anomali']);
        $this->assertEquals('Kab. Garut', $analysis['suggested']['kabupaten_kota']);
        $this->assertEquals('Karangpawitan', $analysis['suggested']['kecamatan']);
        $this->assertEquals('Suci Kaler', $analysis['suggested']['kelurahan']);
    }

    public function test_service_detects_international_address(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Sabah CLC',
            'nipd' => '7003',
            'nisn' => '7003001',
            'status' => 'aktif',
            'alamat' => 'CLC Ladang Holyrood, Tawau, Sabah, Malaysia',
            'kelurahan' => '',
            'kecamatan' => 'Tawau',
            'kabupaten_kota' => '',
            'provinsi' => '',
        ]);

        $service = new PemetaanAlamatService();
        $analysis = $service->analyzeSiswa($siswa);

        $this->assertEquals('luar_negeri', $analysis['status']);
        $this->assertTrue($analysis['is_anomali']);
        $this->assertEquals('Malaysia', $analysis['suggested']['negara']);
        $this->assertEquals('Sabah', $analysis['suggested']['provinsi']);
        $this->assertEquals('Tawau', $analysis['suggested']['kabupaten_kota']);
    }

    public function test_service_handles_trailing_whitespace_in_master_wilayah_cleanly(): void
    {
        // Simulasi master wilayah dengan trailing space (seperti Jakarta Utara)
        MasterWilayah::firstOrCreate(['kode' => '31.72'], [
            'nama' => 'Kota Administrasi Jakarta Utara ', // Trailing space
            'level' => 2,
            'parent_kode' => '31',
        ]);
        MasterWilayah::firstOrCreate(['kode' => '31.72.01'], [
            'nama' => 'Cilincing',
            'level' => 3,
            'parent_kode' => '31.72',
        ]);
        MasterWilayah::firstOrCreate(['kode' => '31.72.01.1001'], [
            'nama' => 'Semper Barat',
            'level' => 4,
            'parent_kode' => '31.72.01',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Raihan Azka Lubis',
            'nipd' => '12501153',
            'nisn' => '1250115301',
            'status' => 'aktif',
            'kelurahan' => 'Semper Barat',
            'kecamatan' => 'Cilincing',
            'kabupaten_kota' => 'Kota Administrasi Jakarta Utara',
            'provinsi' => 'Daerah Khusus Ibukota Jakarta',
        ]);

        $service = new PemetaanAlamatService();
        $analysis = $service->analyzeSiswa($siswa);

        $this->assertEquals('sudah_valid', $analysis['status']);
        $this->assertFalse($analysis['is_anomali']);
        $this->assertEmpty($analysis['issues']);
        $this->assertEquals('Kota Administrasi Jakarta Utara', $analysis['suggested']['kabupaten_kota']);
    }

    public function test_livewire_can_apply_single_suggestion(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Single Apply',
            'nipd' => '7004',
            'nisn' => '7004001',
            'status' => 'aktif',
            'kelurahan' => 'Desa/Kel. Pananjung',
            'kecamatan' => 'Tarogong Kaler',
            'kabupaten_kota' => 'Kab. Garut',
            'provinsi' => 'Jawa Barat',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PemetaanAlamatSiswa::class)
            ->call('applySingle', $siswa->id);

        $siswa->refresh();
        $this->assertEquals('Pananjung', $siswa->kelurahan);
        $this->assertEquals('Tarogong Kaler', $siswa->kecamatan);
    }

    public function test_livewire_can_apply_batch_suggestions(): void
    {
        $siswa1 = Siswa::create([
            'nama' => 'Siswa Batch 1',
            'nipd' => '7005',
            'nisn' => '7005001',
            'status' => 'aktif',
            'kelurahan' => 'Desa/Kel. Pananjung',
            'kecamatan' => 'Tarogong Kaler',
            'kabupaten_kota' => 'Kab. Garut',
            'provinsi' => 'Jawa Barat',
        ]);

        $siswa2 = Siswa::create([
            'nama' => 'Siswa Batch 2',
            'nipd' => '7006',
            'nisn' => '7006001',
            'status' => 'aktif',
            'kelurahan' => 'Suci Kaler',
            'kecamatan' => 'Karangpawitan',
            'kabupaten_kota' => 'Kota Bandung', // Mismatch
            'provinsi' => 'Jawa Barat',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PemetaanAlamatSiswa::class)
            ->set('selectedSiswaIds', [(string) $siswa1->id, (string) $siswa2->id])
            ->call('applySelected');

        $siswa1->refresh();
        $siswa2->refresh();

        $this->assertEquals('Pananjung', $siswa1->kelurahan);
        $this->assertEquals('Kab. Garut', $siswa2->kabupaten_kota);
    }

    public function test_livewire_manual_modal_edit_and_save(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Manual Edit',
            'nipd' => '7007',
            'nisn' => '7007001',
            'status' => 'aktif',
            'alamat' => 'Alamat Awal',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PemetaanAlamatSiswa::class)
            ->call('openManualModal', $siswa->id)
            ->assertSet('showManualModal', true)
            ->set('manualProvinsi', 'Jawa Barat')
            ->set('manualKabupaten', 'Kab. Garut')
            ->set('manualKecamatan', 'Tarogong Kaler')
            ->set('manualKelurahan', 'Pananjung')
            ->set('manualAlamat', 'Jl. Rancabango No. 77')
            ->call('saveManual')
            ->assertSet('showManualModal', false);

        $siswa->refresh();
        $this->assertEquals('Jawa Barat', $siswa->provinsi);
        $this->assertEquals('Kab. Garut', $siswa->kabupaten_kota);
        $this->assertEquals('Tarogong Kaler', $siswa->kecamatan);
        $this->assertEquals('Pananjung', $siswa->kelurahan);
        $this->assertEquals('Jl. Rancabango No. 77', $siswa->alamat);
    }
}
