<?php

namespace App\Modules\Absensi\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Absensi\Services\DashboardAbsensiService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Absensi')]
class AbsensiDashboard extends Component
{
    use GuardsPermission;

    public string $tanggal = '';
    public string $rombel_id = '';
    public string $rayon_id = '';
    public string $tingkat = '';
    public string $gender = '';
    public string $chartRange = '7d';
    public string $progressTab = 'rombel';
    public string $topAbsentTab = 'rombel';

    public function mount(): void
    {
        $this->guard('absensi.harian.lihat');
        $this->tanggal = now()->toDateString();
    }

    public function updatedTanggal(): void
    {
        if (trim($this->tanggal) === '') {
            $this->tanggal = now()->toDateString();

            return;
        }

        try {
            $this->tanggal = Carbon::parse($this->tanggal)->toDateString();
        } catch (\Throwable) {
            $this->tanggal = now()->toDateString();
        }
    }

    public function setChartRange(string $range): void
    {
        if (in_array($range, ['7d', '14d', '30d', 'bulan_ini', 'semester'], true)) {
            $this->chartRange = $range;
        }
    }

    public function setProgressTab(string $tab): void
    {
        if (in_array($tab, ['rombel', 'rayon'], true)) {
            $this->progressTab = $tab;
        }
    }

    public function setTopAbsentTab(string $tab): void
    {
        if (in_array($tab, ['rombel', 'rayon'], true)) {
            $this->topAbsentTab = $tab;
        }
    }

    public function resetFilters(): void
    {
        $this->tanggal = now()->toDateString();
        $this->rombel_id = '';
        $this->rayon_id = '';
        $this->tingkat = '';
        $this->gender = '';
        $this->chartRange = '7d';
    }

    public function render(DashboardAbsensiService $service)
    {
        $user = auth()->user();

        $data = $service->buildDashboardData($user, [
            'tanggal' => $this->tanggal,
            'rombel_id' => $this->rombel_id,
            'rayon_id' => $this->rayon_id,
            'tingkat' => $this->tingkat,
            'gender' => $this->gender,
            'chart_range' => $this->chartRange,
        ]);

        $filterOptions = $service->getFilterOptions($user);

        $this->dispatch('absensi-dashboard-chart-updated', chart: $data['chart']);

        return view('absensi::livewire.absensi-dashboard', [
            'data' => $data,
            'rombels' => $filterOptions['rombels'],
            'rayons' => $filterOptions['rayons'],
            'tingkats' => $filterOptions['tingkats'],
            'hasFullAccess' => $filterOptions['has_full_access'],
            'canInput' => $user !== null && ($user->isSuperAdmin() || $user->can('absensi.harian.input')),
        ]);
    }
}
