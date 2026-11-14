<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\PenugasanMengajarIndex;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\AkademikPenugasanPermissionSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\StrukturKurikulumPembelajaranMendalamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenugasanMengajarTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected TahunAjaran $ta;

    protected Semester $semGanjil;

    protected Semester $semGenap;

    protected Pegawai $guru;

    protected Mapel $mapel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(StrukturKurikulumPembelajaranMendalamSeeder::class);
        $this->seed(AkademikPenugasanPermissionSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kurikulum',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::first();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->first();
        $this->semGenap = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'genap')->first();

        // Buat rombel lintas tingkat jika belum ada
        $pplg = Jurusan::where('kode', 'PPLG')->first();
        $tjkt = Jurusan::where('kode', 'TJKT')->first();

        Rombel::firstOrCreate(
            ['tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $pplg->id, 'tingkat' => 'XI', 'nomor_urut' => 1],
            ['nama' => 'XI PPLG 1']
        );
        Rombel::firstOrCreate(
            ['tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $tjkt->id, 'tingkat' => 'XII', 'nomor_urut' => 1],
            ['nama' => 'XII TJKT 1']
        );

        $this->guru = Pegawai::create([
            'nama_lengkap' => 'Budi Santoso, S.Pd.',
            'jenis_gtk' => 'guru',
            'status_kepegawaian' => 'gty',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->mapel = Mapel::first();
    }

    public function test_can_create_penugasan_full_year_for_multiple_rombels_across_tingkat_and_jurusan(): void
    {
        $rombelX = Rombel::where('tingkat', 'X')->first();
        $rombelXI = Rombel::where('tingkat', 'XI')->first();
        $rombelXII = Rombel::where('tingkat', 'XII')->first();

        $this->assertNotNull($rombelX);
        $this->assertNotNull($rombelXI);
        $this->assertNotNull($rombelXII);
        $this->assertNotEquals($rombelX->tingkat, $rombelXI->tingkat);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'full_year')
            ->set('pegawai_id', $this->guru->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('selectedRombels', [$rombelX->id, $rombelXI->id, $rombelXII->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('Berhasil menyimpan 6 alokasi penugasan mengajar');

        // Harus ada 6 record di tabel penugasan_mengajars (3 rombel x 2 semester)
        $this->assertEquals(6, PenugasanMengajar::where('pegawai_id', $this->guru->id)->count());

        $this->assertDatabaseHas('penugasan_mengajars', [
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombelX->id,
            'mapel_id' => $this->mapel->id,
        ]);

        $this->assertDatabaseHas('penugasan_mengajars', [
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombelX->id,
            'mapel_id' => $this->mapel->id,
        ]);

        $this->assertDatabaseHas('penugasan_mengajars', [
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombelXI->id,
            'mapel_id' => $this->mapel->id,
        ]);

        $this->assertDatabaseHas('penugasan_mengajars', [
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombelXII->id,
            'mapel_id' => $this->mapel->id,
        ]);
    }

    public function test_can_toggle_all_rombels_in_a_tingkat(): void
    {
        $rombelIdsInTingkatX = Rombel::where('tingkat', 'X')->pluck('id')->toArray();

        $comp = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->call('toggleTingkat', 'X');

        // Semua rombel tingkat X harus masuk ke selectedRombels
        foreach ($rombelIdsInTingkatX as $id) {
            $this->assertContains($id, $comp->get('selectedRombels'));
        }

        // Panggil lagi untuk membatalkan
        $comp->call('toggleTingkat', 'X');
        foreach ($rombelIdsInTingkatX as $id) {
            $this->assertNotContains($id, $comp->get('selectedRombels'));
        }
    }

    public function test_can_create_single_semester_penugasan(): void
    {
        $rombel = Rombel::first();

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'ganjil')
            ->set('pegawai_id', $this->guru->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('selectedRombels', [$rombel->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Berhasil menyimpan 1 alokasi penugasan mengajar');

        $this->assertEquals(1, PenugasanMengajar::where('pegawai_id', $this->guru->id)->count());
        $this->assertDatabaseHas('penugasan_mengajars', [
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
        ]);
        $this->assertDatabaseMissing('penugasan_mengajars', [
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
        ]);
    }

    public function test_smart_delete_full_year_deletes_both_semesters(): void
    {
        $rombel = Rombel::first();

        $pGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 3,
        ]);

        $pGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 3,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('promptDelete', $pGanjil->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('partnerPenugasanId', $pGenap->id)
            ->call('executeDelete', 'full_year')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('penugasan_mengajars', ['id' => $pGanjil->id]);
        $this->assertDatabaseMissing('penugasan_mengajars', ['id' => $pGenap->id]);
    }

    public function test_smart_delete_single_semester_only_deletes_target(): void
    {
        $rombel = Rombel::first();

        $pGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 3,
        ]);

        $pGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 3,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('promptDelete', $pGanjil->id)
            ->call('executeDelete', 'single')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('penugasan_mengajars', ['id' => $pGanjil->id]);
        $this->assertDatabaseHas('penugasan_mengajars', ['id' => $pGenap->id]);
    }

    public function test_prevents_deleting_penugasan_with_grades(): void
    {
        $rombel = Rombel::first();

        $p = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 3,
        ]);

        $siswa = Siswa::create([
            'nama' => 'Ahmad Siswa',
            'nisn' => '9988776655',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $p->id,
            'siswa_id' => $siswa->id,
            'nilai_akhir' => 88.5,
            'capaian_kompetensi' => 'Sangat Baik',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('promptDelete', $p->id)
            ->assertSet('showDeleteModal', false)
            ->assertSee('tidak dapat dihapus karena sudah memiliki');

        $this->assertDatabaseHas('penugasan_mengajars', ['id' => $p->id]);
    }

    public function test_modal_only_shows_rombels_allocated_in_struktur_kurikulum_by_tingkat_and_jurusan(): void
    {
        $dpkr = Mapel::where('kode', 'DDK-PPLG')->first();
        $this->assertNotNull($dpkr, 'Mapel DDK-PPLG harus ada dari seeder.');

        $comp = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'full_year')
            ->set('mapel_id', $dpkr->id);

        $allowedRombels = $comp->instance()->getAllowedRombels();
        $this->assertTrue($allowedRombels->isNotEmpty());

        foreach ($allowedRombels as $r) {
            $this->assertEquals('X', $r->tingkat);
            $this->assertEquals('PPLG', $r->jurusan->kode);
        }

        $rombelTjkt = Rombel::whereHas('jurusan', fn ($q) => $q->where('kode', 'TJKT'))->first();
        if ($rombelTjkt) {
            $this->assertFalse($allowedRombels->contains('id', $rombelTjkt->id));
        }

        $rombelXI = Rombel::where('tingkat', 'XI')->first();
        if ($rombelXI) {
            $this->assertFalse($allowedRombels->contains('id', $rombelXI->id));
        }
    }

    public function test_modal_filters_rombels_based_on_semester_jp_allocation(): void
    {
        $paibp = Mapel::where('kode', 'PAIBP')->first();
        $rombelXII = Rombel::where('tingkat', 'XII')->first();
        $this->assertNotNull($rombelXII);

        // Saat periode_mode = 'ganjil', rombel XII tidak boleh muncul karena JP Ganjil = 0
        $compGanjil = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'ganjil')
            ->set('mapel_id', $paibp->id);

        $this->assertFalse($compGanjil->instance()->getAllowedRombels()->contains('id', $rombelXII->id));

        // Saat periode_mode = 'genap', rombel XII harus muncul karena JP Genap = 3
        $compGenap = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'genap')
            ->set('mapel_id', $paibp->id);

        $this->assertTrue($compGenap->instance()->getAllowedRombels()->contains('id', $rombelXII->id));

        // Saat periode_mode = 'full_year', rombel XII harus muncul karena aktif di salah satu semester
        $compFull = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'full_year')
            ->set('mapel_id', $paibp->id);

        $this->assertTrue($compFull->instance()->getAllowedRombels()->contains('id', $rombelXII->id));
    }

    public function test_cannot_save_penugasan_for_rombel_not_in_struktur_kurikulum(): void
    {
        $dpkr = Mapel::where('kode', 'DDK-PPLG')->first();
        $this->assertNotNull($dpkr);
        $rombelTjkt = Rombel::whereHas('jurusan', fn ($q) => $q->where('kode', 'TJKT'))->first();
        $this->assertNotNull($rombelTjkt);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('periode_mode', 'full_year')
            ->set('pegawai_id', $this->guru->id)
            ->set('mapel_id', $dpkr->id)
            ->set('selectedRombels', [$rombelTjkt->id])
            ->call('save')
            ->assertHasErrors(['selectedRombels'])
            ->assertSet('showModal', true);

        $this->assertDatabaseMissing('penugasan_mengajars', [
            'pegawai_id' => $this->guru->id,
            'mapel_id' => $dpkr->id,
            'rombel_id' => $rombelTjkt->id,
        ]);
    }

    public function test_empty_state_shown_when_mapel_not_in_struktur_kurikulum(): void
    {
        $mapelBaru = Mapel::create([
            'kode' => 'ROBOTIK',
            'nama' => 'Robotika dan Otomasi',
            'kelompok' => 'pilihan',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('create')
            ->set('tahun_ajaran_id', $this->ta->id)
            ->set('mapel_id', $mapelBaru->id)
            ->assertSee('Mata Pelajaran Belum Dialokasikan di Kurikulum')
            ->assertSee('Atur di Struktur Kurikulum');
    }

    public function test_single_edit_validates_against_curriculum(): void
    {
        $dpkr = Mapel::where('kode', 'DDK-PPLG')->first();
        $this->assertNotNull($dpkr);
        $rombelPplg = Rombel::where('tingkat', 'X')->whereHas('jurusan', fn ($q) => $q->where('kode', 'PPLG'))->first();
        $rombelTjkt = Rombel::whereHas('jurusan', fn ($q) => $q->where('kode', 'TJKT'))->first();

        $p = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombelPplg->id,
            'mapel_id' => $dpkr->id,
            'jp' => 12,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->call('edit', $p->id)
            ->set('edit_rombel_id', $rombelTjkt->id)
            ->call('save')
            ->assertHasErrors(['edit_rombel_id'])
            ->assertSet('showModal', true);

        $this->assertEquals($rombelPplg->id, $p->fresh()->rombel_id);
    }

    public function test_can_toggle_select_all_and_bulk_delete_penugasan(): void
    {
        $rombel = Rombel::first();

        $p1 = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 2,
        ]);

        $p2 = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 2,
        ]);

        $comp = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterPegawai', $this->guru->id)
            ->call('toggleSelectAll')
            ->assertSet('selectedPenugasanIds', [$p1->id, $p2->id])
            ->call('toggleSelectAll')
            ->assertSet('selectedPenugasanIds', [])
            ->call('toggleSelectAll')
            ->assertSet('selectedPenugasanIds', [$p1->id, $p2->id])
            ->call('promptBulkDelete')
            ->assertSet('showBulkDeleteModal', true)
            ->assertSet('bulkDeleteSafeCount', 2)
            ->assertSet('bulkDeleteSkippedCount', 0)
            ->call('executeBulkDelete')
            ->assertSet('showBulkDeleteModal', false)
            ->assertSet('selectedPenugasanIds', []);

        $this->assertDatabaseMissing('penugasan_mengajars', ['id' => $p1->id]);
        $this->assertDatabaseMissing('penugasan_mengajars', ['id' => $p2->id]);
    }

    public function test_bulk_delete_skips_penugasan_with_grades(): void
    {
        $rombel = Rombel::first();

        $p1 = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 2,
        ]);

        $p2 = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->guru->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $this->mapel->id,
            'jp' => 2,
        ]);

        $siswa = Siswa::create([
            'nama' => 'Budi Siswa',
            'nisn' => '9911223344',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $p1->id,
            'siswa_id' => $siswa->id,
            'nilai_akhir' => 90.0,
            'capaian_kompetensi' => 'Sangat Baik',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('selectedPenugasanIds', [$p1->id, $p2->id])
            ->call('promptBulkDelete')
            ->assertSet('showBulkDeleteModal', true)
            ->assertSet('bulkDeleteSafeCount', 1)
            ->assertSet('bulkDeleteSkippedCount', 1)
            ->call('executeBulkDelete')
            ->assertSet('showBulkDeleteModal', false)
            ->assertSet('selectedPenugasanIds', [])
            ->assertSee('dilewati');

        // p1 yang ada nilai TIDAK boleh terhapus
        $this->assertDatabaseHas('penugasan_mengajars', ['id' => $p1->id]);
        // p2 yang aman terhapus
        $this->assertDatabaseMissing('penugasan_mengajars', ['id' => $p2->id]);
    }

    public function test_route_akademik_penugasan_can_be_accessed(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.penugasan'))
            ->assertOk()
            ->assertSeeLivewire(PenugasanMengajarIndex::class);
    }

    public function test_legacy_kepegawaian_penugasan_redirects_to_akademik_penugasan(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kepegawaian.penugasan'))
            ->assertRedirect(route('akademik.penugasan'));
    }

    public function test_user_with_role_kurikulum_can_access_penugasan_mengajar(): void
    {
        $userKurikulum = User::factory()->create([
            'username' => 'staf_kurikulum',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $userKurikulum->assignRole('Kurikulum');

        $this->actingAs($userKurikulum)
            ->get(route('akademik.penugasan'))
            ->assertOk()
            ->assertSee('Penugasan Mengajar Guru');
    }
}
