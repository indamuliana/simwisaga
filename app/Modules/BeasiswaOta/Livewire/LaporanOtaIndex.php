<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\KasMutasi;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Livewire\Component;
use Livewire\WithPagination;

class LaporanOtaIndex extends Component
{
    use GuardsPermission, WithPagination;

    public string $activeTab = 'buku-kas'; // 'buku-kas' | 'rekap-donatur' | 'rekap-penyaluran'

    public string $filterPosKas = '';
    public string $startDate = '';
    public string $endDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfYear()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        $ringkasanKas = OtaKasService::getRingkasanKas();

        // Buku Kas Mutasi
        $mutasis = KasMutasi::query()
            ->with('kelompok')
            ->when($this->filterPosKas, fn($q) => $q->where('pos_kas', $this->filterPosKas))
            ->when($this->startDate, fn($q) => $q->whereDate('tanggal', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('tanggal', '<=', $this->endDate))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(20);

        // Rekap Donatur
        $rekapDonatur = Donatur::with(['kelompoks', 'donasiMasuks'])
            ->orderBy('nama')
            ->get();

        // Rekap Penyaluran Siswa
        $rekapAlokasi = Alokasi::with(['siswa.rombel', 'kelompok', 'details', 'semester'])
            ->latest('tanggal_alokasi')
            ->get();

        return view('beasiswaota::livewire.laporan-ota-index', [
            'ringkasanKas' => $ringkasanKas,
            'mutasis' => $mutasis,
            'rekapDonatur' => $rekapDonatur,
            'rekapAlokasi' => $rekapAlokasi,
        ])->layout('components.layouts.app', ['title' => 'Laporan & Buku Kas OTA']);
    }
}
