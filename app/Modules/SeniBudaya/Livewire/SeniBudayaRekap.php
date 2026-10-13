<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;

#[Layout('components.layouts.app')]
#[Title('Rekapitulasi Nilai Seni Budaya')]
class SeniBudayaRekap extends Component
{
    public string $viewMode = 'cabang'; // 'cabang' atau 'rombel'
    public ?int $selectedCabangId = null;
    public ?int $selectedRombelId = null;

    public function mount()
    {
        $firstCabang = SeniBudaya::where('is_active', true)->orderBy('nama')->first();
        if ($firstCabang) {
            $this->selectedCabangId = $firstCabang->id;
        }

        $firstRombel = Rombel::orderBy('tingkat')->orderBy('nama')->first();
        if ($firstRombel) {
            $this->selectedRombelId = $firstRombel->id;
        }
    }

    public function render()
    {
        $semester = Semester::where('is_active', true)->first();
        $semesterId = $semester?->id ?? 0;

        $cabangs = SeniBudaya::where('is_active', true)->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        $rows = collect();

        if ($this->viewMode === 'cabang' && $this->selectedCabangId) {
            $rows = SeniBudayaAnggota::with('siswa.rombel')
                ->where('seni_budaya_id', $this->selectedCabangId)
                ->where('semester_id', $semesterId)
                ->get()
                ->sortBy(fn($a) => $a->siswa?->nama);
        } elseif ($this->viewMode === 'rombel' && $this->selectedRombelId) {
            $rows = SeniBudayaAnggota::with(['siswa', 'seniBudaya'])
                ->whereHas('siswa', fn($q) => $q->where('rombel_id', $this->selectedRombelId))
                ->where('semester_id', $semesterId)
                ->get()
                ->sortBy(fn($a) => $a->siswa?->nama);
        }

        return view('senibudaya::livewire.rekap', [
            'cabangs' => $cabangs,
            'rombels' => $rombels,
            'rows' => $rows,
            'semester' => $semester,
        ]);
    }
}
