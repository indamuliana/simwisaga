<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Services\KeuanganService;
use Livewire\Component;
use Livewire\WithPagination;

class TerbitkanTagihanIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedRombelId = null;
    public ?int $selectedRayonId = null;
    public string $filterTunggakan = 'menunggak'; // 'semua', 'menunggak', 'lunas'

    public ?int $detailSiswaId = null;
    public array $waData = [];
    public bool $modalWaOpen = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRombelId(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRayonId(): void
    {
        $this->resetPage();
    }

    public function prepareWa(int $siswaId): void
    {
        $siswa = Siswa::with('orangTuas')->findOrFail($siswaId);
        $service = app(KeuanganService::class);
        $this->waData = $service->generateWhatsappTagihan($siswa);
        $this->modalWaOpen = true;
    }

    public function terbitkanTagihanMassal(): void
    {
        $ta = \App\Modules\Akademik\Models\TahunAjaran::first();
        if (! $ta) {
            session()->flash('error', 'Tahun ajaran aktif belum ditemukan.');
            return;
        }

        $service = app(KeuanganService::class);
        $totalCreated = 0;
        $totalSiswa = 0;

        Siswa::where('status', 'aktif')->chunkById(100, function ($siswas) use ($service, $ta, &$totalCreated, &$totalSiswa) {
            foreach ($siswas as $s) {
                $totalCreated += $service->generateSemuaTagihanSiswa($s, $ta->id);
                $totalSiswa++;
            }
        });

        session()->flash('status', "Berhasil menerbitkan {$totalCreated} tagihan (SPP 12 Bulan & DSP Cicilan) untuk {$totalSiswa} siswa aktif.");
    }

    public function render()
    {
        $query = Siswa::with(['rombel', 'rayon', 'orangTuas'])
            ->withSum('tagihans as total_tunggakan', 'sisa_tagihan')
            ->where('status', 'aktif');

        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama', 'like', $term)
                    ->orWhere('nipd', 'like', $term)
                    ->orWhere('nisn', 'like', $term);
            });
        }

        if ($this->selectedRombelId) {
            $query->where('rombel_id', $this->selectedRombelId);
        }

        if ($this->selectedRayonId) {
            $query->where('rayon_id', $this->selectedRayonId);
        }

        if ($this->filterTunggakan === 'menunggak') {
            $query->having('total_tunggakan', '>', 0);
        } elseif ($this->filterTunggakan === 'lunas') {
            $query->having('total_tunggakan', '<=', 0);
        }

        $siswas = $query->orderByDesc('total_tunggakan')->paginate(15);

        $rombels = Rombel::orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();

        return view('keuangan::livewire.terbitkan-tagihan-index', [
            'siswas' => $siswas,
            'rombels' => $rombels,
            'rayons' => $rayons,
        ])->layout('components.layouts.app', ['title' => 'Terbitkan Tagihan & Notifikasi WhatsApp']);
    }
}

