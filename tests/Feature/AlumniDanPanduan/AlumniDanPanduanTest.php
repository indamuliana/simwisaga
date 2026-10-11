<?php

namespace Tests\Feature\AlumniDanPanduan;

use App\Models\User;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Alumni\Livewire\AlumniIndex;
use App\Modules\Alumni\Models\Alumni;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Panduan\Livewire\PanduanIndex;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlumniDanPanduanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $siswaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $this->siswaUser = User::factory()->create();
        $this->siswaUser->assignRole('Siswa');
    }

    public function test_halaman_alumni_dapat_diakses_dan_menampilkan_data(): void
    {
        $ta = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);
        $siswa = Siswa::create(['nama' => 'Rian Hidayat', 'nisn' => '7777777777', 'status' => 'lulus', 'is_active' => false]);
        
        $alumni = Alumni::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
            'tahun_lulus' => '2025',
            'status_karir' => 'bekerja',
            'nama_instansi' => 'PT Telkom Indonesia',
        ]);

        $this->actingAs($this->admin)
            ->get(route('alumni.index'))
            ->assertOk()
            ->assertSee('Pengelolaan Data Alumni & Tracer Study')
            ->assertSee('Rian Hidayat')
            ->assertSee('PT Telkom Indonesia');
    }

    public function test_alumni_tracer_study_dapat_diperbarui(): void
    {
        $ta = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);
        $siswa = Siswa::create(['nama' => 'Siti Nurhaliza', 'nisn' => '8888888888', 'status' => 'lulus']);
        
        $alumni = Alumni::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
            'tahun_lulus' => '2025',
            'status_karir' => 'belum_memutuskan',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AlumniIndex::class)
            ->call('editTracer', $alumni->id)
            ->set('status_karir', 'kuliah')
            ->set('nama_instansi', 'Institut Teknologi Bandung')
            ->set('posisi_jurusan', 'Teknik Informatika')
            ->call('saveTracer')
            ->assertHasNoErrors();

        $alumni->refresh();
        $this->assertEquals('kuliah', $alumni->status_karir);
        $this->assertEquals('Institut Teknologi Bandung', $alumni->nama_instansi);
        $this->assertEquals('Teknik Informatika', $alumni->posisi_jurusan);
    }

    public function test_export_excel_alumni_berhasil_dijalankan(): void
    {
        $ta = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);
        $siswa = Siswa::create(['nama' => 'Taufik Hidayat', 'nisn' => '9999999999', 'status' => 'lulus']);
        
        Alumni::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
            'tahun_lulus' => '2025',
            'status_karir' => 'wirausaha',
            'nama_instansi' => 'Kopi Wikrama',
        ]);

        $response = Livewire::actingAs($this->admin)
            ->test(AlumniIndex::class)
            ->call('exportExcel');

        $response->assertFileDownloaded();
    }

    public function test_modul_panduan_menampilkan_bab_staf_untuk_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('panduan.index'))
            ->assertOk()
            ->assertSee('1. Konsep Siklus Akademik')
            ->assertSee('2. Pergantian Semester')
            ->assertSee('3. Pergantian Tahun Ajaran')
            ->assertSee('4. Kenaikan Kelas & Siswa Tinggal')
            ->assertSee('5. Kelulusan Siswa Kelas XII');
    }

    public function test_modul_panduan_menampilkan_bab_khusus_untuk_siswa(): void
    {
        $this->actingAs($this->siswaUser)
            ->get(route('panduan.index'))
            ->assertOk()
            ->assertSee('1. Panduan Portal Akun Anda')
            ->assertSee('2. Memeriksa Catatan Kehadiran')
            ->assertSee('3. Melihat & Cetak e-Rapor')
            ->assertDontSee('2. Pergantian Semester');
    }

    public function test_modul_panduan_bab_4_menampilkan_panduan_lengkap_kenaikan_kelas_dan_ledger(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Modules\Panduan\Livewire\PanduanIndex::class)
            ->set('activeChapter', 'kenaikan-kelas')
            ->assertSee('Panduan Lengkap Kenaikan Kelas, Tinggal Kelas, & Integritas Ledger')
            ->assertSee('Konsep Dasar & Arsitektur Integritas Data (Ledger)')
            ->assertSee('Prasyarat Wajib Sebelum Memulai Kenaikan Kelas')
            ->assertSee('Langkah Operasional Wizard Kenaikan Kelas')
            ->assertSee('Kasus A: Siswa Tinggal Kelas (Mengulang)')
            ->assertSee('Dampak Menyeluruh ke Modul Lain dalam SIRAGA')
            ->assertSee('scopeInRombel', false)
            ->assertSee('Q1: Apakah proses kenaikan kelas bisa diulang atau dikoreksi jika operator salah klik?');
    }
}
