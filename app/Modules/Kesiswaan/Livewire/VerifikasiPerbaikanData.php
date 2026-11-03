<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\PortalSiswa\Models\PengajuanPerbaikanDataSiswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Verifikasi Perbaikan Data Siswa')]
class VerifikasiPerbaikanData extends Component
{
    use GuardsPermission, WithPagination;

    public string $filterStatus = 'menunggu';

    public ?int $selectedPengajuanId = null;

    public string $catatanTolak = '';

    public bool $showRejectModal = false;

    public function mount(): void
    {
        $this->guard('kesiswaan.siswa.edit');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $this->guard('kesiswaan.siswa.edit');

        $pengajuan = PengajuanPerbaikanDataSiswa::with('siswa')->findOrFail($id);
        if ($pengajuan->status !== PengajuanPerbaikanDataSiswa::STATUS_MENUNGGU) {
            session()->flash('error', 'Pengajuan ini telah diproses sebelumnya.');

            return;
        }

        $siswa = $pengajuan->siswa;
        if (! $siswa) {
            session()->flash('error', 'Data siswa tidak ditemukan.');

            return;
        }

        // Terapkan perubahan ke data siswa / orang tua
        if (is_array($pengajuan->rincian_perubahan)) {
            foreach ($pengajuan->rincian_perubahan as $field => $data) {
                $baru = $data['baru'] ?? null;
                if ($baru !== null) {
                    if (str_ends_with($field, '_ayah')) {
                        $col = str_replace('_ayah', '', $field);
                        if ($col === 'pendidikan') {
                            $col = 'jenjang_pendidikan';
                        }
                        $ortu = $siswa->orangTuas()->whereIn('hubungan', ['ayah', 'Ayah'])->first();
                        if ($ortu) {
                            $ortu->update([$col => $baru]);
                        } else {
                            $siswa->orangTuas()->create(['hubungan' => 'ayah', $col => $baru]);
                        }
                    } elseif (str_ends_with($field, '_ibu')) {
                        $col = str_replace('_ibu', '', $field);
                        if ($col === 'pendidikan') {
                            $col = 'jenjang_pendidikan';
                        }
                        $ortu = $siswa->orangTuas()->whereIn('hubungan', ['ibu', 'Ibu'])->first();
                        if ($ortu) {
                            $ortu->update([$col => $baru]);
                        } else {
                            $siswa->orangTuas()->create(['hubungan' => 'ibu', $col => $baru]);
                        }
                    } elseif (str_ends_with($field, '_wali')) {
                        $col = str_replace('_wali', '', $field);
                        if ($col === 'pendidikan') {
                            $col = 'jenjang_pendidikan';
                        }
                        $ortu = $siswa->orangTuas()->whereIn('hubungan', ['wali', 'Wali'])->first();
                        if ($ortu) {
                            $ortu->update([$col => $baru]);
                        } else {
                            $siswa->orangTuas()->create(['hubungan' => 'wali', $col => $baru]);
                        }
                    } else {
                        $siswa->update([$field => $baru]);
                    }
                }
            }
        }

        $pengajuan->update([
            'status' => PengajuanPerbaikanDataSiswa::STATUS_DISETUJUI,
            'catatan_admin' => 'Telah disetujui dan diterapkan ke data induk siswa.',
            'diproses_oleh' => auth()->id(),
            'diproses_at' => now(),
        ]);

        session()->flash('status', 'Pengajuan perbaikan data berhasil disetujui dan diterapkan ke data siswa.');
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
        $this->guard('kesiswaan.siswa.edit');

        $this->validate([
            'catatanTolak' => ['required', 'string', 'max:500'],
        ], [
            'catatanTolak.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pengajuan = PengajuanPerbaikanDataSiswa::findOrFail($this->selectedPengajuanId);
        $pengajuan->update([
            'status' => PengajuanPerbaikanDataSiswa::STATUS_DITOLAK,
            'catatan_admin' => $this->catatanTolak,
            'diproses_oleh' => auth()->id(),
            'diproses_at' => now(),
        ]);

        $this->closeRejectModal();
        session()->flash('status', 'Pengajuan perbaikan data telah ditolak.');
    }

    public function render()
    {
        $query = PengajuanPerbaikanDataSiswa::with(['siswa.rombel', 'siswa.rayon', 'diprosesOleh'])
            ->latest();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        $pengajuans = $query->paginate(15);

        return view('kesiswaan::livewire.verifikasi-perbaikan-data', [
            'pengajuans' => $pengajuans,
        ]);
    }
}
