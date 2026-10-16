<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Prestasi\Models\Prestasi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PrestasiVerifikasi extends Component
{
    use WithPagination;

    #[Url]
    public string $tabStatus = 'pending'; // 'pending' | 'approved' | 'rejected'

    #[Url]
    public string $search = '';

    // Modal Action
    public bool $showDetailModal = false;
    public ?Prestasi $selectedPrestasi = null;

    public bool $showRejectModal = false;
    public string $catatanVerifikasi = '';

    public function openDetail(int $id)
    {
        $this->selectedPrestasi = Prestasi::with(['siswas.rombel', 'siswas.rayon', 'pembimbingPegawai', 'tahunAjaran', 'semester', 'periodeRapor'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function approve(int $id)
    {
        $item = Prestasi::findOrFail($id);
        $item->update([
            'status_verifikasi' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan_verifikasi' => 'Disetujui oleh ' . (auth()->user()->name ?? 'Verifikator'),
        ]);

        $this->showDetailModal = false;
        session()->flash('message', 'Pengajuan prestasi siswa berhasil disetujui dan resmi masuk ke buku rapor!');
    }

    public function openReject(int $id)
    {
        $this->selectedPrestasi = Prestasi::findOrFail($id);
        $this->catatanVerifikasi = '';
        $this->showRejectModal = true;
    }

    public function submitReject()
    {
        $this->validate([
            'catatanVerifikasi' => 'required|string|min:5|max:500',
        ], [
            'catatanVerifikasi.required' => 'Wajib memberikan alasan atau catatan revisi penolakan.',
        ]);

        if ($this->selectedPrestasi) {
            $this->selectedPrestasi->update([
                'status_verifikasi' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'catatan_verifikasi' => $this->catatanVerifikasi,
            ]);
        }

        $this->showRejectModal = false;
        $this->showDetailModal = false;
        session()->flash('message', 'Pengajuan prestasi telah ditolak disertai catatan perbaikan.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = Prestasi::with(['siswas.rombel', 'siswas.rayon', 'pembimbingPegawai', 'tahunAjaran', 'semester', 'periodeRapor'])
            ->where('status_verifikasi', $this->tabStatus)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                        ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                        ->orWhereHas('siswas', function ($sq) {
                            $sq->where('nama', 'like', "%{$this->search}%")
                               ->orWhere('nisn', 'like', "%{$this->search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $items = $query->paginate(10);
        $counts = [
            'pending' => Prestasi::pending()->count(),
            'approved' => Prestasi::approved()->count(),
            'rejected' => Prestasi::rejected()->count(),
        ];

        return view('prestasi::livewire.prestasi-verifikasi', [
            'items' => $items,
            'counts' => $counts,
        ]);
    }
}
