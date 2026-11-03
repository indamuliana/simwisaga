<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Services\TranskripNilaiService;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nilai & Rapor')]
class NilaiRaporSiswa extends Component
{
    use GuardsPermission;

    public string $viewMode = 'semester'; // 'semester' atau 'transkrip'
    public ?int $selectedSemesterId = null;

    public function mount(): void
    {
        $this->guard('portal_siswa.nilai.lihat');

        $activeSemester = Semester::where('is_active', true)->first();
        $this->selectedSemesterId = $activeSemester?->id;
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->siswa) {
            return $user->siswa;
        }

        return Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nipd', $user->username)
            ->first();
    }

    public function render()
    {
        $siswa = $this->getSiswa();

        $semesters = Semester::with('tahunAjaran')
            ->orderByDesc('id')
            ->get();

        $nilaiSemesters = collect();
        $catatanWali = null;
        $transkripData = [];

        if ($siswa) {
            $siswa->loadMissing(['rombel.jurusan', 'rombel.waliKelas', 'rayon']);

            // 1. Data Nilai Semester Terpilih
            if ($this->selectedSemesterId) {
                $nilaiSemesters = NilaiAkhir::with([
                    'penugasanMengajar.mapel.kelompokMapel',
                    'penugasanMengajar.guru',
                ])
                    ->where('siswa_id', $siswa->id)
                    ->whereHas('penugasanMengajar', function ($q) {
                        $q->where('semester_id', $this->selectedSemesterId);
                    })
                    ->get();

                $catatanWali = CatatanWaliKelas::where('siswa_id', $siswa->id)
                    ->where('semester_id', $this->selectedSemesterId)
                    ->first();
            }

            // 2. Data Transkrip Kumulatif
            if ($this->viewMode === 'transkrip') {
                try {
                    $transkripService = app(TranskripNilaiService::class);
                    $transkripData = $transkripService->getSiswaTranskripData($siswa->id);
                } catch (\Throwable $e) {
                    report($e);
                    $transkripData = [];
                }
            }
        }

        return view('portalsiswa::livewire.nilai-rapor-siswa', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'nilaiSemesters' => $nilaiSemesters,
            'catatanWali' => $catatanWali,
            'transkripData' => $transkripData,
        ]);
    }
}
