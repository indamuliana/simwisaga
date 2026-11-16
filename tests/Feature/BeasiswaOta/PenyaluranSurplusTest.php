<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukIndex;
use App\Modules\BeasiswaOta\Livewire\PenyaluranIndex;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KomponenBiaya;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenyaluranSurplusTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Kelompok $kelompok;
    protected Donatur $donatur;
    protected Siswa $siswa;
    protected SiswaPenerima $penerima;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_ota_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->kelompok = Kelompok::create([
            'kode_kelompok' => 'KLP-SURPLUS',
            'nama_kelompok' => 'Kelompok Al-Hikam Test',
            'target_nominal' => 900000,
            'is_aktif' => true,
        ]);

        $this->donatur = Donatur::create([
            'nama' => 'Donatur Test 1',
            'tipe' => 'rutin',
            'is_aktif' => true,
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Siswa Binaan Test',
            'nisn' => '9988776655',
            'is_active' => true,
        ]);

        $this->penerima = SiswaPenerima::create([
            'siswa_id' => $this->siswa->id,
            'kelompok_id' => $this->kelompok->id,
            'status' => 'aktif',
            'tanggal_mulai' => now()->toDateString(),
        ]);

        KomponenBiaya::create([
            'siswa_penerima_id' => $this->penerima->id,
            'jenis_komponen' => 'spp',
            'nama_komponen' => 'SPP Bulanan',
            'nominal_per_bulan' => 150000,
            'total_target_semester' => 900000,
        ]);
    }

    public function test_penyaluran_6_bulan_tidak_memotong_sisa_saldo_12_bulan_ke_kas_insidental(): void
    {
        $this->actingAs($this->admin);

        // 1. Catat Donasi Masuk 12 Bulan (Rp 1.800.000)
        OtaKasService::catatDonasiMasuk(
            donatur: $this->donatur,
            nominal: 1800000,
            tanggal: now()->toDateString(),
            kelompokId: $this->kelompok->id,
            keterangan: 'Iuran Penuh 12 Bulan',
            userId: $this->admin->id,
            tahunAjaranId: $this->ta->id,
            periodeBulan: ['2026-07', '2026-08', '2026-09', '2026-10', '2026-11', '2026-12', '2027-01', '2027-02', '2027-03', '2027-04', '2027-05', '2027-06'],
            jumlahBulan: 12,
            keteranganPeriode: '1 Tahun Penuh (12 Bulan)'
        );

        $this->assertEquals(1800000, OtaKasService::getSaldoKelompok($this->kelompok->id));
        $this->assertEquals(0, OtaKasService::getSaldoInsidental());

        // 2. Buka Penyaluran Livewire dan pilih kelompok
        $lw = Livewire::test(PenyaluranIndex::class)
            ->set('rutinKelompokId', $this->kelompok->id);

        // Alokasi otomatis untuk 6 bulan Ganjil (6 x 150.000 = 900.000)
        $this->assertEquals(900000, $lw->get('rutinTotalAlokasi'));
        // Pastikan surplus DEFAULT adalah 0 (tidak otomatis memotong saldo 6 bulan sisa ke insidental)
        $this->assertEquals(0, $lw->get('rutinSurplus'));

        // 3. Simpan penyaluran
        $lw->call('saveAlokasiRutin');

        // 4. Verifikasi saldo:
        // Kas Kelompok harus tersisa Rp 900.000 (untuk semester genap berikutnya)
        $this->assertEquals(900000, OtaKasService::getSaldoKelompok($this->kelompok->id));
        // Kas Insidental TIDAK boleh kemasukan dana (tetap 0)
        $this->assertEquals(0, OtaKasService::getSaldoInsidental());

        // Pastikan alokasi tercatat dengan surplus 0
        $alokasi = Alokasi::where('kelompok_id', $this->kelompok->id)->latest('id')->first();
        $this->assertNotNull($alokasi);
        $this->assertEquals(900000, (float) $alokasi->total_nominal);
        $this->assertEquals(0, (float) $alokasi->surplus_dialihkan);
    }

    public function test_penghapusan_donasi_kolektif_satuan_ditolak_harus_lewat_batal_batch(): void
    {
        $this->actingAs($this->admin);

        $batchResult = OtaKasService::catatDonasiKolektifKelompok(
            kelompok: $this->kelompok,
            koordinatorNama: 'Koordinator Al-Hikam',
            items: [
                ['donatur_id' => $this->donatur->id, 'nominal' => 300000, 'jumlah_bulan' => 2],
            ],
            tanggal: now()->toDateString(),
            userId: $this->admin->id,
            tahunAjaranId: $this->ta->id
        );

        $donasi = $batchResult['donasis'][0];

        // Hapus satuan harus ditolak
        Livewire::test(DonasiMasukIndex::class)
            ->call('delete', $donasi->id)
            ->assertSee('Donasi kolektif tidak dapat dihapus satuan');

        // Pastikan donasi masih ada
        $this->assertDatabaseHas('ota_donasi_masuks', ['id' => $donasi->id]);

        // Hapus via batch harus berhasil
        Livewire::test(DonasiMasukIndex::class)
            ->call('deleteBatch', $batchResult['batch_kode']);

        $this->assertDatabaseMissing('ota_donasi_masuks', ['id' => $donasi->id]);
    }
}
