<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\RombelIndex;
use App\Modules\Akademik\Livewire\TahunAjaranManager;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Alumni\Models\Alumni;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\RiwayatAkademik;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SemesterTransitionAndRombelGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
    }

    public function test_transisi_semester_mengunci_nilai_absensi_dan_salin_penugasan(): void
    {
        $ta = TahunAjaran::create([
            'nama' => '2024/2025',
            'is_active' => true,
        ]);

        $ganjil = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $genap = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'genap',
            'is_active' => false,
        ]);

        $jurusan = Jurusan::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $rombel = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Budi Santoso, S.Kom',
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
            'jenis_gtk' => 'guru',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $mapel = Mapel::create([
            'kode' => 'PROG-DASAR',
            'nama' => 'Pemrograman Dasar',
            'kelompok' => 'kejuruan',
            'urutan' => 1,
        ]);

        PenugasanMengajar::create([
            'semester_id' => $ganjil->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $rombel->id,
            'jp' => 4,
            'keterangan' => 'Guru Pengampu',
        ]);

        // Simulasikan transisi semester via Livewire
        Livewire::actingAs($this->admin)
            ->test(TahunAjaranManager::class)
            ->call('openTransitionModal', $genap->id)
            ->assertSet('showTransitionModal', true)
            ->assertSet('penugasanCountToCopy', 1)
            ->set('lockNilaiSemesterLama', true)
            ->set('lockAbsensiSemesterLama', true)
            ->set('salinPenugasanMengajar', true)
            ->call('executeTransition')
            ->assertHasNoErrors()
            ->assertSet('showTransitionModal', false);

        $ganjil->refresh();
        $genap->refresh();

        $this->assertFalse($ganjil->is_active);
        $this->assertTrue($ganjil->isLockedForNilai());
        $this->assertTrue($ganjil->isLockedForAbsensi());

        $this->assertTrue($genap->is_active);

        // Verifikasi penugasan disalin ke semester Genap
        $this->assertDatabaseHas('penugasan_mengajars', [
            'semester_id' => $genap->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $rombel->id,
        ]);
    }

    public function test_generator_rombel_dapat_menyalin_rombel_ke_tahun_ajaran_baru(): void
    {
        $ta1 = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);
        $ta2 = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);

        $jurusan = Jurusan::create(['kode' => 'TJKT', 'nama' => 'Teknik Jaringan Komputer']);

        Rombel::create([
            'tahun_ajaran_id' => $ta1->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X TJKT 1',
            'kapasitas' => 36,
        ]);

        Rombel::create([
            'tahun_ajaran_id' => $ta1->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI TJKT 1',
            'kapasitas' => 36,
        ]);

        Livewire::actingAs($this->admin)
            ->test(RombelIndex::class)
            ->call('openDuplicateModal')
            ->set('sourceTahunAjaranId', $ta1->id)
            ->set('targetTahunAjaranId', $ta2->id)
            ->set('duplicateTingkat', 'semua')
            ->call('executeDuplicate')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rombels', [
            'tahun_ajaran_id' => $ta2->id,
            'nama' => 'X TJKT 1',
        ]);
        $this->assertDatabaseHas('rombels', [
            'tahun_ajaran_id' => $ta2->id,
            'nama' => 'XI TJKT 1',
        ]);

        $this->assertEquals(2, Rombel::where('tahun_ajaran_id', $ta2->id)->count());

        // Jalankan duplikasi lagi, jumlah harus tetap 2 (tidak ada duplikasi)
        Livewire::actingAs($this->admin)
            ->test(RombelIndex::class)
            ->set('sourceTahunAjaranId', $ta1->id)
            ->set('targetTahunAjaranId', $ta2->id)
            ->set('duplicateTingkat', 'semua')
            ->call('executeDuplicate')
            ->assertHasNoErrors();

        $this->assertEquals(2, Rombel::where('tahun_ajaran_id', $ta2->id)->count());
    }

    public function test_riwayat_akademik_dan_alumni_model_berelasi_dengan_siswa(): void
    {
        $ta = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);

        $siswa = Siswa::create([
            'nama' => 'Ahmad Dahlan',
            'nisn' => '0098765432',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $riwayat = RiwayatAkademik::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
            'tingkat_asal' => 'X',
            'tingkat_tujuan' => 'XI',
            'status_keputusan' => 'naik_kelas',
            'catatan' => 'Lulus kriteria kenaikan kelas.',
            'diproses_oleh' => $this->admin->id,
        ]);

        $alumni = Alumni::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
            'tahun_lulus' => '2025',
            'status_karir' => 'bekerja',
            'nama_instansi' => 'PT Google Indonesia',
            'posisi_jurusan' => 'Software Engineer',
        ]);

        $siswa->refresh();

        $this->assertEquals(1, $siswa->riwayatAkademiks()->count());
        $this->assertEquals('Naik Kelas', $siswa->riwayatAkademiks->first()->label_keputusan);
        $this->assertNotNull($siswa->alumni);
        $this->assertEquals('Bekerja', $siswa->alumni->status_karir_badge);
        $this->assertEquals('PT Google Indonesia', $siswa->alumni->nama_instansi);
    }
}
