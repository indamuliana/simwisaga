<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\RekapPenilaianIndex;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\RekapPenilaianService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RekapPenilaianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Rombel $rombel;
    protected Rayon $rayon;
    protected Mapel $mapel1;
    protected Mapel $mapel2;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected PenugasanMengajar $pm1;
    protected PenugasanMengajar $pm2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_rekap',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->where('is_active', true)->first()
            ?? Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->rombel = Rombel::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->rayon = Rayon::create([
            'nama' => 'Ciawi 1',
            'wilayah_cakupan' => 'Kec. Ciawi',
        ]);

        // 2 Siswa
        $this->siswa1 = Siswa::create([
            'nama' => 'Aditya Pratama',
            'nipd' => '10001',
            'nisn' => '0011223344',
            'nik' => '3201010101010001',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-01-01',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'status' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '10002',
            'nisn' => '0011223345',
            'nik' => '3201010101010002',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-02-02',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'status' => 'aktif',
        ]);

        // 2 Mapel dengan KKM 75
        $this->mapel1 = Mapel::create([
            'kode' => 'MAT-REKAP',
            'nama' => 'Matematika Rekap',
            'kelompok' => Mapel::KELOMPOK_UMUM,
            'kkm_default' => 75,
            'is_active' => true,
        ]);

        $this->mapel2 = Mapel::create([
            'kode' => 'PROG-REKAP',
            'nama' => 'Pemrograman Web Rekap',
            'kelompok' => Mapel::KELOMPOK_KEJURUAN,
            'kkm_default' => 75,
            'is_active' => true,
        ]);

        // Struktur Kurikulum
        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => $this->rombel->tingkat,
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
        ]);

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => $this->rombel->tingkat,
            'mapel_id' => $this->mapel2->id,
            'jp_ganjil' => 6,
            'jp_genap' => 6,
            'urutan' => 2,
        ]);

        // Pegawai / Guru
        $guru = Pegawai::create([
            'nama_lengkap' => 'Guru Penilai Rekap',
            'nip' => '198501012010011009',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        // Penugasan Mengajar
        $this->pm1 = PenugasanMengajar::create([
            'semester_id' => $this->semester->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $this->mapel1->id,
            'pegawai_id' => $guru->id,
        ]);

        $this->pm2 = PenugasanMengajar::create([
            'semester_id' => $this->semester->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $this->mapel2->id,
            'pegawai_id' => $guru->id,
        ]);
    }

    public function test_guest_cannot_access_rekap_penilaian_page(): void
    {
        $response = $this->get(route('penilaian.rekap'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_access_rekap_penilaian_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('penilaian.rekap'));
        $response->assertOk();
        $response->assertSee('Rekap Nilai Semester');
    }

    public function test_rekap_penilaian_service_calculates_leger_and_ranking_accurately(): void
    {
        // Setup Nilai:
        // Siswa 1 (Aditya): Mapel 1 = 90, Mapel 2 = 80 -> Rerata = 85 (Tuntas semua)
        // Siswa 2 (Budi): Mapel 1 = 70 (< KKM 75), Mapel 2 = 60 (< KKM 75) -> Rerata = 65 (Remedial 2 mapel)
        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm1->id,
            'siswa_id' => $this->siswa1->id,
            'rata_formatif' => 90,
            'sts' => 90,
            'sas' => 90,
            'nilai_akhir' => 90,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm2->id,
            'siswa_id' => $this->siswa1->id,
            'rata_formatif' => 80,
            'sts' => 80,
            'sas' => 80,
            'nilai_akhir' => 80,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm1->id,
            'siswa_id' => $this->siswa2->id,
            'rata_formatif' => 70,
            'sts' => 70,
            'sas' => 70,
            'nilai_akhir' => 70,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm2->id,
            'siswa_id' => $this->siswa2->id,
            'rata_formatif' => 60,
            'sts' => 60,
            'sas' => 60,
            'nilai_akhir' => 60,
        ]);

        $service = app(RekapPenilaianService::class);
        $data = $service->getLegerRombelData($this->rombel, $this->semester);

        $this->assertNotEmpty($data['mapel_list']);
        $this->assertCount(2, $data['siswa_rows']);

        // Cek Siswa 1
        $rowSiswa1 = collect($data['siswa_rows'])->firstWhere('siswa_id', $this->siswa1->id);
        $this->assertNotNull($rowSiswa1);
        $this->assertEquals(170, $rowSiswa1['total_nilai']);
        $this->assertEquals(85.0, $rowSiswa1['rata_rata']);
        $this->assertEquals(1, $rowSiswa1['rank']);
        $this->assertEquals(0, $rowSiswa1['below_kkm_count']);
        $this->assertTrue($rowSiswa1['is_tuntas']);

        // Cek Siswa 2
        $rowSiswa2 = collect($data['siswa_rows'])->firstWhere('siswa_id', $this->siswa2->id);
        $this->assertNotNull($rowSiswa2);
        $this->assertEquals(130, $rowSiswa2['total_nilai']);
        $this->assertEquals(65.0, $rowSiswa2['rata_rata']);
        $this->assertEquals(2, $rowSiswa2['rank']);
        $this->assertEquals(2, $rowSiswa2['below_kkm_count']);
        $this->assertFalse($rowSiswa2['is_tuntas']);

        // Cek Summary Rombel
        $summary = $data['summary'];
        $this->assertEquals(2, $summary['total_siswa']);
        $this->assertEquals(75.0, $summary['rata_rata_rombel']); // (85 + 65) / 2
        $this->assertEquals(1, $summary['siswa_tuntas_count']);
        $this->assertEquals(1, $summary['siswa_remedial_count']);
        $this->assertEquals('Aditya Pratama', $summary['top_student_nama']);
        $this->assertEquals(85.0, $summary['top_student_avg']);

        // Cek Statistik Mapel 1 (Matematika)
        $m1Stats = $data['mapel_stats'][$this->mapel1->id];
        $this->assertEquals(80.0, $m1Stats['avg']); // (90 + 70) / 2
        $this->assertEquals(90, $m1Stats['max']);
        $this->assertEquals(70, $m1Stats['min']);
        $this->assertEquals(1, $m1Stats['tuntas_count']);
        $this->assertEquals(1, $m1Stats['remedial_count']);
        $this->assertEquals(50.0, $m1Stats['persen_tuntas']);
        $this->assertCount(1, $m1Stats['remedial_students']);
        $this->assertEquals('Budi Santoso', $m1Stats['remedial_students'][0]['nama']);
        $this->assertEquals(5, $m1Stats['remedial_students'][0]['selisih']); // 75 - 70 = 5
    }

    public function test_livewire_component_interactivity_and_tab_switching(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RekapPenilaianIndex::class)
            ->assertOk()
            ->assertSet('activeSubTab', 'leger')
            ->call('switchSubTab', 'mapel')
            ->assertSet('activeSubTab', 'mapel')
            ->call('switchSubTab', 'komparasi')
            ->assertSet('activeSubTab', 'komparasi')
            ->call('switchSubTab', 'leger')
            ->assertSet('activeSubTab', 'leger');
    }

    public function test_livewire_student_search_filter(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RekapPenilaianIndex::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->call('tampilkanData')
            ->set('search', 'Aditya')
            ->assertSee('Aditya Pratama')
            ->assertDontSee('Budi Santoso');
    }

    public function test_excel_export_leger_streams_valid_spreadsheet(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(RekapPenilaianIndex::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedSemesterId', $this->semester->id)
            ->call('tampilkanData');

        $response = $component->call('exportLegerExcel');
        $this->assertNotNull($response);
    }

    public function test_excel_export_analisis_mapel_streams_valid_spreadsheet(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(RekapPenilaianIndex::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedSemesterId', $this->semester->id)
            ->call('tampilkanData');

        $response = $component->call('exportAnalisisExcel');
        $this->assertNotNull($response);
    }

    public function test_rekap_penilaian_service_calculates_cross_rayon_comparison_accurately(): void
    {
        // Siswa 1 (Aditya): Mapel 1 = 90, Mapel 2 = 80 -> Rerata 85 (Tuntas)
        // Siswa 2 (Budi): Mapel 1 = 70, Mapel 2 = 60 -> Rerata 65 (Remedial)
        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm1->id,
            'siswa_id' => $this->siswa1->id,
            'rata_formatif' => 90,
            'sts' => 90,
            'sas' => 90,
            'nilai_akhir' => 90,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm2->id,
            'siswa_id' => $this->siswa1->id,
            'rata_formatif' => 80,
            'sts' => 80,
            'sas' => 80,
            'nilai_akhir' => 80,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm1->id,
            'siswa_id' => $this->siswa2->id,
            'rata_formatif' => 70,
            'sts' => 70,
            'sas' => 70,
            'nilai_akhir' => 70,
        ]);

        NilaiAkhir::create([
            'penugasan_mengajar_id' => $this->pm2->id,
            'siswa_id' => $this->siswa2->id,
            'rata_formatif' => 60,
            'sts' => 60,
            'sas' => 60,
            'nilai_akhir' => 60,
        ]);

        $service = app(RekapPenilaianService::class);
        $rayonData = $service->getCrossRayonComparison('semua', $this->semester);

        $this->assertNotEmpty($rayonData);
        $ciawi = $rayonData->firstWhere('rayon_id', $this->rayon->id);
        $this->assertNotNull($ciawi);
        $this->assertEquals(2, $ciawi['total_siswa']);
        $this->assertEquals(75.0, $ciawi['rata_rata_rayon']);
        $this->assertEquals(50.0, $ciawi['persen_tuntas_klasikal']);
        $this->assertEquals(1, $ciawi['siswa_tuntas_count']);
        $this->assertEquals(1, $ciawi['siswa_remedial_count']);
        $this->assertEquals('Aditya Pratama', $ciawi['top_student_nama']);
        $this->assertCount(2, $ciawi['students']);
    }

    public function test_livewire_switch_to_rayon_tab_and_toggle_detail(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RekapPenilaianIndex::class)
            ->call('switchSubTab', 'rayon')
            ->assertSet('activeSubTab', 'rayon')
            ->call('toggleRayonDetail', $this->rayon->id)
            ->assertSet('selectedRayonId', $this->rayon->id)
            ->call('toggleRayonDetail', $this->rayon->id)
            ->assertSet('selectedRayonId', null);
    }
}
