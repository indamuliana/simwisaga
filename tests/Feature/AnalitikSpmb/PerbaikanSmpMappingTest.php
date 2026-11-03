<?php

namespace Tests\Feature\AnalitikSpmb;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\AnalitikSpmb\Livewire\PerbaikanSmpIndex;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsalAlias;
use App\Modules\AnalitikSpmb\Services\SpmbDataCleansingService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\AnalitikSpmbPermissionSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PerbaikanSmpMappingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Rombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AnalitikSpmbPermissionSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_analitik',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->rombel = Rombel::first() ?? Rombel::create([
            'nama' => 'PPLG X-1',
            'tingkat' => 'X',
            'jurusan_id' => Jurusan::first()->id,
            'tahun_ajaran_id' => TahunAjaran::first()->id,
            'nomor_urut' => 1,
        ]);
    }

    public function test_merge_school_updates_siswa_and_preserves_raw(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '12345',
            'nisn' => '9876543210',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMP 1 Garut',
        ]);

        $this->assertEquals('SMP 1 Garut', $siswa->sekolah_asal_raw);

        $cleansingService = app(SpmbDataCleansingService::class);
        $result = $cleansingService->mergeSchoolStrings(
            ['SMP 1 Garut'],
            'SMPN 1 GARUT',
            true,
            true
        );

        $this->assertEquals(1, $result['affected_students']);
        $this->assertEquals('SMPN 1 GARUT', $result['target_name']);

        $siswa->refresh();
        $this->assertEquals('SMPN 1 GARUT', $siswa->sekolah_asal);
        $this->assertEquals('SMP 1 Garut', $siswa->sekolah_asal_raw);

        $alias = SpmbSekolahAsalAlias::where('alias_raw', 'SMP 1 Garut')->first();
        $this->assertNotNull($alias);
        $this->assertEquals('SMPN 1 GARUT', $alias->sekolahAsal->nama_resmi);
    }

    public function test_unmap_school_reverts_siswa_and_deletes_alias(): void
    {
        $master = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 GARUT',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Utama',
        ]);

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $master->id,
            'alias_raw' => 'SMP 1 Grt',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Ahmad Dani',
            'nipd' => '22334',
            'nisn' => '9988776655',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMPN 1 GARUT',
            'sekolah_asal_raw' => 'SMP 1 Grt',
        ]);

        $cleansingService = app(SpmbDataCleansingService::class);
        $result = $cleansingService->unmapSchoolString('SMP 1 Grt');

        $this->assertEquals(1, $result['affected_students']);
        $this->assertEquals('SMP 1 Grt', $result['raw_name']);

        $siswa->refresh();
        $this->assertEquals('SMP 1 Grt', $siswa->sekolah_asal);
        $this->assertEquals('SMP 1 Grt', $siswa->sekolah_asal_raw);

        $alias = SpmbSekolahAsalAlias::where('alias_raw', 'SMP 1 Grt')->first();
        $this->assertNull($alias);
    }

    public function test_edit_mapping_updates_siswa_to_new_master(): void
    {
        $oldMaster = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 GARUT',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Utama',
        ]);

        $newMaster = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 2 GARUT',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Potensial',
        ]);

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $oldMaster->id,
            'alias_raw' => 'SMP Satu Grt',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Siti Nurhaliza',
            'nipd' => '55667',
            'nisn' => '1122334455',
            'jk' => 'P',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMPN 1 GARUT',
            'sekolah_asal_raw' => 'SMP Satu Grt',
        ]);

        $cleansingService = app(SpmbDataCleansingService::class);
        $result = $cleansingService->editMapping('SMP Satu Grt', $newMaster->id);

        $this->assertEquals(1, $result['affected_students']);
        $this->assertEquals('SMPN 2 GARUT', $result['target_name']);

        $siswa->refresh();
        $this->assertEquals('SMPN 2 GARUT', $siswa->sekolah_asal);
        $this->assertEquals('SMP Satu Grt', $siswa->sekolah_asal_raw);

        $alias = SpmbSekolahAsalAlias::where('alias_raw', 'SMP Satu Grt')->first();
        $this->assertNotNull($alias);
        $this->assertEquals($newMaster->id, $alias->sekolah_asal_id);
    }

    public function test_livewire_unmap_flow(): void
    {
        $master = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 GARUT',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Utama',
        ]);

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $master->id,
            'alias_raw' => 'SMP 1 Garut Varian',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Dewi Lestari',
            'nipd' => '99001',
            'nisn' => '7788990011',
            'jk' => 'P',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMPN 1 GARUT',
            'sekolah_asal_raw' => 'SMP 1 Garut Varian',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->call('openUnmapModal', 'SMP 1 Garut Varian', 'SMPN 1 GARUT')
            ->assertSet('showUnmapModal', true)
            ->assertSet('unmapRawName', 'SMP 1 Garut Varian')
            ->assertSet('unmapAffectedCount', 1)
            ->call('executeUnmap')
            ->assertSet('showUnmapModal', false);

        $siswa->refresh();
        $this->assertEquals('SMP 1 Garut Varian', $siswa->sekolah_asal);
        $this->assertDatabaseMissing('spmb_sekolah_asal_aliases', [
            'alias_raw' => 'SMP 1 Garut Varian',
        ]);
    }

    public function test_livewire_edit_mapping_flow(): void
    {
        $master1 = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 KADUNGORA',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Utama',
        ]);

        $master2 = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 2 KADUNGORA',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Potensial',
        ]);

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $master1->id,
            'alias_raw' => 'SMP Kadungora Dua',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Rian Pratama',
            'nipd' => '88112',
            'nisn' => '5566778899',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMPN 1 KADUNGORA',
            'sekolah_asal_raw' => 'SMP Kadungora Dua',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->call('openEditMappingModal', 'SMP Kadungora Dua', $master1->id, 'SMPN 1 KADUNGORA')
            ->assertSet('showEditMappingModal', true)
            ->assertSet('editMappingRawName', 'SMP Kadungora Dua')
            ->set('editMappingNewMasterId', $master2->id)
            ->call('executeEditMapping')
            ->assertSet('showEditMappingModal', false);

        $siswa->refresh();
        $this->assertEquals('SMPN 2 KADUNGORA', $siswa->sekolah_asal);
        $this->assertDatabaseHas('spmb_sekolah_asal_aliases', [
            'alias_raw' => 'SMP Kadungora Dua',
            'sekolah_asal_id' => $master2->id,
        ]);
    }

    public function test_sync_all_mapped_to_siswa(): void
    {
        $master = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 3 GARUT',
            'jenis' => 'SMP',
            'status_sekolah' => 'Negeri',
            'tier' => 'Utama',
        ]);

        SpmbSekolahAsalAlias::create([
            'sekolah_asal_id' => $master->id,
            'alias_raw' => 'SMP Tiga Garut',
        ]);

        // Siswa masih memiliki teks raw di sekolah_asal dan belum sinkron
        $siswa = Siswa::create([
            'nama' => 'Farhan Kamil',
            'nipd' => '33441',
            'nisn' => '4455667788',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMP Tiga Garut',
            'sekolah_asal_raw' => 'SMP Tiga Garut',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->call('syncAllMappedToSiswa');

        $siswa->refresh();
        $this->assertEquals('SMPN 3 GARUT', $siswa->sekolah_asal);
        $this->assertEquals('SMP Tiga Garut', $siswa->sekolah_asal_raw);
    }

    public function test_card_rekomendasi_duplikat_menghilang_setelah_disatukan(): void
    {
        // Buat 2 variasi nama sekolah yang mirip dan belum disatukan
        Siswa::create([
            'nama' => 'Siswa A',
            'nipd' => '10001',
            'nisn' => '1000000001',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMP Baitul Hikmah',
            'sekolah_asal_raw' => 'SMP Baitul Hikmah',
        ]);

        Siswa::create([
            'nama' => 'Siswa B',
            'nipd' => '10002',
            'nisn' => '1000000002',
            'jk' => 'P',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMP Baitul Hikmah Tarogong',
            'sekolah_asal_raw' => 'SMP Baitul Hikmah Tarogong',
        ]);

        $cleansingService = app(SpmbDataCleansingService::class);

        // Sebelum disatukan, harus terdeteksi sebagai rekomendasi duplikat
        $suggestionsBefore = $cleansingService->detectPotentialDuplicates();
        $this->assertNotEmpty($suggestionsBefore);

        // Satukan kedua variasi nama tersebut ke master yang sama
        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->call('openMergeFromSuggestion', 0)
            ->set('mergeTargetName', 'SMP Baitul Hikmah Tarogong')
            ->call('executeMerge')
            ->assertSet('showMergeModal', false);

        // Setelah disatukan, card rekomendasi untuk kelompok ini HARUS HILANG
        $suggestionsAfter = $cleansingService->detectPotentialDuplicates();
        $this->assertEmpty($suggestionsAfter);
    }

    public function test_filter_npsn_pada_perbaikan_smp(): void
    {
        $masterA = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMPN 1 Garut',
            'npsn' => '20209001',
            'tier' => 'Utama',
        ]);
        SpmbSekolahAsalAlias::firstOrCreate([
            'alias_raw' => 'SMP 1 Garut',
        ], [
            'sekolah_asal_id' => $masterA->id,
        ]);

        $masterB = SpmbSekolahAsal::create([
            'nama_resmi' => 'SMP Swasta B',
            'npsn' => null,
            'tier' => 'Lainnya',
        ]);
        SpmbSekolahAsalAlias::firstOrCreate([
            'alias_raw' => 'SMP Swasta B Alt',
        ], [
            'sekolah_asal_id' => $masterB->id,
        ]);

        Siswa::create([
            'nama' => 'Siswa 1',
            'nipd' => '20001',
            'nisn' => '2000000001',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMPN 1 Garut',
            'sekolah_asal_raw' => 'SMP 1 Garut',
            'sekolah_asal_id' => $masterA->id,
        ]);

        Siswa::create([
            'nama' => 'Siswa 2',
            'nipd' => '20002',
            'nisn' => '2000000002',
            'jk' => 'P',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMP Swasta B',
            'sekolah_asal_raw' => 'SMP Swasta B Alt',
            'sekolah_asal_id' => $masterB->id,
        ]);

        Siswa::create([
            'nama' => 'Siswa 3',
            'nipd' => '20003',
            'nisn' => '2000000003',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'aktif',
            'sekolah_asal' => 'SMP Belum Petakan',
            'sekolah_asal_raw' => 'SMP Belum Petakan',
        ]);

        $cleansingService = app(SpmbDataCleansingService::class);

        // 1. Tanpa filter: 3 variasi
        $all = $cleansingService->getUniqueSchoolStrings();
        $this->assertCount(3, $all);

        // 2. Filter NPSN filled: hanya Siswa 1 (masterA memiliki NPSN)
        $filled = $cleansingService->getUniqueSchoolStrings(null, null, 'filled');
        $this->assertCount(1, $filled);
        $this->assertEquals('SMP 1 Garut', $filled->first()->raw_name);

        // 3. Filter NPSN mapped_unfilled: hanya Siswa 2 (masterB belum ada NPSN)
        $mappedUnfilled = $cleansingService->getUniqueSchoolStrings(null, null, 'mapped_unfilled');
        $this->assertCount(1, $mappedUnfilled);
        $this->assertEquals('SMP Swasta B Alt', $mappedUnfilled->first()->raw_name);

        // 4. Filter NPSN unfilled (semua yang NPSN-nya kosong: Siswa 2 & Siswa 3)
        $unfilled = $cleansingService->getUniqueSchoolStrings(null, null, 'unfilled');
        $this->assertCount(2, $unfilled);
        $this->assertTrue($unfilled->pluck('raw_name')->contains('SMP Swasta B Alt'));
        $this->assertTrue($unfilled->pluck('raw_name')->contains('SMP Belum Petakan'));

        // 5. Test Livewire integration
        Livewire::actingAs($this->admin)
            ->test(PerbaikanSmpIndex::class)
            ->set('filterNpsn', 'mapped_unfilled')
            ->assertSee('SMP Swasta B Alt')
            ->assertDontSee('SMP 1 Garut')
            ->set('filterNpsn', 'filled')
            ->assertSee('SMP 1 Garut')
            ->assertDontSee('SMP Swasta B Alt');
    }
}
