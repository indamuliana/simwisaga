<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\PortalSiswa\Models\PengajuanBeasiswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Verifikasi Pengajuan Beasiswa Siswa')]
class VerifikasiPengajuanBeasiswa extends Component
{
    use GuardsPermission, WithPagination;

    public string $filterStatus = 'menunggu';
    public ?int $selectedPengajuanId = null;
    public string $catatanTolak = '';
    public bool $showRejectModal = false;

    public function mount(): void
    {
        $this->guard('beasiswa_ota.kelola');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $this->guard('beasiswa_ota.kelola');

        $pengajuan = PengajuanBeasiswa::findOrFail($id);
        if ($pengajuan->status !== PengajuanBeasiswa::STATUS_MENUNGGU) {
            session()->flash('error', 'Permohonan beasiswa ini telah diproses sebelumnya.');
            return;
        }

        $pengajuan->update([
            'status' => PengajuanBeasiswa::STATUS_DISETUJUI,
            'catatan_admin' => 'Permohonan beasiswa disetujui untuk diproses ke dalam program beasiswa.',
            'diproses_oleh' => auth()->id(),
            'diproses_at' => now(),
        ]);

        session()->flash('status', 'Permohonan beasiswa berhasil disetujui.');
    }

    public function openRejectModal(int $id): void
    {
        $this->selectedPengajuanId = $id;
        $this->catatanTolak = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedPengajuanId = null;
        $this->catatanTolak = '';
    }

    public function reject(): void
    {
        $this->guard('beasiswa_ota.kelola');

        $this->validate([
            'catatanTolak' => ['required', 'string', 'max:500'],
        ], [
            'catatanTolak.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pengajuan = PengajuanBeasiswa::findOrFail($this->selectedPengajuanId);
        $pengajuan->update([
            'status' => PengajuanBeasiswa::STATUS_DITOLAK,
            'catatan_admin' => $this->catatanTolak,
            'diproses_oleh' => auth()->id(),
            'diproses_at' => now(),
        ]);

        $this->closeRejectModal();
        session()->flash('status', 'Permohonan beasiswa telah ditolak.');
    }

    public function render()
    {
        $query = PengajuanBeasiswa::with(['siswa.rombel', 'siswa.rayon', 'tahunAjaran', 'semester', 'diprosesOleh'])
            ->latest();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        $pengajuans = $query->paginate(15);

        return view('beasiswaota::livewire.verifikasi-pengajuan-beasiswa', [
            'pengajuans' => $pengajuans,
        ]);
    }
}
