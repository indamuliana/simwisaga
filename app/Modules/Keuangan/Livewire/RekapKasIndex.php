<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Transaksi;
use Livewire\Component;
use Livewire\WithPagination;

class RekapKasIndex extends Component
{
    use WithPagination;

    public string $startDate = '';
    public string $endDate = '';
    public ?int $selectedRekeningId = null;

    public function mount(): void
    {
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-d');
    }

    public function render()
    {
        $query = Transaksi::with(['siswa.rombel', 'rekening', 'items.posBiaya'])
            ->whereDate('tanggal_bayar', '>=', $this->startDate)
            ->whereDate('tanggal_bayar', '<=', $this->endDate);

        if ($this->selectedRekeningId) {
            $query->where('rekening_id', $this->selectedRekeningId);
        }

        $totalPenerimaan = (clone $query)->sum('total_bayar');
        $transaksis = $query->latest('tanggal_bayar')->paginate(20);
        $rekenings = Rekening::where('is_aktif', true)->get();

        return view('keuangan::livewire.rekap-kas-index', [
            'transaksis' => $transaksis,
            'totalPenerimaan' => $totalPenerimaan,
            'rekenings' => $rekenings,
        ])->layout('components.layouts.app', ['title' => 'Rekapitulasi & Buku Kas Penerimaan']);
    }
}

