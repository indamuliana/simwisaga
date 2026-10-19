<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Pemeliharaan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pemeliharaan & Servis Aset')]
class PemeliharaanIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // Modal Lapor Kerusakan
    public bool $showReportModal = false;
    public ?int $aset_id = null;
    public string $tanggal_lapor = '';
    public string $deskripsi_kerusakan = '';
    public string $tingkat_urgensi = 'Sedang';

    // Modal Tindakan Servis (Sarpras)
    public bool $showActionModal = false;
    public ?Pemeliharaan $activeTicket = null;
    public string $statusAction = 'Sedang Diperbaiki';
    public ?string $tindakan_perbaikan = '';
    public ?string $teknisi_vendor = '';
    public float $biaya = 0;
    public ?string $tanggal_selesai = '';

    public function mount()
    {
        $this->tanggal_lapor = date('Y-m-d');
        $this->tanggal_selesai = date('Y-m-d');
    }

    public function createReport()
    {
        $this->resetValidation();
        $this->aset_id = null;
        $this->tanggal_lapor = date('Y-m-d');
        $this->deskripsi_kerusakan = '';
        $this->tingkat_urgensi = 'Sedang';
        $this->showReportModal = true;
    }

    public function submitReport()
    {
        $this->validate([
            'aset_id' => 'required|exists:sarpras_asets,id',
            'tanggal_lapor' => 'required|date',
            'deskripsi_kerusakan' => 'required|string',
            'tingkat_urgensi' => 'required|in:Ringan,Sedang,Berat',
        ]);

        $aset = Aset::findOrFail($this->aset_id);

        $ticket = Pemeliharaan::create([
            'nomor_tiket' => Pemeliharaan::generateNomor(),
            'aset_id' => $aset->id,
            'pelapor_id' => Auth::id(),
            'tanggal_lapor' => $this->tanggal_lapor,
            'deskripsi_kerusakan' => $this->deskripsi_kerusakan,
            'tingkat_urgensi' => $this->tingkat_urgensi,
            'status' => 'Menunggu Tindakan',
        ]);

        // Tandai kondisi aset
        $kondisiBaru = $this->tingkat_urgensi === 'Berat' ? 'Rusak Berat' : 'Rusak Ringan';
        $aset->update([
            'kondisi' => $kondisiBaru,
            'status' => 'Dalam Perbaikan',
        ]);

        session()->flash('message', "Laporan kerusakan {$aset->nama_barang} berhasil dikirim dengan tiket {$ticket->nomor_tiket}.");
        $this->showReportModal = false;
    }

    public function openAction(int $id)
    {
        $this->activeTicket = Pemeliharaan::with(['aset', 'pelapor'])->findOrFail($id);
        $this->statusAction = $this->activeTicket->status === 'Menunggu Tindakan' ? 'Sedang Diperbaiki' : $this->activeTicket->status;
        $this->tindakan_perbaikan = $this->activeTicket->tindakan_perbaikan;
        $this->teknisi_vendor = $this->activeTicket->teknisi_vendor;
        $this->biaya = (float) $this->activeTicket->biaya;
        $this->tanggal_selesai = $this->activeTicket->tanggal_selesai?->format('Y-m-d') ?? date('Y-m-d');
        $this->showActionModal = true;
    }

    public function processAction()
    {
        abort_unless(Auth::user()->can('sarpras.pemeliharaan.proses'), 403);

        if (!$this->activeTicket) {
            return;
        }

        $this->validate([
            'statusAction' => 'required|in:Sedang Diperbaiki,Selesai,Afkir',
            'tindakan_perbaikan' => 'nullable|string',
            'teknisi_vendor' => 'nullable|string|max:100',
            'biaya' => 'required|numeric|min:0',
            'tanggal_selesai' => 'nullable|date',
        ]);

        $ticket = $this->activeTicket;
        $aset = $ticket->aset;

        $ticket->update([
            'status' => $this->statusAction,
            'tindakan_perbaikan' => $this->tindakan_perbaikan,
            'teknisi_vendor' => $this->teknisi_vendor,
            'biaya' => $this->biaya,
            'tanggal_selesai' => ($this->statusAction === 'Selesai' || $this->statusAction === 'Afkir') ? ($this->tanggal_selesai ?: date('Y-m-d')) : null,
        ]);

        if ($aset) {
            if ($this->statusAction === 'Selesai') {
                $aset->update([
                    'status' => 'Tersedia',
                    'kondisi' => 'Baik',
                ]);
            } elseif ($this->statusAction === 'Afkir') {
                $aset->update([
                    'status' => 'Dihapuskan',
                    'kondisi' => 'Rusak Berat',
                ]);
            } else {
                $aset->update([
                    'status' => 'Dalam Perbaikan',
                ]);
            }
        }

        session()->flash('message', "Tiket perbaikan {$ticket->nomor_tiket} berhasil diperbarui.");
        $this->showActionModal = false;
    }

    public function render()
    {
        $query = Pemeliharaan::with(['aset', 'pelapor']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nomor_tiket', 'like', "%{$this->search}%")
                  ->orWhere('deskripsi_kerusakan', 'like', "%{$this->search}%")
                  ->orWhereHas('aset', fn ($a) => $a->where('nama_barang', 'like', "%{$this->search}%")->orWhere('kode_aset', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $pemeliharaans = $query->latest()->paginate(10);
        $allAsets = Aset::where('kondisi', '!=', 'Dimusnahkan')
            ->orderBy('nama_barang')
            ->select('id', 'kode_aset', 'nama_barang')
            ->get();

        return view('sarpras::livewire.pemeliharaan-index', compact('pemeliharaans', 'allAsets'));
    }
}
