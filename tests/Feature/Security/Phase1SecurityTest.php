<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class Phase1SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guruPolos;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected Rombel $rombel;
    protected TahunAjaran $ta;
    protected Semester $sem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        // Buat permissions yang diperlukan
        Permission::findOrCreate('beasiswa_ota.lihat', 'web');
        Permission::findOrCreate('beasiswa_ota.kelola', 'web');
        Permission::findOrCreate('beasiswa_ota.setting', 'web');
        Permission::findOrCreate('seni_budaya.kelola', 'web');
        Permission::findOrCreate('ekskul.kelola', 'web');
        Permission::findOrCreate('ekskul.setting', 'web');
        Permission::findOrCreate('rapor.cetak', 'web');
        Permission::findOrCreate('absensi.rekap.cetak', 'web');

        $this->admin = User::factory()->create([
            'username' => 'superadmin_sec',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->guruPolos = User::factory()->create([
            'username' => 'guru_polos',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruPolos->assignRole('Guru');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->sem = Semester::where('is_active', true)->firstOrFail();
        $this->rombel = Rombel::firstOrFail();

        $this->siswa1 = Siswa::create([
            'nama' => 'Siswa Satu',
            'nisn' => '1111222233',
            'nipd' => '111122',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'is_active' => true,
            'status' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Siswa Dua',
            'nisn' => '4444555566',
            'nipd' => '444455',
            'jk' => 'P',
            'rombel_id' => $this->rombel->id,
            'is_active' => true,
            'status' => 'aktif',
        ]);
    }

    public function test_modul_beasiswa_ota_menolak_user_tanpa_hak_akses(): void
    {
        $this->actingAs($this->guruPolos);

        $this->get(route('beasiswa-ota.index'))->assertForbidden();
        $this->get(route('beasiswa-ota.setting'))->assertForbidden();
        $this->get(route('beasiswa-ota.donatur-kelompok'))->assertForbidden();
    }

    public function test_modul_seni_budaya_dan_ekskul_menolak_user_tanpa_hak_akses(): void
    {
        $this->actingAs($this->guruPolos);

        $this->get(route('seni-budaya.index'))->assertForbidden();
        $this->get(route('ekskul.index'))->assertForbidden();
        $this->get(route('ekskul.setting'))->assertForbidden();
    }

    public function test_cetak_rapor_individual_menolak_user_yang_bukan_walikelas_atau_pemilik(): void
    {
        $this->actingAs($this->guruPolos);

        // Guru polos bukan walikelas rombel ini
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa1->id]))->assertForbidden();
        $this->get(route('penilaian.transkrip.pdf', ['siswa_id' => $this->siswa1->id]))->assertForbidden();
    }

    public function test_walikelas_dapat_melihat_rapor_siswanya_sendiri(): void
    {
        $pegawai = Pegawai::create([
            'user_id' => $this->guruPolos->id,
            'nama_lengkap' => 'Guru Walikelas',
            'is_active' => true,
        ]);
        $this->rombel->update(['wali_kelas_id' => $pegawai->id]);

        $this->actingAs($this->guruPolos);

        $this->assertTrue($this->guruPolos->canViewStudent($this->siswa1));
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa1->id]))->assertOk();
    }

    public function test_siswa_dapat_melihat_rapor_sendiri_tetapi_ditolak_melihat_rapor_siswa_lain(): void
    {
        $userSiswa1 = User::factory()->create([
            'username' => '111122', // NIPD siswa 1
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $userSiswa1->assignRole('Siswa');

        $this->actingAs($userSiswa1);

        // Boleh lihat rapor dirinya sendiri
        $this->assertTrue($userSiswa1->canViewStudent($this->siswa1));
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa1->id]))->assertOk();

        // Ditolak melihat rapor siswa 2
        $this->assertFalse($userSiswa1->canViewStudent($this->siswa2));
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa2->id]))->assertForbidden();
    }

    public function test_f1_ai_assistant_hanya_dapat_diakses_staf_akademik(): void
    {
        $userSiswa = User::factory()->create(['username' => 'siswa_ai', 'is_active' => true]);
        $userSiswa->assignRole('Siswa');

        // Siswa tidak boleh mengakses endpoint AI Assistant
        $this->actingAs($userSiswa)
            ->get(route('ai-assistant.chat.view'))
            ->assertForbidden();

        $this->actingAs($userSiswa)
            ->postJson(route('ai-assistant.chat.api'), ['message' => 'Halo'])
            ->assertForbidden();

        // Admin boleh mengakses
        $this->actingAs($this->admin)
            ->get(route('ai-assistant.chat.view'))
            ->assertOk();
    }

    public function test_f5_qrcode_service_menghasilkan_svg_offline(): void
    {
        $svg = \App\Modules\Sarpras\Services\QrCodeService::generateSvg('SRG-TEST-12345', 150);

        $this->assertNotEmpty($svg);
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('xmlns="http://www.w3.org/2000/svg"', $svg);
        $this->assertStringNotContainsString('api.qrserver.com', $svg);
    }

    public function test_f7_siswa_model_otomatis_sinkron_is_active_dengan_status(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Status Test',
            'nisn' => '9998887771',
            'nipd' => '999881',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'status' => 'lulus',
        ]);

        $this->assertFalse($siswa->is_active);

        $siswa->update(['status' => 'aktif']);
        $this->assertTrue($siswa->fresh()->is_active);

        $siswa->update(['status' => 'mutasi_keluar']);
        $this->assertFalse($siswa->fresh()->is_active);
    }

    public function test_f6_rekap_absensi_service_menghitung_libur_kalender_pendidikan(): void
    {
        $rekapService = new \App\Modules\Absensi\Services\RekapAbsensiService();
        $startDate = '2026-10-01'; // Kamis
        $endDate = '2026-10-07';   // Rabu (total hari: 7, weekdays: 5)

        $initialEfektif = $rekapService->hitungHariEfektif($startDate, $endDate);

        // Tambah hari libur kalender pada hari kerja (Jumat 2026-10-02)
        \App\Modules\KalenderPendidikan\Models\KalenderLibur::create([
            'nama' => 'Libur Uji Coba',
            'tgl_mulai' => '2026-10-02',
            'tgl_selesai' => '2026-10-02',
            'jenis' => \App\Modules\KalenderPendidikan\Models\KalenderLibur::JENIS_SEKOLAH,
            'keterangan' => 'Libur Uji Coba',
            'is_kbm_libur' => true,
        ]);

        $afterLiburEfektif = $rekapService->hitungHariEfektif($startDate, $endDate);
        $this->assertEquals($initialEfektif - 1, $afterLiburEfektif);
    }

    public function test_f3_unique_constraint_mencegah_duplikasi_nilai_akhir(): void
    {
        $pegawai = Pegawai::create([
            'user_id' => $this->admin->id,
            'nama_lengkap' => 'Guru Penilai',
            'is_active' => true,
        ]);

        $mapel = \App\Modules\Akademik\Models\Mapel::create([
            'kode' => 'MP01',
            'nama' => 'Mata Pelajaran 01',
            'kelompok' => \App\Modules\Akademik\Models\Mapel::KELOMPOK_UMUM,
        ]);

        $penugasan = \App\Modules\Kepegawaian\Models\PenugasanMengajar::create([
            'semester_id' => $this->sem->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $mapel->id,
            'pegawai_id' => $pegawai->id,
        ]);

        \App\Modules\Penilaian\Models\NilaiAkhir::create([
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $penugasan->id,
            'nilai_akhir' => 85,
        ]);

        // Percobaan duplikasi record dengan kombinasi siswa_id dan penugasan_mengajar_id yang sama
        $this->expectException(\Illuminate\Database\QueryException::class);
        \App\Modules\Penilaian\Models\NilaiAkhir::create([
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $penugasan->id,
            'nilai_akhir' => 90,
        ]);
    }
}
