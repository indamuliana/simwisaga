<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukCreate;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Database\Seeders\DummyOtaKelompokSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DonasiMultiKelompokStatusLunasTest extends TestCase
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

    public function test_dummy_seeder_creates_4_kelompok_dan_inda_in_3_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        // Harus ada 4 kelompok
        $this->assertEquals(4, Kelompok::count());

        $kel1 = Kelompok::where('kode_kelompok', 'KEL-01')->first();
        $kel2 = Kelompok::where('kode_kelompok', 'KEL-02')->first();
        $kel3 = Kelompok::where('kode_kelompok', 'KEL-03')->first();
        $kel4 = Kelompok::where('kode_kelompok', 'KEL-04')->first();

        $this->assertNotNull($kel1);
        $this->assertNotNull($kel2);
        $this->assertNotNull($kel3);
        $this->assertNotNull($kel4);

        // Donatur Inda Muliana harus terdaftar di kelompok 1, 2, 3 tapi tidak di kelompok 4
        $inda = Donatur::where('nama', 'Inda Muliana')->first();
        $this->assertNotNull($inda);
        $this->assertCount(3, $inda->kelompoks);

        $this->assertTrue($kel1->donaturs->contains('id', $inda->id));
        $this->assertTrue($kel2->donaturs->contains('id', $inda->id));
        $this->assertTrue($kel3->donaturs->contains('id', $inda->id));
        $this->assertFalse($kel4->donaturs->contains('id', $inda->id));
    }

    public function test_setor_di_kelompok_1_tidak_membuat_lunas_di_kelompok_2(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        $kel1 = Kelompok::where('kode_kelompok', 'KEL-01')->first();
        $kel2 = Kelompok::where('kode_kelompok', 'KEL-02')->first();
        $inda = Donatur::where('nama', 'Inda Muliana')->first();

        // 1. Simulasikan setor via koordinator di Kelompok 1 untuk Inda Muliana (bulan 2026-07)
        OtaKasService::catatDonasiKolektifKelompok(
            kelompok: $kel1,
            koordinatorNama: 'H. Ahmad Fauzi',
            items: [
                [
                    'donatur_id' => $inda->id,
                    'nominal' => 150000,
                    'periode_bulan' => ['2026-07'],
                    'jumlah_bulan' => 1,
                    'keterangan_periode' => "Jul'26 (1 Bulan)",
                ]
            ],
            tanggal: '2026-07-10',
            userId: $this->admin->id,
            tahunAjaranId: $this->ta->id
        );

        // Pastikan donasi tercatat di DB dengan kelompok_id = Kelompok 1
        $donasiKel1 = DonasiMasuk::where('donatur_id', $inda->id)->where('kelompok_id', $kel1->id)->first();
        $this->assertNotNull($donasiKel1);
        $this->assertEquals(['2026-07'], $donasiKel1->periode_bulan);

        // 2. Cek getPaidMonthsForDonatur untuk Kelompok 1 dan Kelompok 2
        $paidKel1 = DonasiMasukCreate::getPaidMonthsForDonatur($inda->id, $this->ta->id, $kel1->id);
        $this->assertContains('2026-07', $paidKel1);

        $paidKel2 = DonasiMasukCreate::getPaidMonthsForDonatur($inda->id, $this->ta->id, $kel2->id);
        $this->assertNotContains('2026-07', $paidKel2, 'Inda di Kelompok 2 tidak boleh lunas pada 2026-07');
        $this->assertEmpty($paidKel2);

        // 3. Test Livewire komponen DonasiMasukCreate pada Mode Kolektif Kelompok
        $component = Livewire::actingAs($this->admin)
            ->test(DonasiMasukCreate::class)
            ->set('modeDonasi', 'kelompok')
            ->set('tahunAjaranId', $this->ta->id);

        // Saat memilih Kelompok 1: Inda Muliana harus terdeteksi sudah lunas 2026-07
        $component->set('kelompokKolektifId', $kel1->id);
        $anggotaKel1 = $component->get('anggotaItems');
        $indaItemKel1 = collect($anggotaKel1)->firstWhere('donatur_id', $inda->id);
        $this->assertNotNull($indaItemKel1);
        $this->assertContains('2026-07', $indaItemKel1['paid_bulan']);

        // Saat beralih memilih Kelompok 2: Inda Muliana TIDAK BOLEH lunas pada 2026-07
        $component->set('kelompokKolektifId', $kel2->id);
        $anggotaKel2 = $component->get('anggotaItems');
        $indaItemKel2 = collect($anggotaKel2)->firstWhere('donatur_id', $inda->id);
        $this->assertNotNull($indaItemKel2);
        $this->assertNotContains('2026-07', $indaItemKel2['paid_bulan'], 'Pada Kelompok 2, 2026-07 harus belum lunas');

        // Inda di Kelompok 2 harus bisa memilih bulan 2026-07 dan setor donasi
        $indaIndexKel2 = collect($anggotaKel2)->search(fn($item) => $item['donatur_id'] === $inda->id);
        $component->call('toggleMemberMonth', $indaIndexKel2, '2026-07');

        $updatedAnggotaKel2 = $component->get('anggotaItems');
        $this->assertContains('2026-07', $updatedAnggotaKel2[$indaIndexKel2]['selected_bulan']);
        $this->assertEquals(150000, $updatedAnggotaKel2[$indaIndexKel2]['nominal']);
    }

    public function test_mode_perorangan_lunas_status_per_kelompok(): void
    {
        $this->seed(DummyOtaKelompokSeeder::class);

        $kel1 = Kelompok::where('kode_kelompok', 'KEL-01')->first();
        $kel2 = Kelompok::where('kode_kelompok', 'KEL-02')->first();
        $inda = Donatur::where('nama', 'Inda Muliana')->first();

        // Catat donasi di Kelompok 1 untuk Inda
        OtaKasService::catatDonasiMasuk(
            donatur: $inda,
            nominal: 150000,
            tanggal: '2026-07-15',
            kelompokId: $kel1->id,
            tahunAjaranId: $this->ta->id,
            periodeBulan: ['2026-07']
        );

        // Livewire Mode Perorangan
        $component = Livewire::actingAs($this->admin)
            ->test(DonasiMasukCreate::class)
            ->set('modeDonasi', 'perorangan')
            ->set('tahunAjaranId', $this->ta->id)
            ->set('donaturId', $inda->id);

        // Jika kelompokId adalah Kelompok 1, paidMonthsPerorangan berisi 2026-07
        $component->set('kelompokId', $kel1->id);
        $this->assertContains('2026-07', $component->get('paidMonthsPerorangan'));

        // Jika diubah ke Kelompok 2, paidMonthsPerorangan TIDAK berisi 2026-07
        $component->set('kelompokId', $kel2->id);
        $this->assertNotContains('2026-07', $component->get('paidMonthsPerorangan'));

        // Bisa memilih 2026-07 untuk Kelompok 2
        $component->call('toggleBulanPerorangan', '2026-07');
        $this->assertContains('2026-07', $component->get('selectedBulanPerorangan'));
    }
}
