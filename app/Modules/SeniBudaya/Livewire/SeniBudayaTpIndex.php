<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaTp;
use App\Modules\Akademik\Models\Semester;

#[Layout('components.layouts.app')]
#[Title('Tujuan Pembelajaran Seni Budaya')]
class SeniBudayaTpIndex extends Component
{
    public ?int $selectedCabangId = null;
    public ?int $selectedSemesterId = null;
    public string $filterTargetAsesmen = 'all';

    // Modal Form State
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $kode = '';
    public string $deskripsi = '';
    public string $target_asesmen = 'semua';
    public int $urutan = 1;
    public bool $is_active = true;

    protected function rules()
    {
        return [
            'selectedCabangId' => 'required|exists:seni_budayas,id',
            'selectedSemesterId' => 'required|exists:semesters,id',
            'kode' => 'required|string|max:20',
            'deskripsi' => 'required|string',
            'target_asesmen' => 'required|string|max:50',
            'urutan' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $firstCabang = SeniBudaya::where('is_active', true)->orderBy('nama')->first();
        if ($firstCabang) {
            $this->selectedCabangId = $firstCabang->id;
        }

        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->selectedSemesterId = $activeSemester->id;
        }
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingId = null;
        
        $lastUrutan = SeniBudayaTp::where('seni_budaya_id', $this->selectedCabangId)
            ->where('semester_id', $this->selectedSemesterId)
            ->max('urutan') ?? 0;

        $this->urutan = $lastUrutan + 1;
        $this->kode = 'TP ' . $this->urutan;
        $this->deskripsi = '';
        $this->target_asesmen = $this->getDefaultTargetAsesmen();
        $this->is_active = true;

        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $tp = SeniBudayaTp::findOrFail($id);
        $this->editingId = $tp->id;
        $this->selectedCabangId = $tp->seni_budaya_id;
        $this->selectedSemesterId = $tp->semester_id;
        $this->kode = $tp->kode;
        $this->deskripsi = $tp->deskripsi;
        $this->target_asesmen = $tp->target_asesmen ?? 'semua';
        $this->urutan = $tp->urutan;
        $this->is_active = (bool) $tp->is_active;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function getDefaultTargetAsesmen(): string
    {
        $sem = Semester::find($this->selectedSemesterId);
        if ($sem && str_contains(strtolower($sem->semester), 'genap')) {
            return 'STS 2';
        }
        return 'STS 1';
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editingId) {
            $tp = SeniBudayaTp::findOrFail($this->editingId);
            $tp->update([
                'kode' => $this->kode,
                'deskripsi' => $this->deskripsi,
                'target_asesmen' => $this->target_asesmen,
                'urutan' => $this->urutan,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', "Tujuan Pembelajaran '{$tp->kode}' berhasil diperbarui.");
        } else {
            $tp = SeniBudayaTp::create([
                'seni_budaya_id' => $this->selectedCabangId,
                'semester_id' => $this->selectedSemesterId,
                'kode' => $this->kode,
                'deskripsi' => $this->deskripsi,
                'target_asesmen' => $this->target_asesmen,
                'urutan' => $this->urutan,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', "Tujuan Pembelajaran baru '{$tp->kode}' berhasil dialokasikan.");
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $tp = SeniBudayaTp::findOrFail($id);
        $code = $tp->kode;
        $tp->delete();
        session()->flash('message', "TP '{$code}' berhasil dihapus.");
    }

    public function render()
    {
        $cabangs = SeniBudaya::where('is_active', true)->orderBy('nama')->get();
        $semesters = Semester::with('tahunAjaran')->latest('id')->take(6)->get();

        $selectedSem = Semester::find($this->selectedSemesterId);
        $isGenap = $selectedSem ? str_contains(strtolower($selectedSem->semester), 'genap') : false;

        $targetOptions = $isGenap ? [
            'semua' => 'Semua Asesmen (Genap Penuh)',
            'STS 2' => 'STS 2 (Tengah Semester Genap)',
            'SAT' => 'SAT (Sumatif Akhir Tahun)',
            'STS 2, SAT' => 'STS 2 & SAT (Kedua Asesmen)',
        ] : [
            'semua' => 'Semua Asesmen (Ganjil Penuh)',
            'STS 1' => 'STS 1 (Tengah Semester Ganjil)',
            'SAS' => 'SAS (Sumatif Akhir Semester)',
            'STS 1, SAS' => 'STS 1 & SAS (Kedua Asesmen)',
        ];

        $query = SeniBudayaTp::query();
        if ($this->selectedCabangId && $this->selectedSemesterId) {
            $query->where('seni_budaya_id', $this->selectedCabangId)
                ->where('semester_id', $this->selectedSemesterId);

            if ($this->filterTargetAsesmen !== 'all') {
                $query->where(function ($q) {
                    $q->where('target_asesmen', $this->filterTargetAsesmen)
                      ->orWhere('target_asesmen', 'like', "%{$this->filterTargetAsesmen}%");
                });
            }

            $tps = $query->orderBy('urutan')->get();
        } else {
            $tps = collect();
        }

        return view('senibudaya::livewire.tp-index', [
            'cabangs' => $cabangs,
            'semesters' => $semesters,
            'tps' => $tps,
            'targetOptions' => $targetOptions,
            'isGenap' => $isGenap,
        ]);
    }
}
