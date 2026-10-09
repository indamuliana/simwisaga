<?php

namespace App\Modules\Rapor\Livewire;

use Livewire\Component;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\Akademik\Models\Semester;
use Livewire\Attributes\Layout;

class RaporDashboard extends Component
{
    public $rombel_id = null;
    public $siswas = [];
    public $catatans = []; // [siswa_id => catatan]
    public $semester_id;
    
    // For Print Modal
    public $showPrintModal = false;
    public $printSiswaId = null;
    public $printTanggal = '';

    public function mount()
    {
        $this->printTanggal = date('Y-m-d');
        $semester = Semester::where('is_active', true)->first();
        if ($semester) {
            $this->semester_id = $semester->id;
        }

        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            // Can select rombel? Just take first for now or let them select.
            $rombel = Rombel::first();
            $this->rombel_id = $rombel ? $rombel->id : null;
        } else {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                // Assuming Pegawai has a 'wali_kelas_rombel' or similar relation.
                // In S4, we didn't explicitly create wali kelas relation, let's assume it's in Rombel (wali_kelas_id).
                $rombel = Rombel::where('wali_kelas_id', $pegawai->id)->first();
                $this->rombel_id = $rombel ? $rombel->id : null;
            }
        }

        if ($this->rombel_id) {
            $this->loadSiswas();
        }
    }

    public function loadSiswas()
    {
        $this->siswas = Siswa::where('rombel_id', $this->rombel_id)->orderBy('nama')->get();
        $cat = CatatanWaliKelas::where('semester_id', $this->semester_id)
            ->whereIn('siswa_id', $this->siswas->pluck('id'))->get()->keyBy('siswa_id');
            
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
        $this->showPrintModal = true;
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
