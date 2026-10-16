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
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\PenilaianInput;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\PenilaianScoreCalculator;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianStrukturPerTpTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PenugasanMengajar $penugasan;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected TujuanPembelajaran $tp1;
    protected TujuanPembelajaran $tp2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_tp_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $ta = TahunAjaran::first();
        $semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->first();
        $jurusan = Jurusan::first();
        $rombel = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 10,
            'nama' => 'X PPLG 10',
            'kapasitas' => 36,
        ]);

        $mapel = Mapel::firstOrCreate(
            ['kode' => 'PAIBP'],
            [
                'nama' => 'Pendidikan Agama dan Budi Pekerti',
                'kelompok' => 'A',
                'kkm_default' => 75,
                'is_active' => true,
            ]
        );

        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Guru Agama Islam',
            'nip' => '198801012015011001',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->penugasan = PenugasanMengajar::create([
            'semester_id' => $semGanjil->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $rombel->id,
            'jp' => 3,
        ]);

        $this->siswa1 = Siswa::create([
            'nama' => 'Ahmad Fadhil',
            'nisn' => '0091122334',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Budi Santoso',
            'nisn' => '0091122335',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        RombelSiswa::create([
            'tahun_ajaran_id' => $ta->id,
            'rombel_id' => $rombel->id,
            'siswa_id' => $this->siswa1->id,
            'status' => 'aktif',
        ]);
        RombelSiswa::create([
            'tahun_ajaran_id' => $ta->id,
            'rombel_id' => $rombel->id,
            'siswa_id' => $this->siswa2->id,
            'status' => 'aktif',
        ]);

        // Buat 2 TP di 9M1
        $this->tp1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->penugasan->id,
            'target_periode' => '9M1',
            'kode' => 'TP 1',
            'deskripsi' => 'Memahami rukun iman dan rukun islam',
            'alokasi_ah' => 'AH 1',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $this->tp2 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->penugasan->id,
            'target_periode' => '9M1',
            'kode' => 'TP 2',
            'deskripsi' => 'Menerapkan perilaku jujur dalam kehidupan',
            'alokasi_ah' => 'AH 2',
            'urutan' => 2,
            'is_active' => true,
        ]);
    }

    public function test_tab_formatif_menampilkan_kolom_tp1_tp2_nh_dan_sts1_untuk_9m1(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->call('switchPeriode', '9M1')
            ->assertSee('TP 1')
            ->assertSee('TP 2')
            ->assertSee('NH (Harian)')
            ->assertSee('Default: 100')
            ->assertSee('STS 1')
            ->assertSee('Ahmad Fadhil')
            ->assertSee('Budi Santoso');
    }

    public function test_input_dan_simpan_nilai_tp_nh_dan_sts1_menghasilkan_na_dan_predikat_akurat(): void
    {
        // Ahmad: TP1=90, TP2=94 (Rata TP=92), NH=100, STS1=88
        // Formula: (92 * 0.5) + (88 * 0.3) + (100 * 0.2) = 46 + 26.4 + 20 = 92.4
        // Predikat 92.4 -> Cakap (88 - 93.99)
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->call('switchPeriode', '9M1')
            ->set("tpScores.{$this->siswa1->id}.{$this->tp1->id}", 90)
            ->set("tpScores.{$this->siswa1->id}.{$this->tp2->id}", 94)
            ->set("nhData.{$this->siswa1->id}.9M1", 100)
            ->set("sumatifData.{$this->siswa1->id}.9M1", 88)
            ->call('saveNilai')
            ->assertHasNoErrors()
            ->assertSee('Seluruh Nilai Akademik');

        // Pastikan tersimpan di DB
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 90,
        ]);
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $this->tp2->id,
            'nilai' => 94,
        ]);
        $this->assertDatabaseHas('nilai_harians', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->penugasan->id,
            'target_periode' => '9M1',
            'nilai' => 100,
        ]);
        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->penugasan->id,
            'sts' => 88,
        ]);

        // Cek NilaiAkhir data_9m
        $na = NilaiAkhir::where('siswa_id', $this->siswa1->id)
            ->where('penugasan_mengajar_id', $this->penugasan->id)
            ->first();

        $this->assertNotNull($na);
        $d9m = $na->data_9m['9M1'] ?? [];
        $this->assertEquals(92.0, $d9m['rata_tp']);
        $this->assertEquals(100.0, $d9m['nh']);
        $this->assertEquals(88.0, $d9m['sts']);
        $this->assertEquals(92.4, $d9m['na']);
        $this->assertEquals('Cakap', $d9m['predikat']);
        $this->assertEquals('kompeten', $d9m['status']);
    }

    public function test_nilai_belum_lengkap_dan_di_bawah_kkm_menghasilkan_na_null_dan_predikat_sesuai(): void
    {
        // Siswa 2: TP 1 diisi 70 (< KKM 75), TP 2 kosong
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->call('switchPeriode', '9M1')
            ->set("tpScores.{$this->siswa2->id}.{$this->tp1->id}", 70)
            // TP 2 dibiarkan kosong
            ->set("nhData.{$this->siswa2->id}.9M1", 100)
            ->set("sumatifData.{$this->siswa2->id}.9M1", 80)
            ->call('saveNilai');

        $na = NilaiAkhir::where('siswa_id', $this->siswa2->id)
            ->where('penugasan_mengajar_id', $this->penugasan->id)
            ->first();

        $this->assertNotNull($na);
        $d9m = $na->data_9m['9M1'] ?? [];
        $this->assertNull($d9m['na']); // Ditahan / Null
        $this->assertEquals('Belum Lengkap', $d9m['predikat']);

        // Jika TP 2 diisi 80 (lengkap), tapi TP 1 = 70 (< KKM 75)
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->call('switchPeriode', '9M1')
            ->set("tpScores.{$this->siswa2->id}.{$this->tp2->id}", 80)
            ->call('saveNilai');

        $na = $na->fresh();
        $d9m = $na->data_9m['9M1'] ?? [];
        $this->assertNull($d9m['na']); // Ditahan / Null
        $this->assertEquals('Belum Kompeten', $d9m['predikat']);
        $this->assertEquals('belum_kompeten', $d9m['status']);
    }

    public function test_tab_rekap_menampilkan_rata_tp_sts1_harian_dan_na_9m1(): void
    {
        // Ahmad: TP1=90, TP2=94, NH=100, STS1=88 -> NA=92.4 (Cakap)
        NilaiFormatif::create(['siswa_id' => $this->siswa1->id, 'tujuan_pembelajaran_id' => $this->tp1->id, 'nilai' => 90]);
        NilaiFormatif::create(['siswa_id' => $this->siswa1->id, 'tujuan_pembelajaran_id' => $this->tp2->id, 'nilai' => 94]);
        NilaiHarian::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->penugasan->id, 'target_periode' => '9M1', 'nilai' => 100]);
        NilaiSumatif::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->penugasan->id, 'sts' => 88]);

        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'rekap')
            ->call('switchRekapPeriode', '9M1')
            ->assertSee('Rata-rata TP')
            ->assertSee('Harian / NH')
            ->assertSee('STS 1')
            ->assertSee('NA 9M1')
            ->assertSee('Predikat Capaian')
            ->assertSee('Ahmad Fadhil')
            ->assertSee('92.4')
            ->assertSee('Cakap');
    }

    public function test_tab_rekap_menampilkan_semua_tombol_periode_9m1_sampai_9m4_dan_semua_periode(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'rekap')
            ->assertSee('9M1 (STS 1)')
            ->assertSee('9M2 (SAS)')
            ->assertSee('9M3 (STS 2)')
            ->assertSee('9M4 (SAT)')
            ->assertSee('1 Semester')
            ->assertSee('Semua Periode (9M1 - 9M4)');
    }

    public function test_tab_rekap_akses_url_periode_9m3_pada_penugasan_ganjil_sinkron_ke_genap_dan_tampilkan_data(): void
    {
        $ta = TahunAjaran::first();
        $semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->first();
        $pmGenap = PenugasanMengajar::firstOrCreate([
            'semester_id' => $semGenap->id,
            'pegawai_id' => $this->penugasan->pegawai_id,
            'mapel_id' => $this->penugasan->mapel_id,
            'rombel_id' => $this->penugasan->rombel_id,
        ], ['jp' => 3]);

        $tpGenap = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $pmGenap->id,
            'kode' => 'TP 3',
            'deskripsi' => 'Memahami rukun iman secara mendalam',
            'alokasi_ah' => 'AH 5',
            'target_periode' => '9M3',
            'urutan' => 3,
        ]);

        NilaiFormatif::create(['siswa_id' => $this->siswa1->id, 'tujuan_pembelajaran_id' => $tpGenap->id, 'nilai' => 88]);
        NilaiHarian::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $pmGenap->id, 'target_periode' => '9M3', 'nilai' => 100]);
        NilaiSumatif::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $pmGenap->id, 'sts' => 90]);

        Livewire::withQueryParams(['periode' => '9M3', 'tab' => 'rekap'])
            ->actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->assertSet('rekapPeriode', '9M3')
            ->assertSet('activeNilaiSemesterId', $semGenap->id)
            ->assertSee('STS 2')
            ->assertSee('NA 9M3')
            ->assertSee('91.0')
            ->assertSee('Cakap');
    }

    public function test_tab_rekap_mode_semua_menampilkan_tabel_4_periode_9m1_sampai_9m4_berdampingan(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'rekap')
            ->call('switchRekapPeriode', 'semua')
            ->assertSee('9M1 (STS 1)')
            ->assertSee('9M2 (SAS)')
            ->assertSee('9M3 (STS 2)')
            ->assertSee('9M4 (SAT)')
            ->assertSee('Ringkasan 1 Tahun');
    }
}
