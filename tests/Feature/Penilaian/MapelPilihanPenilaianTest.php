<?php

namespace Tests\Feature\Penilaian;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Livewire\MapelPilihanPenilaianIndex;
use App\Modules\Penilaian\Livewire\MapelPilihanPenilaianInput;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Services\MapelPilihanScoreService;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MapelPilihanPenilaianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guruUser;
    protected Pegawai $guru;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Semester $semGenap;
    protected MapelPilihan $mapil;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_penilaian',
            'must_change_password' => false,
        ]);
        $this->admin->assignRole(User::SUPER_ADMIN_ROLE);

        $this->ta = TahunAjaran::create([
            'tahun' => '2026/2027',
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->semGanjil = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'nama' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->semGenap = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'genap',
            'nama' => 'Genap',
            'is_active' => false,
        ]);

        $this->guru = Pegawai::create([
            'nama_lengkap' => 'Bambang Guru Mapil',
            'nik' => '1122334455667788',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
        ]);

        $rombel = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'XI',
            'nama' => 'XI RPL 1',
            'is_active' => true,
        ]);

        $this->siswa1 = Siswa::create([
            'rombel_id' => $rombel->id,
            'nisn' => '1112223334',
            'nama' => 'Ahmad Fajar',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'rombel_id' => $rombel->id,
            'nisn' => '1112223335',
            'nama' => 'Dini Lestari',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->mapil = MapelPilihan::create([
            'tahun_ajaran_id' => $this->ta->id,
            'kode' => 'MP-MOB',
            'nama' => 'Pemrograman Aplikasi Mobile',
            'tingkats' => ['XI'],
            'kkm_default' => 75,
            'jp' => 4,
            'guru_id' => $this->guru->id,
            'is_active' => true,
        ]);

        MapelPilihanAnggota::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'siswa_id' => $this->siswa1->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status' => 'aktif',
        ]);

        MapelPilihanAnggota::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'siswa_id' => $this->siswa2->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status' => 'aktif',
        ]);
    }

    public function test_can_view_mapil_penilaian_dashboard_and_input(): void
    {
        Livewire::actingAs($this->admin)
            ->test(MapelPilihanPenilaianIndex::class)
            ->assertOk()
            ->assertSee('Pemrograman Aplikasi Mobile')
            ->assertSee('KKM 75');

        Livewire::actingAs($this->admin)
            ->test(MapelPilihanPenilaianInput::class, ['id' => $this->mapil->id])
            ->assertOk()
            ->assertSee('Ahmad Fajar')
            ->assertSee('Dini Lestari');
    }

    public function test_can_create_edit_and_delete_tp_mapil(): void
    {
        $comp = Livewire::actingAs($this->admin)
            ->test(MapelPilihanPenilaianInput::class, ['id' => $this->mapil->id])
            ->set('activeTab', 'tp')
            ->set('tp_semester_id', $this->semGanjil->id)
            ->set('tp_kode', 'TP 1')
            ->set('tp_lingkup_materi', 'Pengenalan Flutter')
            ->set('tp_deskripsi', 'memahami arsitektur dasar widget Flutter')
            ->set('tp_alokasi_ah', 'AH 1')
            ->set('tp_target_periode', '9M1')
            ->set('tp_urutan', 1)
            ->call('saveTp')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapel_pilihan_tps', [
            'mapel_pilihan_id' => $this->mapil->id,
            'kode_tp' => 'TP 1',
            'kode_ah' => 'AH 1',
        ]);

        $tp = MapelPilihanTp::where('mapel_pilihan_id', $this->mapil->id)->first();

        // Edit TP
        $comp->call('editTp', $tp->id)
            ->set('tp_deskripsi', 'memahami widget Flutter dan state management')
            ->call('saveTp')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapel_pilihan_tps', [
            'id' => $tp->id,
            'deskripsi_tp' => 'memahami widget Flutter dan state management',
        ]);

        // Delete TP
        $comp->call('deleteTp', $tp->id);
        $this->assertDatabaseMissing('mapel_pilihan_tps', ['id' => $tp->id]);
    }

    public function test_strict_kkm_and_empty_scores_produce_null_na_and_proper_status(): void
    {
        // Setup 2 TPs for 9M1 (AH 1 & AH 2)
        MapelPilihanTp::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_tp' => 'TP 1',
            'deskripsi_tp' => 'merancang antarmuka UI mobile',
            'kode_ah' => 'AH 1',
            'target_periode' => '9M1',
            'urutan' => 1,
        ]);

        MapelPilihanTp::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_tp' => 'TP 2',
            'deskripsi_tp' => 'menerapkan navigasi multi-halaman',
            'kode_ah' => 'AH 2',
            'target_periode' => '9M1',
            'urutan' => 2,
        ]);

        // Siswa 1: AH 1 diisi, tapi AH 2 dan STS kosong -> Status BELUM LENGKAP, NA null
        // Siswa 2: AH 1 & 2 diisi >= KKM, tapi STS di bawah KKM (65 < 75) -> Status BELUM KOMPETEN, NA null
        Livewire::actingAs($this->admin)
            ->test(MapelPilihanPenilaianInput::class, ['id' => $this->mapil->id])
            ->set('ahData.' . $this->siswa1->id . '.AH 1', 85)
            ->set('ahData.' . $this->siswa1->id . '.AH 2', null) // empty
            ->set('sumatifDataGanjil.' . $this->siswa1->id . '.sts', null) // empty
            ->set('ahData.' . $this->siswa2->id . '.AH 1', 80)
            ->set('ahData.' . $this->siswa2->id . '.AH 2', 85)
            ->set('sumatifDataGanjil.' . $this->siswa2->id . '.sts', 65) // below KKM!
            ->call('saveNilai')
            ->assertHasNoErrors();

        // Verifikasi Nilai Akhir Siswa 1
        $na1 = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $this->mapil->id)
            ->where('siswa_id', $this->siswa1->id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $this->assertNotNull($na1);
        $block9M1_1 = $na1->data_9m['9M1'];
        $this->assertEquals('belum_lengkap', $block9M1_1['status']);
        $this->assertNull($block9M1_1['nilai_akhir']);
        $this->assertStringContainsString('Nilai belum lengkap', $block9M1_1['deskripsi_tertinggi']);

        // Verifikasi Nilai Akhir Siswa 2 (Di bawah KKM)
        $na2 = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $this->mapil->id)
            ->where('siswa_id', $this->siswa2->id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $this->assertNotNull($na2);
        $block9M1_2 = $na2->data_9m['9M1'];
        $this->assertEquals('belum_kompeten', $block9M1_2['status']);
        $this->assertNull($block9M1_2['nilai_akhir']);
        $this->assertStringContainsString('Nilai belum kompeten', $block9M1_2['deskripsi_tertinggi']);
    }

    public function test_complete_and_competent_scores_generate_final_grade_and_kurikulum_merdeka_narration(): void
    {
        // Setup TPs for 9M1
        MapelPilihanTp::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_tp' => 'TP 1',
            'deskripsi_tp' => 'merancang antarmuka UI mobile',
            'kode_ah' => 'AH 1',
            'target_periode' => '9M1',
            'urutan' => 1,
        ]);

        MapelPilihanTp::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_tp' => 'TP 2',
            'deskripsi_tp' => 'menerapkan navigasi multi-halaman',
            'kode_ah' => 'AH 2',
            'target_periode' => '9M1',
            'urutan' => 2,
        ]);

        // Siswa 1 mendapatkan nilai lengkap dan tuntas:
        // AH 1: 90 (Sangat Baik), AH 2: 78 (Baik), STS: 86
        Livewire::actingAs($this->admin)
            ->test(MapelPilihanPenilaianInput::class, ['id' => $this->mapil->id])
            ->set('ahData.' . $this->siswa1->id . '.AH 1', 90)
            ->set('ahData.' . $this->siswa1->id . '.AH 2', 78)
            ->set('sumatifDataGanjil.' . $this->siswa1->id . '.sts', 86)
            ->call('saveNilai')
            ->assertHasNoErrors();

        $na = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $this->mapil->id)
            ->where('siswa_id', $this->siswa1->id)
            ->where('semester_id', $this->semGanjil->id)
            ->first();

        $this->assertNotNull($na);
        $block = $na->data_9m['9M1'];
        $this->assertEquals('kompeten', $block['status']);

        // Rata formatif = (90 + 78) / 2 = 84. STS = 86. NH default = 100.
        // NA = (84 * 0.5) + (86 * 0.3) + (100 * 0.2) = 42 + 25.8 + 20 = 87.80
        $this->assertEquals(87.80, $block['nilai_akhir']);

        // Narasi Kurikulum Merdeka 2 baris
        $this->assertNotEmpty($block['deskripsi_tertinggi']);
        $this->assertNotEmpty($block['deskripsi_terendah']);
        $this->assertStringContainsString('merancang antarmuka UI mobile', $block['deskripsi_tertinggi']);
        $this->assertStringContainsString('menerapkan navigasi multi-halaman', $block['deskripsi_terendah']);
    }

    public function test_can_override_manual_description_for_9m(): void
    {
        $comp = Livewire::actingAs($this->admin)
            ->test(MapelPilihanPenilaianInput::class, ['id' => $this->mapil->id]);

        $comp->call('openModalDeskripsi9M', $this->siswa1->id, '9M1')
            ->set('tempDeskripsiManual9M', 'Sangat aktif dan berbakat dalam pembuatan mockup antarmuka mobile.')
            ->call('saveDeskripsi9M')
            ->assertHasNoErrors();

        $na = NilaiMapelPilihanAkhir::where('mapel_pilihan_id', $this->mapil->id)
            ->where('siswa_id', $this->siswa1->id)
            ->first();

        $this->assertNotNull($na);
        $this->assertEquals(
            'Sangat aktif dan berbakat dalam pembuatan mockup antarmuka mobile.',
            $na->data_9m['9M1']['deskripsi_manual']
        );
    }
}
