<?php

namespace App\Modules\Panduan\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Panduan Rapor Wikrama - SIRAGA')]
class PanduanRaporWikrama extends Component
{
    public string $activeSection = 'konsep';
    public string $search = '';

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function render()
    {
        return view('panduan::livewire.panduan-rapor-wikrama');
    }
}
