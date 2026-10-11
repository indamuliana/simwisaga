<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\StrukturKurikulumIndex;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Penilaian\Livewire\PenilaianDashboard;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StrukturKurikulumAlokasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Jurusan $jurusan1;
    protected Jurusan $jurusan2;
    protected Mapel $mapel1;
    protected Mapel $mapel2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kurikulum_alokasi',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::first();
        $this->jurusan1 = Jurusan::first();
        $this->jurusan2 = Jurusan::skip(1)->first();

        $this->mapel1 = Mapel::first();
        $this->mapel2 = Mapel::skip(1)->first();
    }

    public function test_model_helper_semester_status_dan_jp(): void
    {
        $skSepanjangTahun = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'jp_per_minggu' => 4,
            'urutan' => 1,
        ]);

        $this->assertEquals('Sepanjang Tahun', $skSepanjangTahun->semester_status);
        $this->assertEquals(4, $skSepanjangTahun->getJpForSemester('Semester 1 (Ganjil)'));
        $this->assertEquals(4, $skSepanjangTahun->getJpForSemester('Semester 2 (Genap)'));

        $skGanjilSaja = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'XI',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 3,
            'jp_genap' => 0,
            'jp_per_minggu' => 3,
            'urutan' => 2,
        ]);

        $this->assertEquals('Ganjil Saja', $skGanjilSaja->semester_status);
        $this->assertEquals(3, $skGanjilSaja->getJpForSemester('Ganjil'));
        $this->assertEquals(0, $skGanjilSaja->getJpForSemester('Genap'));
    }

    public function test_batch_alokasi_multi_jurusan_dan_tingkat_berbeda_jp(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->call('openBatchModal')
            ->assertSet('showBatchModal', true)
            ->set('batch_tahun_ajaran_id', $this->ta->id)
            ->set('batch_mapel_id', $this->mapel1->id)
            ->set('selectedJurusans', [$this->jurusan1->id, $this->jurusan2->id])
            // Tingkat X: 4 JP Ganjil & Genap
            ->set('tingkatSettings.X.aktif', true)
            ->set('tingkatSettings.X.jp_ganjil', 4)
            ->set('tingkatSettings.X.jp_genap', 4)
            // Tingkat XI: 3 JP Ganjil & Genap
            ->set('tingkatSettings.XI.aktif', true)
            ->set('tingkatSettings.XI.jp_ganjil', 3)
            ->set('tingkatSettings.XI.jp_genap', 3)
            // Tingkat XII: Tidak Aktif
            ->set('tingkatSettings.XII.aktif', false)
            ->call('saveBatch')
            ->assertHasNoErrors()
            ->assertSet('showBatchModal', false);

        // Verifikasi Tingkat X di kedua jurusan memiliki 4 JP
        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'jp_per_minggu' => 4,
        ]);

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan2->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'jp_per_minggu' => 4,
        ]);

        // Verifikasi Tingkat XI di kedua jurusan memiliki 3 JP
        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'XI',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 3,
            'jp_genap' => 3,
            'jp_per_minggu' => 3,
        ]);

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan2->id,
            'tingkat' => 'XI',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 3,
            'jp_genap' => 3,
            'jp_per_minggu' => 3,
        ]);

        // Verifikasi Tingkat XII tidak dibuat
        $this->assertDatabaseMissing('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta->id,
            'tingkat' => 'XII',
            'mapel_id' => $this->mapel1->id,
        ]);
    }

    public function test_batch_alokasi_mapel_1_semester_saja(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->call('openBatchModal')
            ->set('batch_tahun_ajaran_id', $this->ta->id)
            ->set('batch_mapel_id', $this->mapel2->id)
            ->set('selectedJurusans', [$this->jurusan1->id])
            ->set('tingkatSettings.X.aktif', true)
            ->set('tingkatSettings.X.samakan', false)
            ->set('tingkatSettings.X.jp_ganjil', 2)
            ->set('tingkatSettings.X.jp_genap', 0)
            ->set('tingkatSettings.XI.aktif', false)
            ->set('tingkatSettings.XII.aktif', false)
            ->call('saveBatch')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel2->id,
            'jp_ganjil' => 2,
            'jp_genap' => 0,
            'jp_per_minggu' => 2,
        ]);
    }

    public function test_duplikasi_struktur_kurikulum_menyalin_jp_ganjil_dan_genap(): void
    {
        $taTarget = TahunAjaran::create([
            'nama' => '2027/2028',
            'tanggal_mulai' => '2027-07-15',
            'tanggal_selesai' => '2028-06-25',
            'is_active' => false,
        ]);

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 3,
            'jp_per_minggu' => 4,
            'urutan' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('sourceTahunAjaranId', $this->ta->id)
            ->set('targetTahunAjaranId', $taTarget->id)
            ->set('duplicateJurusanId', (string) $this->jurusan1->id)
            ->call('executeDuplicate')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $taTarget->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 3,
            'jp_per_minggu' => 4,
        ]);
    }

    public function test_penilaian_dashboard_menyaring_mapel_berdasarkan_semester_aktif(): void
    {
        $rombel = Rombel::firstOrFail();
        $ta = TahunAjaran::find($rombel->tahun_ajaran_id);

        $semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->firstOrFail();
        $semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->firstOrFail();

        // Mapel 1: Hanya Ganjil (jp_ganjil = 3, jp_genap = 0)
        StrukturKurikulum::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $rombel->jurusan_id,
            'tingkat' => $rombel->tingkat,
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 3,
            'jp_genap' => 0,
            'jp_per_minggu' => 3,
            'urutan' => 1,
        ]);

        // Mapel 2: Hanya Genap (jp_ganjil = 0, jp_genap = 2)
        StrukturKurikulum::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $rombel->jurusan_id,
            'tingkat' => $rombel->tingkat,
            'mapel_id' => $this->mapel2->id,
            'jp_ganjil' => 0,
            'jp_genap' => 2,
            'jp_per_minggu' => 2,
            'urutan' => 2,
        ]);

        // Pastikan semester kerja adalah Ganjil
        \App\Modules\Akademik\Support\AcademicContext::setWorkingSemester($semGanjil->id);

        // Test dashboard penilaian saat Ganjil: Mapel 1 tampil, Mapel 2 TIDAK tampil
        Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->set('selectedRombelId', $rombel->id)
            ->assertSee($this->mapel1->nama)
            ->assertDontSee($this->mapel2->nama);

        // Ubah semester kerja menjadi Genap
        \App\Modules\Akademik\Support\AcademicContext::setWorkingSemester($semGenap->id);

        // Test dashboard penilaian saat Genap: Mapel 2 tampil, Mapel 1 TIDAK tampil
        Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->set('selectedRombelId', $rombel->id)
            ->assertSee($this->mapel2->nama)
            ->assertDontSee($this->mapel1->nama);
    }

    public function test_penugasan_mengajar_mengambil_jp_sesuai_semester(): void
    {
        $rombel = Rombel::firstOrFail();
        $ta = TahunAjaran::find($rombel->tahun_ajaran_id);

        $semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->firstOrFail();
        $semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->firstOrFail();

        // Mapel dengan beban berbeda per semester: Ganjil = 4 JP, Genap = 2 JP
        StrukturKurikulum::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $rombel->jurusan_id,
            'tingkat' => $rombel->tingkat,
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 2,
            'jp_per_minggu' => 4,
            'urutan' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Modules\Kepegawaian\Livewire\PenugasanMengajarIndex::class)
            ->set('rombel_id', $rombel->id)
            ->set('mapel_id', $this->mapel1->id)
            ->set('semester_id', $semGanjil->id)
            ->assertSet('jp', 4)
            ->set('semester_id', $semGenap->id)
            ->assertSet('jp', 2);
    }

    public function test_alokasi_dan_penugasan_mendukung_jp_tinggi_seperti_pkl_44_jp(): void
    {
        $rombel = Rombel::firstOrFail();
        $ta = TahunAjaran::find($rombel->tahun_ajaran_id);
        $semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->firstOrFail();

        // 1. Batch alokasi PKL dengan 44 JP di Semester Genap
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->call('openBatchModal')
            ->set('batch_tahun_ajaran_id', $ta->id)
            ->set('batch_mapel_id', $this->mapel2->id)
            ->set('selectedJurusans', [$this->jurusan1->id])
            ->set('tingkatSettings.XII.aktif', true)
            ->set('tingkatSettings.XII.samakan', false)
            ->set('tingkatSettings.XII.jp_ganjil', 0)
            ->set('tingkatSettings.XII.jp_genap', 44)
            ->set('tingkatSettings.X.aktif', false)
            ->set('tingkatSettings.XI.aktif', false)
            ->call('saveBatch')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $this->jurusan1->id,
            'tingkat' => 'XII',
            'mapel_id' => $this->mapel2->id,
            'jp_ganjil' => 0,
            'jp_genap' => 44,
            'jp_per_minggu' => 44,
        ]);

        // 2. Single edit menyimpan JP tinggi (misal 46 JP)
        $sk = StrukturKurikulum::where('tingkat', 'XII')->where('mapel_id', $this->mapel2->id)->firstOrFail();
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->call('edit', $sk->id)
            ->set('jp_genap', 46)
            ->set('samakanJp', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(46, $sk->fresh()->jp_genap);
    }
}


