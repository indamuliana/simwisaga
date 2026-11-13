<?php

namespace Tests\Feature\Kepegawaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Livewire\PenugasanMengajarIndex;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
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
}
