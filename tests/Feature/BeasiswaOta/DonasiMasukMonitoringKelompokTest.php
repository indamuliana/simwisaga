<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukCreate;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukIndex;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaExcelService;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Database\Seeders\DummyOtaKelompokSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DonasiMasukMonitoringKelompokTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_ota',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);
    }

    public function test_default_view_is_rekap_per_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        Livewire::actingAs($this->admin)
            ->test(DonasiMasukIndex::class)
            ->assertSet('activeTab', 'kelompok')
            ->assertSee('Rekap Per Kelompok')
            ->assertSee('Kelompok Peduli Siswa 1')
            ->assertSee('KEL-01')
            ->assertSee('KAS-BEBAS');
    }

    public function test_filter_tahun_ajaran_dan_pencarian_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        $kel1 = Kelompok::where('kode_kelompok', 'KEL-01')->first();
        $inda = Donatur::where('nama', 'Inda Muliana')->first();

        // Catat donasi masuk di T.A. 2026/2027
        OtaKasService::catatDonasiMasuk(
            donatur: $inda,
            nominal: 150000,
            tanggal: '2026-07-20',
            kelompokId: $kel1->id,
            tahunAjaranId: $this->ta->id,
            periodeBulan: ['2026-07']
        );

        // Test Livewire
        Livewire::actingAs($this->admin)
            ->test(DonasiMasukIndex::class)
            ->set('tahunAjaranId', $this->ta->id)
            ->assertSee('Rp 150.000')
            ->set('searchKelompok', 'Peduli Siswa 2')
            ->assertSee('Kelompok Peduli Siswa 2')
            ->assertDontSee('Kelompok Peduli Siswa 1');
    }

    public function test_buka_drawer_rincian_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        $kel1 = Kelompok::where('kode_kelompok', 'KEL-01')->first();
        $inda = Donatur::where('nama', 'Inda Muliana')->first();

        OtaKasService::catatDonasiMasuk(
            donatur: $inda,
            nominal: 150000,
            tanggal: '2026-07-20',
            kelompokId: $kel1->id,
            tahunAjaranId: $this->ta->id,
            periodeBulan: ['2026-07']
        );

        Livewire::actingAs($this->admin)
            ->test(DonasiMasukIndex::class)
            ->set('tahunAjaranId', $this->ta->id)
            ->call('openDrawer', $kel1->id)
            ->assertSet('showDrawer', true)
            ->assertSet('drawerKelompokId', $kel1->id)
            ->assertSee('Matrix Status Iuran Anggota')
            ->assertSee('Inda Muliana')
            ->assertSee('Jul')
            ->call('closeDrawer')
            ->assertSet('showDrawer', false);
    }

    public function test_drawer_kas_bebas(): void
    {
        $donatur = Donatur::create([
            'nama' => 'Hamba Allah',
            'tipe' => 'insidental',
            'is_aktif' => true,
        ]);

        OtaKasService::catatDonasiMasuk(
            donatur: $donatur,
            nominal: 500000,
            tanggal: '2026-08-01',
            kelompokId: null,
            tahunAjaranId: $this->ta->id,
            keterangan: 'Donasi Insidental Bebas'
        );

        Livewire::actingAs($this->admin)
            ->test(DonasiMasukIndex::class)
            ->set('tahunAjaranId', $this->ta->id)
            ->call('openDrawer', 0)
            ->assertSet('showDrawer', true)
            ->assertSee('Kas Bebas (Donasi Insidental)')
            ->assertSee('Hamba Allah')
            ->assertSee('Rp 500.000');
    }

    public function test_shortcut_catat_donasi_preselects_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        $kel2 = Kelompok::where('kode_kelompok', 'KEL-02')->first();

        // 1. Mengakses create dengan query param kelompok_id via withQueryParams
        Livewire::actingAs($this->admin)
            ->withQueryParams(['kelompok_id' => $kel2->id])
            ->test(DonasiMasukCreate::class)
            ->assertSet('modeDonasi', 'kelompok')
            ->assertSet('kelompokKolektifId', $kel2->id)
            ->assertSee('Dr. Rahmat Hidayat'); // Koordinator Kelompok 2

        // 2. Mengakses via parameter mount
        Livewire::actingAs($this->admin)
            ->test(DonasiMasukCreate::class, ['kelompok_id' => $kel2->id])
            ->assertSet('modeDonasi', 'kelompok')
            ->assertSet('kelompokKolektifId', $kel2->id);
    }

    public function test_ekspor_excel_rekap_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        $spreadsheet = OtaExcelService::exportRekapMonitoringKelompok($this->ta->id);
        $this->assertNotNull($spreadsheet);
        $this->assertEquals('Rekap Kelompok OTA', $spreadsheet->getActiveSheet()->getTitle());

        Livewire::actingAs($this->admin)
            ->test(DonasiMasukIndex::class)
            ->set('tahunAjaranId', $this->ta->id)
            ->call('exportKelompokExcel')
            ->assertFileDownloaded();
    }
}
