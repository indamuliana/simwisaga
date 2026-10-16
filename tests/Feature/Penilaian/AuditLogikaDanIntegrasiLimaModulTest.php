<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use App\Modules\Penilaian\Services\RekapPenilaianService;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use App\Modules\Rapor\Services\RaporScoreService;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogikaDanIntegrasiLimaModulTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Rombel $rombel;
    protected Rayon $rayon;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_audit',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->where('is_active', true)->first()
            ?? Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->rombel = Rombel::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->rayon = Rayon::create([
            'nama' => 'Rayon Audit 1',
            'wilayah_cakupan' => 'Garut Kota',
        ]);

        $this->siswa1 = Siswa::create([
            'nama' => 'Siti Aminah Audit',
            'nipd' => '99001',
            'nisn' => '9951112221',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Budi Santoso Audit',
            'nipd' => '99002',
            'nisn' => '9951112222',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    public function test_ekstrakurikuler_accessors_and_rapor_rendering(): void
    {
        $ekskul = Ekstrakurikuler::create([
            'nama' => 'Pramuka Wikrama',
            'kode' => 'PRAM',
            'is_active' => true,
        ]);

        $anggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $this->semester->id,
            'nilai_huruf' => 'Sangat Baik',
            'deskripsi' => 'Sangat aktif dalam memimpin regu dan disiplin.',
            'status_keanggotaan' => 'aktif',
        ]);

        // Verifikasi accessors getPredikatAttribute dan getKeteranganAttribute
        $this->assertEquals('Sangat Baik', $anggota->predikat);
        $this->assertEquals('Sangat aktif dalam memimpin regu dan disiplin.', $anggota->keterangan);

        // Verifikasi Rapor Wikrama PDF template rendering
        $raporService = app(RaporScoreService::class);
        $ekskuls = $raporService->getEkskuls($this->siswa1->id, $this->semester->id);
        $this->assertCount(1, $ekskuls);

        $sekolah = \App\Modules\Sistem\Models\School::current();
        $periodMeta = $raporService->getPeriodMeta('9M1');

        $studentsData = [
            [
                'siswa' => $this->siswa1,
                'semester' => $this->semester,
                'fase' => 'E',
                'semNama' => 'Ganjil',
                'periode' => null,
                'periodMeta' => $periodMeta,
                'groupedAcademic' => collect(),
                'sumatifLabel' => $periodMeta['sumatif_label'],
                'sekolah' => $sekolah,
                'tanggal' => now()->toDateString(),
                'ekskuls' => $ekskuls,
                'rekapAbsen' => ['sakit' => 0, 'izin' => 0, 'alpa' => 0],
                'catatan' => null,
                'statusData' => ['kelengkapan' => 'Lengkap', 'kompetensi' => 'Kompeten', 'seni' => null],
            ]
        ];

        $htmlWikrama = view('rapor::pdf.wikrama-nilai', compact('studentsData'))->render();

        $this->assertStringContainsString('Pramuka Wikrama', $htmlWikrama);
        $this->assertStringContainsString('Sangat Baik - Sangat aktif dalam memimpin regu dan disiplin.', $htmlWikrama);
        $this->assertStringNotContainsString('Aktif Mengikuti', $htmlWikrama);

        // Verifikasi Rapor Pemerintah PDF template rendering
        $htmlPemerintah = view('rapor::pdf.pemerintah-nilai', compact('studentsData'))->render();

        $this->assertStringContainsString('Pramuka Wikrama', $htmlPemerintah);
        $this->assertStringContainsString('Sangat Baik - Sangat aktif dalam memimpin regu dan disiplin.', $htmlPemerintah);
        // Verifikasi empty table row colspan=5
        $this->assertStringContainsString('colspan="5"', $htmlPemerintah);
    }

    public function test_mapel_pilihan_included_in_leger_and_dynamic_weights_fallback(): void
    {
        $mapil = MapelPilihan::create([
            'nama' => 'Bahasa Jepang Praktis',
            'kode' => 'JPN',
            'kkm_default' => 75,
            'is_active' => true,
        ]);

        // Siswa 1 terdaftar di Mapel Pilihan
        MapelPilihanAnggota::create([
            'mapel_pilihan_id' => $mapil->id,
            'siswa_id' => $this->siswa1->id,
            'status' => 'aktif',
            'tahun_ajaran_id' => $this->ta->id,
        ]);

        NilaiMapelPilihanAkhir::create([
            'mapel_pilihan_id' => $mapil->id,
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $this->semester->id,
            'nilai_akhir' => 88.50,
            'rata_formatif' => 90.0,
            'sts_1' => 85.0,
            'predikat' => 'Cakap',
            'status' => 'kompeten',
            'deskripsi_tertinggi' => 'Sangat baik dalam percakapan sehari-hari.',
            'deskripsi_terendah' => '',
        ]);

        $service = app(RekapPenilaianService::class);
        $leger = $service->getLegerRombelData($this->rombel, $this->semester);

        // 1. Verifikasi Mapel Pilihan ada di mapel_list
        $mapilKey = 'mapil_' . $mapil->id;
        $this->assertArrayHasKey($mapilKey, $leger['mapel_list']);
        $this->assertEquals('Kelompok C (Mata Pelajaran Pilihan)', $leger['mapel_list'][$mapilKey]['kelompok_label']);
        $this->assertEquals('Bahasa Jepang Praktis', $leger['mapel_list'][$mapilKey]['nama']);

        // 2. Verifikasi nilai siswa 1 masuk ke matriks
        $siswa1Row = collect($leger['siswa_rows'])->firstWhere('siswa_id', $this->siswa1->id);
        $this->assertNotNull($siswa1Row);
        $this->assertEquals(88.50, $siswa1Row['scores'][$mapilKey]['nilai_akhir']);
        $this->assertFalse($siswa1Row['scores'][$mapilKey]['is_below_kkm']);

        // 3. Verifikasi siswa 2 (tidak terdaftar) tidak terpengaruh (nilai_akhir null, tidak merusak rata-rata)
        $siswa2Row = collect($leger['siswa_rows'])->firstWhere('siswa_id', $this->siswa2->id);
        $this->assertNotNull($siswa2Row);
        $this->assertNull($siswa2Row['scores'][$mapilKey]['nilai_akhir']);
    }

    public function test_mapel_pilihan_included_in_6_semester_transcript(): void
    {
        $mapil = MapelPilihan::create([
            'nama' => 'Internet of Things (IoT)',
            'kode' => 'IOT',
            'kkm_default' => 75,
            'is_active' => true,
        ]);

        MapelPilihanAnggota::create([
            'mapel_pilihan_id' => $mapil->id,
            'siswa_id' => $this->siswa1->id,
            'status' => 'aktif',
            'tahun_ajaran_id' => $this->ta->id,
        ]);

        NilaiMapelPilihanAkhir::create([
            'mapel_pilihan_id' => $mapil->id,
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $this->semester->id,
            'nilai_akhir' => 92.0,
            'predikat' => 'Mahir',
            'status' => 'kompeten',
        ]);

        $transcriptService = app(TranskripNilaiService::class);
        $data = $transcriptService->getSiswaTranskripData($this->siswa1->id);

        // Verifikasi kelompok Mata Pelajaran Pilihan muncul di grouped_mapels
        $pilihanGroup = collect($data['grouped_mapels'])->first(function ($g) {
            return str_contains(strtolower($g['kelompok_nama']), 'pilihan');
        });

        $this->assertNotNull($pilihanGroup, 'Kelompok Mata Pelajaran Pilihan harus muncul di transkrip siswa.');
        $iotItem = collect($pilihanGroup['items'])->firstWhere('kode', 'IOT');
        $this->assertNotNull($iotItem, 'Mata Pelajaran Pilihan IoT harus ada dalam item transkrip.');
        $this->assertEquals(92.0, $iotItem['rata_mapel']);
        $this->assertEquals('A', $iotItem['predikat']);

        // Verifikasi cohort transcript menyertakan nilai mapil
        $cohort = $transcriptService->getCohortTranskripData($this->rombel->jurusan_id, 'X', $this->rombel->id);
        $this->assertGreaterThan(0, $cohort['total_siswa']);
        $studentInCohort = collect($cohort['students'])->firstWhere('siswa_id', $this->siswa1->id);
        $this->assertNotNull($studentInCohort);
        $this->assertGreaterThan(0, $studentInCohort['nilai_kumulatif']);
    }

    public function test_seni_budaya_handling_when_registered_and_unregistered(): void
    {
        $raporService = app(RaporScoreService::class);

        // 1. Siswa 1 belum terdaftar di cabang Seni Budaya
        $recordsSiswa1 = $raporService->getWikramaAcademicRecords($this->siswa1, $this->semester, '9M1');
        $seniItem1 = $recordsSiswa1->firstWhere('is_seni', true);
        $this->assertNotNull($seniItem1);
        $this->assertEquals('Belum Lengkap', $seniItem1->predikat);
        $this->assertStringContainsString('belum terdaftar di cabang Seni Budaya', $seniItem1->deskripsi_tertinggi);

        // 2. Daftarkan siswa 2 ke cabang Seni Budaya
        $seni = SeniBudaya::create([
            'nama' => 'Seni Musik Tradisional Sunda',
            'kode' => 'S-MUS',
            'kkm' => 75,
            'is_active' => true,
        ]);

        SeniBudayaAnggota::create([
            'seni_budaya_id' => $seni->id,
            'siswa_id' => $this->siswa2->id,
            'semester_id' => $this->semester->id,
            'sts_1' => 88.0,
            'nilai_presensi' => 100.0,
            'nilai_akhir_sts_1' => 91.6,
            'nilai_akhir' => 91.6,
            'status_keanggotaan' => 'aktif',
        ]);

        $recordsSiswa2 = $raporService->getWikramaAcademicRecords($this->siswa2, $this->semester, '9M1');
        $seniItem2 = $recordsSiswa2->firstWhere('is_seni', true);
        $this->assertNotNull($seniItem2);
        $this->assertEquals('kompeten', $seniItem2->status);
        $this->assertStringContainsString('Seni Musik Tradisional Sunda', $seniItem2->mapel_nama);
        $this->assertEquals(91.6, $seniItem2->nilai_akhir);
    }
}
