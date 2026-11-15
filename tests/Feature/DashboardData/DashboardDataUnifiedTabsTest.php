<?php

namespace Tests\Feature\DashboardData;

use App\Models\User;
use App\Modules\DashboardData\Livewire\Index as DashboardIndex;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardDataUnifiedTabsTest extends TestCase
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
            'username' => 'superadmin_dashboard_test',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_dashboard_data_renders_tabs_for_super_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard-data.index'));
        $response->assertOk();
        $response->assertSee('Dashboard Data');
        $response->assertSee('Ringkasan');
        $response->assertSee('Rayon');
        $response->assertSee('Jurusan');
        $response->assertSee('Rombel');
        $response->assertSee('Sekolah Asal');
    }

    public function test_legacy_routes_redirect_to_tab_query(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard-data.rayon'))
            ->assertRedirect(route('dashboard-data.index', ['tab' => 'rayon']));

        $this->actingAs($this->admin)
            ->get(route('dashboard-data.program'))
            ->assertRedirect(route('dashboard-data.index', ['tab' => 'rayon']));

        $this->actingAs($this->admin)
            ->get(route('dashboard-data.jurusan'))
            ->assertRedirect(route('dashboard-data.index', ['tab' => 'jurusan']));

        $this->actingAs($this->admin)
            ->get(route('dashboard-data.rombel'))
            ->assertRedirect(route('dashboard-data.index', ['tab' => 'rombel']));

        $this->actingAs($this->admin)
            ->get(route('dashboard-data.sekolah-asal'))
            ->assertRedirect(route('dashboard-data.index', ['tab' => 'sekolah-asal']));
    }

    public function test_livewire_interactive_tab_switching(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DashboardIndex::class)
            ->assertSet('tab', 'overview')
            ->assertSee('Rekapitulasi Siswa Aktif per Jurusan')
            ->call('setTab', 'rayon')
            ->assertSet('tab', 'rayon')
            ->call('setTab', 'jurusan')
            ->assertSet('tab', 'jurusan')
            ->call('setTab', 'rombel')
            ->assertSet('tab', 'rombel');
    }
}
