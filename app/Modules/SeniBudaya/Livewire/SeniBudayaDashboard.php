<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Models\SeniBudayaPresensi;
use App\Modules\Akademik\Models\Semester;

#[Layout('components.layouts.app')]
#[Title('Portal Seni Budaya')]
class SeniBudayaDashboard extends Component
{
    public string $search = '';

    public function render()
    {
        $semester = Semester::where('is_active', true)->first();
        $semesterId = $semester?->id ?? 0;

        $query = SeniBudaya::with('guru')->where('is_active', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('kode', 'like', '%' . $this->search . '%')
                    ->orWhere('instruktur_nama', 'like', '%' . $this->search . '%')
                    ->orWhereHas('guru', function ($g) {
                        $g->where('nama_lengkap', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $cabangs = $query->orderBy('nama')->get();

        // Hitung metrik per cabang di semester aktif
        $anggotaCounts = SeniBudayaAnggota::where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->selectRaw('seni_budaya_id, count(*) as total')
            ->groupBy('seni_budaya_id')
            ->pluck('total', 'seni_budaya_id')
            ->all();

        $presensiCounts = SeniBudayaPresensi::where('semester_id', $semesterId)
            ->selectRaw('seni_budaya_id, count(*) as total')
            ->groupBy('seni_budaya_id')
            ->pluck('total', 'seni_budaya_id')
            ->all();

        $gradedCounts = SeniBudayaAnggota::where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->whereNotNull('nilai_akhir')
            ->selectRaw('seni_budaya_id, count(*) as total')
            ->groupBy('seni_budaya_id')
            ->pluck('total', 'seni_budaya_id')
            ->all();

        $totalSiswa = SeniBudayaAnggota::where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->count();

        $totalPresensi = SeniBudayaPresensi::where('semester_id', $semesterId)->count();

        return view('senibudaya::livewire.dashboard', [
            'cabangs' => $cabangs,
            'anggotaCounts' => $anggotaCounts,
            'presensiCounts' => $presensiCounts,
            'gradedCounts' => $gradedCounts,
            'totalCabang' => $cabangs->count(),
            'totalSiswa' => $totalSiswa,
            'totalPresensi' => $totalPresensi,
            'semester' => $semester,
        ]);
    }
}
