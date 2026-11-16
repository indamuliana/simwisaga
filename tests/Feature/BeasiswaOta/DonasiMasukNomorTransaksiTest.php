<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonasiMasukNomorTransaksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Kelompok $kelompok;
    protected Donatur $donatur1;
    protected Donatur $donatur2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_nomor_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();

        $this->kelompok = Kelompok::create([
            'kode_kelompok' => 'KLP-01',
            'nama_kelompok' => 'Kelompok Al-Hikam',
            'target_nominal' => 3000000,
            'is_aktif' => true,
        ]);

        $this->donatur1 = Donatur::create([
            'nama' => 'Donatur A',
            'tipe' => 'rutin',
            'is_aktif' => true,
        ]);

        $this->donatur2 = Donatur::create([
            'nama' => 'Donatur B',
            'tipe' => 'rutin',
            'is_aktif' => true,
        ]);
    }

    public function test_nomor_transaksi_sequential_generation(): void
    {
        $d1 = DonasiMasuk::create([
            'donatur_id' => $this->donatur1->id,
            'tipe_donatur' => 'rutin',
            'nominal' => 100000,
            'tanggal_donasi' => '2026-11-16',
        ]);

        $d2 = DonasiMasuk::create([
            'donatur_id' => $this->donatur2->id,
            'tipe_donatur' => 'rutin',
            'nominal' => 150000,
            'tanggal_donasi' => '2026-11-16',
        ]);

        $this->assertEquals('OTA-IN-202611-0001', $d1->nomor_transaksi);
        $this->assertEquals('OTA-IN-202611-0002', $d2->nomor_transaksi);
    }

    public function test_nomor_transaksi_does_not_collide_when_0151_already_exists(): void
    {
        // Simulasikan nomor transaksi 0151 sudah ada di database (seperti kasus duplikasi di hosting)
        DonasiMasuk::create([
            'nomor_transaksi' => 'OTA-IN-202611-0151',
            'donatur_id' => $this->donatur1->id,
            'tipe_donatur' => 'rutin',
            'nominal' => 500000,
            'tanggal_donasi' => '2026-11-16',
        ]);

        // Donasi berikutnya harus otomatis mencari angka tertinggi + 1 (yaitu 0152), BUKAN 0151 atau 0002
        $dNext = DonasiMasuk::create([
            'donatur_id' => $this->donatur2->id,
            'tipe_donatur' => 'rutin',
            'nominal' => 600000,
            'tanggal_donasi' => '2026-11-16',
        ]);

        $this->assertEquals('OTA-IN-202611-0152', $dNext->nomor_transaksi);
    }

    public function test_catat_donasi_kolektif_batch_avoids_duplicate_entry(): void
    {
        // Misal 0151 sudah ada di database
        DonasiMasuk::create([
            'nomor_transaksi' => 'OTA-IN-202611-0151',
            'donatur_id' => $this->donatur1->id,
            'tipe_donatur' => 'rutin',
            'nominal' => 500000,
            'tanggal_donasi' => '2026-11-16',
        ]);

        // Jalankan setoran kolektif 2 donatur sekaligus
        $results = OtaKasService::catatDonasiKolektifKelompok(
            kelompok: $this->kelompok,
            koordinatorNama: '57. Catur Iriyanto',
            tanggal: '2026-11-16',
            items: [
                [
                    'donatur_id' => $this->donatur1->id,
                    'nominal' => 1800000,
                    'jumlah_bulan' => 12,
                    'periode_bulan' => ["2026-07","2026-08","2026-09","2026-10","2026-11","2026-12","2027-01","2027-02","2027-03","2027-04","2027-05","2027-06"],
                    'keterangan_periode' => '1 Tahun Penuh',
                    'keterangan' => 'Setoran Iuran Al-Hikam 5 via Koordinator',
                ],
                [
                    'donatur_id' => $this->donatur2->id,
                    'nominal' => 900000,
                    'jumlah_bulan' => 6,
                    'periode_bulan' => ["2026-07","2026-08","2026-09","2026-10","2026-11","2026-12"],
                    'keterangan_periode' => '6 Bulan',
                    'keterangan' => 'Setoran Iuran Al-Hikam 5 via Koordinator',
                ],
            ],
            userId: $this->admin->id,
            tahunAjaranId: $this->ta->id
        );

        $this->assertCount(2, $results['donasis']);
        $this->assertEquals('OTA-IN-202611-0152', $results['donasis'][0]->nomor_transaksi);
        $this->assertEquals('OTA-IN-202611-0153', $results['donasis'][1]->nomor_transaksi);
    }

    public function test_nomor_kwitansi_alokasi_gap_safe(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Alokasi Test',
            'nipd' => '12209999',
            'nisn' => '0099881133',
            'jk' => 'L',
            'status' => 'aktif',
        ]);

        Alokasi::create([
            'nomor_kwitansi' => 'OTA-KW-202611-0080',
            'kelompok_id' => $this->kelompok->id,
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'semester_id' => Semester::where('tahun_ajaran_id', $this->ta->id)->first()->id,
            'tipe_alokasi' => 'rutin',
            'tanggal_alokasi' => '2026-11-16',
            'total_nominal' => 200000,
        ]);

        $nextAlokasi = Alokasi::create([
            'kelompok_id' => $this->kelompok->id,
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'semester_id' => Semester::where('tahun_ajaran_id', $this->ta->id)->first()->id,
            'tipe_alokasi' => 'rutin',
            'tanggal_alokasi' => '2026-11-16',
            'total_nominal' => 300000,
        ]);

        $this->assertEquals('OTA-KW-202611-0081', $nextAlokasi->nomor_kwitansi);
    }
}
