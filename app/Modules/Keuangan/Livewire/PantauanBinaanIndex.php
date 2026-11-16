<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Services\KeuanganService;
use Livewire\Component;
use Livewire\WithPagination;

class PantauanBinaanIndex extends Component
{
    use WithPagination;

    public ?int $selectedRayonId = null;
    public string $search = '';
    public array $waData = [];
    public bool $modalWaOpen = false;

    public function mount(): void
    {
        $userId = auth()->id();
        // Cek rayon yang dibimbing oleh user login
        $rayon = Rayon::where('pembimbing_id', $userId)->first();
        if ($rayon) {
            $this->selectedRayonId = $rayon->id;
        } else {
            // Fallback rayon pertama jika admin/superadmin
            $firstRayon = Rayon::first();
            $this->selectedRayonId = $firstRayon?->id;
        }
    }

    public function prepareWa(int $siswaId): void
    {
        $siswa = Siswa::with('orangTuas')->findOrFail($siswaId);
        $service = app(KeuanganService::class);
        $this->waData = $service->generateWhatsappTagihan($siswa);
        $this->modalWaOpen = true;
    }

    public function render()
    {
        $user = auth()->user();
        $isStaffKeuangan = $user && $user->hasAnyRole(['Super Admin', 'Bendahara', 'Tata Usaha']);
        $pembimbingRayon = Rayon::where('pembimbing_id', $user?->id)->first();

        // Kunci akses guru pembimbing hanya pada rayon binaannya sendiri
        if ($pembimbingRayon && ! $isStaffKeuangan) {
            $this->selectedRayonId = $pembimbingRayon->id;
        }

        $rayons = Rayon::orderBy('nama')->get();

        $query = Siswa::with(['rombel', 'rayon', 'orangTuas'])
            ->withSum('tagihans as total_tunggakan', 'sisa_tagihan')
            ->where('status', 'aktif');

        if ($this->selectedRayonId) {
            $query->where('rayon_id', $this->selectedRayonId);
        }

        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $query->where('nama', 'like', $term);
        }

        $siswas = $query->orderBy('nama')->paginate(20);

        return view('keuangan::livewire.pantauan-binaan-index', [
            'siswas' => $siswas,
            'rayons' => $rayons,
            'pembimbingRayon' => $pembimbingRayon,
            'isStaffKeuangan' => $isStaffKeuangan,
        ])->layout('components.layouts.app', ['title' => 'Pantauan Pembayaran Siswa Binaan']);
    }
}

