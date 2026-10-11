<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\StrukturKurikulumIndex;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StrukturKurikulumDuplicateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta1;
    protected TahunAjaran $ta2;
    protected Jurusan $jurusan;
    protected Mapel $mapel1;
    protected Mapel $mapel2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kurikulum',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta1 = TahunAjaran::first();
        $this->ta2 = TahunAjaran::create([
            'nama' => '2026/2027',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => '2027-06-25',
            'is_active' => false,
        ]);

        $this->jurusan = Jurusan::first();
        $this->mapel1 = Mapel::first();
        $this->mapel2 = Mapel::skip(1)->first();

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_per_minggu' => 4,
            'urutan' => 1,
        ]);

        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta1->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel2->id,
            'jp_per_minggu' => 2,
            'urutan' => 2,
        ]);
    }

    public function test_modal_duplikasi_terbuka_dengan_perhitungan_mapel_benar(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->call('openDuplicateModal')
            ->assertSet('showDuplicateModal', true)
            ->set('sourceTahunAjaranId', $this->ta1->id)
            ->set('targetTahunAjaranId', $this->ta2->id)
            ->assertSet('sourceKurikulumCount', 2);
    }

    public function test_eksekusi_duplikasi_struktur_kurikulum_berhasil_menyalin_mapel(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('sourceTahunAjaranId', $this->ta1->id)
            ->set('targetTahunAjaranId', $this->ta2->id)
            ->set('duplicateJurusanId', (string) $this->jurusan->id)
            ->call('executeDuplicate')
            ->assertHasNoErrors()
            ->assertSee('Berhasil menyalin struktur kurikulum!');

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_per_minggu' => 4,
            'urutan' => 1,
        ]);

        $this->assertDatabaseHas('struktur_kurikulums', [
            'tahun_ajaran_id' => $this->ta2->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel2->id,
            'jp_per_minggu' => 2,
            'urutan' => 2,
        ]);

        // Duplikasi ulang harus bersifat idempoten (mengupdate tanpa duplikasi error)
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('sourceTahunAjaranId', $this->ta1->id)
            ->set('targetTahunAjaranId', $this->ta2->id)
            ->call('executeDuplicate')
            ->assertHasNoErrors();

        $this->assertEquals(2, StrukturKurikulum::where('tahun_ajaran_id', $this->ta2->id)->count());
    }

    public function test_duplikasi_menolak_sumber_dan_tujuan_yang_sama(): void
    {
        Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('sourceTahunAjaranId', $this->ta1->id)
            ->set('targetTahunAjaranId', $this->ta1->id)
            ->call('executeDuplicate')
            ->assertSee('Tahun Ajaran sumber dan tujuan tidak boleh sama.');
    }
}
