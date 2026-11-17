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
            ->assertSee('Pemetaan Alamat (Kesiswaan)')
            ->assertDontSee('Perbaikan Wilayah & Anomali')
            ->assertSee('Auto-Detect Wilayah');
    }

    public function test_akses_active_tab_perbaikan_dialihkan_ke_pemetaan_alamat_kesiswaan(): void
    {
        $this->actingAs($this->admin)
            ->get(route('analitik-spmb.wilayah', ['activeTab' => 'perbaikan']))
            ->assertRedirect(route('kesiswaan.pemetaan-alamat'));
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

    public function test_auto_detect_wilayah_tidak_menimpa_domisili_valid_dengan_sekolah_asal(): void
    {
        // Simulasi sekolah asal yang berada di Garut
        SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 GARUT',
            'kabupaten_kota' => 'Kab. Garut',
            'provinsi' => 'Jawa Barat',
        ]);

        // Siswa yang domisili rumahnya di Jakarta Utara tapi sekolah asalnya tercatat di Garut
        $siswa = Siswa::create([
            'nama' => 'Raihan Azka Lubis',
            'nisn' => '12501153',
            'jk' => 'L',
            'kelurahan' => 'Semper Barat',
            'kecamatan' => 'Cilincing',
            'kabupaten_kota' => 'Kota Administrasi Jakarta Utara',
            'provinsi' => 'Daerah Khusus Ibukota Jakarta',
            'negara' => 'Indonesia',
            'alamat' => 'JL RAYA TUGU NO 09 A',
            'sekolah_asal' => 'SMPN 1 GARUT',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->call('autoDetectWilayah')
            ->assertHasNoErrors();

        // Pastikan kabupaten_kota domisili siswa TIDAK tertimpa menjadi Kab. Garut
        $siswa->refresh();
        $this->assertEquals('Kota Administrasi Jakarta Utara', $siswa->kabupaten_kota);
        $this->assertEquals('Daerah Khusus Ibukota Jakarta', $siswa->provinsi);
        $this->assertEquals('Cilincing', $siswa->kecamatan);
        $this->assertEquals('Semper Barat', $siswa->kelurahan);
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

        // Filter Luar Negeri: hanya Malaysia/Sabah/Sandakan yang muncul di tree, dan Shafiq terlihat saat buka drawer
        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->set('filterCakupan', 'luar_negeri')
            ->assertSee('Malaysia')
            ->assertSee('Sabah')
            ->assertSee('Sandakan')
            ->assertDontSee('Tarogong Kidul')
            ->call('openDrawer', 'Malaysia')
            ->assertSee('Mohammad Shafiq');

        // Filter Dalam Negeri: hanya Budi yang muncul
        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->set('filterCakupan', 'dalam_negeri')
            ->assertSee('Tarogong Kidul')
            ->assertSee('Kab. Garut')
            ->assertDontSee('Mohammad Shafiq')
            ->call('openDrawer', 'Indonesia', 'Jawa Barat')
            ->assertSee('Budi Santoso');
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

    public function test_tree_hirarki_expand_collapse_dan_toggle(): void
    {
        Siswa::create([
            'nama' => 'Siswa Bandung',
            'nisn' => '7770001',
            'nipd' => '7001',
            'jk' => 'L',
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kota Bandung',
            'kecamatan' => 'Coblong',
            'kelurahan' => 'Dago',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            ->assertSee('Tabel Hirarki Sebaran Wilayah Asal Siswa')
            ->assertSee('Indonesia')
            ->assertSee('Jawa Barat')
            ->assertSee('Kota Bandung');

        // Test collapse all: anak-cabang tertutup
        $test->call('collapseAll')
            ->assertSet('expandedNodes', [])
            ->assertDontSee('Coblong');

        // Test expand all: semua terbuka sampai level desa
        $test->call('expandAll')
            ->assertSee('Coblong')
            ->assertSee('Dago');

        // Test toggle node spesifik
        $test->call('toggleNode', 'C:Indonesia:Jawa Barat:Kota Bandung:Coblong')
            ->assertDontSee('Dago');
    }

    public function test_drawer_multi_level_negara_provinsi_kabupaten_kecamatan_desa(): void
    {
        $siswaIndo = Siswa::create([
            'nama' => 'Siswa Garut Kota',
            'nisn' => '7770002',
            'nipd' => '7002',
            'jk' => 'L',
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kab. Garut',
            'kecamatan' => 'Garut Kota',
            'kelurahan' => 'Kota Kulon',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $siswaLN = Siswa::create([
            'nama' => 'Siswa Tawau Sabah',
            'nisn' => '7770003',
            'nipd' => '7003',
            'jk' => 'P',
            'negara' => 'Malaysia',
            'provinsi' => 'Sabah',
            'kabupaten_kota' => 'Tawau',
            'kecamatan' => 'Tawau',
            'kelurahan' => 'Ladang Sahabat',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $lw = Livewire::actingAs($this->admin)->test(WilayahIndex::class);

        // 1. Drawer level Negara (Malaysia) -> menampilkan siswa Tawau
        $lw->call('openDrawer', 'Malaysia')
            ->assertSet('showDrawer', true)
            ->assertSee('Siswa Tawau Sabah')
            ->assertDontSee('Siswa Garut Kota');

        // 2. Drawer level Provinsi (Jawa Barat) -> menampilkan siswa Garut
        $lw->call('openDrawer', 'Indonesia', 'Jawa Barat')
            ->assertSee('Siswa Garut Kota')
            ->assertDontSee('Siswa Tawau Sabah');

        // 3. Drawer level Kabupaten (Kab. Garut)
        $lw->call('openDrawer', 'Indonesia', 'Jawa Barat', 'Kab. Garut')
            ->assertSee('Siswa Garut Kota');

        // 4. Drawer level Kecamatan (Garut Kota)
        $lw->call('openDrawer', 'Indonesia', 'Jawa Barat', 'Kab. Garut', 'Garut Kota')
            ->assertSee('Siswa Garut Kota');

        // 5. Drawer level Desa (Kota Kulon)
        $lw->call('openDrawer', 'Indonesia', 'Jawa Barat', 'Kab. Garut', 'Garut Kota', 'Kota Kulon')
            ->assertSee('Siswa Garut Kota');
    }

    public function test_pivot_table_hirarki_dengan_kolom_program_dan_jurusan(): void
    {
        $this->actingAs($this->admin);

        $tjktRombel = Rombel::whereHas('jurusan', fn ($q) => $q->where('kode', 'TJKT'))->first();
        $pplgRombel = Rombel::whereHas('jurusan', fn ($q) => $q->where('kode', 'PPLG'))->first();

        // Buat siswa di Garut Kota dengan program Unggulan dan Reguler
        Siswa::create([
            'nama' => 'Siswa Unggulan TJKT',
            'nisn' => '99001122',
            'jk' => 'L',
            'rombel_id' => $tjktRombel?->id,
            'program_id' => 2, // UNG
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kab. Garut',
            'kecamatan' => 'Garut Kota',
            'kelurahan' => 'Kota Kulon',
            'is_active' => true,
        ]);

        Siswa::create([
            'nama' => 'Siswa Reguler PPLG',
            'nisn' => '99001123',
            'jk' => 'P',
            'rombel_id' => $pplgRombel?->id,
            'program_id' => 1, // REG
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kab. Garut',
            'kecamatan' => 'Garut Kota',
            'kelurahan' => 'Kota Kulon',
            'is_active' => true,
        ]);

        // Verifikasi Service agregasi
        $service = app(SpmbWilayahService::class);
        $analytics = $service->getWilayahAnalytics();
        $tree = $analytics['tree'];

        $this->assertArrayHasKey('N:Indonesia', $tree);
        $indo = $tree['N:Indonesia'];
        $this->assertEquals(2, $indo['total_siswa']);
        $this->assertEquals(1, $indo['siswa_l']);
        $this->assertEquals(1, $indo['siswa_p']);
        $this->assertEquals(1, $indo['program_ung']);
        $this->assertEquals(1, $indo['program_reg']);
        $this->assertEquals(1, $indo['jurusan_counts']['TJKT']);
        $this->assertEquals(1, $indo['jurusan_counts']['PPLG']);
        $this->assertEquals(0, $indo['jurusan_counts']['HTL']);

        // Verifikasi Livewire Render dengan Header Baru & Shading Pivot (setelah expandAll)
        Livewire::test(WilayahIndex::class)
            ->assertSee('Wilayah asal siswa')
            ->assertSee('Total Siswa')
            ->assertSee('Ung')
            ->assertSee('Reg')
            ->assertSee('TJKT')
            ->assertSee('PPLG')
            ->assertSee('PMN')
            ->assertSee('HTL')
            ->assertSee('Porsi (%)')
            ->assertSee('Negara')
            ->assertSee('Provinsi')
            ->assertSee('Kab/Kota')
            ->call('expandAll')
            ->assertSee('Kecamatan')
            ->assertSee('Desa')
            ->assertSee('Kota Kulon');
    }

    public function test_top_10_kabupaten_feeder_bisa_di_toggle_dan_default_tertutup(): void
    {
        Siswa::create([
            'nama' => 'Siswa Garut',
            'nisn' => '99009988',
            'jk' => 'L',
            'negara' => 'Indonesia',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kab. Garut',
            'kecamatan' => 'Tarogong Kaler',
            'kelurahan' => 'Pasawahan',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(WilayahIndex::class)
            // Assert default tertutup (showTopKabupaten = false)
            ->assertSet('showTopKabupaten', false)
            ->assertSee('Top 10 Kabupaten / Kota Feeder')
            ->assertSee('Tampilkan')
            ->assertDontSee('Porsi kontribusi')
            // Toggle buka
            ->call('toggleTopKabupaten')
            ->assertSet('showTopKabupaten', true)
            ->assertSee('Sembunyikan')
            ->assertSee('Porsi kontribusi')
            ->assertSee('Peringkat')
            // Toggle tutup kembali
            ->call('toggleTopKabupaten')
            ->assertSet('showTopKabupaten', false)
            ->assertSee('Tampilkan')
            ->assertDontSee('Porsi kontribusi');
    }
}
