<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Pengajuan;
use App\Modules\Sarpras\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pengajuan Pembelian Aset')]
class PengajuanIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // Modal Create
    public bool $showCreateModal = false;
    public string $nama_barang = '';
    public ?int $kategori_id = null;
    public ?string $spesifikasi = '';
    public int $jumlah = 1;
    public float $estimasi_harga_satuan = 0;
    public string $urgensi = 'Sedang';
    public ?int $ruangan_id = null;

    // Modal Approval / Action
    public bool $showActionModal = false;
    public ?Pengajuan $activePengajuan = null;
    public string $actionType = ''; // 'review', 'approve', 'realisasi', 'selesai', 'tolak'
    public string $actionCatatan = '';

    protected function rules(): array
    {
        return [
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'nullable|exists:sarpras_kategoris,id',
            'spesifikasi' => 'nullable|string',
            'jumlah' => 'required|integer|min:1',
            'estimasi_harga_satuan' => 'required|numeric|min:0',
            'urgensi' => 'required|in:Rendah,Sedang,Tinggi,Mendesak',
            'ruangan_id' => 'nullable|exists:sarpras_ruangans,id',
        ];
    }

    public function create()
    {
        $this->resetValidation();
        $this->nama_barang = '';
        $this->kategori_id = null;
        $this->spesifikasi = '';
        $this->jumlah = 1;
        $this->estimasi_harga_satuan = 0;
        $this->urgensi = 'Sedang';
        $this->ruangan_id = null;
        $this->showCreateModal = true;
    }

    public function submitPengajuan()
    {
        $data = $this->validate();
        $totalEstimasi = $this->jumlah * $this->estimasi_harga_satuan;

        $pengajuan = Pengajuan::create([
            'nomor_pengajuan' => Pengajuan::generateNomor(),
            'user_id' => Auth::id(),
            'nama_barang' => $data['nama_barang'],
            'kategori_id' => $data['kategori_id'],
            'spesifikasi' => $data['spesifikasi'],
            'jumlah' => $data['jumlah'],
            'estimasi_harga_satuan' => $data['estimasi_harga_satuan'],
            'total_estimasi' => $totalEstimasi,
            'urgensi' => $data['urgensi'],
            'ruangan_id' => $data['ruangan_id'],
            'status' => 'Diajukan',
        ]);

        session()->flash('message', "Pengajuan {$pengajuan->nomor_pengajuan} berhasil dikirim dan menunggu review Sarpras.");
        $this->showCreateModal = false;
    }

    public function openAction(int $id, string $type)
    {
        $this->activePengajuan = Pengajuan::with(['user', 'kategori', 'ruangan'])->findOrFail($id);
        $this->actionType = $type;
        $this->actionCatatan = '';
        $this->showActionModal = true;
    }

    public function processAction()
    {
        if (!$this->activePengajuan) {
            return;
        }

        $p = $this->activePengajuan;

        switch ($this->actionType) {
            case 'review':
                abort_unless(Auth::user()->can('sarpras.pengajuan.review'), 403);
                $p->update([
                    'status' => 'Review Sarpras',
                    'catatan_sarpras' => $this->actionCatatan ?: 'Layak diproses ke Kepala Sekolah.',
                ]);
                session()->flash('message', "Pengajuan {$p->nomor_pengajuan} telah direview dan diteruskan ke Kepala Sekolah.");
                break;

            case 'approve_kepsek':
                abort_unless(Auth::user()->can('sarpras.pengajuan.approve'), 403);
                $p->update([
                    'status' => 'Disetujui Kepsek',
                    'catatan_kepsek' => $this->actionCatatan ?: 'Disetujui untuk diproses anggarannya.',
                    'approved_at' => now(),
                ]);
                session()->flash('message', "Pengajuan {$p->nomor_pengajuan} telah disetujui Kepala Sekolah.");
                break;

            case 'realisasi_bendahara':
                abort_unless(Auth::user()->can('sarpras.pengajuan.realisasi'), 403);
                $p->update([
                    'status' => 'Realisasi Bendahara',
                    'catatan_bendahara' => $this->actionCatatan ?: 'Anggaran telah dialokasikan / barang sedang dibeli.',
                ]);
                session()->flash('message', "Pengajuan {$p->nomor_pengajuan} diproses realisasi pembelanjaannya.");
                break;

            case 'selesai':
                abort_unless(Auth::user()->can('sarpras.pengajuan.realisasi') || Auth::user()->can('sarpras.pengajuan.review'), 403);
                $p->update([
                    'status' => 'Selesai',
                ]);
                session()->flash('message', "Pengajuan {$p->nomor_pengajuan} telah selesai / barang tiba di sekolah.");
                break;

            case 'tolak':
                abort_unless(Auth::user()->can('sarpras.pengajuan.review') || Auth::user()->can('sarpras.pengajuan.approve'), 403);
                $p->update([
                    'status' => 'Ditolak',
                    'catatan_sarpras' => $p->status === 'Diajukan' ? ($this->actionCatatan ?: 'Ditolak oleh Sarpras.') : $p->catatan_sarpras,
                    'catatan_kepsek' => $p->status === 'Review Sarpras' ? ($this->actionCatatan ?: 'Ditolak oleh Kepala Sekolah.') : $p->catatan_kepsek,
                ]);
                session()->flash('message', "Pengajuan {$p->nomor_pengajuan} telah ditolak.");
                break;
        }

        $this->showActionModal = false;
    }

    public function delete(int $id)
    {
        $p = Pengajuan::findOrFail($id);
        if ($p->user_id !== Auth::id() && !Auth::user()->can('sarpras.pengajuan.hapus')) {
            session()->flash('error', 'Anda tidak memiliki hak untuk menghapus pengajuan ini.');
            return;
        }

        $nomor = $p->nomor_pengajuan;
        $p->delete();
        session()->flash('message', "Pengajuan {$nomor} berhasil dihapus.");
    }

    public function render()
    {
        $query = Pengajuan::with(['user', 'kategori', 'ruangan']);

        $user = Auth::user();
        $isSarprasOrKepsekOrBendahara = $user->hasRole(['Koordinator Sarpras', 'Kepala Sekolah', 'Bendahara', 'Super Admin']);

        if (!$isSarprasOrKepsekOrBendahara) {
            $query->where('user_id', $user->id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nomor_pengajuan', 'like', "%{$this->search}%")
                  ->orWhere('nama_barang', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $pengajuans = $query->latest()->paginate(10);
        $kategoris = Kategori::orderBy('nama')->get();
        $ruangans = Ruangan::where('is_active', true)->orderBy('nama_ruangan')->get();

        return view('sarpras::livewire.pengajuan-index', compact('pengajuans', 'kategoris', 'ruangans'));
    }
}
