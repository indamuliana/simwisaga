<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Keuangan\Models\PembayaranOnline;
use App\Modules\Keuangan\Services\KeuanganService;
use Livewire\Component;
use Livewire\WithPagination;

class VerifikasiPembayaranIndex extends Component
{
    use WithPagination;

    public string $filterStatus = 'menunggu';
    public ?int $selectedPengajuanId = null;
    public string $alasanPenolakan = '';
    public bool $modalPreviewOpen = false;
    public bool $modalTolakOpen = false;

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function previewPengajuan(int $id): void
    {
        $this->selectedPengajuanId = $id;
        $this->modalPreviewOpen = true;
    }

    public function closePreview(): void
    {
        $this->modalPreviewOpen = false;
    }

    public function openModalTolak(int $id): void
    {
        $this->selectedPengajuanId = $id;
        $this->alasanPenolakan = '';
        $this->modalTolakOpen = true;
    }

    public function setujuiPengajuan(int $id): void
    {
        try {
            $pengajuan = PembayaranOnline::findOrFail($id);
            $service = app(KeuanganService::class);
            $service->verifikasiPembayaranOnline($pengajuan, 'disetujui');

            session()->flash('status', "Pembayaran transfer {$pengajuan->kode_pengajuan} berhasil disetujui & kwitansi telah diterbitkan.");
            $this->modalPreviewOpen = false;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyetujui transfer: ' . $e->getMessage());
        }
    }

    public function tolakPengajuan(): void
    {
        if (! $this->selectedPengajuanId) {
            return;
        }

        try {
            $pengajuan = PembayaranOnline::findOrFail($this->selectedPengajuanId);
            $service = app(KeuanganService::class);
            $service->verifikasiPembayaranOnline(
                $pengajuan,
                'ditolak',
                $this->alasanPenolakan ?: 'Bukti transfer tidak valid atau dana tidak ditemukan.'
            );

            session()->flash('status', "Pengajuan transfer {$pengajuan->kode_pengajuan} telah ditolak.");
            $this->modalTolakOpen = false;
            $this->modalPreviewOpen = false;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menolak transfer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = PembayaranOnline::with(['siswa.rombel', 'rekeningTujuan', 'user', 'transaksi'])
            ->latest();

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $pengajuans = $query->paginate(15);
        $selectedPengajuan = $this->selectedPengajuanId ? PembayaranOnline::with(['siswa.rombel', 'rekeningTujuan', 'user'])->find($this->selectedPengajuanId) : null;

        return view('keuangan::livewire.verifikasi-pembayaran-index', [
            'pengajuans' => $pengajuans,
            'selectedPengajuan' => $selectedPengajuan,
        ])->layout('components.layouts.app', ['title' => 'Verifikasi Bukti Transfer Orang Tua']);
    }
}

