<?php

namespace App\Modules\Rapor\Livewire;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\Sistem\Models\PeriodeRapor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CatatanWaliKelasIndex extends Component
{
    #[Url]
    public string $filterScope = 'rombel'; // 'rombel' atau 'rayon'

    #[Url]
    public ?int $selectedRombelId = null;

    #[Url]
    public ?int $selectedRayonId = null;

    #[Url]
    public ?int $selectedPeriodeId = null; // null = Catatan Semester, int = Catatan Periode 9M

    public string $search = '';
    public array $catatans = []; // [siswa_id => string]
    public array $siswaPrestasis = []; // [siswa_id => Collection]

    public bool $canSelectAll = false;

    public function mount()
    {
        $activeTa = AcademicContext::currentTahunAjaran();
        $currentSem = AcademicContext::currentSemester();
        $semName = $currentSem ? (str_contains(strtolower($currentSem->semester), 'genap') ? 'genap' : 'ganjil') : 'ganjil';

        $user = auth()->user();
        $this->canSelectAll = $user && ($user->isSuperAdmin() || $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('Kurikulum') || $user->hasRole('Kepala Sekolah') || $user->hasRole('Operator'));

        // Default ke periode aktif
        if ($activeTa) {
            $activePeriode = PeriodeRapor::where('tahun_ajaran_id', $activeTa->id)
                ->where('semester', $semName)
                ->where('is_active', true)
                ->first();
            if ($activePeriode) {
                $this->selectedPeriodeId = $activePeriode->id;
            }
        }

        $rombels = $this->availableRombels;
        $rayons = $this->availableRayons;

        if ($rombels->isNotEmpty() && !$this->selectedRombelId) {
            $this->selectedRombelId = $rombels->first()->id;
        }

        if ($rayons->isNotEmpty() && !$this->selectedRayonId) {
            $this->selectedRayonId = $rayons->first()->id;
        }

        $this->loadCatatans();
    }

    public function updatedFilterScope(): void
    {
        $this->loadCatatans();
    }

    public function updatedSelectedRombelId(): void
    {
        $this->loadCatatans();
    }

    public function updatedSelectedRayonId(): void
    {
        $this->loadCatatans();
    }

    public function updatedSelectedPeriodeId(): void
    {
        $this->loadCatatans();
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

    public function loadCatatans(): void
    {
        $currentSem = AcademicContext::currentSemester();
        if (!$currentSem) return;

        $siswas = $this->siswas;
        if ($siswas->isEmpty()) {
            $this->catatans = [];
            return;
        }

        $siswaIds = $siswas->pluck('id')->all();

        $query = CatatanWaliKelas::where('semester_id', $currentSem->id)
            ->whereIn('siswa_id', $siswaIds);

        if ($this->selectedPeriodeId) {
            $query->where('periode_rapor_id', $this->selectedPeriodeId);
        } else {
            $query->whereNull('periode_rapor_id');
        }

        $existing = $query->get()->keyBy('siswa_id');

        $raporService = app(\App\Modules\Rapor\Services\RaporScoreService::class);
        $this->catatans = [];
        $this->siswaPrestasis = [];
        foreach ($siswas as $siswa) {
            $this->catatans[$siswa->id] = $existing->get($siswa->id)?->catatan ?? '';
            $this->siswaPrestasis[$siswa->id] = $raporService->getPrestasiForSiswa($siswa->id, $currentSem->id, $this->selectedPeriodeId);
        }
    }

    public function appendPrestasiToCatatan(int $siswaId): void
    {
        $prestasis = $this->siswaPrestasis[$siswaId] ?? collect();
        if ($prestasis->isEmpty()) return;

        $lines = ["Prestasi diraih:"];
        foreach ($prestasis as $p) {
            $lines[] = "- {$p->nama_prestasi} ({$p->peringkat} Tingkat {$p->tingkat_label})";
        }

        $current = trim($this->catatans[$siswaId] ?? '');
        $this->catatans[$siswaId] = $current . ($current !== '' ? "\n\n" : '') . implode("\n", $lines);
    }

    public function applyTemplate(int $siswaId, string $templateKey): void
    {
        $templates = [
            'sangat_baik' => 'Pertahankan prestasi dan semangat belajarmu yang luar biasa. Teruslah menjadi teladan positif bagi teman-temanmu.',
            'pemantapan' => 'Tunjukkan sikap proaktif dalam memahami materi pelajaran yang dirasa sulit. Tetap tekun dan disiplin dalam mengulang materi di rumah.',
            'perlu_bimbingan' => 'Tingkatkan kehadiran dan fokus dalam mengikuti pembelajaran. Jangan ragu berkonsultasi dengan guru pengampu jika mengalami kesulitan belajar.',
            'kedisiplinan' => 'Tingkatkan kedisiplinan dan tanggung jawab dalam mematuhi tata tertib sekolah serta pengumpulan tugas-tugas tepat waktu.',
            'keaktifan' => 'Kemampuan akademik sudah baik, silakan lebih aktif dalam diskusi kelompok dan kegiatan pengembangan diri atau ekstrakurikuler.',
        ];

        if (isset($templates[$templateKey])) {
            $this->catatans[$siswaId] = $templates[$templateKey];
        }
    }

    public function saveCatatan(): void
    {
        $currentSem = AcademicContext::currentSemester();
        if (!$currentSem) {
            session()->flash('error', 'Semester aktif tidak ditemukan.');
            return;
        }

        foreach ($this->catatans as $siswaId => $text) {
            $clean = trim($text);

            CatatanWaliKelas::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'semester_id' => $currentSem->id,
                    'periode_rapor_id' => $this->selectedPeriodeId ?: null,
                ],
                [
                    'catatan' => $clean !== '' ? $clean : null,
                ]
            );
        }

        session()->flash('message', 'Seluruh Catatan Wali Kelas / Pembimbing Rayon berhasil disimpan.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $currentRombel = $this->filterScope === 'rombel' && $this->selectedRombelId ? Rombel::find($this->selectedRombelId) : null;
        $currentRayon = $this->filterScope === 'rayon' && $this->selectedRayonId ? Rayon::find($this->selectedRayonId) : null;
        $currentPeriode = $this->selectedPeriodeId ? PeriodeRapor::find($this->selectedPeriodeId) : null;

        return view('rapor::livewire.catatan-wali-kelas-index', [
            'currentRombel' => $currentRombel,
            'currentRayon' => $currentRayon,
            'currentPeriode' => $currentPeriode,
            'siswas' => $this->siswas,
        ]);
    }
}
