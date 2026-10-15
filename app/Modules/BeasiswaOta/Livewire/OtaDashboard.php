<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Livewire\Component;

class OtaDashboard extends Component
{
    use GuardsPermission;

    public function render()
    {
        $ringkasanKas = OtaKasService::getRingkasanKas();

        $totalDonaturRutin = Donatur::where('tipe', 'rutin')->where('is_aktif', true)->count();
        $totalDonaturInsidental = Donatur::where('tipe', 'insidental')->where('is_aktif', true)->count();
        $totalKelompok = Kelompok::where('is_aktif', true)->count();
        $totalSiswaPenerima = SiswaPenerima::where('status', 'aktif')->count();

        $kelompoks = Kelompok::with(['donaturs', 'siswaPenerima.siswa.rombel'])
            ->where('is_aktif', true)
            ->get();

        $recentDonasi = DonasiMasuk::with(['donatur', 'kelompok'])
            ->latest('tanggal_donasi')
            ->latest('id')
            ->take(5)
            ->get();

        $recentAlokasi = Alokasi::with(['siswa.rombel', 'kelompok', 'semester'])
            ->latest('tanggal_alokasi')
            ->latest('id')
            ->take(5)
            ->get();

        return view('beasiswaota::livewire.dashboard', [
            'ringkasanKas' => $ringkasanKas,
            'totalDonaturRutin' => $totalDonaturRutin,
            'totalDonaturInsidental' => $totalDonaturInsidental,
            'totalKelompok' => $totalKelompok,
            'totalSiswaPenerima' => $totalSiswaPenerima,
            'kelompoks' => $kelompoks,
            'recentDonasi' => $recentDonasi,
            'recentAlokasi' => $recentAlokasi,
        ])->layout('components.layouts.app', ['title' => 'Dashboard Beasiswa OTA']);
    }
}
