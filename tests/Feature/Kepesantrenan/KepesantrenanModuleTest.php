<?php

namespace Tests\Feature\Kepesantrenan;

use App\Core\Support\PermissionSync;
use App\Models\User;
use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepesantrenan\Database\Seeders\QuranSurahSeeder;
use App\Modules\Kepesantrenan\Livewire\HafalanInput;
use App\Modules\Kepesantrenan\Livewire\HalaqohIndex;
use App\Modules\Kepesantrenan\Livewire\KamarIndex;
use App\Modules\Kepesantrenan\Livewire\RekapHafalanIndex;
use App\Modules\Kepesantrenan\Models\PesantrenHafalanSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqoh;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohPresensi;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohPresensiDetail;
use App\Modules\Kepesantrenan\Models\PesantrenKamar;
use App\Modules\Kepesantrenan\Services\HafalanRankService;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\PortalSiswa\Livewire\KepesantrenanSiswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KepesantrenanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $studentUser;

    protected Siswa $siswaUnggulan;

    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(QuranSurahSeeder::class);
        PermissionSync::run();

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
        ]);

        $programUnggulan = Program::firstOrCreate(
            ['kode' => 'UNG'],
            ['nama' => 'Unggulan', 'deskripsi' => 'Program Unggulan', 'is_active' => true]
        );

        $this->adminUser = User::factory()->create([
            'username' => 'admin_test',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole(User::SUPER_ADMIN_ROLE);

        $this->studentUser = User::factory()->create([
            'username' => 'santri_test',
            'is_active' => true,
        ]);
        $this->studentUser->assignRole('Siswa');

        $this->siswaUnggulan = Siswa::create([
            'user_id' => $this->studentUser->id,
            'nama' => 'Ahmad Santri Unggulan',
            'nipd' => '12345678',
            'nisn' => '9988776655',
            'jk' => 'L',
            'program_id' => $programUnggulan->id,
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    public function test_rank_calculation_mlbb_tiers(): void
    {
        $this->assertEquals('Warrior', HafalanRankService::getRankFromJuz(0.2)['tier']);
        $this->assertEquals('Elite', HafalanRankService::getRankFromJuz(0.7)['tier']);
        $this->assertEquals('Master', HafalanRankService::getRankFromJuz(1.2)['tier']);
        $this->assertEquals('Grandmaster', HafalanRankService::getRankFromJuz(1.7)['tier']);
        $this->assertEquals('Epic', HafalanRankService::getRankFromJuz(2.2)['tier']);
        $this->assertEquals('Legend', HafalanRankService::getRankFromJuz(2.7)['tier']);
        $this->assertEquals('Mythic', HafalanRankService::getRankFromJuz(3.2)['tier']);
        $this->assertEquals('Mythical Honor', HafalanRankService::getRankFromJuz(4.5)['tier']);
        $this->assertEquals('Mythical Glory', HafalanRankService::getRankFromJuz(5.5)['tier']);
        $this->assertEquals('Mythical Immortal', HafalanRankService::getRankFromJuz(7.5)['tier']);
    }

    public function test_kamar_crud_and_mudabbir_plotting(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(KamarIndex::class)
            ->set('nama_kamar', 'Kamar Al-Fatih 01')
            ->set('gedung', 'Gedung Asrama Putra Lt 1')
            ->set('jenis_kelamin', 'L')
            ->set('kapasitas', 8)
            ->call('saveKamar');

        $this->assertDatabaseHas('pesantren_kamars', [
            'nama_kamar' => 'Kamar Al-Fatih 01',
            'jenis_kelamin' => 'L',
        ]);

        $kamar = PesantrenKamar::first();

        Livewire::test(KamarIndex::class)
            ->call('openPlotting', $kamar->id)
            ->set('selectedSiswaId', $this->siswaUnggulan->id)
            ->set('selectedPeran', 'mudabbir')
            ->call('addSiswaToKamar');

        $this->assertDatabaseHas('pesantren_kamar_siswas', [
            'kamar_id' => $kamar->id,
            'siswa_id' => $this->siswaUnggulan->id,
            'peran' => 'mudabbir',
        ]);

        $this->assertEquals(1, $kamar->mudabbirs()->count());

        // Pastikan pivot->id terpanggil dengan baik melalui withPivot('id')
        $kamar->load(['siswas.rombel']);
        $this->assertNotNull($kamar->siswas->first()->pivot->id);
        $pivotId = $kamar->siswas->first()->pivot->id;

        // Uji togglePeran tanpa argumen tidak melempar BindingResolutionException
        Livewire::test(KamarIndex::class)
            ->call('openPlotting', $kamar->id)
            ->call('togglePeran')
            ->assertOk();

        // Uji togglePeran dengan pivot id
        Livewire::test(KamarIndex::class)
            ->call('openPlotting', $kamar->id)
            ->call('togglePeran', $pivotId)
            ->assertOk();

        $this->assertDatabaseHas('pesantren_kamar_siswas', [
            'id' => $pivotId,
            'peran' => 'anggota',
        ]);

        // Uji togglePeran dengan fallback siswa_id
        Livewire::test(KamarIndex::class)
            ->call('openPlotting', $kamar->id)
            ->call('togglePeran', null, $this->siswaUnggulan->id)
            ->assertOk();

        $this->assertDatabaseHas('pesantren_kamar_siswas', [
            'id' => $pivotId,
            'peran' => 'mudabbir',
        ]);

        // Uji removeSiswaFromKamar tanpa argumen aman
        Livewire::test(KamarIndex::class)
            ->call('openPlotting', $kamar->id)
            ->call('removeSiswaFromKamar')
            ->assertOk();

        // Uji removeSiswaFromKamar
        Livewire::test(KamarIndex::class)
            ->call('openPlotting', $kamar->id)
            ->call('removeSiswaFromKamar', $pivotId)
            ->assertOk();

        $this->assertDatabaseMissing('pesantren_kamar_siswas', [
            'id' => $pivotId,
        ]);
    }

    public function test_hafalan_input_and_setoran_audit(): void
    {
        $this->actingAs($this->adminUser);

        // Pilih santri unggulan dan tandai Al-Fatihah ayat 1 s/d 7
        Livewire::test(HafalanInput::class)
            ->call('selectSiswa', $this->siswaUnggulan->id)
            ->set('rangeSurahId', 1)
            ->set('rangeStart', 1)
            ->set('rangeEnd', 7)
            ->call('applyRange')
            ->set('setoranTanggal', now()->toDateString())
            ->set('setoranJenis', 'ziyadah')
            ->set('setoranPredikat', 'mumtaz')
            ->set('setoranCatatan', 'Tajwid dan makhraj sangat baik')
            ->call('saveProgress');

        // Pastikan 7 ayat tersimpan di database
        $this->assertEquals(7, PesantrenHafalanSiswa::where('siswa_id', $this->siswaUnggulan->id)->count());

        // Pastikan 1 log setoran tercatat
        $this->assertDatabaseHas('pesantren_setoran_hafalans', [
            'siswa_id' => $this->siswaUnggulan->id,
            'surah_id' => 1,
            'total_ayat' => 7,
            'predikat' => 'mumtaz',
        ]);

        // Pastikan predikat tersimpan pada ayat
        $this->assertDatabaseHas('pesantren_hafalan_siswas', [
            'siswa_id' => $this->siswaUnggulan->id,
            'surah_id' => 1,
            'ayat_number' => 1,
            'predikat' => 'mumtaz',
        ]);
    }

    public function test_accidental_uncheck_protection(): void
    {
        $this->actingAs($this->adminUser);

        // Buat 1 ayat yang sudah dihafal di database
        PesantrenHafalanSiswa::create([
            'siswa_id' => $this->siswaUnggulan->id,
            'surah_id' => 1,
            'ayat_number' => 1,
            'tanggal_hafal' => now()->toDateString(),
            'status' => 'lancar',
        ]);

        $test = Livewire::test(HafalanInput::class)
            ->call('selectSiswa', $this->siswaUnggulan->id);

        // 1. Saat mode koreksi NONAKTIF, klik ayat hijau TIDAK boleh menghapus
        $test->set('modeKoreksi', false)
            ->call('toggleAyat', 1, 1);

        $this->assertDatabaseHas('pesantren_hafalan_siswas', [
            'siswa_id' => $this->siswaUnggulan->id,
            'surah_id' => 1,
            'ayat_number' => 1,
        ]);

        // 2. Saat mode koreksi AKTIF, klik ayat hijau baru menghapus
        $test->set('modeKoreksi', true)
            ->call('toggleAyat', 1, 1);

        $this->assertDatabaseMissing('pesantren_hafalan_siswas', [
            'siswa_id' => $this->siswaUnggulan->id,
            'surah_id' => 1,
            'ayat_number' => 1,
        ]);
    }

    public function test_autocomplete_student_search(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(HafalanInput::class)
            // Default awal santri kosong
            ->assertSet('selectedSiswaId', null)
            ->assertSee('Belum Ada Santri yang Dipilih')
            // Ketik nama santri
            ->set('searchSiswa', 'Ahmad')
            ->assertSee('Ahmad Santri Unggulan')
            // Pilih santri dari dropdown
            ->call('selectSiswa', $this->siswaUnggulan->id)
            ->assertSet('selectedSiswaId', $this->siswaUnggulan->id)
            ->assertDontSee('Belum Ada Santri yang Dipilih')
            ->assertSee('grid-cols-5')
            ->assertSee('md:grid-cols-20')
            // Ganti / bersihkan santri
            ->call('clearSiswaSelection')
            ->assertSet('selectedSiswaId', null)
            ->assertSee('Belum Ada Santri yang Dipilih');
    }

    public function test_halaqoh_and_presensi(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(HalaqohIndex::class)
            ->set('nama_halaqoh', 'Halaqoh A (Imam Bukhari)')
            ->set('jenis_kelamin', 'L')
            ->call('saveHalaqoh');

        $halaqoh = PesantrenHalaqoh::first();
        $this->assertNotNull($halaqoh);

        // Masukkan santri ke halaqoh
        Livewire::test(HalaqohIndex::class)
            ->call('openPlotting', $halaqoh->id)
            ->set('selectedSiswaId', $this->siswaUnggulan->id)
            ->call('addSiswaToHalaqoh');

        $this->assertDatabaseHas('pesantren_halaqoh_siswas', [
            'halaqoh_id' => $halaqoh->id,
            'siswa_id' => $this->siswaUnggulan->id,
        ]);

        // Simpan presensi
        Livewire::test(HalaqohIndex::class)
            ->set('selectedHalaqohId', $halaqoh->id)
            ->set('presensiTanggal', now()->toDateString())
            ->set('presensiWaktuSesi', 'subuh')
            ->set('attendanceMap.'.$this->siswaUnggulan->id, 'H')
            ->call('savePresensi');

        $this->assertDatabaseHas('pesantren_halaqoh_presensi_details', [
            'siswa_id' => $this->siswaUnggulan->id,
            'status' => 'H',
        ]);
    }

    public function test_rekap_and_leaderboard_rendering(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(RekapHafalanIndex::class)
            ->assertSee('Leaderboard Hafalan')
            ->assertSee('Rekap Keseluruhan')
            ->assertSee('Laporan Hafalan Per Siswa');
    }

    public function test_portal_siswa_view_for_santri_unggulan(): void
    {
        $this->actingAs($this->studentUser);

        Livewire::test(KepesantrenanSiswa::class)
            ->assertSee('Ahmad Santri Unggulan')
            ->assertSee('PROGRAM UNGGULAN KEPESANTRENAN')
            ->assertSee('Lembar Kontrol Hafalan Saya');
    }

    public function test_rekap_presensi_halaqoh_per_siswa_kamar_rayon(): void
    {
        $this->actingAs($this->adminUser);

        // Buat halaqoh & presensi
        $halaqoh = PesantrenHalaqoh::create([
            'nama_halaqoh' => 'Halaqoh Rekap Test',
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_kelamin' => 'L',
        ]);

        $presensi = PesantrenHalaqohPresensi::create([
            'halaqoh_id' => $halaqoh->id,
            'tanggal' => now()->toDateString(),
            'waktu_sesi' => 'Subuh',
            'created_by' => $this->adminUser->id,
        ]);

        PesantrenHalaqohPresensiDetail::create([
            'presensi_id' => $presensi->id,
            'siswa_id' => $this->siswaUnggulan->id,
            'status' => 'H',
            'catatan' => 'Hadir tepat waktu',
        ]);

        // Uji komponen HalaqohIndex pada tab rekap
        Livewire::test(HalaqohIndex::class)
            ->set('activeTab', 'rekap')
            ->assertSee('Filter Rekapitulasi Presensi Halaqoh')
            ->assertSee('1. Rekap Per Santri')
            ->assertSee('2. Rekap Per Kamar')
            ->assertSee('3. Rekap Per Rayon')
            ->assertSee('Ahmad Santri Unggulan')
            // Uji filter preset tanggal
            ->call('setDatePreset', 'today')
            ->assertSet('rekapTanggalMulai', now()->toDateString())
            ->assertSet('rekapTanggalSelesai', now()->toDateString())
            // Uji sub-tab kamar
            ->set('rekapSubTab', 'kamar')
            ->assertSee('Tabel Rekapitulasi Presensi Halaqoh Per Kamar / Asrama')
            // Uji sub-tab rayon
            ->set('rekapSubTab', 'rayon')
            ->assertSee('Tabel Rekapitulasi Presensi Halaqoh Per Rayon')
            // Uji modal detail siswa
            ->call('openDetailSiswa', $this->siswaUnggulan->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('Riwayat Detail Presensi Santri')
            ->assertSee('Hadir tepat waktu')
            ->call('closeDetailSiswa')
            ->assertSet('showDetailModal', false);
    }
}
