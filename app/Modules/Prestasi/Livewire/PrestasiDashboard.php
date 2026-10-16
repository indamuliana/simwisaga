<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Prestasi\Models\PrestasiGuru;
use App\Modules\Prestasi\Models\PrestasiSekolah;
use App\Modules\Prestasi\Services\PrestasiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class PrestasiDashboard extends Component
{
    #[Url]
    public ?int $selectedTahunAjaranId = null;

    #[Url]
    public ?int $selectedSemesterId = null;

    public function mount()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $activeSem = AcademicContext::currentSemester();

        $this->selectedTahunAjaranId = $activeTa?->id;
        $this->selectedSemesterId = $activeSem?->id;
    }

    #[Layout('components.layouts.app')]
    public function render(PrestasiService $service)
    {
        $stats = $service->getDashboardStats($this->selectedTahunAjaranId, $this->selectedSemesterId);

        $recentSiswa = Prestasi::with(['siswas.rombel', 'siswas.rayon', 'pembimbingPegawai'])
            ->approved()
            ->when($this->selectedTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->selectedTahunAjaranId))
            ->when($this->selectedSemesterId, fn($q) => $q->where('semester_id', $this->selectedSemesterId))
            ->orderBy('tanggal_perolehan', 'desc')
            ->limit(5)
            ->get();

        $recentGuru = PrestasiGuru::with('pegawais')
            ->when($this->selectedTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->selectedTahunAjaranId))
            ->when($this->selectedSemesterId, fn($q) => $q->where('semester_id', $this->selectedSemesterId))
            ->orderBy('tanggal_perolehan', 'desc')
            ->limit(5)
            ->get();

        $recentSekolah = PrestasiSekolah::query()
            ->when($this->selectedTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->selectedTahunAjaranId))
            ->orderBy('tanggal_perolehan', 'desc')
            ->limit(5)
            ->get();

        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $semesters = Semester::when($this->selectedTahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->selectedTahunAjaranId))->get();

        return view('prestasi::livewire.dashboard', [
            'stats' => $stats,
            'recentSiswa' => $recentSiswa,
            'recentGuru' => $recentGuru,
            'recentSekolah' => $recentSekolah,
            'tahunAjarans' => $tahunAjarans,
            'semesters' => $semesters,
        ]);
    }
}
