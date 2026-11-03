<?php

namespace Tests\Feature\AnalitikSpmb;

use App\Models\User;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\AnalitikSpmb\Livewire\WilayahIndex;
use App\Modules\AnalitikSpmb\Models\MasterWilayah;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Services\SpmbWilayahService;
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

    public function test_edit_alamat_siswa_dari_drawer_dan_simpan_lanjut(): void
    {
        $siswa1 = Siswa::create([
            'nama' => 'Siswa Pertama Garut',
            'nisn' => '8888888801',
            'nipd' => '8001',
            'jk' => 'L',
            'alamat' => 'Kp. Sukamaju',
            'kecamatan' => 'Tarogong Kaler',
            'kabupaten_kota' => 'Kab. Garut',
            'provinsi' => 'Jawa Barat',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $siswa2 = Siswa::create([
            'nama' => 'Siswa Kedua Garut',
            'nisn' => '8888888802',
            'nipd' => '8002',
            'jk' => 'P',
            'alamat' => 'Jl. Pahlawan',
            'kecamatan' => 'Tarogong Kaler',
            'kabupaten_kota' => 'Kab. Garut',
            'provinsi' => 'Jawa Barat',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            // Buka drawer untuk wilayah Tarogong Kaler
            ->call('openDrawer', 'Indonesia', 'Jawa Barat', 'Kab. Garut', 'Tarogong Kaler')
            ->assertSet('showDrawer', true)
            ->assertSee('Siswa Pertama Garut')
            ->assertSee('Siswa Kedua Garut')
            // Buka modal edit alamat untuk siswa 2 (urutan pertama alfabetis: 'Siswa Kedua Garut')
            ->call('openEditAddress', $siswa2->id)
            ->assertSet('showEditAddressModal', true)
            ->assertSet('editingAddressSiswaId', $siswa2->id)
            ->assertSet('editAddressAlamat', 'Jl. Pahlawan')
            // Ubah RT, RW, Kelurahan, Kode Pos untuk siswa 2
            ->set('editAddressAlamat', 'Jl. Pahlawan No. 45')
            ->set('editAddressRt', '01')
            ->set('editAddressRw', '02')
            ->set('editAddressKelurahan', 'Sirnajaya')
            ->set('editAddressKodePos', '44151')
            // Uji salin wilayah drawer
            ->call('copyDrawerWilayahToAddress')
            ->assertSet('editAddressKecamatan', 'Tarogong Kaler')
            ->assertSet('editAddressKabupaten', 'Kab. Garut')
            ->assertSet('editAddressProvinsi', 'Jawa Barat')
            // Simpan dan Lanjut ke siswa 1 ('Siswa Pertama Garut')
            ->call('saveStudentAddress', true)
            ->assertSet('showEditAddressModal', true)
            ->assertSet('editingAddressSiswaId', $siswa1->id)
            ->assertSet('editAddressAlamat', 'Kp. Sukamaju')
            // Simpan siswa 1 (siswa terakhir di drawer), modal otomatis tertutup
            ->call('saveStudentAddress', true)
            ->assertSet('showEditAddressModal', false);

        // Pastikan database siswa 2 sudah terupdate
        $siswa2->refresh();
        $this->assertEquals('Jl. Pahlawan No. 45', $siswa2->alamat);
        $this->assertEquals('01', $siswa2->rt);
        $this->assertEquals('02', $siswa2->rw);
        $this->assertEquals('Sirnajaya', $siswa2->kelurahan);
        $this->assertEquals('44151', $siswa2->kode_pos);
    }

    public function test_cascading_selection_dan_smart_upward_fill_pada_edit_alamat_siswa(): void
    {
        SpmbMasterSmp::create([
            'npsn' => '20200001',
            'nama' => 'SMPN 1 CIBINONG',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Bogor',
            'kecamatan' => 'Cibinong',
            'kelurahan' => 'Cirimekar',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Siswa Test Cascading',
            'nisn' => '99990001',
            'nipd' => '9001',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('openEditAddress', $siswa->id)
            ->assertSet('showEditAddressModal', true)
            // 1. Uji Smart Upward Fill: Isi Kecamatan 'Cibinong' -> otomatis isi Kabupaten 'Kab. Bogor' & Provinsi 'Jawa Barat'
            ->set('editAddressKecamatan', 'Cibinong')
            ->assertSet('editAddressKabupaten', 'Kab. Bogor')
            ->assertSet('editAddressProvinsi', 'Jawa Barat')
            ->assertSet('editAddressNegara', 'Indonesia')
            // 2. Uji Cascading Reset: Mengubah Provinsi ke 'Jawa Tengah' mereset Kab, Kec, dan Kelurahan
            ->set('editAddressProvinsi', 'Jawa Tengah')
            ->assertSet('editAddressKabupaten', '')
            ->assertSet('editAddressKecamatan', '')
            ->assertSet('editAddressKelurahan', '')
            // 3. Uji Mode Hybrid Manual: Aktifkan mode manual pada Provinsi dan Kabupaten
            ->set('isManualAddressProvinsi', true)
            ->set('editAddressProvinsi', 'Provinsi Khusus')
            ->set('isManualAddressKabupaten', true)
            ->set('editAddressKabupaten', 'Kabupaten Khusus')
            ->set('isManualAddressKecamatan', true)
            ->set('editAddressKecamatan', 'Kecamatan Baru')
            ->set('isManualAddressKelurahan', true)
            ->set('editAddressKelurahan', 'Desa Maju')
            ->call('saveStudentAddress')
            ->assertSet('showEditAddressModal', false);

        $siswa->refresh();
        $this->assertEquals('Provinsi Khusus', $siswa->provinsi);
        $this->assertEquals('Kabupaten Khusus', $siswa->kabupaten_kota);
        $this->assertEquals('Kecamatan Baru', $siswa->kecamatan);
        $this->assertEquals('Desa Maju', $siswa->kelurahan);
    }

    public function test_cascading_dan_smart_fill_pada_modal_standarisasi_kelompok(): void
    {
        SpmbMasterSmp::create([
            'npsn' => '20200002',
            'nama' => 'SMPN 1 KADUNGORA',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Garut',
            'kecamatan' => 'Kadungora',
            'kelurahan' => 'Karangtengah',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Siswa Kadungora',
            'nisn' => '99990002',
            'nipd' => '9002',
            'jk' => 'L',
            'kecamatan' => 'Kadungora',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('openEditGroupModal', 'Kadungora', '', (string) $siswa->id, 1)
            ->assertSet('showEditModal', true)
            // Uji Smart Upward Fill dari Kecamatan di Tab 2
            ->set('editKecamatan', 'Kadungora')
            ->assertSet('editKabupaten', 'Kab. Garut')
            ->assertSet('editProvinsi', 'Jawa Barat')
            // Uji Cascading Reset pada Tab 2
            ->set('editProvinsi', 'DKI Jakarta')
            ->assertSet('editKabupaten', '')
            ->assertSet('editKecamatan', '');
    }

    public function test_master_wilayah_nasional_kemendagri_terintegrasi_dengan_service(): void
    {
        // Masukkan sampel data hirarki Kemendagri
        MasterWilayah::create(['kode' => '33', 'nama' => 'Jawa Tengah', 'level' => 1, 'parent_kode' => null]);
        MasterWilayah::create(['kode' => '33.28', 'nama' => 'Kab. Tegal', 'level' => 2, 'parent_kode' => '33']);
        MasterWilayah::create(['kode' => '33.28.11', 'nama' => 'Adiwerna', 'level' => 3, 'parent_kode' => '33.28']);
        MasterWilayah::create(['kode' => '33.28.11.2001', 'nama' => 'Harjosari Kidul', 'level' => 4, 'parent_kode' => '33.28.11']);

        $service = app(SpmbWilayahService::class);

        // Uji hirarki
        $this->assertContains('Jawa Tengah', $service->getProvincesList());
        $this->assertContains('Kab. Tegal', $service->getKabupatenList('Jawa Tengah'));
        $this->assertContains('Adiwerna', $service->getKecamatanList('Kab. Tegal'));
        $this->assertContains('Harjosari Kidul', $service->getDesaList('Adiwerna', 'Kab. Tegal'));

        // Uji upward fill dari kecamatan Adiwerna
        $resolved = $service->resolveLocationFromKecamatan('Adiwerna');
        $this->assertNotNull($resolved);
        $this->assertEquals('Kab. Tegal', $resolved['kabupaten']);
        $this->assertEquals('Jawa Tengah', $resolved['provinsi']);
    }
}
