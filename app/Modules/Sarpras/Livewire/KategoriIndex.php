<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Kategori;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Master Kategori Aset')]
class KategoriIndex extends Component
{
    public string $search = '';

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $kategoriId = null;

    public string $kode = '';
    public string $nama = '';
    public string $jenis = 'aset_tetap';
    public ?string $deskripsi = '';

    protected function rules(): array
    {
        return [
            'kode' => 'required|string|max:20|unique:sarpras_kategoris,kode,' . ($this->kategoriId ?? 'NULL') . ',id',
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:aset_tetap,habis_pakai',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function create()
    {
        $this->resetValidation();
        $this->isEdit = false;
        $this->kategoriId = null;
        $this->kode = '';
        $this->nama = '';
        $this->jenis = 'aset_tetap';
        $this->deskripsi = '';
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->kategoriId = $id;

        $k = Kategori::findOrFail($id);
        $this->kode = $k->kode;
        $this->nama = $k->nama;
        $this->jenis = $k->jenis;
        $this->deskripsi = $k->deskripsi;

        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->isEdit && $this->kategoriId) {
            $k = Kategori::findOrFail($this->kategoriId);
            $k->update($data);
            session()->flash('message', "Kategori {$k->nama} berhasil diperbarui.");
        } else {
            $k = Kategori::create($data);
            session()->flash('message', "Kategori {$k->nama} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $k = Kategori::withCount(['asets', 'bhps'])->findOrFail($id);
        $totalItems = $k->asets_count + $k->bhps_count;
        if ($totalItems > 0) {
            session()->flash('error', "Kategori '{$k->nama}' tidak dapat dihapus karena masih digunakan oleh {$totalItems} barang.");
            return;
        }

        $nama = $k->nama;
        $k->delete();
        session()->flash('message', "Kategori '{$nama}' berhasil dihapus.");
    }

    public function render()
    {
        $query = Kategori::withCount(['asets', 'bhps']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('kode', 'like', "%{$this->search}%");
            });
        }

        $kategoris = $query->orderBy('jenis')->orderBy('nama')->get();

        return view('sarpras::livewire.kategori-index', compact('kategoris'));
    }
}
