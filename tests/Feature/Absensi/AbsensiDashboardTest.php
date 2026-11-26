<?php

namespace Tests\Feature\Absensi;

use App\Core\Support\PermissionSync;
use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Absensi\Livewire\AbsensiDashboard;
use App\Modules\Absensi\Livewire\AbsensiHarianIndex;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Absensi\Services\DashboardAbsensiService;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Models\SiswaOrangTua;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AbsensiDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Rombel $rombelA;
    protected Rombel $rombelB;
    protected Rayon $rayonA;
    protected Rayon $rayonB;
    protected Pegawai $pegawaiWaliA;
    protected Pegawai $pegawaiPembimbingA;
    protected Siswa $siswaAlpa;
    protected Siswa $siswaIzin;
    protected Siswa $siswaSakit;
    protected Siswa $siswaDispen;
    protected Siswa $siswaBelumAbsen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        PermissionSync::run();

        $this->admin = User::factory()->create([
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create(['kode' => 'PPLG', 'nama' => 'PPLG']);
        $this->rombelA = Rombel::create([
            'nama' => 'X PPLG 1',
            'tingkat' => 'X',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->ta->id,
        ]);
        $this->rombelB = Rombel::create([
            'nama' => 'XI PPLG 1',
            'tingkat' => 'XI',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->ta->id,
        ]);

        $this->rayonA = Rayon::create(['nama' => 'Rayon Cisurupan']);
        $this->rayonB = Rayon::create(['nama' => 'Rayon Tarogong']);

        $waliUserA = User::factory()->create(['name' => 'Pak Wali A', 'is_active' => true, 'must_change_password' => false]);
        $this->pegawaiWaliA = Pegawai::create([
            'user_id' => $waliUserA->id,
            'nama_lengkap' => 'Ahmad Hidayat',
            'gelar_belakang' => 'S.Pd',
            'no_hp' => '081211112222',
            'is_active' => true,
        ]);
        PenugasanWaliKelas::create([
            'tahun_ajaran_id' => $this->ta->id,
            'rombel_id' => $this->rombelA->id,
            'pegawai_id' => $this->pegawaiWaliA->id,
        ]);

        $pembimbingUserA = User::factory()->create(['name' => 'Ibu Pembimbing A', 'is_active' => true, 'must_change_password' => false]);
        $this->pegawaiPembimbingA = Pegawai::create([
            'user_id' => $pembimbingUserA->id,
            'nama_lengkap' => 'Siti Aminah',
            'gelar_belakang' => 'M.Pd',
            'no_hp' => '081322223333',
            'is_active' => true,
        ]);
        PenugasanPembimbingRayon::create([
            'tahun_ajaran_id' => $this->ta->id,
            'rayon_id' => $this->rayonA->id,
            'pegawai_id' => $this->pegawaiPembimbingA->id,
        ]);

        $this->siswaAlpa = Siswa::create([
            'nama' => 'Andi Alpa',
            'nipd' => '2001',
            'nisn' => '002001',
            'jk' => 'L',
            'hp' => '085611110001',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'rayon_id' => $this->rayonA->id,
            'tanggal_lahir' => '2008-01-01',
        ]);
        SiswaOrangTua::create(['siswa_id' => $this->siswaAlpa->id, 'hubungan' => 'ayah', 'nama' => 'Bapak Andi', 'no_hp' => '081200001111']);
        SiswaOrangTua::create(['siswa_id' => $this->siswaAlpa->id, 'hubungan' => 'ibu', 'nama' => 'Ibu Andi', 'no_hp' => '081200002222']);
        SiswaOrangTua::create(['siswa_id' => $this->siswaAlpa->id, 'hubungan' => 'wali', 'nama' => 'Paman Andi', 'no_hp' => '081200003333']);

        $this->siswaIzin = Siswa::create([
            'nama' => 'Budi Izin',
            'nipd' => '2002',
            'nisn' => '002002',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'rayon_id' => $this->rayonA->id,
            'tanggal_lahir' => '2008-01-02',
        ]);

        $this->siswaSakit = Siswa::create([
            'nama' => 'Citra Sakit',
            'nipd' => '2003',
            'nisn' => '002003',
            'jk' => 'P',
            'status' => 'aktif',
            'rombel_id' => $this->rombelB->id,
            'rayon_id' => $this->rayonB->id,
            'tanggal_lahir' => '2008-01-03',
        ]);

        $this->siswaDispen = Siswa::create([
            'nama' => 'Dina Dispen',
            'nipd' => '2004',
            'nisn' => '002004',
            'jk' => 'P',
            'status' => 'aktif',
            'rombel_id' => $this->rombelB->id,
            'rayon_id' => $this->rayonB->id,
            'tanggal_lahir' => '2008-01-04',
        ]);

        $this->siswaBelumAbsen = Siswa::create([
            'nama' => 'Eko Belum',
            'nipd' => '2005',
            'nisn' => '002005',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'rayon_id' => $this->rayonA->id,
            'tanggal_lahir' => '2008-01-05',
        ]);
    }

    public function test_route_dashboard_absensi_tampil_dan_menolak_user_tanpa_permission(): void
    {
        $this->actingAs($this->admin)
            ->get(route('absensi.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Absensi')
            ->assertSee('Salin WA Rekap')
            ->assertSee('Salin WA Rayon')
            ->assertSee('Salin WA Rombel')
            ->assertSee('Daftar Siswa Alpa')
            ->assertSee('Daftar Siswa Izin')
            ->assertSee('Daftar Siswa Sakit');

        $siswaUser = User::factory()->create(['is_active' => true, 'must_change_password' => false]);
        $siswaUser->assignRole('Siswa');

        $this->actingAs($siswaUser)
            ->get(route('absensi.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_menghitung_kpi_delta_progres_rombel_kontak_6_pihak_dan_early_warning(): void
    {
        $today = now()->toDateString();
        $d1 = now()->subDay()->toDateString();
        $d2 = now()->subDays(2)->toDateString();

        // Data kemarin & 2 hari lalu agar siswaAlpa punya streak 3 hari & SP 1 (3x Alpa)
        AbsensiHarian::create(['tanggal' => $d2, 'siswa_id' => $this->siswaAlpa->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::ALPA->value]);
        AbsensiHarian::create(['tanggal' => $d1, 'siswa_id' => $this->siswaAlpa->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::ALPA->value]);
        AbsensiHarian::create(['tanggal' => $d1, 'siswa_id' => $this->siswaIzin->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::HADIR->value]);

        // Data hari ini:
        // Rombel A (3 siswa): Andi Alpa, Budi Izin, Eko Belum diabsen -> 2/3 (66.7%, 1 siswa belum diabsen)
        // Rombel B (2 siswa): Citra Sakit, Dina Dispen -> 2/2 (100% Lengkap)
        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaAlpa->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::ALPA->value, 'keterangan' => 'Tanpa kabar']);
        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaIzin->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::IZIN->value, 'keterangan' => 'Keperluan keluarga']);
        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaSakit->id, 'rombel_id' => $this->rombelB->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::SAKIT->value, 'keterangan' => 'Demam']);
        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaDispen->id, 'rombel_id' => $this->rombelB->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::DISPEN_CBT_PLH->value, 'keterangan' => 'Lomba PLH']);

        $service = app(DashboardAbsensiService::class);
        $data = $service->buildDashboardData($this->admin, [
            'tanggal' => $today,
            'chart_range' => '7d',
        ]);

        // KPI
        $this->assertSame(5, $data['kpi']['total_siswa']);
        $this->assertSame(4, $data['kpi']['sudah_absen']);
        $this->assertSame(1, $data['kpi']['belum_absen']);
        $this->assertSame(1, $data['kpi']['alpa']);
        $this->assertSame(1, $data['kpi']['izin']);
        $this->assertSame(1, $data['kpi']['sakit']);
        $this->assertSame(1, $data['kpi']['dispen_total']);
        $this->assertSame(25.0, $data['kpi']['persen_hadir']); // 1 dispen / 4 tercatat
        $this->assertSame(80.0, $data['kpi']['persen_input']); // 4 / 5
        $this->assertSame(-25.0, $data['kpi']['delta_persen_hadir']); // 25% vs 50% kemarin

        // Progres Rombel: Rombel A (belum tuntas) di urutan pertama, Rombel B (lengkap) kedua
        $this->assertSame('X PPLG 1', $data['progress_rombel']['items'][0]['nama']);
        $this->assertSame('1 siswa belum diabsen', $data['progress_rombel']['items'][0]['status_text']);
        $this->assertNotNull($data['progress_rombel']['items'][0]['pj_wa_url']);
        $this->assertStringContainsString('wa.me/6281211112222', $data['progress_rombel']['items'][0]['pj_wa_url']);
        $this->assertSame('XI PPLG 1', $data['progress_rombel']['items'][1]['nama']);
        $this->assertSame('lengkap', $data['progress_rombel']['items'][1]['state']);

        // 3 Tabel Ketidakhadiran + 6 Kontak WA dengan nama & template pesan
        $this->assertCount(1, $data['daftar_alpa']);
        $this->assertCount(1, $data['daftar_izin']);
        $this->assertCount(1, $data['daftar_sakit']);
        $this->assertCount(1, $data['daftar_dispen']);

        $alpaRow = $data['daftar_alpa'][0];
        $this->assertSame('Andi Alpa', $alpaRow['nama']);
        $this->assertSame('Rayon Cisurupan', $alpaRow['rayon_nama']);
        $this->assertSame('Siti Aminah, M.Pd', $alpaRow['pembimbing_rayon_nama']);
        $this->assertCount(6, $alpaRow['contacts']);
        $this->assertSame(6, $alpaRow['available_contacts_count']);

        $roles = array_column($alpaRow['contacts'], 'role');
        $this->assertSame(['Siswa', 'Ayah', 'Ibu', 'Wali', 'Pembimbing Rayon', 'Wali Kelas'], $roles);
        $this->assertStringContainsString('wa.me/6285611110001?text=', $alpaRow['contacts'][0]['wa_url']);
        $this->assertStringContainsString('wa.me/6281200001111?text=', $alpaRow['contacts'][1]['wa_url']);
        $this->assertStringContainsString('wa.me/6281322223333?text=', $alpaRow['contacts'][4]['wa_url']);
        $this->assertStringContainsString('wa.me/6281211112222?text=', $alpaRow['contacts'][5]['wa_url']);

        // Early Warning: Beruntun 3 hari & SP 1 (3x Alpa)
        $this->assertCount(1, $data['alert_beruntun']);
        $this->assertSame(3, $data['alert_beruntun'][0]['streak']);
        $this->assertCount(1, $data['alert_sp']);
        $this->assertSame('SP 1', $data['alert_sp'][0]['sp_level']);
        $this->assertSame(3, $data['alert_sp'][0]['total_alpa']);

        // WA Summary (3 format: Rekap, Rayon, Rombel)
        $this->assertStringContainsString('LAPORAN ABSENSI HARIAN', $data['wa_summary']);
        $this->assertStringContainsString('X PPLG 1: 1 siswa belum diabsen', $data['wa_summary']);

        $this->assertStringContainsString('REKAP KETIDAKHADIRAN PER RAYON', $data['wa_summary_rayon']);
        $this->assertStringContainsString('*Rayon Cisurupan*', $data['wa_summary_rayon']);
        $this->assertStringContainsString('Andi Alpa (X PPLG 1) [Alpa] - Tanpa kabar', $data['wa_summary_rayon']);
        $this->assertStringContainsString('Budi Izin (X PPLG 1) [Izin] - Keperluan keluarga', $data['wa_summary_rayon']);
        $this->assertStringContainsString('*Rayon Tarogong*', $data['wa_summary_rayon']);
        $this->assertStringContainsString('Citra Sakit (XI PPLG 1) [Sakit] - Demam', $data['wa_summary_rayon']);

        $this->assertStringContainsString('REKAP KETIDAKHADIRAN PER ROMBEL', $data['wa_summary_rombel']);
        $this->assertStringContainsString('*X PPLG 1*', $data['wa_summary_rombel']);
        $this->assertStringContainsString('Andi Alpa (Rayon Cisurupan) [Alpa] - Tanpa kabar', $data['wa_summary_rombel']);
        $this->assertStringContainsString('Budi Izin (Rayon Cisurupan) [Izin] - Keperluan keluarga', $data['wa_summary_rombel']);
        $this->assertStringContainsString('*XI PPLG 1*', $data['wa_summary_rombel']);
        $this->assertStringContainsString('Citra Sakit (Rayon Tarogong) [Sakit] - Demam', $data['wa_summary_rombel']);
    }

    public function test_dashboard_livewire_filter_dan_toggle_berfungsi(): void
    {
        $today = now()->toDateString();

        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaAlpa->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::ALPA->value]);
        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaSakit->id, 'rombel_id' => $this->rombelB->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::SAKIT->value]);

        Livewire::actingAs($this->admin)
            ->test(AbsensiDashboard::class)
            ->assertSee('Andi Alpa')
            ->assertSee('Citra Sakit')
            ->set('rombel_id', (string) $this->rombelA->id)
            ->assertSee('Andi Alpa')
            ->assertDontSee('Citra Sakit')
            ->call('setChartRange', '30d')
            ->assertSet('chartRange', '30d')
            ->call('setProgressTab', 'rayon')
            ->assertSet('progressTab', 'rayon')
            ->assertSee('Rayon Cisurupan');
    }

    public function test_wali_kelas_hanya_melihat_data_siswa_binaannya_di_dashboard(): void
    {
        $today = now()->toDateString();
        $waliUser = User::find($this->pegawaiWaliA->user_id);
        $waliUser->assignRole('Guru');
        $waliUser->givePermissionTo(Permission::findOrCreate('absensi.harian.lihat', 'web'));

        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaAlpa->id, 'rombel_id' => $this->rombelA->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::ALPA->value]);
        AbsensiHarian::create(['tanggal' => $today, 'siswa_id' => $this->siswaSakit->id, 'rombel_id' => $this->rombelB->id, 'semester_id' => $this->semester->id, 'status' => StatusAbsensi::SAKIT->value]);

        Livewire::actingAs($waliUser)
            ->test(AbsensiDashboard::class)
            ->assertSee('Pemantauan presensi siswa binaan Anda')
            ->assertSee('Andi Alpa')
            ->assertDontSee('Citra Sakit');
    }

    public function test_deep_link_dari_dashboard_membuka_absensi_harian_dengan_filter_terisi(): void
    {
        $yesterday = now()->subDay()->toDateString();

        Livewire::actingAs($this->admin)
            ->withQueryParams([
                'tanggal' => $yesterday,
                'rombel_id' => (string) $this->rombelA->id,
            ])
            ->test(AbsensiHarianIndex::class)
            ->assertSet('tanggal', $yesterday)
            ->assertSet('rombel_id', (string) $this->rombelA->id)
            ->assertSee('Andi Alpa')
            ->assertDontSee('Citra Sakit');
    }
}
