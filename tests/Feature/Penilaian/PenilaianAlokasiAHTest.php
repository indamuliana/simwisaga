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
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianAlokasiAHTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PenugasanMengajar $pmGanjil;
    protected PenugasanMengajar $pmGenap;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'guru_ah_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $ta = TahunAjaran::first();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->first();
        $this->semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->first();

        $jurusan = Jurusan::first();
        $rombel = Rombel::where('tahun_ajaran_id', $ta->id)->first();
        if (!$rombel) {
            $rombel = Rombel::create([
                'tahun_ajaran_id' => $ta->id,
                'jurusan_id' => $jurusan->id,
                'tingkat' => 'X',
                'nomor_urut' => 99,
                'nama' => 'X PPLG 99',
                'kapasitas' => 36,
            ]);
        }

        $mapel = Mapel::first();

        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Guru Informatika',
            'nip' => '199001012015011001',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->pmGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $rombel->id,
            'jp' => 4,
        ]);

        $this->pmGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $rombel->id,
            'jp' => 4,
        ]);

        $this->siswa1 = Siswa::create([
            'nama' => 'Ahmad Dahlan',
            'nisn' => '9988771122',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Bunga Citra',
            'nisn' => '9988771133',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        RombelSiswa::create(['tahun_ajaran_id' => $ta->id, 'rombel_id' => $rombel->id, 'siswa_id' => $this->siswa1->id, 'status' => 'aktif']);
        RombelSiswa::create(['tahun_ajaran_id' => $ta->id, 'rombel_id' => $rombel->id, 'siswa_id' => $this->siswa2->id, 'status' => 'aktif']);
    }

    public function test_form_tp_mewajibkan_alokasi_ah_dan_menyesuaikan_opsi_semester(): void
    {
        // Ganjil: opsi AH 1 s.d AH 4
        $comp = Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('tp_semester_id', $this->semGanjil->id)
            ->set('tp_kode', 'TP 1')
            ->set('tp_deskripsi', 'Mengenal perangkat keras komputer')
            ->set('tp_alokasi_ah', '') // Kosongkan untuk uji validasi required
            ->call('saveTP')
            ->assertHasErrors(['tp_alokasi_ah' => 'required']);

        // Isi AH 1 dan simpan
        $comp->set('tp_alokasi_ah', 'AH 1')
            ->call('saveTP')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'alokasi_ah' => 'AH 1',
        ]);

        // Alihkan ke Semester Genap: opsi AH harus AH 5 s.d AH 8
        $comp->set('tp_semester_id', $this->semGenap->id)
            ->set('tp_kode', 'TP 5')
            ->set('tp_deskripsi', 'Membuat basis data relasional')
            ->set('tp_alokasi_ah', 'AH 5')
            ->call('saveTP')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'kode' => 'TP 5',
            'alokasi_ah' => 'AH 5',
        ]);
    }

    public function test_input_nilai_ah_otomatis_menduplikasi_nilai_ke_seluruh_tp_yang_dinaungi(): void
    {
        // Buat 1 TP di AH 1, dan 2 TP di AH 2
        $tp1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Pengenalan jaringan komputer',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        $tp2 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 2',
            'deskripsi' => 'Konfigurasi IP Address statis',
            'alokasi_ah' => 'AH 2',
            'is_active' => true,
        ]);

        $tp3 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 3',
            'deskripsi' => 'Pengujian konektivitas jaringan LAN',
            'alokasi_ah' => 'AH 2', // Satu AH menaungi 2 TP
            'is_active' => true,
        ]);

        // Input Nilai di Tab 2
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif')
            ->assertSee('AH 1')
            ->assertSee('AH 2')
            ->assertSee('STS 1')
            ->assertSee('AH 3')
            ->assertSee('AH 4')
            ->assertSee('SAS')
            ->set("ahData.{$this->siswa1->id}.AH 1", 85)
            ->set("ahData.{$this->siswa1->id}.AH 2", 90) // AH 2 = 90
            ->set("sumatifData.{$this->siswa1->id}.sts", 80)
            ->set("sumatifData.{$this->siswa1->id}.sas", 88)
            ->call('saveNilai')
            ->assertHasNoErrors();

        // Cek tabel NilaiAsesmenHarian
        $this->assertDatabaseHas('nilai_asesmen_harians', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 85,
        ]);

        $this->assertDatabaseHas('nilai_asesmen_harians', [
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 2',
            'nilai' => 90,
        ]);

        // Pastikan TP 2 dan TP 3 keduanya otomatis mendapat nilai 90
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $tp2->id,
            'nilai' => 90,
        ]);

        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $tp3->id,
            'nilai' => 90,
        ]);

        // Pastikan TP 1 mendapat nilai 85
        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa1->id,
            'tujuan_pembelajaran_id' => $tp1->id,
            'nilai' => 85,
        ]);
    }

    public function test_rekap_menghitung_rata_formatif_berbasis_sesi_ah(): void
    {
        // AH 1 = 80 (1 TP)
        // AH 2 = 90 (2 TP)
        // Rata-rata AH harus (80 + 90) / 2 = 85.00
        $tp1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Pengenalan jaringan dasar',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);
        $tp2 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 2',
            'deskripsi' => 'Konfigurasi IP',
            'alokasi_ah' => 'AH 2',
            'is_active' => true,
        ]);
        $tp3 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 3',
            'deskripsi' => 'Testing koneksi',
            'alokasi_ah' => 'AH 2',
            'is_active' => true,
        ]);

        NilaiAsesmenHarian::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->pmGanjil->id, 'kode_ah' => 'AH 1', 'nilai' => 80]);
        NilaiAsesmenHarian::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->pmGanjil->id, 'kode_ah' => 'AH 2', 'nilai' => 90]);
        NilaiFormatif::create(['siswa_id' => $this->siswa1->id, 'tujuan_pembelajaran_id' => $tp1->id, 'nilai' => 80]);
        NilaiFormatif::create(['siswa_id' => $this->siswa1->id, 'tujuan_pembelajaran_id' => $tp2->id, 'nilai' => 90]);
        NilaiFormatif::create(['siswa_id' => $this->siswa1->id, 'tujuan_pembelajaran_id' => $tp3->id, 'nilai' => 90]);

        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'rekap')
            ->assertSee('85'); // (80 + 90) / 2 = 85

        $na = NilaiAkhir::where('siswa_id', $this->siswa1->id)->where('penugasan_mengajar_id', $this->pmGanjil->id)->first();
        $this->assertNotNull($na);
        $this->assertEquals(85.0, (float) $na->rata_formatif);
        // Deskripsi tertinggi harus membaca TP yang nilainya 90 (TP 2 atau TP 3)
        $this->assertStringContainsString('penguasaan yang sangat baik', $na->deskripsi_tertinggi);
        // Karena nilai terendah adalah 80 (>= KKM 75), siswa tidak diberikan catatan "perlu bimbingan"
        $this->assertEmpty($na->deskripsi_terendah);

        // Jika siswa memiliki nilai di bawah KKM (< 75), harus muncul catatan bimbingan
        NilaiFormatif::where('siswa_id', $this->siswa1->id)->where('tujuan_pembelajaran_id', $tp1->id)->update(['nilai' => 68]);
        NilaiAsesmenHarian::where('siswa_id', $this->siswa1->id)->where('kode_ah', 'AH 1')->update(['nilai' => 68]);

        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('calculateRekap');

        $naRemedial = NilaiAkhir::where('siswa_id', $this->siswa1->id)->where('penugasan_mengajar_id', $this->pmGanjil->id)->first();
        $this->assertStringContainsString('Perlu bimbingan lebih lanjut', $naRemedial->deskripsi_terendah);
    }
}
