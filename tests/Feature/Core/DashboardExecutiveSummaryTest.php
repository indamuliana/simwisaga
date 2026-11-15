<?php

namespace Tests\Feature\Core;

use App\Core\Livewire\Dashboard;
use App\Core\Livewire\ExecutiveSummaryWidget;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardExecutiveSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_super_admin_with_split_grid_and_quick_actions()
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->create();
        $user->assignRole(User::SUPER_ADMIN_ROLE);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Selamat datang');
        $response->assertSee('Pintasan Cepat');
        $response->assertSee('Nilai Mapel');
        $response->assertSee('Rapor Wikrama');
        $response->assertSee('Presensi Harian');
        $response->assertSee('Buku Panduan');
        // Pastikan tidak ada teks demo lama
        $response->assertDontSee('Fondasi SIRAGA siap');
    }

    public function test_executive_summary_widget_computes_real_metrics()
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->create();
        $user->assignRole(User::SUPER_ADMIN_ROLE);

        $this->actingAs($user);

        Livewire::withoutLazyLoading();

        Livewire::test(ExecutiveSummaryWidget::class)
            ->assertSee('Siswa Aktif')
            ->assertSee('Kehadiran Siswa')
            ->assertSee('Kesiapan Rapor 9M')
            ->assertSee('Guru')
            ->assertSee('Tendik')
            ->assertSee('Tren Kehadiran Siswa')
            ->assertSee('Kesiapan Rapor 9 Minggu per Rombel')
            // Pastikan data demo 100% lenyap
            ->assertDontSee('DATA DEMO')
            ->assertDontSee('Bimbingan Konseling (Semester Ini)')
            ->assertDontSee('Status Pembayaran SPP (Bulan Ini)');
    }
}
