<?php

namespace Tests\Feature\Kepegawaian;

use App\Models\User;
use App\Modules\Kepegawaian\Livewire\StrukturOrganisasiIndex;
use App\Modules\Kepegawaian\Models\Jabatan;
use App\Modules\Kepegawaian\Models\Pegawai;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StrukturOrganisasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Pegawai $pegawai1;
    protected Pegawai $pegawai2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_org_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->pegawai1 = Pegawai::create([
            'nama_lengkap' => 'Budi Santoso',
            'gelar_depan' => 'Drs.',
            'gelar_belakang' => 'M.Pd.',
            'jenis_kelamin' => 'L',
            'jenis_gtk' => 'Guru Mapel',
            'status_kepegawaian' => 'GTY',
            'is_active' => true,
        ]);

        $this->pegawai2 = Pegawai::create([
            'nama_lengkap' => 'Siti Aminah',
            'gelar_belakang' => 'S.Kom.',
            'jenis_kelamin' => 'P',
            'jenis_gtk' => 'Tenaga Kependidikan',
            'status_kepegawaian' => 'PTY',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_struktur_organisasi_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kepegawaian.struktur-organisasi'));
        $response->assertOk();
        $response->assertSee('Struktur Organisasi');
        $response->assertSee('Daftar Jabatan');
        $response->assertSee('Bagan Organigram');
        $response->assertSee('Tambah Jabatan Baru');
    }

    public function test_can_create_parent_and_child_jabatan_with_pejabats(): void
    {
        // 1. Buat Jabatan Pucuk: Kepala Sekolah (24 Jam)
        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('openCreateModal')
            ->set('nama', 'Kepala Sekolah')
            ->set('kode', 'KS')
            ->set('bobot_jam', 24)
            ->set('pejabatList', [
                [
                    'pegawai_id' => $this->pegawai1->id,
                    'nama_pejabat' => '',
                    'keterangan' => 'Periode 2024-2028',
                ]
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('jabatans', [
            'nama' => 'Kepala Sekolah',
            'kode' => 'KS',
            'bobot_jam' => 24,
            'parent_id' => null,
        ]);

        $ks = Jabatan::where('kode', 'KS')->firstOrFail();
        $this->assertEquals(1, $ks->pejabats()->count());
        $this->assertEquals($this->pegawai1->id, $ks->pejabats->first()->pegawai_id);

        // 2. Buat Jabatan Anak: Wakasek Kurikulum (12 Jam) di bawah Kepala Sekolah
        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('openCreateModal', $ks->id)
            ->set('nama', 'Wakasek Kurikulum')
            ->set('kode', 'WAKA-KUR')
            ->set('parent_id', $ks->id)
            ->set('bobot_jam', 12)
            ->set('pejabatList', [
                [
                    'pegawai_id' => '',
                    'nama_pejabat' => 'Dr. Hendrawan (Konsultan Eksternal)',
                    'keterangan' => 'Tenaga Ahli Luar',
                ]
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('jabatans', [
            'nama' => 'Wakasek Kurikulum',
            'kode' => 'WAKA-KUR',
            'bobot_jam' => 12,
            'parent_id' => $ks->id,
        ]);

        $waka = Jabatan::where('kode', 'WAKA-KUR')->firstOrFail();
        $this->assertEquals(1, $waka->pejabats()->count());
        $this->assertNull($waka->pejabats->first()->pegawai_id);
        $this->assertEquals('Dr. Hendrawan (Konsultan Eksternal)', $waka->pejabats->first()->nama_pejabat);
    }

    public function test_can_reorder_sibling_jabatans_using_move_up_and_down(): void
    {
        $ks = Jabatan::create([
            'nama' => 'Kepala Sekolah',
            'urutan' => 10,
        ]);

        $waka1 = Jabatan::create([
            'nama' => 'Waka Kurikulum',
            'parent_id' => $ks->id,
            'urutan' => 10,
        ]);

        $waka2 = Jabatan::create([
            'nama' => 'Waka Kesiswaan',
            'parent_id' => $ks->id,
            'urutan' => 20,
        ]);

        $waka3 = Jabatan::create([
            'nama' => 'Waka Humas',
            'parent_id' => $ks->id,
            'urutan' => 30,
        ]);

        // Naikkan Waka Humas (urutan 30) ke atas Waka Kesiswaan (urutan 20)
        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('moveUp', $waka3->id);

        $waka3->refresh();
        $waka2->refresh();

        $this->assertEquals(20, $waka3->urutan);
        $this->assertEquals(30, $waka2->urutan);

        // Turunkan Waka Kurikulum (urutan 10) ke bawah Waka Humas (urutan 20)
        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('moveDown', $waka1->id);

        $waka1->refresh();
        $waka3->refresh();

        $this->assertEquals(20, $waka1->urutan);
        $this->assertEquals(10, $waka3->urutan);
    }

    public function test_cannot_delete_jabatan_with_children(): void
    {
        $parent = Jabatan::create(['nama' => 'Direktur']);
        $child = Jabatan::create(['nama' => 'Manajer', 'parent_id' => $parent->id]);

        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('delete', $parent->id)
            ->assertSee('tidak dapat dihapus karena masih memiliki sub-jabatan');

        $this->assertDatabaseHas('jabatans', ['id' => $parent->id]);

        // Hapus anak terlebih dahulu
        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('delete', $child->id);

        $this->assertDatabaseMissing('jabatans', ['id' => $child->id]);

        // Sekarang parent dapat dihapus
        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('delete', $parent->id);

        $this->assertDatabaseMissing('jabatans', ['id' => $parent->id]);
    }

    public function test_bagan_organigram_tab_renders_hierarchical_nested_tree(): void
    {
        $ks = Jabatan::create(['nama' => 'Kepala Sekolah', 'kode' => 'KS', 'bobot_jam' => 24]);
        $waka = Jabatan::create(['nama' => 'Waka Kurikulum', 'kode' => 'WAKUR', 'parent_id' => $ks->id, 'bobot_jam' => 12]);

        Livewire::actingAs($this->admin)
            ->test(StrukturOrganisasiIndex::class)
            ->call('setTab', 'bagan')
            ->assertSet('activeTab', 'bagan')
            ->assertSee('Bagan Organigram')
            ->assertSee('Kepala Sekolah')
            ->assertSee('Waka Kurikulum')
            ->assertSee('24.0J')
            ->assertSee('12.0J');
    }
}
