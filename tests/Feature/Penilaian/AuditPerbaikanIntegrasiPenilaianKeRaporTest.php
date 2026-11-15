<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\PenilaianScoreCalculator;
use App\Modules\Penilaian\Services\RekapPenilaianService;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use App\Modules\Rapor\Services\RaporScoreService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuditPerbaikanIntegrasiPenilaianKeRaporTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Rombel $rombel;
    protected Rayon $rayon;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_test_audit',
            'is_active' => true,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->where('is_active', true)->first()
            ?? Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->rombel = Rombel::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();
        $this->rayon = Rayon::create([
            'nama' => 'Rayon Sukasari 1',
            'wilayah_cakupan' => 'Sukasari',
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Andi Pratama',
            'nipd' => '12001',
            'nisn' => '0051234567',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    /**
     * Uji 1: Penilaian 9M dengan 0 TP tidak boleh anjlok ke 50.0 atau ditandai belum kompeten,
     * melainkan harus ditandai belum_lengkap karena TP belum dibuat.
     */
    public function test_zero_tp_in_9m_block_marks_as_belum_lengkap_and_prevents_false_failure(): void
    {
        $calculator = app(PenilaianScoreCalculator::class);
        $settings = PengaturanPenilaian::getActive();

        // Kasus: Guru belum membuat TP (tps collection kosong), tetapi menginput NH 100 dan STS 100
        $emptyTps = collect();
        $tpScores = [];
        $nhScore = 100.0;
        $sumatifScore = 100.0;
        $kkm = 75.0;

        $result = $calculator->calculate9MBlock(
            $tpScores,
            $nhScore,
            $sumatifScore,
            $kkm,
            $emptyTps,
            null,
            $settings
        );

        $this->assertEquals('belum_lengkap', $result['status']);
        $this->assertNull($result['nilai_akhir']);
        $this->assertEquals('Belum Lengkap', $result['predikat']);
        $this->assertStringContainsString('Tujuan Pembelajaran belum dibuat', $result['deskripsi_tertinggi']);
    }

    /**
     * Uji 2: Penggabungan narasi semester di PenilaianScoreCalculator tidak boleh membuang capaian blok 1.
     */
    public function test_calculate_semester_combines_narration_from_both_9m_blocks(): void
    {
        $calculator = app(PenilaianScoreCalculator::class);

        $block1 = [
            'status' => 'kompeten',
            'nilai_akhir' => 85.0,
            'narasi_capaian' => 'Menunjukkan penguasaan yang baik dalam algoritma dasar.',
            'deskripsi_tertinggi' => 'Menunjukkan penguasaan yang baik dalam algoritma dasar.',
            'deskripsi_terendah' => '',
        ];

        $block2 = [
            'status' => 'kompeten',
            'nilai_akhir' => 90.0,
            'narasi_capaian' => 'Menunjukkan penguasaan yang sangat baik dalam basis data relasional.',
            'deskripsi_tertinggi' => 'Menunjukkan penguasaan yang sangat baik dalam basis data relasional.',
            'deskripsi_terendah' => '',
        ];

        $result = $calculator->calculateSemester($block1, $block2, 75.0);

        $this->assertEquals('kompeten', $result['status']);
        $this->assertEquals(87.5, $result['nilai_akhir']);
        $this->assertStringContainsString('algoritma dasar', $result['deskripsi']);
        $this->assertStringContainsString('basis data relasional', $result['deskripsi']);
        $this->assertStringContainsString('algoritma dasar', $result['deskripsi_tertinggi']);
        $this->assertStringContainsString('basis data relasional', $result['deskripsi_tertinggi']);
    }

    /**
     * Uji 3: Evaluasi ekstrakurikuler terisolasi per periode penilaian (STS 1 vs SAS)
     * sehingga penyimpanan nilai SAS tidak merusak nilai riwayat 9M1 (STS 1).
     */
    public function test_ekskul_evaluations_isolated_per_period(): void
    {
        $ekskul = Ekstrakurikuler::create([
            'nama' => 'Pramuka Garuda',
            'kode' => 'PRAM-G',
            'is_active' => true,
        ]);

        $anggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'siswa_id' => $this->siswa->id,
            'semester_id' => $this->semester->id,
            'sts_1' => 80.0,
            'nilai_angka' => 86.0,
            'nilai_huruf' => 'Baik',
            'deskripsi' => 'Disiplin dan aktif pada latihan dasar STS 1.',
            'evaluations' => [
                'STS 1' => [
                    'asesmen' => 80.0,
                    'nilai_presensi' => 100.0,
                    'nilai_akhir' => 86.0,
                    'nilai_huruf' => 'Baik',
                    'deskripsi' => 'Disiplin dan aktif pada latihan dasar STS 1.',
                ],
            ],
        ]);

        // Simpan nilai baru untuk periode SAS
        $evals = $anggota->evaluations;
        $evals['SAS'] = [
            'asesmen' => 95.0,
            'nilai_presensi' => 100.0,
            'nilai_akhir' => 96.5,
            'nilai_huruf' => 'Sangat Baik',
            'deskripsi' => 'Memimpin regu dengan sangat baik pada kegiatan kemah akhir semester.',
        ];
        $anggota->update([
            'sas' => 95.0,
            'nilai_angka' => 96.5,
            'nilai_huruf' => 'Sangat Baik',
            'deskripsi' => 'Memimpin regu dengan sangat baik pada kegiatan kemah akhir semester.',
            'evaluations' => $evals,
        ]);

        // Verifikasi isolasi via model method
        $evalSts1 = $anggota->getEvaluationForPeriod('STS 1');
        $this->assertEquals('Baik', $evalSts1['nilai_huruf']);
        $this->assertEquals('Disiplin dan aktif pada latihan dasar STS 1.', $evalSts1['deskripsi']);

        $evalSas = $anggota->getEvaluationForPeriod('SAS');
        $this->assertEquals('Sangat Baik', $evalSas['nilai_huruf']);
        $this->assertStringContainsString('kegiatan kemah', $evalSas['deskripsi']);

        // Verifikasi konsumsi di RaporScoreService sesuai targetKey
        $raporService = app(RaporScoreService::class);
        $ekskul9M1 = $raporService->getEkskuls($this->siswa->id, $this->semester->id, '9M1')->first();
        $this->assertEquals('Baik', $ekskul9M1->nilai_huruf);
        $this->assertEquals('Disiplin dan aktif pada latihan dasar STS 1.', $ekskul9M1->deskripsi);

        $ekskul9M2 = $raporService->getEkskuls($this->siswa->id, $this->semester->id, '9M2')->first();
        $this->assertEquals('Sangat Baik', $ekskul9M2->nilai_huruf);
        $this->assertStringContainsString('kegiatan kemah', $ekskul9M2->deskripsi);
    }

    /**
     * Uji 4: Penentuan indeks semester transkrip nilai bersifat deterministik berdasarkan riwayat rombel.
     */
    public function test_transkrip_resolve_tingkat_for_semester_deterministic(): void
    {
        // Daftarkan siswa ke rombel kelas XI di tabel riwayat rombel_siswa
        $taXI = TahunAjaran::create([
            'nama' => '2027/2028',
            'tgl_mulai' => '2027-07-15',
            'tgl_selesai' => '2028-06-25',
            'is_active' => false,
        ]);
        $semGanjilXI = Semester::create([
            'tahun_ajaran_id' => $taXI->id,
            'semester' => 'ganjil',
            'is_active' => false,
        ]);
        $rombelXI = Rombel::create([
            'tahun_ajaran_id' => $taXI->id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => 'XI',
            'nama' => 'XI PPLG 1',
        ]);

        DB::table('rombel_siswa')->insert([
            'tahun_ajaran_id' => $taXI->id,
            'siswa_id' => $this->siswa->id,
            'rombel_id' => $rombelXI->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resolvedTingkat = TranskripNilaiService::resolveTingkatForSemester($this->siswa->id, $semGanjilXI, $this->rombel);
        $this->assertEquals('XI', $resolvedTingkat);

        $semIdx = TranskripNilaiService::getSemesterIndex($resolvedTingkat, $semGanjilXI->semester);
        $this->assertEquals(3, $semIdx); // Kelas XI Ganjil adalah semester ke-3
    }

    /**
     * Uji 5: Rekap Leger menggunakan NilaiFormatif dan NilaiHarian nyata saat fallback.
     */
    public function test_rekap_penilaian_uses_actual_nilai_harian_and_formatif(): void
    {
        $mapel = Mapel::create([
            'kode' => 'INF-01',
            'nama' => 'Informatika Dasar',
            'kelompok' => Mapel::KELOMPOK_UMUM,
            'kkm_default' => 75,
            'urutan' => 1,
            'is_active' => true,
        ]);

        \App\Modules\Akademik\Models\StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'tingkat' => $this->rombel->tingkat,
            'mapel_id' => $mapel->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
        ]);

        $penugasan = PenugasanMengajar::create([
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $mapel->id,
            'semester_id' => $this->semester->id,
        ]);

        $tp = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $penugasan->id,
            'kode' => 'TP-1',
            'deskripsi' => 'Memahami pemrograman berorientasi objek',
            'target_periode' => '9M1',
            'urutan' => 1,
        ]);

        // Simpan nilai formatif
        NilaiFormatif::create([
            'siswa_id' => $this->siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'nilai' => 88.0,
        ]);

        // Simpan Nilai Harian kustom (bukan dummy 100)
        NilaiHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $penugasan->id,
            'target_periode' => '9M1',
            'nilai' => 80.0,
        ]);

        // Simpan nilai sumatif
        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $penugasan->id,
            'sts' => 90.0,
        ]);

        $rekapService = app(RekapPenilaianService::class);
        $leger = $rekapService->getLegerRombelData($this->rombel, $this->semester);

        $studentRow = collect($leger['siswa_rows'])->firstWhere('siswa_id', $this->siswa->id);
        $this->assertNotNull($studentRow);

        $score = $studentRow['scores'][$mapel->id]['nilai_akhir'] ?? null;
        // Formula: (88 * 50 + 90 * 30 + 80 * 20) / 100 = (4400 + 2700 + 1600) / 100 = 87.0
        $this->assertEquals(87.0, $score);
    }
}
