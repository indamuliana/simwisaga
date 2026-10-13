<?php

namespace Tests\Feature\Ekstrakurikuler;

use App\Models\User;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerDashboard;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerAnggotaIndex;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerPresensi;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerPenilaian;
use App\Modules\Ekstrakurikuler\Livewire\EkstrakurikulerRekap;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensi;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensiDetail;
use App\Modules\Ekstrakurikuler\Services\EkstrakurikulerService;
use App\Modules\Ekstrakurikuler\Services\EkskulExcelService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EkstrakurikulerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instrukturUser;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Rombel $rombelX;
    protected Rombel $rombelXI;
    protected Siswa $siswaX1;
    protected Siswa $siswaX2;
    protected Siswa $siswaXI;
    protected Ekstrakurikuler $pramuka;
    protected Ekstrakurikuler $futsal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_ekskul_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->instrukturUser = User::factory()->create([
            'name' => 'Kak Pelatih Futsal',
            'username' => 'instruktur_futsal',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->instrukturUser->assignRole('Instruktur Ekskul');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->where('is_active', true)->first()
            ?? Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        // Rombels
        $pplg = \App\Modules\Akademik\Models\Jurusan::where('kode', 'PPLG')->firstOrFail();
        $this->rombelX = Rombel::where('tingkat', 'X')->first()
            ?? Rombel::create(['nama' => 'X PPLG 1', 'tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $pplg->id, 'tingkat' => 'X', 'nomor_urut' => 1]);

        $this->rombelXI = Rombel::where('tingkat', 'XI')->first()
            ?? Rombel::create(['nama' => 'XI PPLG 1', 'tahun_ajaran_id' => $this->ta->id, 'jurusan_id' => $pplg->id, 'tingkat' => 'XI', 'nomor_urut' => 1]);

        // Siswas
        $this->siswaX1 = Siswa::create([
            'nama' => 'Ahmad Siswa X-1',
            'nisn' => '0091112221',
            'nipd' => '1021001',
            'rombel_id' => $this->rombelX->id,
            'is_active' => true,
        ]);
        $this->siswaX2 = Siswa::create([
            'nama' => 'Budi Siswa X-2',
            'nisn' => '0091112222',
            'nipd' => '1021002',
            'rombel_id' => $this->rombelX->id,
            'is_active' => true,
        ]);
        $this->siswaXI = Siswa::create([
            'nama' => 'Citra Siswa XI',
            'nisn' => '0081112223',
            'nipd' => '1020003',
            'rombel_id' => $this->rombelXI->id,
            'is_active' => true,
        ]);

        // Ekskuls
        $this->pramuka = Ekstrakurikuler::create([
            'nama' => 'Pramuka (Gugus Depan)',
            'is_active' => true,
            'is_wajib' => true,
            'hari_latihan' => 'Jumat',
            'jam_mulai' => '15:30:00',
            'jam_selesai' => '17:00:00',
            'tempat' => 'Lapangan Utama',
            'bobot_presensi' => 30,
            'bobot_penilaian' => 70,
            'threshold_a' => 90,
            'threshold_b' => 80,
            'threshold_c' => 70,
        ]);

        $this->futsal = Ekstrakurikuler::create([
            'nama' => 'Futsal & Sepakbola',
            'instruktur_user_id' => $this->instrukturUser->id,
            'is_active' => true,
            'is_wajib' => false,
            'hari_latihan' => 'Sabtu',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'tempat' => 'Lapangan Futsal',
            'bobot_presensi' => 30,
            'bobot_penilaian' => 70,
            'threshold_a' => 90,
            'threshold_b' => 80,
            'threshold_c' => 70,
        ]);
    }

    public function test_guest_cannot_access_ekskul_pages(): void
    {
        $this->get(route('ekskul.index'))->assertRedirect('/login');
        $this->get(route('ekskul.anggota'))->assertRedirect('/login');
        $this->get(route('ekskul.setting'))->assertRedirect('/login');
        $this->get(route('ekskul.presensi', $this->futsal->id))->assertRedirect('/login');
        $this->get(route('ekskul.penilaian', $this->futsal->id))->assertRedirect('/login');
        $this->get(route('ekskul.rekap'))->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_all_ekskul_submodules(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('ekskul.index'))->assertOk();
        $this->get(route('ekskul.anggota'))->assertOk();
        $this->get(route('ekskul.setting'))->assertOk();
        $this->get(route('ekskul.presensi', $this->futsal->id))->assertOk();
        $this->get(route('ekskul.penilaian', $this->futsal->id))->assertOk();
        $this->get(route('ekskul.rekap'))->assertOk();
    }

    public function test_instructor_cannot_access_setting_submodule_but_can_access_presensi_and_penilaian(): void
    {
        $this->actingAs($this->instrukturUser);

        // Instruktur dilarang mengakses sub-modul setting (403 Forbidden)
        $this->get(route('ekskul.setting'))->assertForbidden();

        // Instruktur dapat mengakses kegiatan, presensi dan penilaian ekskul binaannya
        $this->get(route('ekskul.index'))->assertOk();
        $this->get(route('ekskul.presensi', $this->futsal->id))->assertOk();
        $this->get(route('ekskul.penilaian', $this->futsal->id))->assertOk();
    }

    public function test_auto_enroll_pramuka_kelas_x_enrolls_all_grade_x_students(): void
    {
        $service = app(EkstrakurikulerService::class);
        $result = $service->autoEnrollPramukaKelasX($this->semester->id);

        $this->assertTrue($result['success']);
        $this->assertGreaterThanOrEqual(2, $result['count']);

        // Siswa X1 dan X2 harus terdaftar di Pramuka
        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $this->pramuka->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);
        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $this->pramuka->id,
            'siswa_id' => $this->siswaX2->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Siswa XI tidak boleh terdaftar otomatis di Pramuka
        $this->assertDatabaseMissing('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $this->pramuka->id,
            'siswa_id' => $this->siswaXI->id,
        ]);
    }

    public function test_student_compliance_detects_missing_choice_ekskul(): void
    {
        $service = app(EkstrakurikulerService::class);

        // Awal: Siswa X1 auto-enrolled di Pramuka (Wajib), tapi belum memilih ekskul pilihan
        $service->autoEnrollPramukaKelasX($this->semester->id);

        $audit = $service->checkStudentCompliance($this->semester->id);

        // Siswa X1 harus masuk daftar non-compliant karena belum punya ekskul pilihan
        $nonCompliantIds = collect($audit['non_compliant_list'])->pluck('siswa.id')->all();
        $this->assertContains($this->siswaX1->id, $nonCompliantIds);

        // Sekarang daftarkan siswa X1 ke Futsal (Pilihan)
        EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $auditAfter = $service->checkStudentCompliance($this->semester->id);
        $compliantIds = collect($auditAfter['compliant_list'])->pluck('siswa.id')->all();
        $this->assertContains($this->siswaX1->id, $compliantIds);
    }

    public function test_weekly_attendance_session_calculates_percentage_accurately(): void
    {
        $service = app(EkstrakurikulerService::class);

        // Daftarkan siswa ke Futsal
        $anggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Buat 4 sesi mingguan: Siswa hadir 3 kali, tidak hadir 1 kali
        for ($i = 1; $i <= 4; $i++) {
            $sesi = EkskulPresensi::create([
                'ekstrakurikuler_id' => $this->futsal->id,
                'semester_id' => $this->semester->id,
                'pertemuan_ke' => $i,
                'tanggal' => now()->subWeeks(4 - $i)->toDateString(),
            ]);

            EkskulPresensiDetail::create([
                'ekskul_presensi_id' => $sesi->id,
                'siswa_id' => $this->siswaX1->id,
                'status' => ($i === 4) ? 'Tidak Hadir' : 'Hadir',
            ]);
        }

        // Recalculate
        $service->recalculateAttendance($this->futsal->id, $this->semester->id);

        $anggota->refresh();
        $this->assertEquals(4, $anggota->total_pertemuan);
        $this->assertEquals(3, $anggota->total_hadir);
        $this->assertEquals(75.00, $anggota->persentase_kehadiran);
        $this->assertEquals(75.00, $anggota->nilai_presensi);
    }

    public function test_final_grade_weighted_calculation_and_predikat_thresholds(): void
    {
        $service = app(EkstrakurikulerService::class);

        // Anggota dengan kehadiran 80%
        $anggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'total_pertemuan' => 10,
            'total_hadir' => 8,
            'persentase_kehadiran' => 80.00,
            'nilai_presensi' => 80.00,
            'status_keanggotaan' => 'aktif',
        ]);

        // Asesmen SAS = 95.00
        // Bobot: 30% Presensi (80 * 0.3 = 24) + 70% Asesmen (95 * 0.7 = 66.5) = 90.5
        // Ambang A >= 90 => Predikat 'Sangat Baik'
        $gradeA = $service->calculateFinalGrade($anggota, 'SAS', 95.00);
        $this->assertEquals(90.50, $gradeA['nilai_angka']);
        $this->assertEquals('Sangat Baik', $gradeA['nilai_huruf']);

        // Asesmen SAS = 80.00
        // (80 * 0.3 = 24) + (80 * 0.7 = 56) = 80.0
        // Ambang B >= 80 => Predikat 'Baik'
        $gradeB = $service->calculateFinalGrade($anggota, 'SAS', 80.00);
        $this->assertEquals(80.00, $gradeB['nilai_angka']);
        $this->assertEquals('Baik', $gradeB['nilai_huruf']);

        // Asesmen SAS = 65.00
        // (80 * 0.3 = 24) + (65 * 0.7 = 45.5) = 69.5
        // Ambang C >= 70, maka 69.5 < 70 => Predikat 'Kurang' (D)
        $gradeD = $service->calculateFinalGrade($anggota, 'SAS', 65.00);
        $this->assertEquals(69.50, $gradeD['nilai_angka']);
        $this->assertEquals('Kurang', $gradeD['nilai_huruf']);
    }

    public function test_livewire_penilaian_saves_and_syncs_to_rapor_columns(): void
    {
        $this->actingAs($this->admin);

        $anggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'nilai_presensi' => 100.00,
            'status_keanggotaan' => 'aktif',
        ]);

        Livewire::test(EkstrakurikulerPenilaian::class, ['ekskul_id' => $this->futsal->id])
            ->set('activePeriod', 'SAS')
            ->set('nilaiData.' . $this->siswaX1->id . '.nilai_asesmen', 88)
            ->call('saveNilai')
            ->assertHasNoErrors();

        $anggota->refresh();
        $this->assertEquals(88.00, $anggota->sas);
        // (100 * 0.3 = 30) + (88 * 0.7 = 61.6) = 91.6
        $this->assertEquals(91.60, $anggota->nilai_angka);
        $this->assertEquals('Sangat Baik', $anggota->nilai_huruf);
        $this->assertNotEmpty($anggota->deskripsi);
    }

    public function test_student_can_mutasi_to_another_choice_ekskul(): void
    {
        $this->actingAs($this->admin);

        // Anggota lama di Futsal
        $oldAnggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $basket = Ekstrakurikuler::create([
            'nama' => 'Basket Club',
            'is_active' => true,
            'is_wajib' => false,
        ]);

        // Uji drill down ke UPD dan mutasi siswa
        Livewire::test(EkstrakurikulerAnggotaIndex::class)
            ->assertSee('Futsal & Sepakbola')
            ->call('selectEkskul', $this->futsal->id)
            ->assertSet('selectedEkskulDetailId', $this->futsal->id)
            ->assertSee($this->siswaX1->nama)
            ->call('openTransferModal', $oldAnggota->id)
            ->set('targetEkskulId', $basket->id)
            ->set('transferCatatan', 'Ingin fokus ke basket')
            ->call('executeTransfer')
            ->assertHasNoErrors();

        $oldAnggota->refresh();
        $this->assertEquals('pindah', $oldAnggota->status_keanggotaan);

        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $basket->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_mass_transfer_students_to_another_upd(): void
    {
        $this->actingAs($this->admin);

        $anggota1 = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);
        $anggota2 = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX2->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $pmr = Ekstrakurikuler::create([
            'nama' => 'PMR (Palang Merah Remaja)',
            'is_active' => true,
            'is_wajib' => false,
        ]);

        Livewire::test(EkstrakurikulerAnggotaIndex::class)
            ->call('selectEkskul', $this->futsal->id)
            ->set('selectedAnggotaIdsForTransfer', [$anggota1->id, $anggota2->id])
            ->call('openMassTransferModal')
            ->assertSet('isMassTransfer', true)
            ->set('targetEkskulId', $pmr->id)
            ->set('transferCatatan', 'Mutasi massal ke PMR')
            ->call('executeTransfer')
            ->assertHasNoErrors();

        $anggota1->refresh();
        $anggota2->refresh();
        $this->assertEquals('pindah', $anggota1->status_keanggotaan);
        $this->assertEquals('pindah', $anggota2->status_keanggotaan);

        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $pmr->id,
            'siswa_id' => $this->siswaX1->id,
            'status_keanggotaan' => 'aktif',
        ]);
        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $pmr->id,
            'siswa_id' => $this->siswaX2->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_student_compliance_for_grade_xi_and_xii(): void
    {
        $service = app(EkstrakurikulerService::class);
        $pplg = \App\Modules\Akademik\Models\Jurusan::where('kode', 'PPLG')->firstOrFail();

        // Buat rombel XII dan siswanya
        $rombelXII = Rombel::create([
            'nama' => 'XII PPLG 1',
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $pplg->id,
            'tingkat' => 'XII',
            'nomor_urut' => 1,
        ]);
        $siswaXII = Siswa::create([
            'nama' => 'Dimas Siswa XII',
            'nisn' => '0071112224',
            'rombel_id' => $rombelXII->id,
            'is_active' => true,
        ]);

        // 1. Siswa XI belum punya UPD apapun -> non-compliant ("Belum Memilih UPD")
        $audit = $service->checkStudentCompliance($this->semester->id);
        $nonCompliantMap = collect($audit['non_compliant_list'])->keyBy('siswa.id');
        $this->assertArrayHasKey($this->siswaXI->id, $nonCompliantMap);
        $this->assertEquals('Belum Memilih UPD', $nonCompliantMap[$this->siswaXI->id]['status_text']);

        // 2. Siswa XII tanpa UPD -> TETAP COMPLIANT (Bebas Kewajiban UPD)
        $compliantMap = collect($audit['compliant_list'])->keyBy('siswa.id');
        $this->assertArrayHasKey($siswaXII->id, $compliantMap);
        $this->assertEquals('Bebas Kewajiban UPD (Tingkat XII)', $compliantMap[$siswaXII->id]['status_text']);
        $this->assertArrayNotHasKey($siswaXII->id, $nonCompliantMap);

        // 3. Siswa XI memilih 1 UPD (Futsal, tanpa Pramuka) -> LANGSUNG COMPLIANT!
        EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaXI->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $auditAfter = $service->checkStudentCompliance($this->semester->id);
        $compliantMapAfter = collect($auditAfter['compliant_list'])->keyBy('siswa.id');
        $this->assertArrayHasKey($this->siswaXI->id, $compliantMapAfter);
        $this->assertEquals('Lengkap (1 UPD)', $compliantMapAfter[$this->siswaXI->id]['status_text']);
    }

    public function test_livewire_targeted_enroll_student_from_compliance_audit(): void
    {
        $this->actingAs($this->admin);

        // Uji alur modal daftarkan untuk siswa X2 yang belum ada UPD
        Livewire::test(EkstrakurikulerAnggotaIndex::class)
            ->set('activeTab', 'kepatuhan')
            ->call('openEnrollSiswaModal', $this->siswaX2->id, true)
            ->assertSet('showEnrollModal', true)
            ->assertSet('enrollingSiswaId', $this->siswaX2->id)
            ->assertSet('enrollingMissingPramuka', true)
            // 1-Klik cepat daftar Pramuka
            ->call('quickEnrollPramuka')
            ->assertSet('showEnrollModal', false);

        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $this->pramuka->id,
            'siswa_id' => $this->siswaX2->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Uji alur pilih UPD lain untuk siswa XI
        Livewire::test(EkstrakurikulerAnggotaIndex::class)
            ->set('activeTab', 'kepatuhan')
            ->call('openEnrollSiswaModal', $this->siswaXI->id, false)
            ->assertSet('showEnrollModal', true)
            ->assertSet('enrollingMissingPramuka', false)
            ->set('enrollingTargetEkskulId', $this->futsal->id)
            ->call('saveEnrollSiswa')
            ->assertSet('showEnrollModal', false);

        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaXI->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_generate_template_for_choice_ekskul_and_reject_wajib(): void
    {
        $excelService = app(EkskulExcelService::class);

        // 1. Sukses generate untuk UPD Pilihan
        $spreadsheet = $excelService->generateTemplateForEkskul($this->futsal->id);
        $sheet = $spreadsheet->getActiveSheet();
        $this->assertEquals('NIS', $sheet->getCell('A4')->getValue());
        $this->assertEquals('Nama Siswa', $sheet->getCell('B4')->getValue());
        $this->assertStringContainsString('FUTSAL', (string) $sheet->getCell('A1')->getValue());

        // 2. Wajib ditolak jika ekskul adalah Pramuka (is_wajib = true)
        $this->expectException(\InvalidArgumentException::class);
        $excelService->generateTemplateForEkskul($this->pramuka->id);
    }

    public function test_preview_and_commit_import_per_ekskul_with_auto_mutation(): void
    {
        $excelService = app(EkskulExcelService::class);

        $pmr = Ekstrakurikuler::create([
            'nama' => 'PMR (Palang Merah Remaja)',
            'is_active' => true,
            'is_wajib' => false,
        ]);

        // Siswa X1 awalnya aktif di Futsal
        $futsalAnggota = EkstrakurikulerAnggota::create([
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'semester_id' => $this->semester->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Siswa X2 belum punya UPD pilihan apapun

        // Buat temporary Excel file untuk import ke PMR
        $tempSpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $tempSheet = $tempSpreadsheet->getActiveSheet();
        $tempSheet->setCellValue('A4', 'NIS');
        $tempSheet->setCellValue('B4', 'Nama Siswa');

        // Baris 5: Siswa X1 (NIS 1021001) - Harus terdeteksi MUTASI dari Futsal
        $tempSheet->setCellValue('A5', '1021001');
        $tempSheet->setCellValue('B5', 'Ahmad Siswa X-1');

        // Baris 6: Siswa X2 (NIS 1021002) - Harus terdeteksi SISWA BARU
        $tempSheet->setCellValue('A6', '1021002');
        $tempSheet->setCellValue('B6', 'Budi Siswa X-2');

        // Baris 7: NIS Palsu 9999999 - Harus terdeteksi ERROR
        $tempSheet->setCellValue('A7', '9999999');
        $tempSheet->setCellValue('B7', 'Siswa Palsu');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_import_') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($tempSpreadsheet);
        $writer->save($tempPath);

        // 1. Preview
        $preview = $excelService->previewImportPerEkskul($tempPath, $pmr->id, $this->semester->id);

        $this->assertEquals(3, $preview['total_rows']);
        $this->assertEquals(2, $preview['valid_count']);
        $this->assertEquals(1, $preview['new_count']);
        $this->assertEquals(1, $preview['transfer_count']);
        $this->assertEquals(1, $preview['error_count']);

        $rowsByNis = collect($preview['rows'])->keyBy('nis');
        $this->assertEquals('transfer', $rowsByNis['1021001']['status']);
        $this->assertStringContainsString('Mutasi dari Futsal', $rowsByNis['1021001']['label']);
        $this->assertEquals('new', $rowsByNis['1021002']['status']);
        $this->assertEquals('error', $rowsByNis['9999999']['status']);

        // 2. Commit
        $commitResult = $excelService->commitImportPerEkskul($preview['rows'], $pmr->id, $this->semester->id);
        $this->assertTrue($commitResult['success']);
        $this->assertEquals(2, $commitResult['total_processed']);
        $this->assertEquals(1, $commitResult['new_count']);
        $this->assertEquals(1, $commitResult['transfer_count']);

        // Verifikasi di Database:
        // Siswa X1 harus aktif di PMR
        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $pmr->id,
            'siswa_id' => $this->siswaX1->id,
            'status_keanggotaan' => 'aktif',
        ]);
        // Keanggotaan lama Siswa X1 di Futsal harus berstatus 'pindah'
        $futsalAnggota->refresh();
        $this->assertEquals('pindah', $futsalAnggota->status_keanggotaan);

        // Siswa X2 harus aktif di PMR
        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $pmr->id,
            'siswa_id' => $this->siswaX2->id,
            'status_keanggotaan' => 'aktif',
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_livewire_import_tab_preview_and_commit(): void
    {
        $this->actingAs($this->admin);

        // Buat file Excel test
        $tempSpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $tempSheet = $tempSpreadsheet->getActiveSheet();
        $tempSheet->setCellValue('A4', 'NIS');
        $tempSheet->setCellValue('B4', 'Nama Siswa');
        $tempSheet->setCellValue('A5', '1021001');
        $tempSheet->setCellValue('B5', 'Ahmad Siswa X-1');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_lw_import_') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($tempSpreadsheet);
        $writer->save($tempPath);

        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'test_import.xlsx',
            file_get_contents($tempPath)
        );

        $test = Livewire::test(EkstrakurikulerAnggotaIndex::class)
            ->set('activeTab', 'import')
            ->set('selectedImportEkskulId', $this->futsal->id)
            ->set('importFile', $uploadedFile)
            ->call('processUploadPreview')
            ->assertHasNoErrors()
            ->call('commitImport')
            ->assertSet('importPreviewData', null);

        $this->assertDatabaseHas('ekstrakurikuler_anggotas', [
            'ekstrakurikuler_id' => $this->futsal->id,
            'siswa_id' => $this->siswaX1->id,
            'status_keanggotaan' => 'aktif',
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }
}
