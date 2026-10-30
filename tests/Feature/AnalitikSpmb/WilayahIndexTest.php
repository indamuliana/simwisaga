<?php

namespace Tests\Feature\AnalitikSpmb;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\AnalitikSpmb\Livewire\WilayahIndex;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\AnalitikSpmbPermissionSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WilayahIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);
        $this->seed(AnalitikSpmbPermissionSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_wilayah',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->regularUser = User::factory()->create([
            'username' => 'user_polos',
            'is_active' => true,
            'must_change_password' => false,
        ]);
    }

    public function test_halaman_sebaran_wilayah_dapat_diakses_oleh_user_berizin(): void
    {
        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->assertStatus(200)
            ->assertSee('Sebaran Wilayah Asal Siswa')
            ->assertSee('Analitik Sebaran')
            ->assertSee('Perbaikan Wilayah')
            ->assertSee('Auto-Detect Wilayah');
    }

    public function test_halaman_sebaran_wilayah_dilarang_untuk_user_tanpa_izin(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('analitik-spmb.wilayah'));
        $response->assertStatus(403);
    }

    public function test_auto_detect_wilayah_standarisasi_lokal_dan_luar_negeri(): void
    {
        // 1. Buat Master SMP referensi
        SpmbMasterSmp::create([
            'npsn' => '20209355',
            'nama' => 'SMP NEGERI 1 GARUT',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Garut',
            'kecamatan' => 'Garut Kota',
        ]);

        // 2. Buat Siswa Lokal dengan awalan 'Kec. '
        $siswaLokal = Siswa::create([
            'nama' => 'Siswa Tarogong',
            'nisn' => '1111111111',
            'nipd' => '1001',
            'jk' => 'L',
            'kecamatan' => 'Kec. Garut Kota',
            'alamat' => 'Jl. Ahmad Yani No 1 Garut',
            'sekolah_asal' => 'SMP NEGERI 1 GARUT',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        // 3. Buat Siswa Luar Negeri (Sabah, Malaysia / CLC)
        $siswaLN = Siswa::create([
            'nama' => 'Siswa Tawau Sabah',
            'nisn' => '2222222222',
            'nipd' => '1002',
            'jk' => 'P',
            'kecamatan' => 'Tawau',
            'alamat' => 'Ladang FGV Sahabat, Tawau, Sabah, Malaysia',
            'sekolah_asal' => 'CLC SMPT FGV Sahabat',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('autoDetectWilayah')
            ->assertHasNoErrors()
            ->assertSee('Auto-Detect Wilayah berhasil!');

        // Cek standarisasi siswa lokal
        $siswaLokal->refresh();
        $this->assertEquals('Garut Kota', $siswaLokal->kecamatan);
        $this->assertEquals('Kab. Garut', $siswaLokal->kabupaten_kota);
        $this->assertEquals('Jawa Barat', $siswaLokal->provinsi);
        $this->assertEquals('Indonesia', $siswaLokal->negara);

        // Cek standarisasi siswa luar negeri
        $siswaLN->refresh();
        $this->assertEquals('Malaysia', $siswaLN->negara);
        $this->assertEquals('Sabah', $siswaLN->provinsi);
        $this->assertEquals('Tawau', $siswaLN->kabupaten_kota);
    }

    public function test_filter_cakupan_luar_negeri_dan_dalam_negeri(): void
    {
        Siswa::create([
            'nama' => 'Budi Santoso',
            'nisn' => '3333333333',
            'nipd' => '2001',
            'jk' => 'L',
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kab. Garut',
            'kecamatan' => 'Tarogong Kidul',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Siswa::create([
            'nama' => 'Mohammad Shafiq',
            'nisn' => '4444444444',
            'nipd' => '2002',
            'jk' => 'L',
            'negara' => 'Malaysia',
            'provinsi' => 'Sabah',
            'kabupaten_kota' => 'Sandakan',
            'kecamatan' => 'Sandakan',
            'sekolah_asal' => 'LC Prolific Sandakan',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        // Filter Luar Negeri: hanya Shafiq yang muncul
        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->set('filterCakupan', 'luar_negeri')
            ->assertSee('Sandakan')
            ->assertSee('Mohammad Shafiq')
            ->assertDontSee('Tarogong Kidul');

        // Filter Dalam Negeri: hanya Budi yang muncul
        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->set('filterCakupan', 'dalam_negeri')
            ->assertSee('Tarogong Kidul')
            ->assertSee('Kab. Garut')
            ->assertDontSee('Mohammad Shafiq');
    }

    public function test_standarisasi_massal_di_tab_perbaikan(): void
    {
        $s1 = Siswa::create([
            'nama' => 'Siswa Anomali 1',
            'nisn' => '5555555551',
            'nipd' => '3001',
            'jk' => 'L',
            'kecamatan' => 'Kec. Arso Barat',
            'kelurahan' => 'Yatuharja',
            'kabupaten_kota' => null,
            'provinsi' => null,
            'negara' => 'Indonesia',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $s2 = Siswa::create([
            'nama' => 'Siswa Anomali 2',
            'nisn' => '5555555552',
            'nipd' => '3002',
            'jk' => 'P',
            'kecamatan' => 'Kec. Arso Barat',
            'kelurahan' => 'Yatuharja',
            'kabupaten_kota' => null,
            'provinsi' => null,
            'negara' => 'Indonesia',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->set('activeTab', 'perbaikan')
            ->assertSee('Kec. Arso Barat')
            ->assertSee('Yatuharja')
            ->call('openEditGroupModal', 'Kec. Arso Barat', 'Yatuharja', "{$s1->id},{$s2->id}", 2)
            ->assertSet('showEditModal', true)
            ->set('editNegara', 'Indonesia')
            ->set('editProvinsi', 'Papua')
            ->set('editKabupaten', 'Kab. Keerom')
            ->set('editKecamatan', 'Arso Barat')
            ->call('saveStandardizedWilayah')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false)
            ->assertSee('Standarisasi berhasil!');

        $s1->refresh();
        $s2->refresh();

        $this->assertEquals('Indonesia', $s1->negara);
        $this->assertEquals('Papua', $s1->provinsi);
        $this->assertEquals('Kab. Keerom', $s1->kabupaten_kota);
        $this->assertEquals('Arso Barat', $s1->kecamatan);

        $this->assertEquals('Indonesia', $s2->negara);
        $this->assertEquals('Papua', $s2->provinsi);
        $this->assertEquals('Kab. Keerom', $s2->kabupaten_kota);
        $this->assertEquals('Arso Barat', $s2->kecamatan);
    }

    public function test_drawer_rincian_siswa_dan_ekspor_excel(): void
    {
        $rombel = Rombel::first();

        $siswa = Siswa::create([
            'nama' => 'Ahmad Rinci',
            'nisn' => '6666666666',
            'nipd' => '4001',
            'jk' => 'L',
            'rombel_id' => $rombel?->id,
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kab. Garut',
            'kecamatan' => 'Tarogong Kaler',
            'alamat' => 'Jl. Pembangunan Garut',
            'sekolah_asal' => 'SMPN 1 Tarogong Kaler',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        // Buka drawer
        $test = Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('openDrawer', 'Indonesia', 'Jawa Barat', 'Kab. Garut', 'Tarogong Kaler')
            ->assertSet('showDrawer', true)
            ->assertSee('Ahmad Rinci')
            ->assertSee('6666666666');

        // Test export rekap
        $responseRekap = $test->call('exportExcelRekap');
        $this->assertNotNull($responseRekap);

        // Test export drawer
        $responseDrawer = $test->call('exportExcelDrawer');
        $this->assertNotNull($responseDrawer);
    }

    public function test_normalisasi_otomatis_garut_menjadi_kab_garut_pada_siswa_dan_sekolah_asal(): void
    {
        $sekolah = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMP IT GARUT TEST',
            'jenis' => 'SMP',
            'status_sekolah' => 'Swasta',
            'kabupaten_kota' => 'Garut',
            'tier' => 'Rintisan',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Santri Garut',
            'nisn' => '7777777771',
            'nipd' => '5001',
            'jk' => 'L',
            'kabupaten_kota' => 'Garut',
            'kecamatan' => 'Tarogong Kaler',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('autoDetectWilayah')
            ->assertHasNoErrors();

        $sekolah->refresh();
        $siswa->refresh();

        $this->assertEquals('Kab. Garut', $sekolah->kabupaten_kota);
        $this->assertEquals('Kab. Garut', $siswa->kabupaten_kota);
        $this->assertEquals('Jawa Barat', $siswa->provinsi);
        $this->assertEquals('Indonesia', $siswa->negara);
    }

    public function test_auto_deduce_bogor_berdasarkan_kecamatan_cibinong_vs_bogor_tengah(): void
    {
        // Setup Master SMP untuk Cibinong (Kab. Bogor) dan Kota Bogor Tengah (Kota Bogor)
        SpmbMasterSmp::create([
            'npsn' => '20200001',
            'nama' => 'SMP NEGERI 1 CIBINONG',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Bogor',
            'kecamatan' => 'Cibinong',
        ]);

        SpmbMasterSmp::create([
            'npsn' => '20200002',
            'nama' => 'SMP NEGERI 1 BOGOR',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bogor',
            'kecamatan' => 'Kota Bogor Tengah',
        ]);

        $siswaKab = Siswa::create([
            'nama' => 'Siswa Cibinong',
            'nisn' => '8888888881',
            'nipd' => '6001',
            'jk' => 'L',
            'kabupaten_kota' => 'Bogor',
            'kecamatan' => 'Cibinong',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $siswaKota = Siswa::create([
            'nama' => 'Siswa Kota Bogor',
            'nisn' => '8888888882',
            'nipd' => '6002',
            'jk' => 'P',
            'kabupaten_kota' => 'Bogor',
            'kecamatan' => 'Bogor Tengah',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('autoDetectWilayah')
            ->assertHasNoErrors();

        $siswaKab->refresh();
        $siswaKota->refresh();

        $this->assertEquals('Kab. Bogor', $siswaKab->kabupaten_kota);
        $this->assertEquals('Kota Bogor', $siswaKota->kabupaten_kota);
    }

    public function test_pill_buttons_dan_saran_master_pada_modal_perbaikan_wilayah(): void
    {
        SpmbMasterSmp::create([
            'npsn' => '20200003',
            'nama' => 'SMP NEGERI 2 BOGOR',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bogor',
            'kecamatan' => 'Tanah Sareal',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Siswa Tanah Sareal',
            'nisn' => '9999999991',
            'nipd' => '7001',
            'jk' => 'L',
            'kecamatan' => 'Tanah Sareal',
            'kabupaten_kota' => null,
            'provinsi' => null,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->set('activeTab', 'perbaikan')
            ->call('openEditGroupModal', 'Tanah Sareal', '', (string) $siswa->id, 1)
            ->assertSet('showEditModal', true)
            ->assertSee('Kota Bogor')
            ->assertSee('Kab. Bogor')
            ->call('selectPillKabupaten', 'Kota Bogor')
            ->assertSet('editKabupaten', 'Kota Bogor')
            ->assertSet('editProvinsi', 'Jawa Barat')
            ->call('saveStandardizedWilayah')
            ->assertHasNoErrors();

        $siswa->refresh();
        $this->assertEquals('Kota Bogor', $siswa->kabupaten_kota);
        $this->assertEquals('Jawa Barat', $siswa->provinsi);
    }
}
