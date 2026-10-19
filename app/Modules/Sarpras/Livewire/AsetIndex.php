<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Ruangan;
use App\Modules\Sarpras\Models\SumberDana;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Aset Tetap')]
class AsetIndex extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterRuangan = '';
    public string $filterKategori = '';
    public string $filterKondisi = '';
    public string $filterStatus = '';
    public ?int $filterSumberDanaId = null;

    // Modal state
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $asetId = null;

    // Modal detail
    public bool $showDetailModal = false;
    public ?Aset $selectedAset = null;

    // Form fields
    public int $jumlah = 1;
    public string $kode_aset = '';
    public string $nama_barang = '';
    public ?string $merk_tipe = '';
    public ?string $nomor_seri = '';
    public ?int $kategori_id = null;
    public ?int $ruangan_id = null;
    public ?int $sumber_dana_id = null;
    public int $tahun_perolehan = 2026;
    public ?string $tanggal_pengadaan = '';
    public string $sumber_dana = 'BOSP 2026';
    public float $harga_perolehan = 0;
    public string $kondisi = 'Baik';
    public string $status = 'Tersedia';
    public ?string $keterangan = '';

    protected function rules(): array
    {
        return [
            'jumlah' => $this->isEdit ? 'nullable|integer' : 'required|integer|min:1|max:100',
            'kode_aset' => $this->isEdit
                ? 'required|string|max:60|unique:sarpras_asets,kode_aset,' . ($this->asetId ?? 'NULL') . ',id'
                : 'required|string|max:60',
            'nama_barang' => 'required|string|max:150',
            'merk_tipe' => 'nullable|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'kategori_id' => 'required|exists:sarpras_kategoris,id',
            'ruangan_id' => 'nullable|exists:sarpras_ruangans,id',
            'sumber_dana_id' => 'nullable|exists:sarpras_sumber_danas,id',
            'tahun_perolehan' => 'required|integer|min:2000|max:2099',
            'tanggal_pengadaan' => 'nullable|date',
            'sumber_dana' => 'required|string|max:50',
            'harga_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang,Dimusnahkan',
            'status' => 'required|in:Tersedia,Dipinjam,Dalam Perbaikan,Dihapuskan',
            'keterangan' => 'nullable|string',
        ];
    }

    public function getPreviewKodeAkhirProperty(): string
    {
        if ($this->jumlah <= 1 || empty($this->kode_aset)) {
            return $this->kode_aset;
        }

        $parts = explode('/', $this->kode_aset);
        $last = (int) end($parts);
        array_pop($parts);
        $prefix = implode('/', $parts) . '/';

        return sprintf('%s%04d', $prefix, $last + $this->jumlah - 1);
    }

    public function mount()
    {
        $this->tahun_perolehan = (int) date('Y');
        $this->tanggal_pengadaan = date('Y-m-d');
        $defaultSumber = SumberDana::where('is_active', true)->first();
        if ($defaultSumber) {
            $this->sumber_dana_id = $defaultSumber->id;
            $this->sumber_dana = $defaultSumber->nama;
        }
    }

    public function regenerateKodeAset()
    {
        if (!$this->isEdit && $this->kategori_id) {
            $kat = Kategori::find($this->kategori_id);
            $sumber = $this->sumber_dana_id ? SumberDana::find($this->sumber_dana_id) : null;
            if ($kat) {
                $this->kode_aset = Aset::generateKodeAset($sumber?->kode, $kat->kode, $this->tahun_perolehan);
            }
        }
    }

    public function updatedKategoriId()
    {
        $this->regenerateKodeAset();
    }

    public function updatedSumberDanaId()
    {
        if ($this->sumber_dana_id) {
            $sumber = SumberDana::find($this->sumber_dana_id);
            if ($sumber) {
                $this->sumber_dana = $sumber->nama;
            }
        }
        $this->regenerateKodeAset();
    }

    public function updatedTahunPerolehan()
    {
        $this->regenerateKodeAset();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $this->resetForm();
        $this->isEdit = true;
        $this->asetId = $id;

        $aset = Aset::findOrFail($id);
        $this->kode_aset = $aset->kode_aset;
        $this->nama_barang = $aset->nama_barang;
        $this->merk_tipe = $aset->merk_tipe;
        $this->nomor_seri = $aset->nomor_seri;
        $this->kategori_id = $aset->kategori_id;
        $this->ruangan_id = $aset->ruangan_id;
        $this->sumber_dana_id = $aset->sumber_dana_id;
        $this->tahun_perolehan = $aset->tahun_perolehan;
        $this->tanggal_pengadaan = $aset->tanggal_pengadaan?->format('Y-m-d') ?? '';
        $this->sumber_dana = $aset->sumber_dana;
        $this->harga_perolehan = (float) $aset->harga_perolehan;
        $this->kondisi = $aset->kondisi;
        $this->status = $aset->status;
        $this->keterangan = $aset->keterangan;

        $this->showModal = true;
    }

    public function showDetail(int $id)
    {
        $this->selectedAset = Aset::with(['kategori', 'ruangan', 'sumberDana', 'peminjamans', 'pemeliharaans.pelapor'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->isEdit && $this->asetId) {
            unset($data['jumlah']);
            $aset = Aset::findOrFail($this->asetId);
            $aset->update($data);
            session()->flash('message', "Aset {$aset->kode_aset} berhasil diperbarui.");
        } else {
            $qty = max(1, min(100, (int) $this->jumlah));
            unset($data['jumlah']);

            $kat = Kategori::findOrFail($this->kategori_id);
            $sumber = $this->sumber_dana_id ? SumberDana::find($this->sumber_dana_id) : null;
            $sumberKode = $sumber?->kode;
            $cleanKat = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $kat->kode));
            $cleanSumber = $sumberKode ? strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $sumberKode), 0, 4)) : '';
            $prefix = $cleanSumber ? "AST/{$cleanSumber}/{$cleanKat}/{$this->tahun_perolehan}/" : "AST/{$cleanKat}/{$this->tahun_perolehan}/";

            $createdIds = [];
            $firstCode = '';
            $lastCode = '';

            DB::transaction(function () use ($qty, $data, $prefix, &$createdIds, &$firstCode, &$lastCode) {
                $latest = Aset::where('kode_aset', 'like', "{$prefix}%")
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->value('kode_aset');

                $startSeq = 1;
                if ($latest) {
                    $parts = explode('/', $latest);
                    $startSeq = ((int) end($parts)) + 1;
                }

                for ($i = 0; $i < $qty; $i++) {
                    $seq = $startSeq + $i;
                    $code = sprintf('%s%04d', $prefix, $seq);
                    if ($i === 0) {
                        $firstCode = $code;
                    }
                    $lastCode = $code;

                    $itemData = $data;
                    $itemData['kode_aset'] = $code;
                    if ($qty > 1) {
                        $itemData['nama_barang'] = $this->nama_barang . ' (Unit ' . ($i + 1) . ')';
                    }

                    $aset = Aset::create($itemData);
                    $createdIds[] = $aset->id;
                }
            });

            if ($qty > 1) {
                session()->flash('message', "Berhasil menambahkan {$qty} unit aset ({$firstCode} s/d {$lastCode}).");
            } else {
                session()->flash('message', "Aset baru {$firstCode} berhasil ditambahkan.");
            }
            session()->flash('batch_print_ids', implode(',', $createdIds));
            session()->flash('batch_count', $qty);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        $aset = Aset::findOrFail($id);
        $kode = $aset->kode_aset;
        $aset->delete();
        session()->flash('message', "Aset {$kode} berhasil dihapus.");
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->asetId = null;
        $this->jumlah = 1;
        $this->kode_aset = '';
        $this->nama_barang = '';
        $this->merk_tipe = '';
        $this->nomor_seri = '';
        $this->kategori_id = null;
        $this->ruangan_id = null;
        $defaultSumber = SumberDana::where('is_active', true)->first();
        $this->sumber_dana_id = $defaultSumber?->id;
        $this->sumber_dana = $defaultSumber?->nama ?? 'BOSP 2026';
        $this->tahun_perolehan = (int) date('Y');
        $this->tanggal_pengadaan = date('Y-m-d');
        $this->harga_perolehan = 0;
        $this->kondisi = 'Baik';
        $this->status = 'Tersedia';
        $this->keterangan = '';
    }

    public function render()
    {
        $query = Aset::with(['kategori', 'ruangan', 'sumberDana']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_barang', 'like', "%{$this->search}%")
                  ->orWhere('kode_aset', 'like', "%{$this->search}%")
                  ->orWhere('merk_tipe', 'like', "%{$this->search}%")
                  ->orWhere('nomor_seri', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterRuangan) {
            $query->where('ruangan_id', $this->filterRuangan);
        }
        if ($this->filterKategori) {
            $query->where('kategori_id', $this->filterKategori);
        }
        if ($this->filterKondisi) {
            $query->where('kondisi', $this->filterKondisi);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterSumberDanaId) {
            $query->where('sumber_dana_id', $this->filterSumberDanaId);
        }

        $asets = $query->latest()->paginate(10);
        $ruangans = Ruangan::where('is_active', true)->orderBy('nama_ruangan')->get();
        $kategoris = Kategori::asetTetap()->orderBy('nama')->get();
        $sumberDanas = SumberDana::where('is_active', true)->orderBy('kode')->get();

        return view('sarpras::livewire.aset-index', compact('asets', 'ruangans', 'kategoris', 'sumberDanas'));
    }
}
