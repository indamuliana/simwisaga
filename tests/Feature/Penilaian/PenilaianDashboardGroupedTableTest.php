<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\PenilaianDashboard;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianDashboardGroupedTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Rombel $rombel;
    protected Mapel $mapelUmum;
    protected Mapel $mapelKejuruan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_penilaian',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->where('is_active', true)->first()
            ?? Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->rombel = Rombel::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        // Siapkan 2 siswa aktif di rombel
        Siswa::create([
            'nama' => 'Siswa Penilaian 1',
            'nipd' => '10001',
            'nisn' => '0091111111',
            'jk' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2008-01-01',
            'status' => 'aktif',
            'is_active' => true,
            'rombel_id' => $this->rombel->id,
        ]);

        Siswa::create([
            'nama' => 'Siswa Penilaian 2',
            'nipd' => '10002',
            'nisn' => '0091111112',
            'jk' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2008-02-02',
            'status' => 'aktif',
            'is_active' => true,
            'rombel_id' => $this->rombel->id,
        ]);

        $this->mapelUmum = Mapel::where('kelompok', 'umum')->firstOrFail();
        $this->mapelKejuruan = Mapel::where('kelompok', 'kejuruan')->firstOrFail();

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->rombel->tahun_ajaran_id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => $this->rombel->tingkat,
            'mapel_id' => $this->mapelUmum->id,
            'jp_per_minggu' => 3,
            'urutan' => 1,
        ]);

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->rombel->tahun_ajaran_id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => $this->rombel->tingkat,
            'mapel_id' => $this->mapelKejuruan->id,
            'jp_per_minggu' => 12,
            'urutan' => 2,
        ]);
    }

    public function test_dashboard_penilaian_menampilkan_pilihan_rombel_dan_tabel_terkelompok_kurikulum(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->assertSet('filterTingkat', 'semua')
            ->assertSet('selectedRombelId', $this->rombel->id)
            ->assertSee('Dashboard Pengolahan Nilai')
            ->assertSee($this->rombel->nama)
            ->assertSee('Kelompok A (Muatan Umum)')
            ->assertSee('Input');
    }

    public function test_pencarian_mapel_atau_guru_menyaring_baris_tabel(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->set('search', 'Pendidikan Agama')
            ->assertSee('Pendidikan Agama')
            ->assertDontSee('Dasar-dasar Kejuruan');
    }

    public function test_perhitungan_progres_penilaian_akurat_pada_card_dan_tabel(): void
    {
        $sk = StrukturKurikulum::where('tahun_ajaran_id', $this->rombel->tahun_ajaran_id)
            ->where('jurusan_id', $this->rombel->jurusan_id)
            ->where('tingkat', $this->rombel->tingkat)
            ->firstOrFail();

        $penugasan = PenugasanMengajar::firstOrCreate(
            [
                'rombel_id' => $this->rombel->id,
                'mapel_id' => $sk->mapel_id,
                'semester_id' => $this->semester->id,
            ],
            [
                'jp' => 2,
            ]
        );

        $siswaList = Siswa::where('rombel_id', $this->rombel->id)->get();
        // Input nilai akhir untuk seluruh siswa di penugasan ini (100% lengkap)
        foreach ($siswaList as $s) {
            NilaiAkhir::updateOrCreate(
                ['siswa_id' => $s->id, 'penugasan_mengajar_id' => $penugasan->id],
                ['nilai_akhir' => 85, 'rata_formatif' => 85, 'sts' => 85, 'sas' => 85]
            );
        }

        Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->assertSee('Lengkap (100%):')
            ->assertSee('100%');
    }

    public function test_tombol_input_nilai_pada_mapel_belum_ditugaskan_otomatis_membuat_penugasan_mengajar(): void
    {
        // Buat mapel baru yang belum ada penugasan mengajarnya di rombel ini
        $mapelBaru = Mapel::create([
            'kode' => 'NEW-TEST',
            'nama' => 'Mata Pelajaran Uji Coba Baru',
            'kelompok' => 'umum',
            'is_active' => true,
        ]);

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->rombel->tahun_ajaran_id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => $this->rombel->tingkat,
            'mapel_id' => $mapelBaru->id,
            'jp_per_minggu' => 2,
            'urutan' => 99,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(PenilaianDashboard::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->assertSee('Mata Pelajaran Uji Coba Baru')
            ->assertSee('Belum Ditugaskan')
            ->call('bukaPenilaian', $mapelBaru->id);

        // Pastikan record penugasan_mengajars baru terbuat otomatis
        $this->assertDatabaseHas('penugasan_mengajars', [
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $mapelBaru->id,
            'semester_id' => $this->semester->id,
        ]);

        $newPm = PenugasanMengajar::where('rombel_id', $this->rombel->id)
            ->where('mapel_id', $mapelBaru->id)
            ->where('semester_id', $this->semester->id)
            ->first();

        $component->assertRedirect(route('penilaian.input', $newPm->id));
    }
}
