<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Peminjaman Aset')]
class PeminjamanIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // Modal Peminjaman Baru
    public bool $showCreateModal = false;
    public ?int $aset_id = null;
    public string $peminjam_nama = '';
    public string $peminjam_tipe = 'Guru/GTK';
    public ?string $kontak_peminjam = '';
    public string $keperluan = '';
    public string $tanggal_pinjam = '';
    public string $batas_kembali = '';

    // Modal Pengembalian
    public bool $showReturnModal = false;
    public ?Peminjaman $activePeminjaman = null;
    public string $tanggal_kembali = '';
    public string $kondisi_kembali = 'Baik';
    public ?string $returnCatatan = '';

    public function mount()
    {
        $this->tanggal_pinjam = date('Y-m-d');
        $this->batas_kembali = date('Y-m-d', strtotime('+1 day'));
        $this->tanggal_kembali = date('Y-m-d');
    }

    public function create()
    {
        $this->resetValidation();
        $this->aset_id = null;
        $this->peminjam_nama = Auth::user()?->name ?? '';
        $this->peminjam_tipe = 'Guru/GTK';
        $this->kontak_peminjam = '';
        $this->keperluan = '';
        $this->tanggal_pinjam = date('Y-m-d');
        $this->batas_kembali = date('Y-m-d', strtotime('+1 day'));
        $this->showCreateModal = true;
    }

    public function submitPinjam()
    {
        $this->validate([
            'aset_id' => 'required|exists:sarpras_asets,id',
            'peminjam_nama' => 'required|string|max:100',
            'peminjam_tipe' => 'required|in:Guru/GTK,Siswa,Eksternal',
            'kontak_peminjam' => 'nullable|string|max:50',
            'keperluan' => 'required|string',
            'tanggal_pinjam' => 'required|date',
            'batas_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        $aset = Aset::findOrFail($this->aset_id);
        if ($aset->status !== 'Tersedia') {
            $this->addError('aset_id', 'Aset ini sedang tidak tersedia untuk dipinjam.');
            return;
        }

        $peminjaman = Peminjaman::create([
            'nomor_peminjaman' => Peminjaman::generateNomor(),
            'aset_id' => $aset->id,
            'peminjam_nama' => $this->peminjam_nama,
            'peminjam_tipe' => $this->peminjam_tipe,
            'user_id' => Auth::id(),
            'kontak_peminjam' => $this->kontak_peminjam,
            'keperluan' => $this->keperluan,
            'tanggal_pinjam' => $this->tanggal_pinjam,
            'batas_kembali' => $this->batas_kembali,
            'kondisi_awal' => $aset->kondisi,
            'status' => 'Dipinjam',
            'created_by' => Auth::id(),
        ]);

        $aset->update(['status' => 'Dipinjam']);

        session()->flash('message', "Peminjaman aset {$aset->nama_barang} berhasil dicatat ({$peminjaman->nomor_peminjaman}).");
        $this->showCreateModal = false;
    }

    public function openReturn(int $id)
    {
        $this->activePeminjaman = Peminjaman::with('aset')->findOrFail($id);
        $this->tanggal_kembali = date('Y-m-d');
        $this->kondisi_kembali = $this->activePeminjaman->kondisi_awal ?: 'Baik';
        $this->returnCatatan = '';
        $this->showReturnModal = true;
    }

    public function processReturn()
    {
        if (!$this->activePeminjaman) {
            return;
        }

        $this->validate([
            'tanggal_kembali' => 'required|date',
            'kondisi_kembali' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'returnCatatan' => 'nullable|string',
        ]);

        $p = $this->activePeminjaman;
        $p->update([
            'tanggal_kembali' => $this->tanggal_kembali,
            'kondisi_kembali' => $this->kondisi_kembali,
            'catatan' => $this->returnCatatan,
            'status' => 'Dikembalikan',
        ]);

        // Kembalikan status aset ke Tersedia dan perbarui kondisi fisiknya
        $aset = $p->aset;
        if ($aset) {
            $aset->update([
                'status' => 'Tersedia',
                'kondisi' => $this->kondisi_kembali,
            ]);
        }

        session()->flash('message', "Aset {$aset?->nama_barang} telah berhasil dikembalikan.");
        $this->showReturnModal = false;
    }

    public function render()
    {
        $query = Peminjaman::with('aset');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nomor_peminjaman', 'like', "%{$this->search}%")
                  ->orWhere('peminjam_nama', 'like', "%{$this->search}%")
                  ->orWhereHas('aset', fn ($a) => $a->where('nama_barang', 'like', "%{$this->search}%")->orWhere('kode_aset', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $peminjamans = $query->latest()->paginate(10);
        $tersediaAsets = Aset::where('status', 'Tersedia')->where('kondisi', '!=', 'Dimusnahkan')->orderBy('nama_barang')->get();

        return view('sarpras::livewire.peminjaman-index', compact('peminjamans', 'tersediaAsets'));
    }
}
