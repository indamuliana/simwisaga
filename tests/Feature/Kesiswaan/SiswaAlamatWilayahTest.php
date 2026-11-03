<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\AnalitikSpmb\Models\MasterWilayah;
use App\Modules\Kesiswaan\Livewire\SiswaCreate;
use App\Modules\Kesiswaan\Livewire\SiswaEdit;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiswaAlamatWilayahTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_wilayah_kesiswaan',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        // Seed data wilayah master untuk pengujian
        MasterWilayah::create(['kode' => '32', 'nama' => 'Jawa Barat', 'level' => 1, 'parent_kode' => null]);
        MasterWilayah::create(['kode' => '32.05', 'nama' => 'Kab. Garut', 'level' => 2, 'parent_kode' => '32']);
        MasterWilayah::create(['kode' => '32.05.01', 'nama' => 'Tarogong Kaler', 'level' => 3, 'parent_kode' => '32.05']);
        MasterWilayah::create(['kode' => '32.05.01.2001', 'nama' => 'Pananjung', 'level' => 4, 'parent_kode' => '32.05.01']);

        MasterWilayah::create(['kode' => '33', 'nama' => 'Jawa Tengah', 'level' => 1, 'parent_kode' => null]);
        MasterWilayah::create(['kode' => '33.28', 'nama' => 'Kab. Tegal', 'level' => 2, 'parent_kode' => '33']);
        MasterWilayah::create(['kode' => '33.28.11', 'nama' => 'Adiwerna', 'level' => 3, 'parent_kode' => '33.28']);
        MasterWilayah::create(['kode' => '33.28.11.2001', 'nama' => 'Harjosari Kidul', 'level' => 4, 'parent_kode' => '33.28.11']);
    }

    public function test_edit_siswa_alamat_dalam_negeri_dengan_cascading(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Garut Asli',
            'nisn' => '1234567890',
            'nipd' => '1001',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SiswaEdit::class, ['id' => $siswa->id])
            ->set('activeTab', 'alamat')
            ->assertSet('cakupan_wilayah', 'dalam_negeri')
            ->set('provinsi', 'Jawa Barat')
            ->set('kabupaten_kota', 'Kab. Garut')
            ->set('kecamatan', 'Tarogong Kaler')
            ->set('kelurahan', 'Pananjung')
            ->set('alamat', 'Jl. Merdeka No. 10')
            ->set('rt', '02')
            ->set('rw', '05')
            ->set('dusun', 'Dusun Cikole')
            ->set('kode_pos', '44151')
            ->call('save')
            ->assertHasNoErrors();

        $siswa->refresh();
        $this->assertEquals('Indonesia', $siswa->negara);
        $this->assertEquals('Jawa Barat', $siswa->provinsi);
        $this->assertEquals('Kab. Garut', $siswa->kabupaten_kota);
        $this->assertEquals('Tarogong Kaler', $siswa->kecamatan);
        $this->assertEquals('Pananjung', $siswa->kelurahan);
        $this->assertEquals('02', $siswa->rt);
        $this->assertEquals('05', $siswa->rw);
        $this->assertEquals('Dusun Cikole', $siswa->dusun);
        $this->assertEquals('44151', $siswa->kode_pos);
    }

    public function test_smart_upward_fill_pada_edit_siswa(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Tegal Santri',
            'nisn' => '1234567891',
            'nipd' => '1002',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SiswaEdit::class, ['id' => $siswa->id])
            ->set('activeTab', 'alamat')
            ->set('kecamatan', 'Adiwerna')
            ->assertSet('kabupaten_kota', 'Kab. Tegal')
            ->assertSet('provinsi', 'Jawa Tengah')
            ->assertSet('negara', 'Indonesia');
    }

    public function test_edit_siswa_alamat_luar_negeri(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Siswa Asal Sabah',
            'nisn' => '1234567892',
            'nipd' => '1003',
            'jk' => 'P',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SiswaEdit::class, ['id' => $siswa->id])
            ->set('activeTab', 'alamat')
            ->set('cakupan_wilayah', 'luar_negeri')
            ->set('negara', 'Malaysia')
            ->set('provinsi', 'Sabah')
            ->set('kabupaten_kota', 'Tawau')
            ->set('alamat', 'CLC Ladang Holyrood, Km 15 Jalan Apas')
            ->set('kode_pos', '91000')
            ->call('save')
            ->assertHasNoErrors();

        $siswa->refresh();
        $this->assertEquals('Malaysia', $siswa->negara);
        $this->assertEquals('Sabah', $siswa->provinsi);
        $this->assertEquals('Tawau', $siswa->kabupaten_kota);
        $this->assertNull($siswa->rt);
        $this->assertNull($siswa->rw);
        $this->assertNull($siswa->dusun);
        $this->assertEquals('CLC Ladang Holyrood, Km 15 Jalan Apas', $siswa->alamat);
        $this->assertEquals('91000', $siswa->kode_pos);
    }

    public function test_tambah_siswa_baru_dengan_alamat_luar_negeri(): void
    {
        $rombel = Rombel::first();
        $rayon = Rayon::first();
        $program = Program::where('kode', 'REG')->first();

        Livewire::actingAs($this->admin)
            ->test(SiswaCreate::class)
            ->set('nama', 'Santri Internasional')
            ->set('nipd', '12208888')
            ->set('nisn', '0089999999')
            ->set('jk', 'L')
            ->set('tempat_lahir', 'Tawau')
            ->set('tanggal_lahir', '2008-01-01')
            ->set('rombel_id', $rombel?->id)
            ->set('rayon_id', $rayon?->id)
            ->set('program_id', $program?->id)
            ->set('cakupan_wilayah', 'luar_negeri')
            ->set('negara', 'Malaysia')
            ->set('provinsi', 'Sabah')
            ->set('kabupaten_kota', 'Sandakan')
            ->set('alamat', 'Konsulat RI Sandakan')
            ->set('kode_pos', '90000')
            ->call('save', true)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('siswas', [
            'nama' => 'Santri Internasional',
            'negara' => 'Malaysia',
            'provinsi' => 'Sabah',
            'kabupaten_kota' => 'Sandakan',
            'kode_pos' => '90000',
        ]);
    }
}
