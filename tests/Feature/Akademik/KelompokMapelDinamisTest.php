<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\MapelIndex;
use App\Modules\Akademik\Models\KelompokMapel;
use App\Modules\Akademik\Models\Mapel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class KelompokMapelDinamisTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->seed(\Database\Seeders\AkademikInitialSeeder::class);

        $this->admin = User::first();
    }

    public function test_halaman_mapel_memiliki_tab_kelompok_mapel(): void
    {
        Livewire::actingAs($this->admin)
            ->test(MapelIndex::class)
            ->assertSet('activeTab', 'mapel')
            ->assertSee('Daftar Mata Pelajaran')
            ->assertSee('Kelompok Mapel')
            ->call('switchTab', 'kelompok')
            ->assertSet('activeTab', 'kelompok')
            ->assertSee('Tambah Kelompok')
            ->assertSee('Kode Header')
            ->assertSee('Urutan Rapor');
    }

    public function test_admin_dapat_menambah_dan_mengubah_kelompok_mapel_dinamis(): void
    {
        Livewire::actingAs($this->admin)
            ->test(MapelIndex::class)
            ->call('switchTab', 'kelompok')
            ->call('createKelompok')
            ->set('kelompokKode', 'E')
            ->set('kelompokNama', 'Muatan Peminatan Khusus')
            ->set('kelompokUrutan', 5)
            ->set('kelompokIsActive', true)
            ->call('saveKelompok')
            ->assertHasNoErrors()
            ->assertSet('showKelompokModal', false);

        $this->assertDatabaseHas('kelompok_mapels', [
            'kode' => 'E',
            'nama' => 'Muatan Peminatan Khusus',
            'urutan' => 5,
        ]);

        $kelompok = KelompokMapel::where('kode', 'E')->firstOrFail();

        // Edit kelompok
        Livewire::actingAs($this->admin)
            ->test(MapelIndex::class)
            ->call('editKelompok', $kelompok->id)
            ->set('kelompokNama', 'Muatan Peminatan Unggulan')
            ->call('saveKelompok')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kelompok_mapels', [
            'id' => $kelompok->id,
            'nama' => 'Muatan Peminatan Unggulan',
        ]);
    }

    public function test_kelompok_mapel_tidak_dapat_dihapus_jika_masih_memiliki_mapel(): void
    {
        $kelompokUmum = KelompokMapel::where('kode', 'A')->firstOrFail();

        // Pastikan kelompok umum memiliki mapel
        $this->assertTrue(Mapel::where('kelompok_mapel_id', $kelompokUmum->id)->exists());

        Livewire::actingAs($this->admin)
            ->test(MapelIndex::class)
            ->call('deleteKelompok', $kelompokUmum->id)
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('kelompok_mapels', [
            'id' => $kelompokUmum->id,
        ]);
    }

    public function test_penomoran_urutan_mapel_otomatis_menyarankan_nomor_berikutnya_per_kelompok(): void
    {
        $kelompokUmum = KelompokMapel::where('kode', 'A')->firstOrFail();
        $maxUrutanSaatIni = Mapel::where('kelompok_mapel_id', $kelompokUmum->id)->max('urutan') ?? 0;

        Livewire::actingAs($this->admin)
            ->test(MapelIndex::class)
            ->call('create')
            ->set('kelompok_mapel_id', $kelompokUmum->id)
            ->assertSet('urutan', $maxUrutanSaatIni + 1);
    }
}
