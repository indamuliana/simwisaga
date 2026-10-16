<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\PenilaianInput;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Sistem\Models\PeriodeRapor;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenilaianRekap9MingguTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected Pegawai $guru;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Rombel $rombel;
    protected Mapel $mapel;
    protected PenugasanMengajar $pmGanjil;
    protected Siswa $siswa;
    protected PeriodeRapor $periode9M1;
    protected PeriodeRapor $periode9M2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)
            ->where('semester', 'ganjil')
            ->firstOrFail();

        $this->rombel = Rombel::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->guruUser = User::factory()->create([
            'username' => 'guru_rekap9m',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruUser->assignRole('Guru');

        $this->guru = Pegawai::create([
            'user_id' => $this->guruUser->id,
            'nama_lengkap' => 'Guru Penilai 9M',
            'nip' => '198501012010011999',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $this->mapel = Mapel::create([
            'kode' => 'MAT-9M',
            'nama' => 'Matematika Terapan',
            'kelompok' => 'umum',
            'kkm_default' => 75,
            'is_active' => true,
        ]);

        $this->pmGanjil = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $this->guru->id,
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Budi Sanjaya',
            'nipd' => '1021999',
            'nisn' => '0099887766',
            'nik' => '3201010101010999',
            'jenis_kelamin' => 'L',
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'is_active' => true,
        ]);

        $this->periode9M1 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => 'GANJIL-PTS',
            'nama' => 'Rapor Penilaian Tengah Semester Ganjil',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => now()->toDateString(),
            'is_active' => false,
            'urutan' => 1,
        ]);

        $this->periode9M2 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => 'GANJIL-PAS',
            'nama' => 'Rapor Penilaian Akhir Semester Ganjil',
            'sebutan' => '9 Minggu ke-2',
            'tanggal_bagi' => now()->addMonths(3)->toDateString(),
            'is_active' => true,
            'urutan' => 2,
        ]);
    }

    public function test_calculate_rekap_generates_isolated_9m_data_and_descriptions(): void
    {
        $this->actingAs($this->guruUser);

        // Buat TP untuk 9M1 (AH 1 & AH 2)
        $tp1 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 1',
            'deskripsi' => 'memahami konsep aljabar linear',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);
        $tp2 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 2',
            'deskripsi' => 'menyelesaikan persamaan kuadrat',
            'alokasi_ah' => 'AH 2',
            'is_active' => true,
        ]);

        // Buat TP untuk 9M2 (AH 3 & AH 4)
        $tp3 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 3',
            'deskripsi' => 'menganalisis geometri ruang',
            'alokasi_ah' => 'AH 3',
            'is_active' => true,
        ]);
        $tp4 = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP 4',
            'deskripsi' => 'menghitung volume bangun ruang',
            'alokasi_ah' => 'AH 4',
            'is_active' => true,
        ]);

        // Nilai Asesmen Harian 9M1: AH 1 = 90, AH 2 = 60 (< KKM 75)
        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 90,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp1->id, 'nilai' => 90]);

        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 2',
            'nilai' => 60,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp2->id, 'nilai' => 60]);

        // Nilai Asesmen Harian 9M2: AH 3 = 88, AH 4 = 86 (keduanya >= KKM 75)
        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 3',
            'nilai' => 88,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp3->id, 'nilai' => 88]);

        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 4',
            'nilai' => 86,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp4->id, 'nilai' => 86]);

        // Sumatif: STS 1 = 80, SAS = 90
        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 80,
            'sas' => 90,
        ]);

        // Jalankan Livewire calculateRekap
        $comp = Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('activeTab', 'rekap')
            ->call('calculateRekap')
            ->assertHasNoErrors();

        // Verifikasi di Database
        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)
            ->where('penugasan_mengajar_id', $this->pmGanjil->id)
            ->firstOrFail();

        $data9m = $na->data_9m;
        $this->assertIsArray($data9m);
        $this->assertArrayHasKey('9M1', $data9m);
        $this->assertArrayHasKey('9M2', $data9m);

        // 1. Verifikasi Blok 9M1:
        // Rata formatif = (90 + 60) / 2 = 75
        // Sumatif STS 1 = 80
        // Karena AH 2 = 60 (< KKM 75), maka NA 9M1 harus NULL dan status belum_kompeten!
        $m1 = $data9m['9M1'];
        $this->assertEquals(75.0, (float) $m1['rata_formatif']);
        $this->assertEquals(80.0, (float) $m1['sumatif']);
        $this->assertEquals('belum_kompeten', $m1['status']);
        $this->assertNull($m1['nilai_akhir']);
        $this->assertEquals('Nilai belum kompeten, silakan hubungi guru yang bersangkutan.', $m1['deskripsi_tertinggi']);

        // 2. Verifikasi Blok 9M2:
        // Rata formatif = (88 + 86) / 2 = 87
        // Sumatif SAS = 90
        // Karena semua nilai >= KKM 75, NA 9M2 = (87 * 0.5) + (90 * 0.3) + (100 * 0.2) = 90.5
        $m2 = $data9m['9M2'];
        $this->assertEquals(87.0, (float) $m2['rata_formatif']);
        $this->assertEquals(90.0, (float) $m2['sumatif']);
        $this->assertEquals('kompeten', $m2['status']);
        $this->assertEquals(90.5, (float) $m2['nilai_akhir']);

        // Deskripsi 9M2: selalu 2 baris (TP 3 = 88 penguasaan tinggi, TP 4 = 86 pemantapan)
        $this->assertStringContainsString('menganalisis geometri ruang', $m2['deskripsi_tertinggi']);
        $this->assertStringContainsString('Perlu pemantapan', $m2['deskripsi_terendah']);
        $this->assertStringContainsString('menghitung volume bangun ruang', $m2['deskripsi_terendah']);
        $this->assertStringNotContainsString('menyelesaikan persamaan kuadrat', $m2['deskripsi_terendah']); // Memastikan TP 2 dari 9M1 tidak bocor ke 9M2!

        // Verifikasi Nilai Akhir Semester (karena ada AH 2 = 60 < KKM, NA semester harus null)
        $this->assertNull($na->nilai_akhir);
        $this->assertEquals('Nilai belum kompeten, silakan hubungi guru yang bersangkutan.', $na->deskripsi_tertinggi);
    }

    public function test_livewire_modal_edit_manual_override_for_specific_9m_period(): void
    {
        $this->actingAs($this->guruUser);

        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 85,
        ]);
        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 85,
        ]);

        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('activeTab', 'rekap')
            ->call('calculateRekap')
            ->call('openEditDeskripsi9M', $this->siswa->id, '9M1')
            ->assertSet('showModalDeskripsi9M', true)
            ->assertSet('selectedPeriodKey9M', '9M1')
            ->set('tempDeskripsiManual9M', 'Siswa sangat aktif dan unggul dalam diskusi aljabar linear.')
            ->call('saveDeskripsi9M')
            ->assertSet('showModalDeskripsi9M', false);

        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)
            ->where('penugasan_mengajar_id', $this->pmGanjil->id)
            ->firstOrFail();

        $descInfo = $na->getDescriptionForPeriod('9M1');
        $this->assertEquals('Siswa sangat aktif dan unggul dalam diskusi aljabar linear.', $descInfo['deskripsi_manual']);

        // Pastikan saat calculateRekap dipanggil lagi, manual override tetap terjaga
        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->call('calculateRekap');

        $na->refresh();
        $descInfo2 = $na->getDescriptionForPeriod('9M1');
        $this->assertEquals('Siswa sangat aktif dan unggul dalam diskusi aljabar linear.', $descInfo2['deskripsi_manual']);
    }

    public function test_rapor_pdf_displays_9m1_and_9m2_dynamically(): void
    {
        $this->actingAs($this->guruUser);

        // Buat record nilai akhir dengan data 9M
        NilaiAkhir::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'nilai_akhir' => 85,
            'data_9m' => [
                '9M1' => [
                    'rata_formatif' => 75,
                    'sumatif' => 80,
                    'nilai_akhir' => 77.5,
                    'deskripsi_tertinggi' => 'Penguasaan aljabar sangat baik.',
                    'deskripsi_terendah' => 'Perlu bimbingan persamaan kuadrat.',
                    'deskripsi_manual' => null,
                ],
                '9M2' => [
                    'rata_formatif' => 87,
                    'sumatif' => 90,
                    'nilai_akhir' => 88.5,
                    'deskripsi_tertinggi' => 'Penguasaan geometri ruang sangat unggul.',
                    'deskripsi_terendah' => null,
                    'deskripsi_manual' => null,
                ],
            ],
        ]);

        // 1. Cetak Rapor untuk periode 9M1 (Periode ID 6)
        $res9M1 = $this->get(route('rapor.cetak.nilai', [
            'siswa_id' => $this->siswa->id,
            'periode_id' => $this->periode9M1->id,
        ]));
        $res9M1->assertOk();

        // Model helper check
        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)->firstOrFail();
        $this->assertEquals(77.5, $na->getScoreForPeriod('9M1'));
        $this->assertEquals('Penguasaan aljabar sangat baik.', $na->getDescriptionForPeriod('9M1')['deskripsi_tertinggi']);

        // Model helper check for 9M2
        $this->assertEquals(88.5, $na->getScoreForPeriod('9M2'));
        $this->assertEquals('Penguasaan geometri ruang sangat unggul.', $na->getDescriptionForPeriod('9M2')['deskripsi_tertinggi']);
    }

    public function test_two_line_narration_with_single_tp(): void
    {
        $this->actingAs($this->guruUser);

        // Hanya buat 1 TP untuk AH 1
        $tp = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP-TUNGGAL',
            'deskripsi' => 'menerapkan kalkulus diferensial dalam rekayasa',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 88,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp->id, 'nilai' => 88]);

        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 84,
        ]);

        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('activeTab', 'rekap')
            ->call('calculateRekap')
            ->assertHasNoErrors();

        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)->firstOrFail();
        $m1 = $na->data_9m['9M1'];

        // Tetap menghasilkan 2 baris meskipun hanya ada 1 TP
        $this->assertNotEmpty($m1['deskripsi_tertinggi']);
        $this->assertNotEmpty($m1['deskripsi_terendah']);
        $this->assertStringContainsString('Menunjukkan penguasaan yang sangat baik', $m1['deskripsi_tertinggi']);
        $this->assertStringContainsString('Perlu pemantapan lebih lanjut', $m1['deskripsi_terendah']);
        $this->assertStringContainsString('menerapkan kalkulus diferensial dalam rekayasa', $m1['deskripsi_terendah']);
    }

    public function test_modal_copy_auto_to_manual_works(): void
    {
        $this->actingAs($this->guruUser);

        $tp = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP-COPY',
            'deskripsi' => 'menganalisis data statistik',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 80,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp->id, 'nilai' => 80]);

        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 85,
        ]);

        $testComp = Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('activeTab', 'rekap')
            ->call('calculateRekap')
            ->call('openEditDeskripsi9M', $this->siswa->id, '9M1')
            ->assertSet('tempDeskripsiManual9M', '');

        $autoInfo = $testComp->get('autoDeskripsiInfo9M');
        $this->assertNotEmpty($autoInfo);

        $testComp->call('copyAutoToManual9M')
            ->assertSet('tempDeskripsiManual9M', fn($val) => !empty($val) && str_contains($val, 'menganalisis data statistik'));
    }

    public function test_empty_score_causes_null_na_and_belum_lengkap_description(): void
    {
        $this->actingAs($this->guruUser);

        $tp = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP-LENGKAP',
            'deskripsi' => 'menerapkan fungsi eksponensial',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        // AH 1 diisi 85, tetapi STS dikosongkan (belum diinput)
        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 85,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp->id, 'nilai' => 85]);

        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('activeTab', 'rekap')
            ->call('calculateRekap')
            ->assertHasNoErrors();

        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)->firstOrFail();
        $m1 = $na->data_9m['9M1'];

        $this->assertEquals('belum_lengkap', $m1['status']);
        $this->assertNull($m1['nilai_akhir']);
        $this->assertEquals('Nilai belum lengkap, silakan hubungi guru yang bersangkutan.', $m1['deskripsi_tertinggi']);
        $this->assertEmpty($m1['deskripsi_terendah']);
    }

    public function test_below_kkm_score_causes_null_na_and_belum_kompeten_description(): void
    {
        $this->actingAs($this->guruUser);

        $tp = TujuanPembelajaran::create([
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode' => 'TP-KKM',
            'deskripsi' => 'menyelesaikan barisan dan deret aritmetika',
            'alokasi_ah' => 'AH 1',
            'is_active' => true,
        ]);

        // AH 1 = 70 (< KKM 75), STS = 80 (>= 75)
        NilaiAsesmenHarian::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 70,
        ]);
        NilaiFormatif::create(['siswa_id' => $this->siswa->id, 'tujuan_pembelajaran_id' => $tp->id, 'nilai' => 70]);

        NilaiSumatif::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'sts' => 80,
        ]);

        Livewire::test(PenilaianInput::class, ['penugasan_id' => $this->pmGanjil->id])
            ->set('activeTab', 'rekap')
            ->call('calculateRekap')
            ->assertHasNoErrors();

        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)->firstOrFail();
        $m1 = $na->data_9m['9M1'];

        $this->assertEquals('belum_kompeten', $m1['status']);
        $this->assertNull($m1['nilai_akhir']);
        $this->assertEquals('Nilai belum kompeten, silakan hubungi guru yang bersangkutan.', $m1['deskripsi_tertinggi']);
        $this->assertEmpty($m1['deskripsi_terendah']);
    }

    public function test_null_score_rendered_as_dash_in_rapor_pdf(): void
    {
        $this->actingAs($this->guruUser);

        // Buat record nilai akhir dengan nilai_akhir NULL (karena belum lengkap/kompeten)
        NilaiAkhir::create([
            'siswa_id' => $this->siswa->id,
            'penugasan_mengajar_id' => $this->pmGanjil->id,
            'nilai_akhir' => null,
            'data_9m' => [
                '9M1' => [
                    'rata_formatif' => 70,
                    'sumatif' => null,
                    'nilai_akhir' => null,
                    'status' => 'belum_lengkap',
                    'deskripsi_tertinggi' => 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.',
                    'deskripsi_terendah' => '',
                    'deskripsi_manual' => null,
                ],
            ],
        ]);

        $res = $this->get(route('rapor.cetak.nilai', [
            'siswa_id' => $this->siswa->id,
            'periode_id' => $this->periode9M1->id,
        ]));
        $res->assertOk();

        $na = NilaiAkhir::where('siswa_id', $this->siswa->id)->firstOrFail();
        $this->assertNull($na->getScoreForPeriod('9M1'));
        $this->assertEquals('Nilai belum lengkap, silakan hubungi guru yang bersangkutan.', $na->getDescriptionForPeriod('9M1')['deskripsi_tertinggi']);
    }
}
