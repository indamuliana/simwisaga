<?php

namespace App\Modules\Akademik\Livewire;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use Livewire\Component;

class AcademicContextSwitcher extends Component
{
    public ?int $selectedSemesterId = null;

    public function mount(): void
    {
        $this->selectedSemesterId = AcademicContext::currentSemester()?->id;
    }

    public function switchSemester(): void
    {
        if ($this->selectedSemesterId) {
            AcademicContext::setWorkingSemester($this->selectedSemesterId);
            $this->dispatch('academic-context-changed');
            $this->redirect(request()->header('Referer') ?: route('dashboard'), navigate: true);
        }
    }

    public function render()
    {
        $current = AcademicContext::currentSemester();
        $semesters = Semester::with('tahunAjaran')
            ->orderBy('id', 'desc')
            ->get();

        return view('akademik::livewire.context-switcher', [
            'current' => $current,
            'semesters' => $semesters,
        ]);
    }
}
