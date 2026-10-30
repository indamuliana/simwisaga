<?php

namespace Tests\Feature\AnalitikSpmb;

use App\Models\User;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\AnalitikSpmb\Livewire\MasterSmpIndex;
use App\Modules\AnalitikSpmb\Livewire\PerbaikanSmpIndex;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Services\SpmbMasterSmpImportService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\AnalitikSpmbPermissionSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class MasterSmpTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);
        $this->seed(AnalitikSpmbPermissionSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_master_smp',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_master_smp_dapat_diakses_oleh_user_berizin(): void
    {
        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->assertStatus(200)
            ->assertSee('Master Data SMP Resmi')
            ->assertSee('Total Master SMP')
            ->assertSee('Import Excel / CSV');
    }

    public function test_import_csv_master_smp_berhasil_dan_upsert_berdasarkan_npsn(): void
    {
        $csvContent = implode("\n", [
            'NPSN,Nama,Bentuk,Status,Jenjang,Kabupaten,Kecamatan,Kelurahan,Alamat',
            '20270258,SMP MANGGALA LELES,SMP,SWASTA,PENDIDIKAN DASAR,KAB. GARUT,KEC. LELES,SUKARAME,LELES',
            '20220385,SMP NEGERI 1 BOGOR,SMP,NEGERI,PENDIDIKAN DASAR,KOTA BOGOR,KEC. KOTA BOGOR TENGAH,PALEDANG,JL. IR. H. DJUANDA BOGOR NO 16',
        ]);

        $file = UploadedFile::fake()->createWithContent('master_smp_jabar.csv', $csvContent);

        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->set('inputProvinsi', 'Jawa Barat')
            ->set('importFile', $file)
            ->call('processImport')
            ->assertHasNoErrors()
            ->assertSee('Import Jawa Barat berhasil!');

        // Verifikasi normalisasi: Kab. dipertahankan, KEC. dihilangkan
        $this->assertDatabaseHas('spmb_master_smps', [
            'npsn' => '20270258',
            'nama' => 'SMP MANGGALA LELES',
            'status' => 'Swasta',
            'kabupaten' => 'Kab. Garut',
            'kecamatan' => 'Leles',
            'provinsi' => 'Jawa Barat',
        ]);

        $this->assertDatabaseHas('spmb_master_smps', [
            'npsn' => '20220385',
            'nama' => 'SMP NEGERI 1 BOGOR',
            'status' => 'Negeri',
            'kabupaten' => 'Kota Bogor',
            'kecamatan' => 'Kota Bogor Tengah',
            'provinsi' => 'Jawa Barat',
        ]);

        // Uji Upsert: import ulang dengan perubahan alamat
        $csvUpdatedContent = implode("\n", [
            'NPSN,Nama,Bentuk,Status,Jenjang,Kabupaten,Kecamatan,Kelurahan,Alamat',
            '20270258,SMP MANGGALA LELES,SMP,SWASTA,PENDIDIKAN DASAR,KAB. GARUT,KEC. LELES,SUKARAME,JALAN RAYA LELES NO 99',
            '70009639,SMP NEGERI 50 KOTA BEKASI,SMP,NEGERI,PENDIDIKAN DASAR,KOTA BEKASI,KEC. RAWALUMBU,SEPANJANG JAYA,JL PARANGTRITIS',
        ]);

        $fileUpdated = UploadedFile::fake()->createWithContent('master_smp_updated.csv', $csvUpdatedContent);

        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->set('inputProvinsi', 'Jawa Barat')
            ->set('importFile', $fileUpdated)
            ->call('processImport')
            ->assertHasNoErrors();

        // Total harus 3 (bukan 4), dan alamat terupdate
        $this->assertEquals(3, SpmbMasterSmp::count());
        $this->assertEquals('JALAN RAYA LELES NO 99', SpmbMasterSmp::where('npsn', '20270258')->value('alamat'));
    }

    public function test_filter_dan_penetrasi_spmb_berfungsi(): void
    {
        $m1 = SpmbMasterSmp::create([
            'npsn' => '20270258',
            'nama' => 'SMP MANGGALA LELES',
            'bentuk' => 'SMP',
            'status' => 'Swasta',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Garut',
            'kecamatan' => 'Leles',
        ]);

        $m2 = SpmbMasterSmp::create([
            'npsn' => '20220385',
            'nama' => 'SMP NEGERI 1 BOGOR',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bogor',
            'kecamatan' => 'Kota Bogor Tengah',
        ]);

        // Hubungkan m1 ke master sekolah asal
        SpmbSekolahAsal::create([
            'master_smp_id' => $m1->id,
            'nama_resmi' => 'SMP MANGGALA LELES',
            'npsn' => '20270258',
            'jenis' => 'SMP',
            'status_sekolah' => 'Swasta',
            'kabupaten_kota' => 'Kab. Garut',
            'tier' => 'Potensial',
        ]);

        // Uji filter penetrasi = terjaring (hanya m1 yang muncul)
        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->set('filterPenetrasi', 'terjaring')
            ->assertSee('SMP MANGGALA LELES')
            ->assertDontSee('SMP NEGERI 1 BOGOR');

        // Uji filter penetrasi = belum (hanya m2 yang muncul)
        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->set('filterPenetrasi', 'belum')
            ->assertSee('SMP NEGERI 1 BOGOR')
            ->assertDontSee('SMP MANGGALA LELES');

        // Uji filter kabupaten = Kota Bogor
        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->set('filterKabupaten', 'Kota Bogor')
            ->assertSee('SMP NEGERI 1 BOGOR')
            ->assertDontSee('SMP MANGGALA LELES');
    }

    public function test_jadikan_sekolah_asal_membuat_master_asal_smp_dan_alias(): void
    {
        $master = SpmbMasterSmp::create([
            'npsn' => '70009639',
            'nama' => 'SMP NEGERI 50 KOTA BEKASI',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bekasi',
            'kecamatan' => 'Rawalumbu',
        ]);

        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->call('jadikanSekolahAsal', $master->id)
            ->assertHasNoErrors()
            ->assertSee("SMP NEGERI 50 KOTA BEKASI");

        $this->assertDatabaseHas('spmb_sekolah_asals', [
            'master_smp_id' => $master->id,
            'nama_resmi' => 'SMP NEGERI 50 KOTA BEKASI',
            'npsn' => '70009639',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'kabupaten_kota' => 'Kota Bekasi',
            'kecamatan' => 'Rawalumbu',
        ]);

        $this->assertDatabaseHas('spmb_sekolah_asal_aliases', [
            'alias_raw' => 'SMP NEGERI 50 KOTA BEKASI',
        ]);
    }

    public function test_auto_match_master_smp_di_perbaikan_smp(): void
    {
        $rombel = Rombel::firstOrFail();

        // Buat data master SMP resmi
        $master = SpmbMasterSmp::create([
            'npsn' => '20270258',
            'nama' => 'SMP MANGGALA LELES',
            'bentuk' => 'SMP',
            'status' => 'Swasta',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Garut',
            'kecamatan' => 'Leles',
        ]);

        // Siswa dengan sekolah asal sama persis nama master tapi belum ada di spmb_sekolah_asals
        Siswa::create([
            'nama' => 'Jajang Mulyana',
            'nipd' => '12900001',
            'nisn' => '0097777771',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMP MANGGALA LELES',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        // Jalankan autoMatch di PerbaikanSmpIndex
        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->call('autoMatchWithMasterSmp')
            ->assertHasNoErrors()
            ->assertSee('Auto-Match selesai!');

        // Verifikasi terpetakan
        $this->assertDatabaseHas('spmb_sekolah_asals', [
            'master_smp_id' => $master->id,
            'npsn' => '20270258',
            'nama_resmi' => 'SMP MANGGALA LELES',
            'kecamatan' => 'Leles',
            'kabupaten_kota' => 'Kab. Garut',
        ]);
    }

    public function test_unduh_template_csv_dan_excel(): void
    {
        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->call('downloadTemplate', 'csv')
            ->assertFileDownloaded('Template_Master_SMP.csv');

        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->call('downloadTemplate', 'xlsx')
            ->assertFileDownloaded('Template_Master_SMP.xlsx');
    }

    public function test_tombol_sinkronkan_ke_master_smp_mencocokkan_data_secara_multilevel(): void
    {
        $rombel = Rombel::firstOrFail();

        // 1. Data Master SMP Resmi
        $m1 = SpmbMasterSmp::create([
            'npsn' => '20220385',
            'nama' => 'SMP NEGERI 1 BOGOR',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bogor',
            'kecamatan' => 'Kota Bogor Tengah',
        ]);

        $m2 = SpmbMasterSmp::create([
            'npsn' => '20270258',
            'nama' => 'SMP MANGGALA LELES',
            'bentuk' => 'SMP',
            'status' => 'Swasta',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kab. Garut',
            'kecamatan' => 'Leles',
        ]);

        $m3 = SpmbMasterSmp::create([
            'npsn' => '70009639',
            'nama' => 'SMP NEGERI 50 KOTA BEKASI',
            'bentuk' => 'SMP',
            'status' => 'Negeri',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bekasi',
            'kecamatan' => 'Rawalumbu',
        ]);

        // 2. Local master 1 dengan singkatan 'SMPN 1 Bogor' (belum ada NPSN/Kecamatan)
        $local1 = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 Bogor',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'kabupaten_kota' => 'Garut',
        ]);

        // Local master 2 dengan nama case-insensitive 'smp manggala leles'
        $local2 = SpmbSekolahAsal::create([
            'nama_resmi' => 'smp manggala leles',
            'jenis' => 'SMP',
            'status_sekolah' => 'Swasta',
            'kabupaten_kota' => 'Garut',
        ]);

        // 3. Siswa unmapped dengan nama 'SMP NEGERI 50 KOTA BEKASI'
        Siswa::create([
            'nama' => 'Ahmad Bekasi',
            'nipd' => '13000001',
            'nisn' => '0098888881',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMP NEGERI 50 KOTA BEKASI',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        // 4. Jalankan tombol sync di MasterSmpIndex
        Livewire::actingAs($this->admin)
            ->test(MasterSmpIndex::class)
            ->call('syncToSekolahAsal')
            ->assertHasNoErrors()
            ->assertSee('Sinkronisasi selesai!');

        // Verifikasi local1 terhubung ke m1 (via Level 4 prefix abbreviation)
        $this->assertEquals($m1->id, $local1->fresh()->master_smp_id);
        $this->assertEquals('20220385', $local1->fresh()->npsn);
        $this->assertEquals('Kota Bogor', $local1->fresh()->kabupaten_kota);
        $this->assertEquals('Kota Bogor Tengah', $local1->fresh()->kecamatan);

        // Verifikasi local2 terhubung ke m2 (via Level 2 exact name)
        $this->assertEquals($m2->id, $local2->fresh()->master_smp_id);
        $this->assertEquals('20270258', $local2->fresh()->npsn);
        $this->assertEquals('Kab. Garut', $local2->fresh()->kabupaten_kota);
        $this->assertEquals('Leles', $local2->fresh()->kecamatan);

        // Verifikasi siswa unmapped otomatis terpetakan ke master m3
        $this->assertDatabaseHas('spmb_sekolah_asals', [
            'master_smp_id' => $m3->id,
            'npsn' => '70009639',
            'nama_resmi' => 'SMP NEGERI 50 KOTA BEKASI',
        ]);

        // 5. Jalankan tombol sync di AsalSmpIndex
        Livewire::actingAs($this->admin)
            ->test(\App\Modules\AnalitikSpmb\Livewire\AsalSmpIndex::class)
            ->call('syncWithMasterSmp')
            ->assertHasNoErrors()
            ->assertSee('Sinkronisasi Master SMP selesai!');
    }
}
