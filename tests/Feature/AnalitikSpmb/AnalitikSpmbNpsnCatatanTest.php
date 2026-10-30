<?php

namespace Tests\Feature\AnalitikSpmb;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\AnalitikSpmb\Livewire\AsalSmpIndex;
use App\Modules\AnalitikSpmb\Livewire\PerbaikanSmpIndex;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsalAlias;
use App\Modules\AnalitikSpmb\Services\SpmbSekolahAsalExporter;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnalitikSpmbNpsnCatatanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'super_admin_spmb',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_asal_smp_index_menampilkan_kolom_npsn_dan_catatan(): void
    {
        $sekolah = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 Garut',
            'npsn' => '20209911',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'kecamatan' => 'Garut Kota',
            'kabupaten_kota' => 'Garut',
            'tier' => 'Utama',
            'catatan' => 'Sekolah mitra utama wilayah kota',
        ]);

        $sekolahTanpaNpsn = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMP Swasta Merdeka',
            'npsn' => null,
            'jenis' => 'SMP',
            'status_sekolah' => 'Swasta',
            'tier' => 'Rintisan',
            'catatan' => null,
        ]);

        $rombel = Rombel::firstOrFail();

        Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '12300001',
            'nisn' => '0098765431',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMPN 1 Garut',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AsalSmpIndex::class)
            ->assertStatus(200)
            ->assertSee('NPSN')
            ->assertSee('Catatan')
            ->assertSee('20209911')
            ->assertSee('Sekolah mitra utama wilayah kota')
            ->assertSee('Belum diisi');
    }

    public function test_perbaikan_smp_index_menampilkan_npsn_catatan_dan_dapat_diedit(): void
    {
        $sekolah = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 2 Tarogong',
            'npsn' => null,
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Potensial',
            'catatan' => null,
        ]);

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $sekolah->id,
            'alias_raw' => 'SMP 2 Tarogong Kidul',
        ]);

        $rombel = Rombel::firstOrFail();

        Siswa::create([
            'nama' => 'Siti Aisyah',
            'nipd' => '12300002',
            'nisn' => '0098765432',
            'jk' => 'P',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMP 2 Tarogong Kidul',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->assertStatus(200)
            ->assertSee('NPSN')
            ->assertSee('Catatan Master')
            ->assertSee('Belum diisi')
            ->call('openEditMasterModal', $sekolah->id)
            ->assertSet('showEditMasterModal', true)
            ->set('editMasterNpsn', '20209988')
            ->set('editMasterCatatan', 'Sudah diverifikasi via Dapodik')
            ->call('saveEditMaster')
            ->assertSet('showEditMasterModal', false);

        $this->assertDatabaseHas('spmb_sekolah_asals', [
            'id' => $sekolah->id,
            'npsn' => '20209988',
            'catatan' => 'Sudah diverifikasi via Dapodik',
        ]);
    }

    public function test_exporter_excel_menyertakan_kolom_catatan_dan_npsn(): void
    {
        $sekolah = (object) [
            'nama_resmi' => 'SMPN 1 Garut',
            'npsn' => '20209911',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'kecamatan' => 'Garut Kota',
            'tier' => 'Utama',
            'total_siswa' => 10,
            'percentage' => 50,
            'siswa_l' => 5,
            'siswa_p' => 5,
            'dominant_jurusan' => 'PPLG',
            'catatan' => 'Mitra Utama',
        ];

        $jurusans = Jurusan::all()->all();
        $exporter = new SpmbSekolahAsalExporter();
        $spreadsheet = $exporter->exportRekapSekolah(collect([$sekolah]), [
            'totalSekolah' => 1,
            'totalSiswa' => 10,
            'avgPerSekolah' => 10,
        ], $jurusans);

        $sheet = $spreadsheet->getActiveSheet();
        $headers = [];
        $col = 1;
        while ($sheet->getCellByColumnAndRow($col, 5)->getValue() !== null) {
            $headers[] = $sheet->getCellByColumnAndRow($col, 5)->getValue();
            $col++;
        }

        $this->assertContains('NPSN', $headers);
        $this->assertContains('Catatan Master', $headers);
    }

    public function test_edit_master_nama_resmi_duplikat_memberikan_pesan_validasi_ramah(): void
    {
        $sekolahA = SpmbSekolahAsal::create([
            'nama_resmi' => 'MTSS MATHLAUL ULUM',
            'npsn' => '20278409',
            'jenis' => 'MTs',
            'status_sekolah' => 'Swasta',
            'tier' => 'Rintisan',
        ]);

        $sekolahB = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMP IT Arrahmah',
            'npsn' => null,
            'jenis' => 'SMP',
            'status_sekolah' => 'Swasta',
            'tier' => 'Rintisan',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AsalSmpIndex::class)
            ->call('openModalKelola', $sekolahB->id)
            ->set('formNamaResmi', 'MTSS MATHLAUL ULUM')
            ->call('saveMasterSekolah')
            ->assertHasErrors(['formNamaResmi']);
    }

    public function test_drawer_sinkron_dengan_total_siswa_dan_filter(): void
    {
        $sekolah = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 Kadungora',
            'npsn' => '20209999',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Utama',
        ]);

        $rombelX = Rombel::where('tingkat', 'X')->firstOrFail();
        $rombelXI = Rombel::create([
            'tahun_ajaran_id' => $rombelX->tahun_ajaran_id,
            'jurusan_id' => $rombelX->jurusan_id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI PPLG 1',
        ]);

        // Siswa Kelas X
        Siswa::create([
            'nama' => 'Siswa X',
            'nipd' => '12400001',
            'nisn' => '0091111111',
            'jk' => 'L',
            'rombel_id' => $rombelX->id,
            'sekolah_asal' => 'SMPN 1 Kadungora',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        // Siswa Kelas Lain
        Siswa::create([
            'nama' => 'Siswa XI',
            'nipd' => '12400002',
            'nisn' => '0092222222',
            'jk' => 'P',
            'rombel_id' => $rombelXI->id,
            'sekolah_asal' => 'SMPN 1 Kadungora',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        // Buka drawer tanpa filter -> harus ada 2 siswa
        $component = Livewire::actingAs($this->admin)
            ->test(AsalSmpIndex::class)
            ->call('openDrawer', $sekolah->id);

        $drawerSiswas = $component->viewData('drawerSiswas');
        $this->assertCount(2, $drawerSiswas);

        // Buka drawer dengan filter Tingkat X aktif di halaman utama -> harus hanya 1 siswa
        $componentWithFilter = Livewire::actingAs($this->admin)
            ->test(AsalSmpIndex::class)
            ->set('filterTingkat', $rombelX->tingkat)
            ->call('openDrawer', $sekolah->id);

        $filteredDrawerSiswas = $componentWithFilter->viewData('drawerSiswas');
        $this->assertCount(1, $filteredDrawerSiswas);
        $this->assertEquals('Siswa X', $filteredDrawerSiswas->first()->nama);
    }

    public function test_smpn_1_dan_smpn_2_tidak_dianggap_duplikat(): void
    {
        $rombel = Rombel::firstOrFail();

        Siswa::create([
            'nama' => 'Ahmad',
            'nipd' => '12500001',
            'nisn' => '0093333331',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMPN 1 Leles',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        Siswa::create([
            'nama' => 'Bambang',
            'nipd' => '12500002',
            'nisn' => '0093333332',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMPN 2 Leles',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        $service = app(\App\Modules\AnalitikSpmb\Services\SpmbDataCleansingService::class);
        $suggestions = $service->detectPotentialDuplicates();

        $combinedClusterFound = false;
        foreach ($suggestions as $cluster) {
            $names = array_column($cluster['items'], 'raw_name');
            if (in_array('SMPN 1 Leles', $names) && in_array('SMPN 2 Leles', $names)) {
                $combinedClusterFound = true;
            }
        }

        $this->assertFalse($combinedClusterFound, 'SMPN 1 Leles dan SMPN 2 Leles seharusnya tidak digabung sebagai duplikat!');
    }

    public function test_user_bisa_menandai_rekomendasi_sebagai_bukan_duplikat(): void
    {
        $rombel = Rombel::firstOrFail();

        Siswa::create([
            'nama' => 'Cecep',
            'nipd' => '12500003',
            'nisn' => '0093333333',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMP Pasundan Garut',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        Siswa::create([
            'nama' => 'Deden',
            'nipd' => '12500004',
            'nisn' => '0093333334',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMP Pasundan Garut Kota',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class);

        $initialSuggestions = $component->viewData('suggestions');
        $this->assertNotEmpty($initialSuggestions);

        // Cari index cluster yang memuat 'SMP Pasundan Garut'
        $targetIndex = null;
        foreach ($initialSuggestions as $idx => $cluster) {
            $names = array_column($cluster['items'], 'raw_name');
            if (in_array('SMP Pasundan Garut', $names)) {
                $targetIndex = $idx;
                break;
            }
        }

        $this->assertNotNull($targetIndex);

        // Klik 'Bukan Duplikat'
        $component->call('markAsNotDuplicate', $targetIndex);

        // Verifikasi tersimpan di spmb_ignored_duplicates
        $this->assertDatabaseHas('spmb_ignored_duplicates', [
            'school_a' => 'SMP Pasundan Garut',
            'school_b' => 'SMP Pasundan Garut Kota',
        ]);

        // Verifikasi setelah diabaikan, cluster tersebut hilang dari rekomendasi
        $newSuggestions = $component->viewData('suggestions');
        foreach ($newSuggestions as $cluster) {
            $names = array_column($cluster['items'], 'raw_name');
            $this->assertFalse(in_array('SMP Pasundan Garut', $names) && in_array('SMP Pasundan Garut Kota', $names));
        }
    }

    public function test_sekolah_dengan_karakter_petik_berjalan_tanpa_error(): void
    {
        $rombel = Rombel::firstOrFail();

        Siswa::create([
            'nama' => 'Agus',
            'nipd' => '12600001',
            'nisn' => '0094444441',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'sekolah_asal' => 'SMP "Bina Taruna"',
            'is_active' => true,
            'status' => 'aktif',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->assertStatus(200)
            ->assertSee('SMP &quot;Bina Taruna&quot;', false)
            ->call('openDrawer', 'SMP "Bina Taruna"')
            ->assertSet('drawerSchoolName', 'SMP "Bina Taruna"')
            ->assertSet('showDrawer', true)
            ->call('openRenameModal', 'SMP "Bina Taruna"')
            ->assertSet('renameOldName', 'SMP "Bina Taruna"')
            ->assertSet('showRenameModal', true)
            ->call('openMergeSingle', 'SMP "Bina Taruna"')
            ->assertSet('selectedSchools', ['SMP "Bina Taruna"'])
            ->assertSet('showMergeModal', true);
    }

    public function test_update_all_tiers_menggunakan_agregasi_tunggal(): void
    {
        $rombel = Rombel::firstOrFail();

        $sekolahUtama = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMP Hebat 1',
            'tier' => 'Rintisan',
        ]);
        $sekolahPotensial = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMP Hebat 2',
            'tier' => 'Rintisan',
        ]);

        // Buat 20 siswa untuk sekolah 1
        for ($i = 1; $i <= 20; $i++) {
            Siswa::create([
                'nama' => "Siswa Utama {$i}",
                'nipd' => "127000{$i}",
                'nisn' => "00955555{$i}",
                'jk' => 'L',
                'rombel_id' => $rombel->id,
                'sekolah_asal' => 'SMP Hebat 1',
                'is_active' => true,
                'status' => 'aktif',
            ]);
        }

        // Buat 8 siswa untuk sekolah 2
        for ($i = 1; $i <= 8; $i++) {
            Siswa::create([
                'nama' => "Siswa Potensial {$i}",
                'nipd' => "128000{$i}",
                'nisn' => "00966666{$i}",
                'jk' => 'P',
                'rombel_id' => $rombel->id,
                'sekolah_asal' => 'SMP Hebat 2',
                'is_active' => true,
                'status' => 'aktif',
            ]);
        }

        app(\App\Modules\AnalitikSpmb\Services\SpmbSekolahAsalSyncService::class)->updateAllTiers();

        $this->assertEquals('Utama', $sekolahUtama->fresh()->tier);
        $this->assertEquals('Potensial', $sekolahPotensial->fresh()->tier);
    }
}
