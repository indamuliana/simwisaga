<?php

namespace Tests\Feature\Rapor;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Rapor\Livewire\RaporDashboard;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RaporDashboardRombelSelectorTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Rombel $rombel1;
    protected Rombel $rombel2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_rapor',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $ta = TahunAjaran::first();
        $this->rombel1 = Rombel::where('tahun_ajaran_id', $ta->id)->first();
        $this->rombel2 = Rombel::where('tahun_ajaran_id', $ta->id)->skip(1)->first();
    }

    public function test_superadmin_memiliki_dropdown_pilih_rombel_di_dashboard_rapor(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RaporDashboard::class)
            ->assertSet('canSelectRombel', true)
            ->assertSet('rombel_id', $this->rombel1->id)
            ->set('rombel_id', $this->rombel2->id)
            ->assertSet('rombel_id', $this->rombel2->id)
            ->assertSee($this->rombel2->nama);
    }
}
