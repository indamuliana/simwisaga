<?php

namespace App\Modules\Rapor\Livewire;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Rapor\Services\RaporScoreService;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\Sistem\Models\PeriodeRapor;
use App\Modules\Sistem\Models\School;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class RaporWikramaIndex extends Component
{
    #[Url]
    public string $filterScope = 'rombel'; // 'rombel' atau 'rayon'

    #[Url]
    public ?int $selectedRombelId = null;

    #[Url]
    public ?int $selectedRayonId = null;

    #[Url]
    public ?int $selectedPeriodeId = null;

    public string $search = '';
    public string $printTanggal = '';

    // Modal Preview Siswa
    public bool $showPreviewModal = false;
    public ?int $previewSiswaId = null;
    public ?array $previewData = null;

    public bool $canSelectAll = false;

    public function mount()
    {
        $this->printTanggal = date('Y-m-d');
        $activeTa = AcademicContext::currentTahunAjaran();
        $currentSem = AcademicContext::currentSemester();
        $semName = $currentSem ? (str_contains(strtolower($currentSem->semester), 'genap') ? 'genap' : 'ganjil') : 'ganjil';

        $user = auth()->user();
        $this->canSelectAll = $user && ($user->isSuperAdmin() || $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('Kurikulum') || $user->hasRole('Kepala Sekolah') || $user->hasRole('Operator'));

        // Load Periode Rapor 9M
        if ($activeTa) {
            $periodes = PeriodeRapor::where('tahun_ajaran_id', $activeTa->id)
                ->where('semester', $semName)
                ->orderBy('urutan')
                ->get();

            // Default ke periode aktif atau yang pertama
            $activePeriode = $periodes->firstWhere('is_active', true) ?? $periodes->first();
            if ($activePeriode) {
                $this->selectedPeriodeId = $activePeriode->id;
                if ($activePeriode->tanggal_bagi) {
                    $this->printTanggal = $activePeriode->tanggal_bagi->format('Y-m-d');
                }
            }
        }

        // Default Rombel & Rayon berdasarkan role
        $rombels = $this->availableRombels;
        $rayons = $this->availableRayons;

        if ($rombels->isNotEmpty() && !$this->selectedRombelId) {
            $this->selectedRombelId = $rombels->first()->id;
        }

        if ($rayons->isNotEmpty() && !$this->selectedRayonId) {
            $this->selectedRayonId = $rayons->first()->id;
        }
    }

    public function updatedSelectedPeriodeId($value): void
    {
        if ($value) {
            $periode = PeriodeRapor::find($value);
            if ($periode && $periode->tanggal_bagi) {
                $this->printTanggal = $periode->tanggal_bagi->format('Y-m-d');
            }
        }
    }

    public function getAvailablePeriodesProperty()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $currentSem = AcademicContext::currentSemester();
        $semName = $currentSem ? (str_contains(strtolower($currentSem->semester), 'genap') ? 'genap' : 'ganjil') : 'ganjil';

        if (!$activeTa) return collect();

        return PeriodeRapor::where('tahun_ajaran_id', $activeTa->id)
            ->where('semester', $semName)
            ->orderBy('urutan')
            ->get();
    }

    public function getAvailableRombelsProperty()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        if (!$activeTa) return collect();

        if ($this->canSelectAll) {
            return Rombel::where('tahun_ajaran_id', $activeTa->id)
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get();
        }

        $user = auth()->user();
        $pegawai = Pegawai::where('user_id', $user?->id)->first();
        if ($pegawai) {
            $myRombels = Rombel::where('wali_kelas_id', $pegawai->id)
                ->where('tahun_ajaran_id', $activeTa->id)
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get();

            if ($myRombels->isNotEmpty()) {
                return $myRombels;
            }
        }

        return Rombel::where('tahun_ajaran_id', $activeTa->id)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    public function getAvailableRayonsProperty()
    {
        if ($this->canSelectAll) {
            return Rayon::with('pembimbing')->orderBy('nama')->get();
        }

        $user = auth()->user();
        $myRayons = Rayon::with('pembimbing')->where('pembimbing_id', $user?->id)->orderBy('nama')->get();
        if ($myRayons->isNotEmpty()) {
            return $myRayons;
        }

        return Rayon::with('pembimbing')->orderBy('nama')->get();
    }

    public function getSelectedPeriodeModelProperty(): ?PeriodeRapor
    {
        return $this->selectedPeriodeId ? PeriodeRapor::find($this->selectedPeriodeId) : null;
    }

    public function getPeriodMetaProperty(): array
    {
        $service = app(RaporScoreService::class);
        $semester = AcademicContext::currentSemester();
        $targetKey = $service->resolveTargetPeriodKey($this->selectedPeriodeModel, $semester);
        return $service->getPeriodMeta($targetKey);
    }

    public function getSiswasProperty()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $query = Siswa::with(['rombel.jurusan', 'rayon.pembimbing'])
            ->where('is_active', true);

        if ($this->filterScope === 'rayon') {
            if (!$this->selectedRayonId) {
                return collect();
            }
            $query->where('rayon_id', $this->selectedRayonId);
            if ($activeTa) {
                $query->whereHas('rombel', fn ($rq) => $rq->where('tahun_ajaran_id', $activeTa->id));
            }
        } else {
            if (!$this->selectedRombelId) {
                return collect();
            }
            $query->inRombel((int) $this->selectedRombelId);
        }

        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama', 'like', $term)
                    ->orWhere('nisn', 'like', $term)
                    ->orWhere('nipd', 'like', $term);
            });
        }

        return $query->orderBy('nama')->get();
    }

    public function getSeniBudayaMapProperty()
    {
        $semester = AcademicContext::currentSemester();
        if (!$semester || $this->siswas->isEmpty()) {
            return collect();
        }

        return SeniBudayaAnggota::with('seniBudaya')
            ->whereIn('siswa_id', $this->siswas->pluck('id'))
            ->where('semester_id', $semester->id)
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->keyBy('siswa_id');
    }

    public function openPreview(int $siswaId): void
    {
        $siswa = Siswa::with(['rombel.jurusan', 'rayon.pembimbing'])->findOrFail($siswaId);
        $semester = AcademicContext::currentSemester();
        $service = app(RaporScoreService::class);

        $periodMeta = $this->periodMeta;
        $records = $service->getWikramaAcademicRecords($siswa, $semester, $periodMeta['period_key']);
        $grouped = $service->groupAcademicRecords($records);
        $catatan = $service->getCatatanForSiswa($siswa->id, $semester->id, $this->selectedPeriodeId);
        $rekapAbsen = $service->getRekapAbsen($siswa->id, $semester->id);
        $ekskuls = $service->getEkskuls($siswa->id, $semester->id);

        $incompleteCount = $records->where('status', 'belum_lengkap')->count();
        $belowKkmCount = $records->where('status', 'belum_kompeten')->count();
        $seniItem = $records->firstWhere('is_seni', true);

        $this->previewSiswaId = $siswaId;
        $this->previewData = [
            'siswa' => $siswa,
            'semester' => $semester,
            'sekolah' => School::current(),
            'periodMeta' => $periodMeta,
            'groupedAcademic' => $grouped,
            'catatan' => $catatan,
            'rekapAbsen' => $rekapAbsen,
            'ekskuls' => $ekskuls,
            'records' => $records,
            'seniItem' => $seniItem,
            'incompleteCount' => $incompleteCount,
            'belowKkmCount' => $belowKkmCount,
        ];

        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewSiswaId = null;
        $this->previewData = null;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $currentRombel = $this->filterScope === 'rombel' && $this->selectedRombelId ? Rombel::find($this->selectedRombelId) : null;
        $currentRayon = $this->filterScope === 'rayon' && $this->selectedRayonId ? Rayon::find($this->selectedRayonId) : null;

        return view('rapor::livewire.rapor-wikrama-index', [
            'currentRombel' => $currentRombel,
            'currentRayon' => $currentRayon,
            'periodMeta' => $this->periodMeta,
            'siswas' => $this->siswas,
            'seniBudayaMap' => $this->seniBudayaMap,
        ]);
    }
}
