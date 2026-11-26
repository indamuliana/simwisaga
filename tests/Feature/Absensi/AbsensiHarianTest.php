<?php

namespace Tests\Feature\Absensi;

use App\Core\Support\PermissionSync;
use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Absensi\Livewire\AbsensiHarianIndex;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use App\Modules\Kesiswaan\Livewire\SiswaDetail;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\PortalSiswa\Livewire\BerandaSiswa;
use App\Modules\PortalSiswa\Livewire\KehadiranSiswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AbsensiHarianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected Rombel $rombelA;
    protected Rombel $rombelB;
    protected Rayon $rayonA;
    protected Rayon $rayonB;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected Siswa $siswa3;

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

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create(['kode' => 'PPLG', 'nama' => 'PPLG']);

        $this->rombelA = Rombel::create([
            'nama' => 'X PPLG 1',
            'tingkat' => 'X',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $this->rombelB = Rombel::create([
            'nama' => 'XI PPLG 1',
            'tingkat' => 'XI',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $this->rayonA = Rayon::create(['nama' => 'Rayon Garut Utara']);
        $this->rayonB = Rayon::create(['nama' => 'Rayon Garut Selatan']);

        // Urutan NIS: Siswa 2 (1001), Siswa 1 (1002), Siswa 3 (1003)
        $this->siswa1 = Siswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '1002',
            'nisn' => '001002',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'rayon_id' => $this->rayonA->id,
            'tanggal_lahir' => '2006-01-01',
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Andi Wijaya',
            'nipd' => '1001',
            'nisn' => '001001',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => $this->rombelB->id,
            'rayon_id' => $this->rayonB->id,
            'tanggal_lahir' => '2006-01-01',
        ]);

        $this->siswa3 = Siswa::create([
            'nama' => 'Citra Lestari',
            'nipd' => '1003',
            'nisn' => '001003',
            'jk' => 'P',
            'status' => 'aktif',
            'rombel_id' => $this->rombelA->id,
            'rayon_id' => $this->rayonA->id,
            'tanggal_lahir' => '2006-01-01',
        ]);
    }

    public function test_default_halaman_langsung_memuat_seluruh_siswa_urut_nipd_dengan_kolom_ringkas(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->assertSet('rombel_id', '')
            ->assertSee('Semua Rombel (Seluruh Siswa)')
            ->assertSee('X PPLG 1')
            ->assertSee('XI PPLG 1')
            ->assertSee('Rayon Garut Utara')
            ->assertSeeHtmlInOrder(['No', 'NIS', 'Nama', 'Rombel', 'Rayon', 'JK', 'H', 'S', 'I', 'A', 'CBT PLH', 'CBT KWH', 'Dispen', 'Keterangan'])
            ->assertSeeHtml("name=\"status_{$this->siswa1->id}\"")
            ->assertSeeHtmlInOrder(['Andi Wijaya', 'Budi Santoso', 'Citra Lestari']);
    }

    public function test_label_enum_status_absensi_sesuai_singkatan_global(): void
    {
        $this->assertSame('H', StatusAbsensi::HADIR->label());
        $this->assertSame('S', StatusAbsensi::SAKIT->label());
        $this->assertSame('I', StatusAbsensi::IZIN->label());
        $this->assertSame('A', StatusAbsensi::ALPA->label());
        $this->assertSame('CBT PLH', StatusAbsensi::DISPEN_CBT_PLH->label());
        $this->assertSame('CBT KWH', StatusAbsensi::DISPEN_CBT_KWH->label());
        $this->assertSame('Dispen', StatusAbsensi::DISPEN_LAINNYA->label());
    }

    public function test_pencarian_cepat_menyaring_siswa(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set('search', 'Citra')
            ->assertSee('Citra Lestari')
            ->assertDontSee('Andi Wijaya')
            ->assertDontSee('Budi Santoso')
            ->set('search', '1001') // NIPD Andi
            ->assertSee('Andi Wijaya')
            ->assertDontSee('Citra Lestari');
    }

    public function test_tandai_semua_hadir_tidak_menimpa_status_yang_sudah_diisi(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set("absensiData.{$this->siswa2->id}.status", StatusAbsensi::SAKIT->value)
            ->call('tandaiSemuaHadir')
            ->assertSet("absensiData.{$this->siswa2->id}.status", StatusAbsensi::SAKIT->value)
            ->assertSet("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->assertSet("absensiData.{$this->siswa3->id}.status", StatusAbsensi::HADIR->value);
    }

    public function test_dapat_menyimpan_absensi_seluruh_siswa_tanpa_memilih_rombel(): void
    {
        $today = now()->toDateString();

        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set('rombel_id', '')
            ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->set("absensiData.{$this->siswa2->id}.status", StatusAbsensi::SAKIT->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Absensi harian berhasil disimpan.');

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'status' => 'Hadir',
        ]);

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombelB->id,
            'status' => 'Sakit',
        ]);

        $record1 = AbsensiHarian::where('siswa_id', $this->siswa1->id)->first();
        $this->assertNotNull($record1);
        $this->assertEquals($today, $record1->tanggal->format('Y-m-d'));
    }

    public function test_tanggal_lampau_terkunci_read_only_sampai_tombol_edit_absen_diklik(): void
    {
        $yesterday = now()->subDay()->toDateString();

        AbsensiHarian::create([
            'tanggal' => $yesterday,
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::SAKIT->value,
            'keterangan' => 'Demam',
            'diinput_oleh' => $this->admin->id,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set('tanggal', $yesterday)
            ->assertSet('isEditingPastDate', false)
            ->assertSee('Mode Baca Saja (Tanggal Lampau)')
            ->assertSee('Edit Absen')
            ->assertDontSee('Simpan Absensi');

        // Mencoba save langsung saat masih read-only ditolak 403
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set('tanggal', $yesterday)
            ->call('save')
            ->assertForbidden();

        // Klik tombol Edit Absen -> terbuka, bisa ubah & simpan, lalu otomatis terkunci kembali
        $component
            ->call('mulaiEditAbsen')
            ->assertSet('isEditingPastDate', true)
            ->assertSee('Batal Edit')
            ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::IZIN->value)
            ->set("absensiData.{$this->siswa1->id}.keterangan", 'Acara keluarga')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isEditingPastDate', false);

        $this->assertDatabaseHas('absensi_harians', [
            'tanggal' => $yesterday,
            'siswa_id' => $this->siswa1->id,
            'status' => StatusAbsensi::IZIN->value,
            'keterangan' => 'Acara keluarga',
        ]);
    }

    public function test_route_menolak_user_tanpa_permission_lihat(): void
    {
        $siswaUser = User::factory()->create([
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $siswaUser->assignRole('Siswa');

        $this->actingAs($siswaUser)
            ->get(route('absensi.harian'))
            ->assertForbidden();
    }

    public function test_wali_kelas_hanya_melihat_dan_mengubah_siswa_binaannya(): void
    {
        $waliUser = User::factory()->create([
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $waliUser->assignRole('Guru');
        $waliUser->givePermissionTo([
            Permission::findOrCreate('absensi.harian.lihat', 'web'),
            Permission::findOrCreate('absensi.harian.input', 'web'),
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $waliUser->id,
            'nama_lengkap' => 'Ibu Wali X PPLG 1',
            'is_active' => true,
        ]);

        PenugasanWaliKelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'rombel_id' => $this->rombelA->id,
            'pegawai_id' => $pegawai->id,
        ]);

        // Siswa 1 & 3 ada di rombelA, Siswa 2 ada di rombelB (di luar binaan)
        $component = Livewire::actingAs($waliUser)
            ->test(AbsensiHarianIndex::class)
            ->assertSee('Statistik Kehadiran Siswa Binaan')
            ->assertSee('Budi Santoso')
            ->assertSee('Citra Lestari')
            ->assertDontSee('Andi Wijaya');

        // Menyimpan siswa binaan berhasil
        $component
            ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);

        // Mencoba menyuntikkan perubahan untuk siswa2 (di luar binaan) ditolak 403
        $component
            ->set("absensiData.{$this->siswa2->id}.status", StatusAbsensi::ALPA->value)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseMissing('absensi_harians', [
            'siswa_id' => $this->siswa2->id,
        ]);
    }

    public function test_pembimbing_rayon_hanya_melihat_siswa_rayon_binaannya(): void
    {
        $pembimbingUser = User::factory()->create([
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $pembimbingUser->assignRole('Guru');
        $pembimbingUser->givePermissionTo([
            Permission::findOrCreate('absensi.harian.lihat', 'web'),
            Permission::findOrCreate('absensi.harian.input', 'web'),
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $pembimbingUser->id,
            'nama_lengkap' => 'Pak Pembimbing Rayon Selatan',
            'is_active' => true,
        ]);

        PenugasanPembimbingRayon::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'rayon_id' => $this->rayonB->id,
            'pegawai_id' => $pegawai->id,
        ]);

        Livewire::actingAs($pembimbingUser)
            ->test(AbsensiHarianIndex::class)
            ->assertSee('Andi Wijaya')
            ->assertDontSee('Budi Santoso')
            ->assertDontSee('Citra Lestari');
    }

    public function test_simpan_bersamaan_tidak_menimpa_perubahan_guru_lain(): void
    {
        $today = now()->toDateString();

        // Guru A membuka halaman terlebih dahulu (snapshot awal siswa2 masih kosong)
        $sessionGuruA = Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class);

        // Sementara itu Guru B menyimpan status siswa2 = Alpa langsung ke DB
        AbsensiHarian::create([
            'tanggal' => $today,
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombelB->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::ALPA->value,
            'keterangan' => 'Tanpa kabar',
            'diinput_oleh' => $this->admin->id,
        ]);

        // Guru A hanya mengubah siswa1 lalu klik Simpan
        $sessionGuruA
            ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->call('save')
            ->assertHasNoErrors();

        // Siswa1 tersimpan Hadir, dan Siswa2 TETAP Alpa (tidak tertimpa/terhapus)
        $this->assertDatabaseHas('absensi_harians', [
            'tanggal' => $today,
            'siswa_id' => $this->siswa1->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);
        $this->assertDatabaseHas('absensi_harians', [
            'tanggal' => $today,
            'siswa_id' => $this->siswa2->id,
            'status' => StatusAbsensi::ALPA->value,
            'keterangan' => 'Tanpa kabar',
        ]);
    }

    public function test_reset_status_menghapus_record_siswa_saat_disimpan(): void
    {
        $today = now()->toDateString();

        AbsensiHarian::create([
            'tanggal' => $today,
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::SAKIT->value,
            'keterangan' => 'Salah klik',
            'diinput_oleh' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->call('resetStatus', $this->siswa1->id)
            ->assertSet("absensiData.{$this->siswa1->id}.status", null)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('absensi_harians', [
            'tanggal' => $today,
            'siswa_id' => $this->siswa1->id,
        ]);
    }

    public function test_validasi_menolak_status_tidak_valid_dan_melewati_siswa_tanpa_rombel(): void
    {
        $siswaTanpaRombel = Siswa::create([
            'nama' => 'Dedi Tanpa Rombel',
            'nipd' => '1004',
            'nisn' => '001004',
            'jk' => 'L',
            'status' => 'aktif',
            'rombel_id' => null,
            'tanggal_lahir' => '2006-01-01',
        ]);

        // Status tidak valid ditolak validasi
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set("absensiData.{$this->siswa1->id}.status", 'INVALID_STATUS')
            ->call('save')
            ->assertHasErrors(["absensiData.{$this->siswa1->id}.status"]);

        // Siswa tanpa rombel dilewati dengan pesan warning, sementara siswa valid tetap tersimpan
        Livewire::actingAs($this->admin)
            ->test(AbsensiHarianIndex::class)
            ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
            ->set("absensiData.{$siswaTanpaRombel->id}.status", StatusAbsensi::HADIR->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Dedi Tanpa Rombel');

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);
        $this->assertDatabaseMissing('absensi_harians', [
            'siswa_id' => $siswaTanpaRombel->id,
        ]);
    }

    public function test_simpan_menghormati_semester_arsip_di_academic_context(): void
    {
        $this->semester->update(['is_active' => false]);
        $semesterGenap = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'genap',
            'is_active' => true,
        ]);

        AcademicContext::setWorkingSemester($this->semester->id);

        try {
            Livewire::actingAs($this->admin)
                ->test(AbsensiHarianIndex::class)
                ->set("absensiData.{$this->siswa1->id}.status", StatusAbsensi::HADIR->value)
                ->call('save')
                ->assertHasNoErrors();
        } finally {
            AcademicContext::resetToActive();
        }

        $this->assertDatabaseHas('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $this->semester->id,
        ]);
        $this->assertDatabaseMissing('absensi_harians', [
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $semesterGenap->id,
        ]);
    }

    public function test_siswa_detail_dan_portal_siswa_membaca_status_enum_dengan_benar(): void
    {
        $studentUser = User::factory()->create([
            'username' => $this->siswa1->nipd,
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $studentUser->assignRole('Siswa');
        $studentUser->givePermissionTo([
            Permission::findOrCreate('portal_siswa.agenda.lihat', 'web'),
            Permission::findOrCreate('portal_siswa.kehadiran.lihat', 'web'),
        ]);
        $this->siswa1->update(['user_id' => $studentUser->id]);

        AbsensiHarian::create([
            'tanggal' => now()->subDays(3)->toDateString(),
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::HADIR->value,
        ]);
        AbsensiHarian::create([
            'tanggal' => now()->subDays(2)->toDateString(),
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::DISPEN_CBT_PLH->value,
        ]);
        AbsensiHarian::create([
            'tanggal' => now()->subDay()->toDateString(),
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::SAKIT->value,
        ]);
        AbsensiHarian::create([
            'tanggal' => now()->toDateString(),
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombelA->id,
            'semester_id' => $this->semester->id,
            'status' => StatusAbsensi::ALPA->value,
        ]);

        // SiswaDetail: H = 2 (Hadir + Dispen), S = 1, I = 0, A = 1, persen = 50%
        Livewire::actingAs($this->admin)
            ->test(SiswaDetail::class, ['id' => $this->siswa1->id])
            ->assertViewHas('absensiStats', ['H' => 2, 'S' => 1, 'I' => 0, 'A' => 1])
            ->assertViewHas('persenHadir', 50.0);

        // BerandaSiswa: H = 2, S = 1, I = 0, A = 1, persen = 50%
        Livewire::actingAs($studentUser)
            ->test(BerandaSiswa::class)
            ->assertViewHas('absensiStats', ['H' => 2, 'S' => 1, 'I' => 0, 'A' => 1, 'persen' => 50.0]);

        // KehadiranSiswa: H = 2 (termasuk Dispen), S = 1, I = 0, A = 1, total = 4, persen = 50%
        Livewire::actingAs($studentUser)
            ->test(KehadiranSiswa::class)
            ->assertViewHas('stats', ['total' => 4, 'H' => 2, 'S' => 1, 'I' => 0, 'A' => 1, 'persen' => 50.0]);
    }
}
