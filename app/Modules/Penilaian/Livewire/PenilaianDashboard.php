<?php

namespace App\Modules\Penilaian\Livewire;

use Livewire\Component;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kepegawaian\Models\Pegawai;
use Livewire\Attributes\Layout;

class PenilaianDashboard extends Component
{
    #[Layout('components.layouts.app')]
    public function render()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $penugasan = PenugasanMengajar::with(['rombel', 'mapel', 'pegawai'])->get();
        } else {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                $penugasan = PenugasanMengajar::with(['rombel', 'mapel'])
                    ->where('pegawai_id', $pegawai->id)
                    ->get();
            } else {
                $penugasan = collect();
            }
        }

        return view('penilaian::livewire.dashboard', [
            'penugasan' => $penugasan
        ]);
    }
}
