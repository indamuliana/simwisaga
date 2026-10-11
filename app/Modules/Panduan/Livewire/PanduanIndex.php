<?php

namespace App\Modules\Panduan\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Panduan Aplikasi SIRAGA')]
class PanduanIndex extends Component
{
    public string $activeChapter = '';
    public string $search = '';

    public function mount(): void
    {
        $user = auth()->user();
        $isSiswaOrOrtu = $user && ($user->hasRole('Siswa') || $user->hasRole('Orang Tua'));

        if ($isSiswaOrOrtu) {
            $this->activeChapter = 'portal-siswa-ortu';
        } else {
            $this->activeChapter = 'siklus-akademik';
        }
    }

    public function setChapter(string $chapter): void
    {
        $this->activeChapter = $chapter;
    }

    public function render()
    {
        $user = auth()->user();
        $isSiswaOrOrtu = $user && ($user->hasRole('Siswa') || $user->hasRole('Orang Tua'));

        return view('panduan::livewire.panduan-index', [
            'isSiswaOrOrtu' => $isSiswaOrOrtu,
            'user' => $user,
        ]);
    }
}
