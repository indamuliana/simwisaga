<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\KelompokMapel;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\TranskripPenilaianIndex;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Services\TranskripNilaiExporter;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TranskripPenilaianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected Jurusan $jurusan;
    protected Rombel $rombelX;
    protected Rombel $rombelXI;
    protected Rombel $rombelXII;
    protected Mapel $mapelA;
    protected Mapel $mapelB;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_transkrip',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();

        $this->semGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->first()
            ?? Semester::create(['tahun_ajaran_id' => $this->ta->id, 'semester' => 'ganjil', 'is_active' => true]);

        $this->semGenap = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'genap')->first()
            ?? Semester::create(['tahun_ajaran_id' => $this->ta->id, 'semester' => 'genap', 'is_active' => false]);

        $this->jurusan = Jurusan::firstOrCreate(
            ['kode' => 'PPLG'],
            ['nama' => 'Pengembangan Perangkat Lunak dan Gim', 'is_active' => true]
        );

        $this->rombelX = Rombel::firstOrCreate(
            ['tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $this->jurusan->id, 'tingkat' => 'X', 'nomor_urut' => 1],
            ['nama' => 'X PPLG 1']
        );

        $this->rombelXI = Rombel::firstOrCreate(
            ['tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $this->jurusan->id, 'tingkat' => 'XI', 'nomor_urut' => 1],
            ['nama' => 'XI PPLG 1']
        );

        $this->rombelXII = Rombel::firstOrCreate(
            ['tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $this->jurusan->id, 'tingkat' => 'XII', 'nomor_urut' => 1],
            ['nama' => 'XII PPLG 1']
        );

        $kelA = KelompokMapel::firstOrCreate(['kode' => 'A'], ['nama' => 'Muatan Umum', 'urutan' => 1, 'is_active' => true]);
        $kelB = KelompokMapel::firstOrCreate(['kode' => 'B'], ['nama' => 'Muatan Kejuruan', 'urutan' => 2, 'is_active' => true]);

        $this->mapelA = Mapel::create([
            'kode' => 'MTK-TR',
            'nama' => 'Matematika Terapan',
            'kelompok' => 'umum',
            'kelompok_mapel_id' => $kelA->id,
            'kkm_default' => 75,
            'urutan' => 1,
            'is_active' => true,
        ]);

        $this->mapelB = Mapel::create([
            'kode' => 'WEB-TR',
            'nama' => 'Pemrograman Web',
            'kelompok' => 'kejuruan',
            'kelompok_mapel_id' => $kelB->id,
            'kkm_default' => 75,
            'urutan' => 2,
            'is_active' => true,
        ]);

        // Struktur Kurikulum
        StrukturKurikulum::firstOrCreate([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapelA->id,
        ], [
            'jp_per_minggu' => 4,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
        ]);

        StrukturKurikulum::firstOrCreate([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XII',
            'mapel_id' => $this->mapelB->id,
        ], [
            'jp_per_minggu' => 6,
            'jp_ganjil' => 6,
            'jp_genap' => 6,
            'urutan' => 2,
        ]);

        // Buat 2 Siswa di Tingkat XII PPLG
        $this->siswa1 = Siswa::create([
            'rombel_id' => $this->rombelXII->id,
            'nama' => 'Ahmad Fadhil',
            'nipd' => '22001',
            'nisn' => '0011223344',
            'jk' => 'L',
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'rombel_id' => $this->rombelXII->id,
            'nama' => 'Bintang Pratama',
            'nipd' => '22002',
            'nisn' => '0011223355',
            'jk' => 'L',
            'is_active' => true,
        ]);

        // Buat Penugasan Mengajar di Kelas X (Sem Ganjil = S1, Genap = S2) & Kelas XII (Sem Ganjil = S5, Genap = S6)
        $pegawai = Pegawai::create([
            'user_id' => $this->admin->id,
            'nama_lengkap' => 'Guru Pengampu',
            'nip' => '198001012010011001',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $pmXGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombelX->id,
            'mapel_id' => $this->mapelA->id,
            'pegawai_id' => $pegawai->id,
        ]);

        $pmXGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'rombel_id' => $this->rombelX->id,
            'mapel_id' => $this->mapelA->id,
            'pegawai_id' => $pegawai->id,
        ]);

        $pmXIIGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombelXII->id,
            'mapel_id' => $this->mapelB->id,
            'pegawai_id' => $pegawai->id,
        ]);

        $pmXIIGenap = PenugasanMengajar::create([
            'semester_id' => $this->semGenap->id,
            'rombel_id' => $this->rombelXII->id,
            'mapel_id' => $this->mapelB->id,
            'pegawai_id' => $pegawai->id,
        ]);

        // Siswa 1: S1 = 90, S2 = 92, S5 = 88, S6 = 90
        NilaiAkhir::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $pmXGanjil->id, 'nilai_akhir' => 90]);
        NilaiAkhir::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $pmXGenap->id, 'nilai_akhir' => 92]);
        NilaiAkhir::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $pmXIIGanjil->id, 'nilai_akhir' => 88]);
        NilaiAkhir::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $pmXIIGenap->id, 'nilai_akhir' => 90]);

        // Siswa 2: S1 = 80, S2 = 82, S5 = 78, S6 = 80
        NilaiAkhir::create(['siswa_id' => $this->siswa2->id, 'penugasan_mengajar_id' => $pmXGanjil->id, 'nilai_akhir' => 80]);
        NilaiAkhir::create(['siswa_id' => $this->siswa2->id, 'penugasan_mengajar_id' => $pmXGenap->id, 'nilai_akhir' => 82]);
        NilaiAkhir::create(['siswa_id' => $this->siswa2->id, 'penugasan_mengajar_id' => $pmXIIGanjil->id, 'nilai_akhir' => 78]);
        NilaiAkhir::create(['siswa_id' => $this->siswa2->id, 'penugasan_mengajar_id' => $pmXIIGenap->id, 'nilai_akhir' => 80]);
    }

    public function test_guest_cannot_access_transkrip_penilaian(): void
    {
        $response = $this->get(route('penilaian.transkrip'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_transkrip_penilaian(): void
    {
        $response = $this->actingAs($this->admin)->get(route('penilaian.transkrip'));
        $response->assertOk();
        $response->assertSee('Transkrip Siswa Tahunan');
    }

    public function test_transkrip_service_calculates_cohort_data_and_ranking_accurately(): void
    {
        $service = app(TranskripNilaiService::class);
        $data = $service->getCohortTranskripData($this->jurusan->id, 'XII');

        $this->assertEquals(2, $data['total_siswa']);
        $this->assertCount(2, $data['students']);

        $topStudent = $data['students'][0];
        $secondStudent = $data['students'][1];

        // Siswa 1 harus memiliki nilai lebih tinggi dan Rank 1
        $this->assertEquals('Ahmad Fadhil', $topStudent['nama']);
        $this->assertEquals(1, $topStudent['ranking']);
        $this->assertEquals(90.0, (float) $topStudent['s1']);
        $this->assertEquals(92.0, (float) $topStudent['s2']);
        $this->assertEquals(91.0, (float) $topStudent['rata_x']);
        $this->assertEquals(88.0, (float) $topStudent['s5']);
        $this->assertEquals(90.0, (float) $topStudent['s6']);
        $this->assertEquals(89.0, (float) $topStudent['rata_xii']);
        $this->assertEquals(90.0, (float) $topStudent['nilai_kumulatif']); // (90+92+88+90)/4 = 90.0
        $this->assertEquals('A', $topStudent['predikat']);

        // Siswa 2 harus Rank 2
        $this->assertEquals('Bintang Pratama', $secondStudent['nama']);
        $this->assertEquals(2, $secondStudent['ranking']);
        $this->assertEquals(80.0, (float) $secondStudent['nilai_kumulatif']);
        $this->assertEquals('B', $secondStudent['predikat']);

        // Stats angkatan
        $this->assertEquals(85.0, (float) $data['stats']['rata_angkatan']); // (90 + 80) / 2 = 85.0
        $this->assertEquals(90.0, (float) $data['stats']['nilai_tertinggi']);
        $this->assertEquals(80.0, (float) $data['stats']['nilai_terendah']);
        $this->assertEquals(100.0, (float) $data['stats']['persen_tuntas']);
    }

    public function test_transkrip_service_calculates_individual_student_transcript_accurately(): void
    {
        $service = app(TranskripNilaiService::class);
        $data = $service->getSiswaTranskripData($this->siswa1->id);

        $this->assertEquals('Ahmad Fadhil', $data['siswa']['nama']);
        $this->assertNotEmpty($data['grouped_mapels']);

        // Nilai kumulatif & status
        $this->assertGreaterThan(0, $data['summary']['nilai_kumulatif']);
        $this->assertEquals('A', $data['summary']['predikat']);
        $this->assertTrue($data['summary']['is_lulus']);
    }

    public function test_livewire_transkrip_component_interactivity(): void
    {
        Livewire::actingAs($this->admin)
            ->test(TranskripPenilaianIndex::class)
            ->assertSet('activeTab', 'matriks')
            ->assertSee('Ahmad Fadhil')
            ->assertSee('Bintang Pratama')
            ->call('switchTab', 'individual')
            ->assertSet('activeTab', 'individual')
            ->call('selectSiswa', $this->siswa2->id)
            ->assertSet('selectedSiswaId', $this->siswa2->id)
            ->assertSet('activeTab', 'individual')
            ->call('switchTab', 'analisis')
            ->assertSet('activeTab', 'analisis')
            ->set('search', 'Ahmad')
            ->assertSet('search', 'Ahmad');
    }

    public function test_excel_export_cohort_transkrip_streams_valid_spreadsheet(): void
    {
        $exporter = app(TranskripNilaiExporter::class);
        $spreadsheet = $exporter->exportCohortTranskrip($this->jurusan->id, 'XII');

        $sheet = $spreadsheet->getActiveSheet();
        $this->assertEquals('SMK WIKRAMA 1 GARUT', $sheet->getCell('A1')->getValue());
        $this->assertEquals('Ahmad Fadhil', $sheet->getCell('D9')->getValue());
    }

    public function test_pdf_export_streams_valid_pdf_document(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('penilaian.transkrip.pdf', ['siswa_id' => $this->siswa1->id]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_rombel_options_display_tahun_ajaran_distinction(): void
    {
        // Buat rombel dengan nama sama di tahun ajaran berbeda
        $ta2 = TahunAjaran::create([
            'nama' => '2026/2027',
            'tgl_mulai' => '2026-07-01',
            'tgl_selesai' => '2027-06-30',
            'is_active' => false,
        ]);

        $rombelXII_future = Rombel::create([
            'tahun_ajaran_id' => $ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XII',
            'nomor_urut' => 2,
            'nama' => 'XII PPLG 2',
        ]);

        // Cek bahwa di Livewire view, label rombel memuat pembeda TP
        Livewire::actingAs($this->admin)
            ->test(TranskripPenilaianIndex::class, ['selectedJurusanId' => $this->jurusan->id])
            ->set('selectedTaId', 'semua')
            ->assertSeeHtml('TP ' . $this->ta->nama)
            ->assertSeeHtml('TP ' . $ta2->nama);
    }

    public function test_filter_by_tahun_ajaran_isolates_cohort(): void
    {
        $service = app(TranskripNilaiService::class);

        // TA aktif harus memuat siswa1 dan siswa2
        $dataCurrentTa = $service->getCohortTranskripData($this->jurusan->id, 'XII', null, null, $this->ta->id);
        $this->assertEquals(2, $dataCurrentTa['total_siswa']);

        // TA baru yang belum ada siswa harus return 0 siswa
        $taEmpty = TahunAjaran::create([
            'nama' => '2027/2028',
            'tgl_mulai' => '2027-07-01',
            'tgl_selesai' => '2028-06-30',
            'is_active' => false,
        ]);
        $dataEmptyTa = $service->getCohortTranskripData($this->jurusan->id, 'XII', null, null, $taEmpty->id);
        $this->assertEquals(0, $dataEmptyTa['total_siswa']);
    }
}
