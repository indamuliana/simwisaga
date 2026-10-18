<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukIndex;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OtaKasValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semester;
    protected Kelompok $kelompok;
    protected Donatur $donatur;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_ota',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semester = Semester::where('tahun_ajaran_id', $this->ta->id)->firstOrFail();

        $this->kelompok = Kelompok::create([
            'kode_kelompok' => 'KLP-01',
            'nama_kelompok' => 'Kelompok Peduli 1',
            'target_nominal' => 3000000,
            'is_aktif' => true,
        ]);

        $this->donatur = Donatur::create([
            'nama' => 'Bapak Dermawan',
            'tipe' => 'rutin',
            'is_aktif' => true,
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Siswa Asuh 1',
            'nipd' => '12209998',
            'nisn' => '0099881122',
            'jk' => 'L',
            'status' => 'aktif',
        ]);

        SiswaPenerima::create([
            'siswa_id' => $this->siswa->id,
            'kelompok_id' => $this->kelompok->id,
            'status' => 'aktif',
            'tanggal_mulai' => now(),
        ]);
    }

    public function test_catat_alokasi_rutin_menolak_pengeluaran_melebihi_saldo_kelompok(): void
    {
        // Donasi masuk Rp 200.000
        OtaKasService::catatDonasiMasuk(
            donatur: $this->donatur,
            nominal: 200000,
            tanggal: now()->toDateString(),
            kelompokId: $this->kelompok->id
        );

        $this->assertEquals(200000, OtaKasService::getSaldoKelompok($this->kelompok->id));

        // Coba alokasi Rp 300.000 (melebihi saldo)
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak mencukupi');

        OtaKasService::catatAlokasiRutin(
            kelompok: $this->kelompok,
            siswa: $this->siswa,
            details: [
                ['nama_komponen' => 'SPP', 'nominal' => 300000]
            ],
            totalNominal: 300000,
            surplusDialihkan: 0,
            tanggal: now()->toDateString()
        );
    }

    public function test_catat_alokasi_insidental_menolak_pengeluaran_melebihi_saldo_insidental(): void
    {
        $this->assertEquals(0, OtaKasService::getSaldoInsidental());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak mencukupi');

        OtaKasService::catatAlokasiInsidental(
            siswa: $this->siswa,
            details: [
                ['nama_komponen' => 'Bantuan Seragam', 'nominal' => 150000]
            ],
            totalNominal: 150000,
            tanggal: now()->toDateString()
        );
    }

    public function test_donasi_masuk_tidak_bisa_dihapus_jika_saldo_telah_terpakai(): void
    {
        // Donasi masuk Rp 500.000
        $donasi = OtaKasService::catatDonasiMasuk(
            donatur: $this->donatur,
            nominal: 500000,
            tanggal: now()->toDateString(),
            kelompokId: $this->kelompok->id
        );

        // Alokasikan Rp 400.000 (sisa saldo kelompok: Rp 100.000)
        OtaKasService::catatAlokasiRutin(
            kelompok: $this->kelompok,
            siswa: $this->siswa,
            details: [
                ['nama_komponen' => 'SPP', 'nominal' => 400000]
            ],
            totalNominal: 400000,
            surplusDialihkan: 0,
            tanggal: now()->toDateString()
        );

        $this->assertEquals(100000, OtaKasService::getSaldoKelompok($this->kelompok->id));

        // Coba hapus donasi Rp 500.000 via Livewire (harus dicegah karena saldo tinggal 100.000)
        Livewire::actingAs($this->admin)
            ->test(DonasiMasukIndex::class)
            ->call('delete', $donasi->id)
            ->assertSee('tidak dapat dibatalkan');

        // Donasi tetap ada di database dan saldo tidak menjadi minus
        $this->assertDatabaseHas('ota_donasi_masuks', ['id' => $donasi->id]);
        $this->assertEquals(100000, OtaKasService::getSaldoKelompok($this->kelompok->id));
    }
}
