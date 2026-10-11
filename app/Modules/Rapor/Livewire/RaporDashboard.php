<?php

namespace App\Modules\Rapor\Livewire;

use Livewire\Component;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Sistem\Models\PeriodeRapor;
use Livewire\Attributes\Layout;

class RaporDashboard extends Component
{
    public $rombel_id = null;
    public $siswas = [];
    public $catatans = []; // [siswa_id => catatan]
    public $semester_id;
    
    // For Rombel Selection (Super Admin / Kurikulum)
    public bool $canSelectRombel = false;
    public $availableRombels = [];

    // For Print Modal
    public $showPrintModal = false;
    public $printSiswaId = null;
    public $printTanggal = '';
    public $availablePeriodes = [];
    public ?int $selectedPeriodeId = null;

    public function mount()
    {
        $this->printTanggal = date('Y-m-d');
        $semester = Semester::where('is_active', true)->first();
        if ($semester) {
            $this->semester_id = $semester->id;
        }

        $activeTa = \App\Modules\Akademik\Support\AcademicContext::currentTahunAjaran();
        $user = auth()->user();

        if ($user && ($user->isSuperAdmin() || $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('Kepala Sekolah'))) {
            $this->canSelectRombel = true;
            $this->availableRombels = Rombel::where('tahun_ajaran_id', $activeTa?->id)
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get();

            if ($this->availableRombels->isNotEmpty()) {
                $this->rombel_id = $this->availableRombels->first()->id;
            }
        } else {
            $pegawai = Pegawai::where('user_id', $user?->id)->first();
            if ($pegawai) {
                $rombel = Rombel::where('wali_kelas_id', $pegawai->id)
                    ->where('tahun_ajaran_id', $activeTa?->id)
                    ->first();
                if (!$rombel) {
                    $rombel = Rombel::where('wali_kelas_id', $pegawai->id)->first();
                }
                $this->rombel_id = $rombel ? $rombel->id : null;
            }
        }

        if ($this->rombel_id) {
            $this->loadSiswas();
        }
    }

    public function updatedRombelId()
    {
        $this->loadSiswas();
    }

    public function loadSiswas()
    {
        if (!$this->rombel_id) {
            $this->siswas = collect();
            $this->catatans = [];
            return;
        }

        $this->siswas = Siswa::inRombel($this->rombel_id)->orderBy('nama')->get();
        $cat = CatatanWaliKelas::where('semester_id', $this->semester_id)
            ->whereIn('siswa_id', $this->siswas->pluck('id'))->get()->keyBy('siswa_id');
            
        $this->catatans = [];
        foreach ($this->siswas as $siswa) {
            $this->catatans[$siswa->id] = $cat[$siswa->id]->catatan ?? '';
        }
    }

    public function saveCatatan()
    {
        foreach ($this->catatans as $siswaId => $text) {
            CatatanWaliKelas::updateOrCreate(
                ['siswa_id' => $siswaId, 'semester_id' => $this->semester_id],
                ['catatan' => $text]
            );
        }
        session()->flash('message', 'Catatan Wali Kelas berhasil disimpan.');
    }

    public function openPrintModal($siswaId)
    {
        $this->printSiswaId = $siswaId;

        $currentSem = AcademicContext::currentSemester();
        $semName = $currentSem ? (str_contains(strtolower($currentSem->semester), 'genap') ? 'genap' : 'ganjil') : 'ganjil';
        $currentTa = AcademicContext::currentTahunAjaran();

        if ($currentTa) {
            $this->availablePeriodes = PeriodeRapor::where('tahun_ajaran_id', $currentTa->id)
                ->where('semester', $semName)
                ->orderBy('urutan')
                ->get();

            // Default ke periode aktif, atau yang pertama
            $activePeriode = $this->availablePeriodes->firstWhere('is_active', true) ?? $this->availablePeriodes->first();
            if ($activePeriode) {
                $this->selectedPeriodeId = $activePeriode->id;
                $this->printTanggal = $activePeriode->tanggal_bagi ? $activePeriode->tanggal_bagi->format('Y-m-d') : date('Y-m-d');
            } else {
                $this->selectedPeriodeId = null;
                $this->printTanggal = date('Y-m-d');
            }
        } else {
            $this->availablePeriodes = collect();
            $this->selectedPeriodeId = null;
            $this->printTanggal = date('Y-m-d');
        }

        $this->showPrintModal = true;
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

    public function closePrintModal()
    {
        $this->showPrintModal = false;
        $this->printSiswaId = null;
    }

    public function getSiswaPrintProperty()
    {
        return $this->printSiswaId ? Siswa::find($this->printSiswaId) : null;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $rombel = $this->rombel_id ? Rombel::find($this->rombel_id) : null;
        
        return view('rapor::livewire.dashboard', [
            'rombel' => $rombel
        ]);
    }
}
