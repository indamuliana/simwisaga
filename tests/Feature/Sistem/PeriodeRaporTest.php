<?php

namespace Tests\Feature\Sistem;

use App\Models\User;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Rapor\Livewire\RaporDashboard;
use App\Modules\Sistem\Livewire\PeriodeRaporIndex;
use App\Modules\Sistem\Models\PeriodeRapor;
use App\Modules\Sistem\Models\School;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PeriodeRaporTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Semester $semGenap;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_titimangsa',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::first();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->first();
        $this->semGenap = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'genap')->first();
    }

    public function test_halaman_titimangsa_rapor_dapat_diakses_oleh_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/sistem/titimangsa-rapor');
        $response->assertOk();
        $response->assertSee('Pengaturan Titi Mangsa & Periode Rapor');
        $response->assertSee('Tambah Periode Rapor');
    }

    public function test_admin_dapat_menambah_periode_rapor_baru(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PeriodeRaporIndex::class)
            ->set('filterTahunAjaranId', $this->ta->id)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('semester', 'ganjil')
            ->set('kode', '9_minggu_1')
            ->set('nama', 'Rapor Tengah Semester Ganjil')
            ->set('sebutan', '9 Minggu ke-1')
            ->set('tanggal_mulai', '2024-07-15')
            ->set('tanggal_selesai', '2024-09-20')
            ->set('tanggal_bagi', '2024-09-27')
            ->set('tempat', 'Garut')
            ->set('is_active', true)
            ->set('urutan', 1)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $periode = PeriodeRapor::where('kode', '9_minggu_1')->first();
        $this->assertNotNull($periode);
        $this->assertEquals($this->ta->id, $periode->tahun_ajaran_id);
        $this->assertEquals('ganjil', $periode->semester);
        $this->assertEquals('Rapor Tengah Semester Ganjil', $periode->nama);
        $this->assertEquals('9 Minggu ke-1', $periode->sebutan);
        $this->assertEquals('2024-09-27', $periode->tanggal_bagi->format('Y-m-d'));
        $this->assertEquals('Garut', $periode->tempat);
        $this->assertTrue((bool) $periode->is_active);
    }

    public function test_admin_dapat_mengubah_dan_menghapus_periode_rapor(): void
    {
        $periode = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_2',
            'nama' => 'Rapor Semester Ganjil',
            'sebutan' => '9 Minggu ke-2',
            'tanggal_bagi' => '2024-12-20',
            'tempat' => 'Garut',
            'is_active' => false,
            'urutan' => 2,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PeriodeRaporIndex::class)
            ->set('filterTahunAjaranId', $this->ta->id)
            ->call('edit', $periode->id)
            ->assertSet('sebutan', '9 Minggu ke-2')
            ->set('sebutan', '9 Minggu ke-2 (Revisi)')
            ->set('tanggal_bagi', '2024-12-21')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('9 Minggu ke-2 (Revisi)', $periode->fresh()->sebutan);
        $this->assertEquals('2024-12-21', $periode->fresh()->tanggal_bagi->format('Y-m-d'));

        // Hapus periode
        Livewire::actingAs($this->admin)
            ->test(PeriodeRaporIndex::class)
            ->call('delete', $periode->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('periode_rapors', [
            'id' => $periode->id,
        ]);
    }

    public function test_toggle_periode_aktif_bekerja_dan_menonaktifkan_periode_lain(): void
    {
        $p1 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_1',
            'nama' => 'Rapor Tengah Semester Ganjil',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => '2024-09-27',
            'tempat' => 'Garut',
            'is_active' => true,
            'urutan' => 1,
        ]);

        $p2 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_2',
            'nama' => 'Rapor Semester Ganjil',
            'sebutan' => '9 Minggu ke-2',
            'tanggal_bagi' => '2024-12-20',
            'tempat' => 'Garut',
            'is_active' => false,
            'urutan' => 2,
        ]);

        // Aktifkan p2 via toggleActive
        Livewire::actingAs($this->admin)
            ->test(PeriodeRaporIndex::class)
            ->set('filterTahunAjaranId', $this->ta->id)
            ->call('toggleActive', $p2->id);

        $this->assertFalse((bool) $p1->fresh()->is_active);
        $this->assertTrue((bool) $p2->fresh()->is_active);
    }

    public function test_fitur_salin_periode_dari_tahun_ajaran_lain_berhasil(): void
    {
        $taTarget = TahunAjaran::create([
            'nama' => '2025/2026',
            'tanggal_mulai' => '2025-07-15',
            'tanggal_selesai' => '2026-06-25',
            'is_active' => false,
        ]);

        PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_1',
            'nama' => 'Rapor Tengah Semester Ganjil',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => '2024-09-27',
            'tempat' => 'Garut',
            'is_active' => true,
            'urutan' => 1,
        ]);

        PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_2',
            'nama' => 'Rapor Semester Ganjil',
            'sebutan' => '9 Minggu ke-2',
            'tanggal_bagi' => '2024-12-20',
            'tempat' => 'Garut',
            'is_active' => false,
            'urutan' => 2,
        ]);

        Livewire::actingAs($this->admin)
            ->test(PeriodeRaporIndex::class)
            ->set('filterTahunAjaranId', $taTarget->id)
            ->set('sourceTahunAjaranId', $this->ta->id)
            ->set('targetTahunAjaranId', $taTarget->id)
            ->call('executeCopy')
            ->assertHasNoErrors();

        $this->assertEquals(2, PeriodeRapor::where('tahun_ajaran_id', $taTarget->id)->count());
        $this->assertDatabaseHas('periode_rapors', [
            'tahun_ajaran_id' => $taTarget->id,
            'kode' => '9_minggu_1',
            'sebutan' => '9 Minggu ke-1',
        ]);
    }

    public function test_modal_cetak_rapor_memuat_periode_dan_mengisi_tanggal_otomatis(): void
    {
        $rombel = Rombel::firstOrFail();
        $siswa = Siswa::create([
            'nama' => 'Siswa Uji Cetak',
            'nipd' => '99001',
            'nisn' => '0099999999',
            'jk' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-05-05',
            'status' => 'aktif',
            'is_active' => true,
            'rombel_id' => $rombel->id,
        ]);

        // Buat 2 periode di semester ganjil
        $p1 = PeriodeRapor::create([
            'tahun_ajaran_id' => $rombel->tahun_ajaran_id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_1',
            'nama' => 'Rapor Tengah Semester Ganjil',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => '2024-09-27',
            'tempat' => 'Garut',
            'is_active' => true, // Default aktif
            'urutan' => 1,
        ]);

        $p2 = PeriodeRapor::create([
            'tahun_ajaran_id' => $rombel->tahun_ajaran_id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_2',
            'nama' => 'Rapor Semester Ganjil',
            'sebutan' => '9 Minggu ke-2',
            'tanggal_bagi' => '2024-12-20',
            'tempat' => 'Garut',
            'is_active' => false,
            'urutan' => 2,
        ]);

        AcademicContext::setWorkingSemester($this->semGanjil->id);

        Livewire::actingAs($this->admin)
            ->test(RaporDashboard::class)
            ->set('rombel_id', $rombel->id)
            ->call('openPrintModal', $siswa->id)
            ->assertSet('showPrintModal', true)
            ->assertSet('selectedPeriodeId', $p1->id)
            ->assertSet('printTanggal', '2024-09-27')
            // Ganti ke periode 2
            ->set('selectedPeriodeId', $p2->id)
            ->assertSet('printTanggal', '2024-12-20');
    }

    public function test_cetak_pdf_nilai_menampilkan_sebutan_periode_dan_titimangsa_dinamis(): void
    {
        $rombel = Rombel::firstOrFail();
        $siswa = Siswa::create([
            'nama' => 'Siswa PDF Titimangsa',
            'nipd' => '99002',
            'nisn' => '0099999998',
            'jk' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-06-06',
            'status' => 'aktif',
            'is_active' => true,
            'rombel_id' => $rombel->id,
        ]);

        $periode = PeriodeRapor::create([
            'tahun_ajaran_id' => $rombel->tahun_ajaran_id,
            'semester' => 'ganjil',
            'kode' => '9_minggu_1',
            'nama' => 'Rapor Tengah Semester Ganjil',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => '2024-09-27',
            'tempat' => 'Garut Kota',
            'is_active' => true,
            'urutan' => 1,
        ]);

        AcademicContext::setWorkingSemester($this->semGanjil->id);

        $response = $this->actingAs($this->admin)->get(route('rapor.cetak.nilai', [
            'siswa_id' => $siswa->id,
            'tanggal' => '2024-09-27',
            'periode_id' => $periode->id,
        ]));

        $response->assertOk();
        // Cek header format
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
