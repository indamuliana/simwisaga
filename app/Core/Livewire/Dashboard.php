<?php

namespace App\Core\Livewire;

use App\Modules\Akademik\Support\AcademicContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $semester = AcademicContext::currentSemester();
        $tahunAjaran = AcademicContext::currentTahunAjaran();

        return view('livewire.dashboard', [
            'roles' => auth()->user()->getRoleNames(),
            'tahunAjaranNama' => $tahunAjaran?->nama ?? '-',
            'semesterNama' => $semester ? ucfirst($semester->semester) : '-',
        ]);
    }
}
