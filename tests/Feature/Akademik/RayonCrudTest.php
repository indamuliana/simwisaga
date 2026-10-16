<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\RayonIndex;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RayonCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guru;
    protected Rayon $rayon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_rayon',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->guru = User::factory()->create([
            'name' => 'Budi Pembina Rayon',
            'username' => 'budi_pembina',
            'is_active' => true,
        ]);
        $this->guru->assignRole('Guru');

        $this->rayon = Rayon::firstOrCreate(
            ['nama' => 'Bales 1'],
            [
                'wilayah_cakupan' => 'Banyuresmi, Leles',
                'pembimbing_id' => $this->guru->id,
                'keterangan' => 'Wilayah Banyuresmi',
            ]
        );
    }

    public function test_halaman_rayon_tampil_dengan_tombol_aksi_lengkap(): void
    {
        $this->actingAs($this->admin)
            ->get(route('akademik.rayon'))
            ->assertOk()
            ->assertSee('Pengelolaan Rayon')
            ->assertSee('Bales 1')
            ->assertSee('Lihat')
            ->assertSee('Edit')
            ->assertSee('Hapus');
    }

    public function test_pencarian_rayon(): void
    {
        Rayon::create([
            'nama' => 'Ciawi 1',
            'wilayah_cakupan' => 'Ciawi Gebang',
            'keterangan' => 'Khusus wilayah timur',
        ]);

        Livewire::actingAs($this->admin, 'web')
            ->test(RayonIndex::class)
            ->set('search', 'Ciawi')
            ->assertSee('Ciawi 1')
            ->assertDontSee('Bales 1');
    }

    public function test_fitur_tambah_rayon(): void
    {
        Livewire::actingAs($this->admin, 'web')
            ->test(RayonIndex::class)
            ->call('create')
            ->assertSet('showModal', true)
            ->set('nama', 'Kadungora 1')
            ->set('wilayah_cakupan', 'Kadungora, Karangpawitan')
            ->set('pembimbing_id', $this->guru->id)
            ->set('keterangan', 'Rayon utara')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('Rayon Kadungora 1 berhasil disimpan.');

        $this->assertDatabaseHas('rayons', [
            'nama' => 'Kadungora 1',
            'wilayah_cakupan' => 'Kadungora, Karangpawitan',
            'pembimbing_id' => $this->guru->id,
        ]);
    }

    public function test_fitur_edit_rayon(): void
    {
        Livewire::actingAs($this->admin, 'web')
            ->test(RayonIndex::class)
            ->call('edit', $this->rayon->id)
            ->assertSet('showModal', true)
            ->assertSet('nama', 'Bales 1')
            ->set('nama', 'Bales 1 Revisi')
            ->set('wilayah_cakupan', 'Banyuresmi & Leles Terpadu')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('Rayon Bales 1 Revisi berhasil disimpan.');

        $this->assertDatabaseHas('rayons', [
            'id' => $this->rayon->id,
            'nama' => 'Bales 1 Revisi',
            'wilayah_cakupan' => 'Banyuresmi & Leles Terpadu',
        ]);
    }

    public function test_fitur_lihat_detail_rayon_menampilkan_siswa_dan_statistik(): void
    {
        $ta = TahunAjaran::first();
        $jurusan = Jurusan::first();
        $rombel = Rombel::firstOrCreate(
            ['nama' => 'X PPLG 1'],
            [
                'tahun_ajaran_id' => $ta->id,
                'jurusan_id' => $jurusan->id,
                'tingkat' => 'X',
                'nomor_urut' => 1,
            ]
        );

        $siswa = Siswa::create([
            'nama' => 'Ahmad Fulan Siswa Bales',
            'nipd' => '2425001',
            'nisn' => '0098765432',
            'jk' => 'L',
            'rombel_id' => $rombel->id,
            'rayon_id' => $this->rayon->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin, 'web')
            ->test(RayonIndex::class)
            ->call('showDetail', $this->rayon->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('detailRayonId', $this->rayon->id)
            ->assertSee('Rayon Bales 1')
            ->assertSee('Ahmad Fulan Siswa Bales')
            ->assertSee('2425001')
            ->assertSee('X PPLG 1')
            ->call('closeDetail')
            ->assertSet('showDetailModal', false);
    }

    public function test_fitur_hapus_rayon_tanpa_siswa(): void
    {
        $rayonKosong = Rayon::create([
            'nama' => 'Rayon Sementara',
            'wilayah_cakupan' => 'Zona Cadangan',
        ]);

        Livewire::actingAs($this->admin, 'web')
            ->test(RayonIndex::class)
            ->call('delete', $rayonKosong->id)
            ->assertSee("Rayon 'Rayon Sementara' berhasil dihapus.");

        $this->assertDatabaseMissing('rayons', [
            'id' => $rayonKosong->id,
        ]);
    }

    public function test_tidak_dapat_menghapus_rayon_yang_masih_memiliki_siswa(): void
    {
        Siswa::create([
            'nama' => 'Siti Aminah',
            'nipd' => '2425002',
            'nisn' => '0098765433',
            'jk' => 'P',
            'rayon_id' => $this->rayon->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin, 'web')
            ->test(RayonIndex::class)
            ->call('delete', $this->rayon->id)
            ->assertSee("Rayon 'Bales 1' tidak dapat dihapus karena masih memiliki 1 siswa terdaftar.");

        $this->assertDatabaseHas('rayons', [
            'id' => $this->rayon->id,
            'nama' => 'Bales 1',
        ]);
    }
}
