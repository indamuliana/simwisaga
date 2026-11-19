<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Livewire\DonasiMasukCreate;
use App\Modules\BeasiswaOta\Livewire\OtaSettingIndex;
use App\Modules\BeasiswaOta\Livewire\PenyaluranIndex;
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

class OtaAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Semester $semGenap;
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
            'username' => 'admin_ota_fixes',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->regularUser = User::factory()->create([
            'username' => 'user_regular',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->firstOrFail();
        $this->semGenap = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'genap')->firstOrFail();

        $this->kelompok = Kelompok::create([
            'kode_kelompok' => 'KLP-FIX',
            'nama_kelompok' => 'Kelompok Audit Fix',
            'target_nominal' => 900000,
            'is_aktif' => true,
        ]);

        $this->donatur = Donatur::create([
            'nama' => 'Donatur Audit Fix',
            'tipe' => 'rutin',
            'nomor_telepon' => '081234567890',
            'is_aktif' => true,
        ]);

        $this->siswa = Siswa::create([
            'nama' => 'Siswa Audit Fix',
            'nisn' => '1122334455',
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

    public function test_penyaluran_semester_switching_updates_months_and_year(): void
    {
        $this->actingAs($this->admin);

        $lw = Livewire::test(PenyaluranIndex::class)
            ->set('rutinSemesterId', $this->semGanjil->id)
            ->set('rutinKelompokId', $this->kelompok->id);

        $details = $lw->get('rutinDetails');
        $this->assertNotEmpty($details);
        $this->assertEquals('Juli', $details[0]['bulan']);

        // Switch to genap
        $lw->set('rutinSemesterId', $this->semGenap->id);

        $detailsGenap = $lw->get('rutinDetails');
        $this->assertNotEmpty($detailsGenap);
        $this->assertEquals('Januari', $detailsGenap[0]['bulan']);
    }

    public function test_penyaluran_prevents_duplicate_disbursement_for_same_semester(): void
    {
        $this->actingAs($this->admin);

        // Berikan saldo cukup
        OtaKasService::catatDonasiMasuk(
            donatur: $this->donatur,
            nominal: 2000000,
            tanggal: now()->toDateString(),
            kelompokId: $this->kelompok->id,
            userId: $this->admin->id,
            tahunAjaranId: $this->ta->id
        );

        Livewire::test(PenyaluranIndex::class)
            ->set('rutinKelompokId', $this->kelompok->id)
            ->set('rutinSemesterId', $this->semGanjil->id)
            ->call('saveAlokasiRutin');

        $this->assertDatabaseHas('ota_alokasis', [
            'siswa_id' => $this->siswa->id,
            'semester_id' => $this->semGanjil->id,
        ]);

        // Coba salurkan lagi pada semester yang sama (harus ditolak, alokasi tetap 1)
        Livewire::test(PenyaluranIndex::class)
            ->set('rutinKelompokId', $this->kelompok->id)
            ->set('rutinSemesterId', $this->semGanjil->id)
            ->call('saveAlokasiRutin');

        $this->assertEquals(1, \App\Modules\BeasiswaOta\Models\Alokasi::where('siswa_id', $this->siswa->id)->where('semester_id', $this->semGanjil->id)->count());
    }

    public function test_donasi_masuk_rejects_already_paid_months(): void
    {
        $this->actingAs($this->admin);

        // Catat donasi Juli
        OtaKasService::catatDonasiMasuk(
            donatur: $this->donatur,
            nominal: 150000,
            tanggal: now()->toDateString(),
            kelompokId: $this->kelompok->id,
            userId: $this->admin->id,
            tahunAjaranId: $this->ta->id,
            periodeBulan: ['2026-07'],
            jumlahBulan: 1
        );

        $this->assertEquals(1, DonasiMasuk::count());

        // Coba input kembali bulan Juli via Livewire
        Livewire::test(DonasiMasukCreate::class)
            ->set('modeDonasi', 'perorangan')
            ->set('donaturId', $this->donatur->id)
            ->set('kelompokId', $this->kelompok->id)
            ->set('tahunAjaranId', $this->ta->id)
            ->set('selectedBulanPerorangan', ['2026-07'])
            ->set('nominal', 150000)
            ->call('save');

        // Jumlah transaksi tetap 1, tidak bertambah karena duplikat bulan ditolak
        $this->assertEquals(1, DonasiMasuk::count());
    }

    public function test_remove_template_bg_requires_permission(): void
    {
        $this->actingAs($this->regularUser);

        Livewire::test(OtaSettingIndex::class)
            ->assertStatus(403);
    }
}
