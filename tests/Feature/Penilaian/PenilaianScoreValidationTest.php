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
use App\Modules\Penilaian\Livewire\PenilaianDashboard;
use App\Modules\Penilaian\Livewire\PenilaianInput;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianScoreValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PenugasanMengajar $penugasan;
    protected Siswa $siswa;
    protected TujuanPembelajaran $tp;
    protected Semester $semGanjil;
    protected Semester $semGenap;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_penilaian',
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
            'nama_lengkap' => 'Guru Penguji',
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->penugasan = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'pegawai_id' => $pegawai->id,
            'mapel_id' => $mapel->id,
            'rombel_id' => $rombel->id,
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Budi Santoso',
            'nisn' => '1234567890',
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

        $this->tp = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->penugasan->id,
            'kode' => 'TP1',
            'deskripsi' => 'Memahami algoritma dasar',
            'is_active' => true,
        ]);
    }

    public function test_penilaian_menolak_skor_formatif_di_luar_0_sampai_100(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->set("formatifData.{$this->siswa->id}.{$this->tp->id}", 125)
            ->call('saveNilai')
            ->assertHasErrors(["formatifData.{$this->siswa->id}.{$this->tp->id}" => 'max'])
            ->assertSee('Nilai formatif tidak boleh lebih dari 100.');

        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->set("formatifData.{$this->siswa->id}.{$this->tp->id}", -10)
            ->call('saveNilai')
            ->assertHasErrors(["formatifData.{$this->siswa->id}.{$this->tp->id}" => 'min'])
            ->assertSee('Nilai formatif tidak boleh kurang dari 0.');
    }

    public function test_penilaian_menolak_skor_sumatif_di_luar_0_sampai_100(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->set("sumatifData.{$this->siswa->id}.sts", 150)
            ->set("sumatifData.{$this->siswa->id}.sas", -5)
            ->call('saveNilai')
            ->assertHasErrors([
                "sumatifData.{$this->siswa->id}.sts" => 'max',
                "sumatifData.{$this->siswa->id}.sas" => 'min',
            ]);
    }

    public function test_penilaian_menerima_skor_valid(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianInput::class, ['penugasan_id' => $this->penugasan->id])
            ->call('switchTab', 'formatif')
            ->set("formatifData.{$this->siswa->id}.{$this->tp->id}", 88)
            ->set("sumatifData.{$this->siswa->id}.sts", 85)
            ->set("sumatifData.{$this->siswa->id}.sas", 90)
            ->call('saveNilai')
            ->assertHasNoErrors()
            ->assertSee('Seluruh Nilai Akademik (Formatif &amp; Sumatif) berhasil disimpan.', false);

        $this->assertDatabaseHas('nilai_formatifs', [
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $this->tp->id,
            'nilai' => 88,
        ]);

        $this->assertDatabaseHas('nilai_sumatifs', [
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->penugasan->id,
            'sts' => 85,
            'sas' => 90,
        ]);
    }

    public function test_penilaian_dashboard_hanya_menampilkan_penugasan_semester_aktif(): void
    {
        // Penugasan di semester ganjil (aktif)
        $this->assertTrue($this->semGanjil->is_active);

        // Buat penugasan di semester genap (tidak aktif)
        $penugasanGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'pegawai_id' => $this->penugasan->pegawai_id,
            'mapel_id' => $this->penugasan->mapel_id,
            'rombel_id' => $this->penugasan->rombel_id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->assertViewHas('currentSem', fn ($sem) => $sem->id === $this->semGanjil->id)
            ->assertViewHas('groupedStrukturKurikulum', function ($grouped) use ($penugasanGenap) {
                $allItems = $grouped->flatten();
                return ! $allItems->contains('penugasan_id', $penugasanGenap->id);
            });
    }
}
