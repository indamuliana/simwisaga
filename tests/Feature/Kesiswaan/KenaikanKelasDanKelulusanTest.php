<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Alumni\Models\Alumni;
use App\Modules\Kesiswaan\Livewire\KelulusanIndex;
use App\Modules\Kesiswaan\Livewire\KenaikanKelasIndex;
use App\Modules\Kesiswaan\Models\RiwayatAkademik;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KenaikanKelasDanKelulusanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta1;
    protected TahunAjaran $ta2;
    protected Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $this->ta1 = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);
        $this->ta2 = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);
        $this->jurusan = Jurusan::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak']);
    }

    public function test_halaman_kenaikan_kelas_dan_kelulusan_dapat_diakses(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.kenaikan-kelas'))
            ->assertOk()
            ->assertSee('Wizard Kenaikan Kelas Siswa');

        $this->actingAs($this->admin)
            ->get(route('kesiswaan.kelulusan'))
            ->assertOk()
            ->assertSee('Kelulusan Siswa Tingkat Akhir');
    }

    public function test_proses_kenaikan_kelas_berhasil_memindahkan_siswa_ke_tingkat_berikutnya(): void
    {
        $rombelAsal = Rombel::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        $rombelTujuan = Rombel::create([
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI PPLG 1',
            'kapasitas' => 36,
        ]);

        $siswa1 = Siswa::create(['nama' => 'Budi Pratama', 'nisn' => '1111111111', 'rombel_id' => $rombelAsal->id, 'status' => 'aktif']);
        $siswa2 = Siswa::create(['nama' => 'Citra Dewi', 'nisn' => '2222222222', 'rombel_id' => $rombelAsal->id, 'status' => 'aktif']);

        Livewire::actingAs($this->admin)
            ->test(KenaikanKelasIndex::class)
            ->set('tahunAjaranAsalId', $this->ta1->id)
            ->set('rombelAsalId', $rombelAsal->id)
            ->set('tahunAjaranTujuanId', $this->ta2->id)
            ->set('rombelTujuanUtamaId', $rombelTujuan->id)
            ->call('prosesKenaikanKelas')
            ->assertHasNoErrors();

        $siswa1->refresh();
        $siswa2->refresh();

        $this->assertEquals($rombelTujuan->id, $siswa1->rombel_id);
        $this->assertEquals($rombelTujuan->id, $siswa2->rombel_id);

        $this->assertDatabaseHas('rombel_siswa', [
            'tahun_ajaran_id' => $this->ta2->id,
            'siswa_id' => $siswa1->id,
            'rombel_id' => $rombelTujuan->id,
        ]);

        $this->assertEquals(2, RiwayatAkademik::where('tahun_ajaran_id', $this->ta2->id)->where('status_keputusan', 'naik_kelas')->count());
    }

    public function test_penanganan_siswa_tinggal_kelas(): void
    {
        $rombelAsal = Rombel::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        $rombelTujuanNaik = Rombel::create([
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI PPLG 1',
            'kapasitas' => 36,
        ]);

        $rombelTujuanTinggal = Rombel::create([
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        $siswaNaik = Siswa::create(['nama' => 'Eko Kurniawan', 'nisn' => '3333333333', 'rombel_id' => $rombelAsal->id, 'status' => 'aktif']);
        $siswaTinggal = Siswa::create(['nama' => 'Fajar Santoso', 'nisn' => '4444444444', 'rombel_id' => $rombelAsal->id, 'status' => 'aktif']);

        $test = Livewire::actingAs($this->admin)
            ->test(KenaikanKelasIndex::class)
            ->set('tahunAjaranAsalId', $this->ta1->id)
            ->set('rombelAsalId', $rombelAsal->id)
            ->set('tahunAjaranTujuanId', $this->ta2->id)
            ->set('rombelTujuanUtamaId', $rombelTujuanNaik->id)
            ->call('setStatusIndividual', $siswaTinggal->id, 'tinggal_kelas');

        // Pastikan target tinggal diarahkan ke rombel tingkat X di TA baru
        $test->set("siswaDecisions.{$siswaTinggal->id}.rombel_tujuan_id", $rombelTujuanTinggal->id)
            ->call('prosesKenaikanKelas')
            ->assertHasNoErrors();

        $siswaNaik->refresh();
        $siswaTinggal->refresh();

        // Siswa Naik berada di XI PPLG 1
        $this->assertEquals($rombelTujuanNaik->id, $siswaNaik->rombel_id);

        // Siswa Tinggal berada di X PPLG 1 TA Baru
        $this->assertEquals($rombelTujuanTinggal->id, $siswaTinggal->rombel_id);

        // Periksa Riwayat Akademik Siswa Tinggal
        $riwayatTinggal = RiwayatAkademik::where('siswa_id', $siswaTinggal->id)->first();
        $this->assertNotNull($riwayatTinggal);
        $this->assertEquals('tinggal_kelas', $riwayatTinggal->status_keputusan);
        $this->assertEquals('X', $riwayatTinggal->tingkat_asal);
        $this->assertEquals('X', $riwayatTinggal->tingkat_tujuan);
    }

    public function test_proses_kelulusan_kelas_xii_dan_otomasi_alumni(): void
    {
        $rombelXII = Rombel::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XII',
            'nomor_urut' => 1,
            'nama' => 'XII PPLG 1',
            'kapasitas' => 36,
        ]);

        $siswaLulus = Siswa::create(['nama' => 'Gilang Ramadhan', 'nisn' => '5555555555', 'rombel_id' => $rombelXII->id, 'status' => 'aktif', 'is_active' => true]);
        $siswaMengulang = Siswa::create(['nama' => 'Hendra Wijaya', 'nisn' => '6666666666', 'rombel_id' => $rombelXII->id, 'status' => 'aktif', 'is_active' => true]);

        Livewire::actingAs($this->admin)
            ->test(KelulusanIndex::class)
            ->set('tahunAjaranId', $this->ta1->id)
            ->set('rombelId', $rombelXII->id)
            ->set("kelulusanDecisions.{$siswaLulus->id}.nomor_ijazah", 'DN-02/M-SMK/2025/0001')
            ->set("kelulusanDecisions.{$siswaMengulang->id}.status", 'tidak_lulus')
            ->call('prosesKelulusan')
            ->assertHasNoErrors();

        $siswaLulus->refresh();
        $siswaMengulang->refresh();

        // Verifikasi Siswa Lulus
        $this->assertEquals('lulus', $siswaLulus->status);
        $this->assertFalse($siswaLulus->is_active);

        // Verifikasi Otomasi Tercatat di Alumni
        $this->assertDatabaseHas('alumnis', [
            'siswa_id' => $siswaLulus->id,
            'tahun_lulus' => '2025',
            'nomor_ijazah' => 'DN-02/M-SMK/2025/0001',
        ]);

        // Verifikasi Riwayat Akademik
        $this->assertDatabaseHas('riwayat_akademiks', [
            'siswa_id' => $siswaLulus->id,
            'status_keputusan' => 'lulus',
            'tingkat_asal' => 'XII',
            'tingkat_tujuan' => 'LULUS',
        ]);

        // Verifikasi Siswa Tidak Lulus / Mengulang
        $this->assertEquals('aktif', $siswaMengulang->status);
        $this->assertTrue($siswaMengulang->is_active);
        $this->assertDatabaseHas('riwayat_akademiks', [
            'siswa_id' => $siswaMengulang->id,
            'status_keputusan' => 'tidak_lulus',
        ]);
    }

    public function test_scope_in_rombel_mempertahankan_data_historis_arsip_dan_ledger(): void
    {
        $rombelLama = Rombel::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        $rombelBaru = Rombel::create([
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI PPLG 1',
            'kapasitas' => 36,
        ]);

        $siswa = Siswa::create([
            'nama' => 'Ahmad Historis',
            'nisn' => '7777777777',
            'rombel_id' => $rombelLama->id,
            'status' => 'aktif',
        ]);

        // Jalankan kenaikan kelas
        Livewire::actingAs($this->admin)
            ->test(KenaikanKelasIndex::class)
            ->set('tahunAjaranAsalId', $this->ta1->id)
            ->set('rombelAsalId', $rombelLama->id)
            ->set('tahunAjaranTujuanId', $this->ta2->id)
            ->set('rombelTujuanUtamaId', $rombelBaru->id)
            ->call('prosesKenaikanKelas');

        $siswa->refresh();

        // 1. Kolom aktif rombel_id sudah bergeser ke rombel baru
        $this->assertEquals($rombelBaru->id, $siswa->rombel_id);

        // 2. Query scope inRombel untuk rombel baru menemukan siswa
        $this->assertTrue(Siswa::inRombel($rombelBaru->id)->where('id', $siswa->id)->exists());

        // 3. Query scope inRombel untuk rombel LAMA TETAP menemukan siswa (menjamin rapor lama & ledger aman)
        $this->assertTrue(Siswa::inRombel($rombelLama->id)->where('id', $siswa->id)->exists());
    }

    public function test_proses_kenaikan_kelas_idempoten_dan_dapat_diedit_ulang(): void
    {
        $rombelX = Rombel::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X PPLG 1',
            'kapasitas' => 36,
        ]);

        $rombelXI = Rombel::create([
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'XI',
            'nomor_urut' => 1,
            'nama' => 'XI PPLG 1',
            'kapasitas' => 36,
        ]);

        $siswa = Siswa::create(['nama' => 'Revisi Kenaikan', 'nisn' => '8888888888', 'rombel_id' => $rombelX->id, 'status' => 'aktif']);

        // Eksekusi pertama: Naik kelas
        $component = Livewire::actingAs($this->admin)
            ->test(KenaikanKelasIndex::class)
            ->set('tahunAjaranAsalId', $this->ta1->id)
            ->set('rombelAsalId', $rombelX->id)
            ->set('tahunAjaranTujuanId', $this->ta2->id)
            ->set('rombelTujuanUtamaId', $rombelXI->id)
            ->call('prosesKenaikanKelas');

        $this->assertEquals(1, RiwayatAkademik::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $this->ta2->id)->count());

        // Re-load component untuk rombel asal yang sama: data siswa tetap muncul dan sudah_diproses bernilai true
        $component2 = Livewire::actingAs($this->admin)
            ->test(KenaikanKelasIndex::class)
            ->set('tahunAjaranAsalId', $this->ta1->id)
            ->set('rombelAsalId', $rombelX->id)
            ->set('tahunAjaranTujuanId', $this->ta2->id);

        $decisions = $component2->get('siswaDecisions');
        $this->assertArrayHasKey($siswa->id, $decisions);
        $this->assertTrue($decisions[$siswa->id]['sudah_diproses']);
        $this->assertEquals('naik_kelas', $decisions[$siswa->id]['status']);

        // Ubah catatan dan simpan ulang: tidak boleh ada duplikasi row di riwayat_akademiks
        $component2->set("siswaDecisions.{$siswa->id}.catatan", 'Catatan revisi wali kelas')
            ->call('prosesKenaikanKelas');

        $this->assertEquals(1, RiwayatAkademik::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $this->ta2->id)->count());
        $this->assertEquals('Catatan revisi wali kelas', RiwayatAkademik::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $this->ta2->id)->first()->catatan);
    }
}

