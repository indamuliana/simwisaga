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
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianBukuNilaiSatuTahunTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PenugasanMengajar $pmGanjil;
    protected PenugasanMengajar $pmGenap;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'guru_buku_nilai',
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
            'nama_lengkap' => 'Guru Pengampu Buku Nilai',
            'nip' => '199201012018011002',
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

        $this->siswa = Siswa::create([
            'nama' => 'Bintang Pratama',
            'nisn' => '1122334455',
            'rombel_id' => $rombel->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);

        RombelSiswa::create([
            'tahun_ajaran_id' => $ta->id,
            'rombel_id' => $rombel->id,
            'siswa_id' => $this->siswa->id,
            'status' => 'aktif',
        ]);

        // Buat TP Ganjil & Genap
        TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'Pengenalan Konsep Dasar',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'kode' => 'TP 5',
            'deskripsi' => 'Pengembangan Sistem Lanjutan',
            'alokasi_ah' => 'AH 5',
            'is_active' => true,
        ]);
    }

    public function test_tabel_input_nilai_menampilkan_1_tahun_penuh_secara_default(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif')
            ->assertSet('tabelTahunMode', 'semua')
            ->assertSee('Semester Ganjil')
            ->assertSee('Semester Genap')
            ->assertSee('AH 1')
            ->assertSee('AH 2')
            ->assertSee('STS 1')
            ->assertSee('AH 3')
            ->assertSee('AH 4')
            ->assertSee('SAS')
            ->assertSee('AH 5')
            ->assertSee('AH 6')
            ->assertSee('STS 2')
            ->assertSee('AH 7')
            ->assertSee('AH 8')
            ->assertSee('SAT')
            ->assertSee('sticky left-0'); // Freeze kolom nama
    }

    public function test_guru_dapat_menyimpan_nilai_ganjil_dan_genap_sekaligus(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif')
            // Input Ganjil
            ->set("ahData.{$this->siswa->id}.AH 1", 88)
            ->set("sumatifDataGanjil.{$this->siswa->id}.sts", 85)
            ->set("sumatifDataGanjil.{$this->siswa->id}.sas", 90)
            // Input Genap
            ->set("ahData.{$this->siswa->id}.AH 5", 92)
            ->set("sumatifDataGenap.{$this->siswa->id}.sts", 89)
            ->set("sumatifDataGenap.{$this->siswa->id}.sas", 94)
            ->call('saveNilai')
            ->assertHasNoErrors()
            ->assertSee('Seluruh Nilai Akademik');

        // Pastikan nilai tersimpan di Ganjil
        $this->assertDatabaseHas('nilai_asesmen_harians', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 88,
        ]);
        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 85,
            'sas' => 90,
        ]);

        // Pastikan nilai tersimpan di Genap
        $this->assertDatabaseHas('nilai_asesmen_harians', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'kode_ah' => 'AH 5',
            'nilai' => 92,
        ]);
        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'sts' => 89,
            'sas' => 94,
        ]);
    }

    public function test_tab_aktif_bertahan_pada_tab_formatif_saat_reload_atau_diakses_dengan_parameter(): void
    {
        $this->actingAs($this->admin);

        // 1. Akses langsung dengan parameter query ?tab=formatif (simulasi reload saat tab 2 terbuka)
        Livewire::withQueryParams(['tab' => 'formatif'])
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->assertSet('activeTab', 'formatif')
            ->assertSee('Tampilan Tabel:')
            ->assertSee('Simpan Semua Nilai');

        // 2. Simulasi ganti tab ke formatif, maka session otomatis tersimpan
        session()->put('penilaian_active_tab_' . $this->pmGanjil->id, 'formatif');

        // Saat halaman di-reload / diakses kembali tanpa query parameter:
        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->assertSet('activeTab', 'formatif')
            ->assertSee('Tampilan Tabel:')
            ->assertSee('Simpan Semua Nilai');

        // 3. Jika berpindah ke tab rekap, session ter-update ke rekap
        $comp = Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id]);
        $comp->call('switchTab', 'rekap')
            ->assertSet('activeTab', 'rekap');

        $this->assertEquals('rekap', session('penilaian_active_tab_' . $this->pmGanjil->id));

        // Saat reload tanpa query parameter, session 'rekap' yang dipakai
        Livewire::withQueryParams([])
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->assertSet('activeTab', 'rekap')
            ->assertSee('Rekap Semester:');
    }

    public function test_nilai_di_bawah_kkm_disorot_merah(): void
    {
        $this->actingAs($this->admin);

        // Mapel default KKM = 75
        $comp = Livewire::withQueryParams(['tab' => 'formatif'])
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id]);

        $this->assertEquals(75, $comp->get('kkm'));
        $this->assertTrue($comp->instance()->isBelowKkm(65));
        $this->assertTrue($comp->instance()->isBelowKkm('74.5'));
        $this->assertFalse($comp->instance()->isBelowKkm(75));
        $this->assertFalse($comp->instance()->isBelowKkm(80));
        $this->assertFalse($comp->instance()->isBelowKkm(null));
        $this->assertFalse($comp->instance()->isBelowKkm(''));

        // Input nilai di bawah KKM pada STS 1 siswa
        $comp->set('sumatifDataGanjil.' . $this->siswa->id . '.sts', 60)
            ->assertSee('border-rose-500')
            ->assertSee('bg-rose-100/70')
            ->assertSee('di bawah KKM (75) - Belum Kompeten');
    }

    public function test_guru_dapat_mengedit_dan_mengosongkan_nilai_sumatif_yang_sudah_ada(): void
    {
        // 1. Buat nilai sumatif awal untuk siswa
        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 70,
            'sas' => 75,
        ]);

        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'sts' => 80,
            'sas' => 85,
        ]);

        // 2. Edit nilai STS 1, SAS, STS 2, SAT
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif')
            ->set("sumatifDataGanjil.{$this->siswa->id}.sts", 95)
            ->set("sumatifDataGanjil.{$this->siswa->id}.sas", 96)
            ->set("sumatifDataGenap.{$this->siswa->id}.sts", 97)
            ->set("sumatifDataGenap.{$this->siswa->id}.sas", 98)
            ->call('saveNilai')
            ->assertHasNoErrors()
            ->assertSee('Seluruh Nilai Akademik');

        // Pastikan nilai ter-update di database
        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 95,
            'sas' => 96,
        ]);

        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGenap->id,
            'sts' => 97,
            'sas' => 98,
        ]);

        // 3. Uji pengosongan nilai (hapus nilai menjadi kosong/null)
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('switchTab', 'formatif')
            ->set("sumatifDataGanjil.{$this->siswa->id}.sts", '')
            ->set("sumatifDataGanjil.{$this->siswa->id}.sas", '')
            ->call('saveNilai')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('nilai_sumatifs', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
        ]);
    }
}

