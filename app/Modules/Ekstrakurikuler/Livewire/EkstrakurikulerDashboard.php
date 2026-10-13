<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensi;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Akademik\Models\Semester;
use Livewire\Attributes\Layout;

class EkstrakurikulerDashboard extends Component
{
    public $search = '';
    public $filterKategori = 'all'; // all, wajib, pilihan

    #[Layout('components.layouts.app')]
    public function render()
    {
        $user = auth()->user();
        $semester = Semester::where('is_active', true)->first();
        $semesterId = $semester?->id ?? 0;

        $query = Ekstrakurikuler::with(['pembina', 'instrukturUser'])
            ->where('is_active', true);

        // Jika user adalah instruktur / guru biasa (bukan admin), hanya tampilkan ekskul yang dibina
        $isManager = $user->isSuperAdmin() || $user->hasRole(['Kesiswaan', 'Kurikulum', 'Operator']);
        if (!$isManager) {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            $query->where(function ($q) use ($user, $pegawai) {
                $q->where('instruktur_user_id', $user->id);
                if ($pegawai) {
                    $q->orWhere('pembina_id', $pegawai->id);
                }
            });
        }

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        if ($this->filterKategori === 'wajib') {
            $query->where('is_wajib', true);
        } elseif ($this->filterKategori === 'pilihan') {
            $query->where('is_wajib', false);
        }

        $ekskuls = $query->orderByDesc('is_wajib')->orderBy('nama')->get();

        // Hitung total anggota & presensi per ekskul
        $anggotaCounts = EkstrakurikulerAnggota::where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->selectRaw('ekstrakurikuler_id, count(*) as total')
            ->groupBy('ekstrakurikuler_id')
            ->pluck('total', 'ekstrakurikuler_id')
            ->all();

        $presensiCounts = EkskulPresensi::where('semester_id', $semesterId)
            ->selectRaw('ekstrakurikuler_id, count(*) as total')
            ->groupBy('ekstrakurikuler_id')
            ->pluck('total', 'ekstrakurikuler_id')
            ->all();

        // Ringkasan KPI
        $totalEkskul = $ekskuls->count();
        $totalAnggota = EkstrakurikulerAnggota::where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->when(!$isManager, fn($q) => $q->whereIn('ekstrakurikuler_id', $ekskuls->pluck('id')))
            ->count();
        $totalSesi = EkskulPresensi::where('semester_id', $semesterId)
            ->when(!$isManager, fn($q) => $q->whereIn('ekstrakurikuler_id', $ekskuls->pluck('id')))
            ->count();

        return view('ekstrakurikuler::livewire.dashboard', [
            'ekskuls' => $ekskuls,
            'anggotaCounts' => $anggotaCounts,
            'presensiCounts' => $presensiCounts,
            'semester' => $semester,
            'totalEkskul' => $totalEkskul,
            'totalAnggota' => $totalAnggota,
            'totalSesi' => $totalSesi,
            'isManager' => $isManager,
        ]);
    }
}
