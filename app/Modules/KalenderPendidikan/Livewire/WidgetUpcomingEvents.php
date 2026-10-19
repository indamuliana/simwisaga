<?php

namespace App\Modules\KalenderPendidikan\Livewire;

use App\Modules\KalenderPendidikan\Models\KalenderAgenda;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WidgetUpcomingEvents extends Component
{
    public int $limit = 5;
    public bool $isPublic = false;

    public function render()
    {
        $user = Auth::user();

        $query = KalenderAgenda::with('kategori')
            ->where('tgl_selesai', '>=', now()->toDateString())
            ->orderBy('tgl_mulai')
            ->orderBy('jam_mulai');

        if ($this->isPublic || ! $user) {
            $query->where('status', KalenderAgenda::STATUS_PUBLIK);
        } else {
            $query->visibleForUser($user);
        }

        $events = $query->take($this->limit)->get();

        return view('kalenderpendidikan::livewire.widget-upcoming-events', [
            'events' => $events,
        ]);
    }
}
