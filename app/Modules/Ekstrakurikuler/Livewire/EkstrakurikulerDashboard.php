<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Kepegawaian\Models\Pegawai;
use Livewire\Attributes\Layout;

class EkstrakurikulerDashboard extends Component
{
    #[Layout('components.layouts.app')]
    public function render()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $ekskuls = Ekstrakurikuler::with('pembina')->get();
        } else {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                $ekskuls = Ekstrakurikuler::where('pembina_id', $pegawai->id)->get();
            } else {
                $ekskuls = collect();
            }
        }

        return view('ekstrakurikuler::livewire.dashboard', [
            'ekskuls' => $ekskuls
        ]);
    }
}
