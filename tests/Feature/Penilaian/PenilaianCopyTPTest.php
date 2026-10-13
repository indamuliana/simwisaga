<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\PenilaianInput;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\TujuanPembelajaranCopier;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianCopyTPTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected Pegawai $pegawai;
    protected Mapel $mapel;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected Rombel $rombelSource;
    protected Rombel $rombelTarget1;
    protected Rombel $rombelTarget2;
    protected Rombel $rombelBedaTingkat;
    protected PenugasanMengajar $pmSourceGanjil;
    protected PenugasanMengajar $pmSourceGenap;
    protected TujuanPembelajaran $tpGanjil1;
    protected TujuanPembelajaran $tpGanjil2;
    protected TujuanPembelajaran $tpGenap1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_copy_tp',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruUser->assignRole('Super Admin');

        $ta = TahunAjaran::first();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->first();
        $this->semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->first();

        $jurusan1 = Jurusan::first();
        $jurusan2 = Jurusan::skip(1)->first() ?? $jurusan1;

        $this->pegawai = Pegawai::first() ?? Pegawai::create([
            'nama_lengkap' => 'Guru Penilaian Test',
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
        ]);

        $this->mapel = Mapel::first() ?? Mapel::create([
            'kode' => 'MAPEL_TEST',
            'nama' => 'Mata Pelajaran Test',
            'kelompok' => 'A',
        ]);

        // Rombel Sumber (Tingkat X)
        $this->rombelSource = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan1->id,
            'tingkat' => 'X',
            'nomor_urut' => 101,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        // Rombel Target 1 (Tingkat X beda jurusan)
        $this->rombelTarget1 = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan2->id,
            'tingkat' => 'X',
            'nomor_urut' => 102,
            'nama' => 'X HTL 1',
            'kapasitas' => 36,
        ]);

        // Rombel Target 2 (Tingkat X)
        $this->rombelTarget2 = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan1->id,
            'tingkat' => 'X',
            'nomor_urut' => 103,
            'nama' => 'X PPLG 2',
            'kapasitas' => 36,
        ]);

        // Rombel Beda Tingkat (Tingkat XI)
        $this->rombelBedaTingkat = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan1->id,
            'tingkat' => 'XI',
            'nomor_urut' => 104,
            'nama' => 'XI PPLG 1',
            'kapasitas' => 36,
        ]);

        // Penugasan Mengajar Sumber
        $this->pmSourceGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombelSource->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $this->pegawai->id,
            'jp' => 4,
            'keterangan' => 'Penugasan Sumber Ganjil',
        ]);

        $this->pmSourceGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'rombel_id' => $this->rombelSource->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $this->pegawai->id,
            'jp' => 4,
            'keterangan' => 'Penugasan Sumber Genap',
        ]);

        // TP Sumber
        $this->tpGanjil1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmSourceGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Memahami konsep dasar algoritma pemrograman',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        $this->tpGanjil2 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmSourceGanjil->id,
            'kode' => 'TP 2',
            'deskripsi' => 'Menerapkan struktur kontrol percabangan dan perulangan',
            'alokasi_ah' => 'AH 2',
            'is_active' => true,
        ]);

        $this->tpGenap1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmSourceGenap->id,
            'kode' => 'TP 5',
            'deskripsi' => 'Mengembangkan aplikasi web sederhana berbasis framework',
            'alokasi_ah' => 'AH 5',
            'is_active' => true,
        ]);
    }

    public function test_get_available_target_rombels_filters_by_tingkat_and_excludes_source(): void
    {
        $copier = new TujuanPembelajaranCopier();
        $targetRombels = $copier->getAvailableTargetRombels($this->pmSourceGanjil);

        $targetIds = $targetRombels->pluck('id')->all();

        // Rombel sumber tidak boleh masuk dalam daftar tujuan
        $this->assertNotContains($this->rombelSource->id, $targetIds);

        // Rombel tingkat X lain harus masuk
        $this->assertContains($this->rombelTarget1->id, $targetIds);
        $this->assertContains($this->rombelTarget2->id, $targetIds);

        // Rombel beda tingkat (XI) tidak boleh masuk
        $this->assertNotContains($this->rombelBedaTingkat->id, $targetIds);
    }

    public function test_copy_tps_copies_all_year_tps_to_target_rombel_and_creates_penugasan_if_missing(): void
    {
        $copier = new TujuanPembelajaranCopier();

        // Target rombel 1 awalnya belum punya penugasan sama sekali
        $result = $copier->copyTps($this->pmSourceGanjil, [$this->rombelTarget1->id], 'all');

        $this->assertEquals(1, $result['rombels_count']);
        $this->assertEquals(3, $result['source_tps_count']);
        $this->assertEquals(3, $result['created_count']);
        $this->assertEquals(0, $result['updated_count']);

        // Pastikan penugasan mengajar ganjil & genap di rombel target otomatis terbuat
        $targetPmGanjil = PenugasanMengajar::where('rombel_id', $this->rombelTarget1->id)
            ->where('mapel_id', $this->mapel->id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $targetPmGenap = PenugasanMengajar::where('rombel_id', $this->rombelTarget1->id)
            ->where('mapel_id', $this->mapel->id)
            ->where('semester_id', $this->semGenap->id)
            ->first();

        $this->assertNotNull($targetPmGanjil);
        $this->assertNotNull($targetPmGenap);

        // Pastikan TP ganjil dan genap berhasil disalin
        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $targetPmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Memahami konsep dasar algoritma pemrograman',
            'alokasi_ah' => 'AH 1',
        ]);

        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $targetPmGenap->id,
            'kode' => 'TP 5',
            'deskripsi' => 'Mengembangkan aplikasi web sederhana berbasis framework',
            'alokasi_ah' => 'AH 5',
        ]);
    }

    public function test_copy_tps_smart_sync_updates_existing_and_preserves_nilai_formatif(): void
    {
        $copier = new TujuanPembelajaranCopier();

        // Buat penugasan di target rombel 1
        $targetPmGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombelTarget1->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $this->pegawai->id,
            'jp' => 4,
        ]);

        // Buat TP 1 yang sudah ada sebelumnya dengan deskripsi lama
        $existingTpTarget = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $targetPmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Deskripsi lama yang belum diselaraskan',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        // Simulasikan sudah ada siswa dengan nilai formatif pada TP 1 tersebut
        $siswaTarget = Siswa::create([
            'nipd' => '10009901',
            'nisn' => '0010009901',
            'nama' => 'Siswa Target Test',
            'jenis_kelamin' => 'L',
            'rombel_id' => $this->rombelTarget1->id,
            'status' => 'aktif',
        ]);
        $nilaiFormatif = NilaiFormatif::create([
            'siswa_id' => $siswaTarget->id,
            'tujuan_pembelajaran_id' => $existingTpTarget->id,
            'nilai' => 88,
        ]);

        // Jalankan copy TPs
        $result = $copier->copyTps($this->pmSourceGanjil, [$this->rombelTarget1->id], 'all');

        $this->assertEquals(1, $result['updated_count']); // TP 1 diupdate
        $this->assertEquals(2, $result['created_count']); // TP 2 & TP 5 dibuat baru

        // Cek bahwa TP 1 di target rombel telah terupdate deskripsinya
        $existingTpTarget->refresh();
        $this->assertEquals('Memahami konsep dasar algoritma pemrograman', $existingTpTarget->deskripsi);

        // Pastikan relasi nilai formatif siswa tidak hilang atau terhapus
        $this->assertDatabaseHas('nilai_formatifs', [
            'id' => $nilaiFormatif->id,
            'tujuan_pembelajaran_id' => $existingTpTarget->id,
            'nilai' => 88,
        ]);
    }

    public function test_copy_tps_filters_by_semester_scope(): void
    {
        $copier = new TujuanPembelajaranCopier();

        // Salin hanya Semester Ganjil
        $result = $copier->copyTps($this->pmSourceGanjil, [$this->rombelTarget1->id], 'ganjil');

        $this->assertEquals(2, $result['source_tps_count']); // TP 1 & TP 2 saja
        $this->assertEquals(2, $result['created_count']);

        $targetPmGanjil = PenugasanMengajar::where('rombel_id', $this->rombelTarget1->id)
            ->where('mapel_id', $this->mapel->id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $targetPmGanjil->id,
            'kode' => 'TP 1',
        ]);
        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $targetPmGanjil->id,
            'kode' => 'TP 2',
        ]);

        // Pastikan TP 5 (Genap) TIDAK ikut tersalin
        $this->assertDatabaseMissing('tujuan_pembelajarans', [
            'kode' => 'TP 5',
            'deskripsi' => 'Mengembangkan aplikasi web sederhana berbasis framework',
            'penugasan_mengajar_id' => PenugasanMengajar::where('rombel_id', $this->rombelTarget1->id)->pluck('id')->all(),
        ]);
    }

    public function test_independent_edit_after_copy(): void
    {
        $copier = new TujuanPembelajaranCopier();
        $copier->copyTps($this->pmSourceGanjil, [$this->rombelTarget1->id], 'all');

        $targetPm = PenugasanMengajar::where('rombel_id', $this->rombelTarget1->id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $targetTp = TujuanPembelajaran::where('penugasan_mengajar_id', $targetPm->id)
            ->where('kode', 'TP 1')
            ->first();

        // Ubah deskripsi di rombel target
        $targetTp->update(['deskripsi' => 'Deskripsi Khusus untuk Jurusan Perhotelan']);

        // Pastikan rombel sumber tidak berubah
        $this->tpGanjil1->refresh();
        $this->assertEquals('Memahami konsep dasar algoritma pemrograman', $this->tpGanjil1->deskripsi);
        $this->assertEquals('Deskripsi Khusus untuk Jurusan Perhotelan', $targetTp->deskripsi);
    }

    public function test_livewire_copy_tp_modal_flow(): void
    {
        $this->actingAs($this->guruUser);

        $expectedCount = Rombel::where('tingkat', 'X')->where('id', '!=', $this->rombelSource->id)->count();

        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmSourceGanjil->id])
            ->assertSet('showCopyTpModal', false)
            ->call('openCopyTpModal')
            ->assertSet('showCopyTpModal', true)
            ->assertCount('availableTargetRombels', $expectedCount)
            ->call('selectAllTargetRombels')
            ->assertCount('selectedTargetRombelIds', $expectedCount)
            ->call('deselectAllTargetRombels')
            ->assertSet('selectedTargetRombelIds', [])
            ->call('executeCopyTp') // Tanpa rombel terpilih
            ->assertSee('Pilih minimal satu rombel tujuan')
            ->set('selectedTargetRombelIds', [$this->rombelTarget1->id, $this->rombelTarget2->id])
            ->set('copyTpSemesterScope', 'all')
            ->call('executeCopyTp')
            ->assertSet('showCopyTpModal', false)
            ->assertSee('Sukses! Berhasil menyalin');

        // Pastikan data TP sudah tersalin di kedua rombel
        $pmTarget1 = PenugasanMengajar::where('rombel_id', $this->rombelTarget1->id)->where('semester_id', $this->semGanjil->id)->first();
        $pmTarget2 = PenugasanMengajar::where('rombel_id', $this->rombelTarget2->id)->where('semester_id', $this->semGanjil->id)->first();

        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $pmTarget1->id,
            'kode' => 'TP 1',
        ]);
        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $pmTarget2->id,
            'kode' => 'TP 1',
        ]);
    }
}
