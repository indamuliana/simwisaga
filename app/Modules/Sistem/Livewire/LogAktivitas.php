<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;

#[Layout('components.layouts.app')]
#[Title('Log Aktivitas')]
class LogAktivitas extends Component
{
    use GuardsPermission, WithPagination;

    public string $search = '';
    public string $eventFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEventFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->guard('sistem.audit.lihat');

        $audits = Audit::with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('auditable_type', 'like', "%{$this->search}%")
                        ->orWhere('ip_address', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('username', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->eventFilter, function ($q) {
                $q->where('event', $this->eventFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('sistem::livewire.log-aktivitas', [
            'audits' => $audits,
        ]);
    }
}
