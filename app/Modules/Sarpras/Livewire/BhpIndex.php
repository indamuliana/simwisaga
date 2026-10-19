<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Bhp;
use App\Modules\Sarpras\Models\BhpMutasi;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Bahan Habis Pakai (BHP)')]
class BhpIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterKategori = '';
    public bool $filterMenipis = false;

    // Modal master BHP
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $bhpId = null;

    public string $kode_barang = '';
    public string $nama_barang = '';
    public ?int $kategori_id = null;
    public string $satuan = 'Pcs';
    public int $stok_saat_ini = 0;
    public int $stok_minimum = 5;
    public ?string $keterangan = '';

    // Modal Mutasi Stok
    public bool $showMutasiModal = false;
    public ?Bhp $mutasiBhp = null;
    public string $mutasiJenis = 'masuk';
    public string $mutasiTanggal = '';
    public int $mutasiJumlah = 1;
    public ?int $mutasiRuanganId = null;
    public string $mutasiPenerima = '';
    public string $mutasiKeterangan = '';

    // Modal Riwayat Kartu Stok
    public bool $showHistoryModal = false;
    public ?Bhp $selectedBhpHistory = null;

    public function mount()
    {
        $this->mutasiTanggal = date('Y-m-d');
    }

    public function create()
    {
        $this->resetValidation();
        $this->isEdit = false;
        $this->bhpId = null;
        $this->kode_barang = Bhp::generateKode();
        $this->nama_barang = '';
        $this->kategori_id = Kategori::habisPakai()->first()?->id;
        $this->satuan = 'Pcs';
        $this->stok_saat_ini = 0;
        $this->stok_minimum = 5;
        $this->keterangan = '';
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->bhpId = $id;

        $b = Bhp::findOrFail($id);
        $this->kode_barang = $b->kode_barang;
        $this->nama_barang = $b->nama_barang;
        $this->kategori_id = $b->kategori_id;
        $this->satuan = $b->satuan;
        $this->stok_saat_ini = $b->stok_saat_ini;
        $this->stok_minimum = $b->stok_minimum;
        $this->keterangan = $b->keterangan;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'kode_barang' => 'required|string|max:50|unique:sarpras_bhps,kode_barang,' . ($this->bhpId ?? 'NULL') . ',id',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:sarpras_kategoris,id',
            'satuan' => 'required|string|max:30',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if ($this->isEdit && $this->bhpId) {
            $b = Bhp::findOrFail($this->bhpId);
            $b->update([
                'nama_barang' => $this->nama_barang,
                'kategori_id' => $this->kategori_id,
                'satuan' => $this->satuan,
                'stok_saat_ini' => $this->stok_saat_ini,
                'stok_minimum' => $this->stok_minimum,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('message', "Data BHP {$b->nama_barang} berhasil diperbarui.");
        } else {
            $b = Bhp::create([
                'kode_barang' => $this->kode_barang,
                'nama_barang' => $this->nama_barang,
                'kategori_id' => $this->kategori_id,
                'satuan' => $this->satuan,
                'stok_saat_ini' => $this->stok_saat_ini,
                'stok_minimum' => $this->stok_minimum,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('message', "Data BHP baru {$b->nama_barang} berhasil disimpan.");
        }

        $this->showModal = false;
    }

    public function openMutasi(int $id, string $jenis = 'masuk')
    {
        $this->resetValidation();
        $this->mutasiBhp = Bhp::findOrFail($id);
        $this->mutasiJenis = $jenis;
        $this->mutasiTanggal = date('Y-m-d');
        $this->mutasiJumlah = 1;
        $this->mutasiRuanganId = null;
        $this->mutasiPenerima = '';
        $this->mutasiKeterangan = '';
        $this->showMutasiModal = true;
    }

    public function saveMutasi()
    {
        $this->validate([
            'mutasiJenis' => 'required|in:masuk,keluar',
            'mutasiTanggal' => 'required|date',
            'mutasiJumlah' => 'required|integer|min:1',
            'mutasiRuanganId' => 'nullable|exists:sarpras_ruangans,id',
            'mutasiPenerima' => 'nullable|string|max:100',
            'mutasiKeterangan' => 'nullable|string',
        ]);

        if (!$this->mutasiBhp) {
            return;
        }

        $sisaStok = 0;
        $sukses = false;

        DB::transaction(function () use (&$sisaStok, &$sukses) {
            $b = Bhp::lockForUpdate()->findOrFail($this->mutasiBhp->id);

            if ($this->mutasiJenis === 'keluar' && $b->stok_saat_ini < $this->mutasiJumlah) {
                $this->addError('mutasiJumlah', "Stok tidak mencukupi! Sisa stok saat ini hanya {$b->stok_saat_ini} {$b->satuan}.");
                return;
            }

            $sisaStok = $this->mutasiJenis === 'masuk'
                ? ($b->stok_saat_ini + $this->mutasiJumlah)
                : ($b->stok_saat_ini - $this->mutasiJumlah);

            BhpMutasi::create([
                'bhp_id' => $b->id,
                'jenis' => $this->mutasiJenis,
                'tanggal' => $this->mutasiTanggal,
                'jumlah' => $this->mutasiJumlah,
                'sisa_stok_setelahnya' => $sisaStok,
                'ruangan_id' => $this->mutasiRuanganId,
                'penerima' => $this->mutasiPenerima,
                'keterangan' => $this->mutasiKeterangan,
                'created_by' => Auth::id(),
            ]);

            $b->update(['stok_saat_ini' => $sisaStok]);
            $sukses = true;
        });

        if ($sukses) {
            session()->flash('message', "Mutasi {$this->mutasiJenis} berhasil dicatat. Saldo baru: {$sisaStok} {$this->mutasiBhp->satuan}.");
            $this->showMutasiModal = false;
        }
    }

    public function showHistory(int $id)
    {
        $this->selectedBhpHistory = Bhp::with(['mutasis.ruangan', 'mutasis.creator'])->findOrFail($id);
        $this->showHistoryModal = true;
    }

    public function delete(int $id)
    {
        $b = Bhp::findOrFail($id);
        $nama = $b->nama_barang;
        $b->delete();
        session()->flash('message', "Barang {$nama} berhasil dihapus.");
    }

    public function render()
    {
        $query = Bhp::with('kategori');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_barang', 'like', "%{$this->search}%")
                  ->orWhere('kode_barang', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterKategori) {
            $query->where('kategori_id', $this->filterKategori);
        }

        if ($this->filterMenipis) {
            $query->menipis();
        }

        $bhps = $query->orderBy('nama_barang')->paginate(10);
        $kategoris = Kategori::habisPakai()->get();
        $ruangans = Ruangan::where('is_active', true)->orderBy('nama_ruangan')->get();

        return view('sarpras::livewire.bhp-index', compact('bhps', 'kategoris', 'ruangans'));
    }
}
