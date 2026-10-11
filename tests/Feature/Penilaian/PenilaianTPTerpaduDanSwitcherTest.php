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
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianTPTerpaduDanSwitcherTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected Pegawai $guruPegawai;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected Rombel $rombel;
    protected Mapel $mapel;
    protected PenugasanMengajar $pmGanjil;
    protected PenugasanMengajar $pmGenap;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_terpadu',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruUser->assignRole('Guru');

        $this->guruPegawai = Pegawai::create([
            'user_id' => $this->guruUser->id,
            'nama_lengkap' => 'Guru Penilai, S.Pd.',
            'nip' => '198501012010011999',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->ta = TahunAjaran::create([
            'nama' => '2025/2026',
            'tgl_mulai' => '2025-06-01',
            'tgl_selesai' => '2026-06-01',
            'is_active' => true,
        ]);

        $this->semGanjil = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $this->semGenap = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'genap',
            'is_active' => false,
        ]);

        $jurusan = Jurusan::first();
        $this->rombel = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X HTL 1',
        ]);

        $this->mapel = Mapel::first();

        // Penugasan Ganjil
        $this->pmGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $this->guruPegawai->id,
            'jp' => 3,
        ]);

        // Penugasan Genap
        $this->pmGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $this->guruPegawai->id,
            'jp' => 3,
        ]);

        // Siswa
        $this->siswa1 = Siswa::create([
            'nama' => 'Budi Santosa',
            'rombel_id' => $this->rombel->id,
            'nisn' => '0081234561',
            'jk' => 'L',
            'is_active' => true,
        ]);
        $this->siswa2 = Siswa::create([
            'nama' => 'Siti Aminah',
            'rombel_id' => $this->rombel->id,
            'nisn' => '0081234562',
            'jk' => 'P',
            'is_active' => true,
        ]);

        // TP Ganjil: TP 1, TP 2
        TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Kompetensi dasar ganjil satu',
            'is_active' => true,
        ]);
        TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 2',
            'deskripsi' => 'Kompetensi dasar ganjil dua',
            'is_active' => true,
        ]);

        // TP Genap: TP 3, TP 4
        TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'kode' => 'TP 3',
            'deskripsi' => 'Kompetensi lanjutan genap satu',
            'is_active' => true,
        ]);
        TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'kode' => 'TP 4',
            'deskripsi' => 'Kompetensi lanjutan genap dua',
            'is_active' => true,
        ]);
    }

    public function test_tab_tp_menampilkan_seluruh_tp_lintas_semester_dengan_badge(): void
    {
        $test = Livewire::actingAs($this->guruUser)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id]);

        $test->assertOk()
            ->assertSee('Kelola Tujuan Pembelajaran (TP / CP)')
            ->assertSee('TP 1')
            ->assertSee('TP 2')
            ->assertSee('TP 3')
            ->assertSee('TP 4')
            ->assertSee('Semester Ganjil')
            ->assertSee('Semester Genap')
            ->assertSee('Patokan kode berikutnya:')
            ->assertSee('TP 5');

        // Pastikan allTps memuat total 4 TP dan suggested kode TP 5
        $this->assertCount(4, $test->get('allTps'));
        $this->assertEquals('TP 5', $test->get('suggestedNextKode'));
        $this->assertEquals(5, $test->get('suggestedNextNumber'));
    }

    public function test_guru_dapat_menambah_tp_untuk_semester_tertentu(): void
    {
        Livewire::actingAs($this->guruUser)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('tp_semester_id', $this->semGenap->id)
            ->set('tp_kode', 'CP 5')
            ->set('tp_deskripsi', 'Menerapkan kecerdasan buatan dalam akomodasi perhotelan')
            ->call('saveTP')
            ->assertSee('Tujuan Pembelajaran berhasil ditambahkan.');

        // Verifikasi tersimpan ke penugasan genap
        $newTp = TujuanPembelajaran::where('kode', 'CP 5')->first();
        $this->assertNotNull($newTp);
        $this->assertEquals($this->pmGenap->id, $newTp->penugasan_mengajar_id);
    }

    public function test_guru_dapat_mengedit_dan_menghapus_tp(): void
    {
        $tp3 = TujuanPembelajaran::where('kode', 'TP 3')->first();

        $test = Livewire::actingAs($this->guruUser)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('editTP', $tp3->id);

        $test->assertSet('tp_kode', 'TP 3')
            ->assertSet('edit_tp_id', $tp3->id)
            ->set('tp_kode', 'TP 3 REVISI')
            ->call('saveTP')
            ->assertSee('Tujuan Pembelajaran berhasil diperbarui.');

        $this->assertEquals('TP 3 REVISI', $tp3->fresh()->kode);

        // Uji Hapus TP
        $test->call('hapusTP', $tp3->id)
            ->assertSee('Tujuan Pembelajaran berhasil dihapus.');

        $this->assertNull(TujuanPembelajaran::find($tp3->id));
    }

    public function test_switcher_semester_di_tab_nilai_formatif_memuat_tp_sesuai_semester(): void
    {
        $test = Livewire::actingAs($this->guruUser)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif');

        // Di Ganjil: kolom TP 1, TP 2 serta sumatif STS 1 dan SAS
        $test->assertSee('TP 1')
            ->assertSee('TP 2')
            ->assertSee('STS 1')
            ->assertSee('SAS')
            ->assertDontSee('TP 3')
            ->assertDontSee('TP 4')
            ->assertDontSee('STS 2')
            ->assertDontSee('SAT');

        // Beralih ke Semester Genap
        $test->call('switchNilaiSemester', $this->semGenap->id);

        // Di Genap: kolom TP 3, TP 4 serta sumatif STS 2 dan SAT
        $test->assertSee('TP 3')
            ->assertSee('TP 4')
            ->assertSee('STS 2')
            ->assertSee('SAT')
            ->assertDontSee('TP 1')
            ->assertDontSee('TP 2')
            ->assertDontSee('STS 1')
            ->assertDontSee('SAS');
    }

    public function test_input_nilai_pada_semester_yang_dialihkan_berhasil_disimpan(): void
    {
        $tp3 = TujuanPembelajaran::where('kode', 'TP 3')->first();

        Livewire::actingAs($this->guruUser)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif')
            ->call('switchNilaiSemester', $this->semGenap->id)
            ->set("formatifData.{$this->siswa1->id}.{$tp3->id}", 88)
            ->set("sumatifData.{$this->siswa1->id}.sts", 86)
            ->set("sumatifData.{$this->siswa1->id}.sas", 90)
            ->call('saveNilai')
            ->assertSee('Seluruh Nilai Akademik');

        // Verifikasi tersimpan ke db
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $tp3->id,
            'nilai' => 88,
        ]);

        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'sts' => 86,
            'sas' => 90,
        ]);
    }
}
