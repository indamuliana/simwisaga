<?php

namespace Tests\Feature\DashboardData;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\DashboardData\Livewire\Index as DashboardIndex;
use App\Modules\DashboardData\Livewire\Rombel\Index as DashboardRombelIndex;
use App\Modules\Akademik\Livewire\RombelIndex;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardDataRombelCountTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $taAktif;
    protected TahunAjaran $taLain;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->taAktif = TahunAjaran::where('is_active', true)->firstOrFail();

        // Buat Tahun Ajaran baru yang tidak aktif
        $this->taLain = TahunAjaran::create([
            'tahun' => '2030/2031',
            'nama' => '2030/2031',
            'tgl_mulai' => '2030-07-01',
            'tgl_selesai' => '2031-06-30',
            'is_active' => false,
        ]);

        $jurusan = Jurusan::first();

        // Tambah rombel di TA lain yang tidak aktif
        Rombel::create([
            'tahun_ajaran_id' => $this->taLain->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X ' . $jurusan->kode . ' 1 (TA Lain)',
            'kapasitas' => 36,
        ]);
        Rombel::create([
            'tahun_ajaran_id' => $this->taLain->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 2,
            'nama' => 'X ' . $jurusan->kode . ' 2 (TA Lain)',
            'kapasitas' => 36,
        ]);
    }

    public function test_dashboard_data_hanya_menghitung_rombel_tahun_ajaran_aktif(): void
    {
        $rombelAktifCount = Rombel::where('tahun_ajaran_id', $this->taAktif->id)->count();
        $totalRombelAll = Rombel::count();

        // Pastikan total di DB lebih banyak karena ada rombel di TA Lain
        $this->assertGreaterThan($rombelAktifCount, $totalRombelAll);

        // Dashboard Data harus menampilkan jumlah rombel T.A. Aktif, BUKAN total seluruh TA
        Livewire::actingAs($this->admin)
            ->test(DashboardIndex::class)
            ->assertViewHas('totalRombel', $rombelAktifCount)
            ->assertSee((string) $rombelAktifCount)
            ->assertSee('T.A. Aktif');
    }

    public function test_dashboard_data_rombel_dan_akademik_rombel_konsisten_dengan_dashboard_data(): void
    {
        $rombelAktifCount = Rombel::where('tahun_ajaran_id', $this->taAktif->id)->count();

        // 1. Dashboard Data
        Livewire::actingAs($this->admin)
            ->test(DashboardIndex::class)
            ->assertViewHas('totalRombel', $rombelAktifCount);

        // 2. Dashboard Data / Rombel
        Livewire::actingAs($this->admin)
            ->test(DashboardRombelIndex::class)
            ->assertSet('filterTahunAjaran', $this->taAktif->id)
            ->assertViewHas('rombels', function ($rombels) use ($rombelAktifCount) {
                return $rombels->total() === $rombelAktifCount;
            });

        // 3. Akademik / Rombel
        Livewire::actingAs($this->admin)
            ->test(RombelIndex::class)
            ->assertSet('filterTahunAjaran', $this->taAktif->id)
            ->assertViewHas('rombels', function ($rombels) use ($rombelAktifCount) {
                return $rombels->count() === $rombelAktifCount;
            });
    }
}
