<?php

namespace App\Modules\KalenderPendidikan\Livewire;

use App\Modules\KalenderPendidikan\Models\KalenderKategori;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kelola Kategori Agenda Kegiatan')]
class KategoriManager extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $kategoriId = null;

    public string $nama = '';
    public string $warna = '#014694';
    public string $deskripsi = '';
    public int $urutan = 0;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'warna' => 'required|string|max:30',
            'deskripsi' => 'nullable|string|max:255',
            'urutan' => 'integer|min:0',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->kategoriId = null;
        $this->nama = '';
        $this->warna = '#014694';
        $this->deskripsi = '';
        $this->urutan = KalenderKategori::count() + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function editKategori(int $id): void
    {
        $this->resetValidation();
        $kategori = KalenderKategori::findOrFail($id);
        $this->isEditing = true;
        $this->kategoriId = $kategori->id;
        $this->nama = $kategori->nama;
        $this->warna = $kategori->warna;
        $this->deskripsi = $kategori->deskripsi ?? '';
        $this->urutan = $kategori->urutan;
        $this->is_active = (bool) $kategori->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditing && $this->kategoriId) {
            $kategori = KalenderKategori::findOrFail($this->kategoriId);
            $kategori->update([
                'nama' => $this->nama,
                'warna' => $this->warna,
                'deskripsi' => $this->deskripsi,
                'urutan' => $this->urutan,
                'is_active' => $this->is_active,
            ]);
            session()->flash('status', 'Kategori kegiatan berhasil diperbarui.');
        } else {
            KalenderKategori::create([
                'nama' => $this->nama,
                'warna' => $this->warna,
                'deskripsi' => $this->deskripsi,
                'urutan' => $this->urutan,
                'is_active' => $this->is_active,
            ]);
            session()->flash('status', 'Kategori kegiatan baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $kategori = KalenderKategori::findOrFail($id);
        $kategori->update(['is_active' => ! $kategori->is_active]);
        session()->flash('status', 'Status kategori berhasil diubah.');
    }

    public function deleteKategori(int $id): void
    {
        $kategori = KalenderKategori::findOrFail($id);
        if ($kategori->agendas()->exists()) {
            session()->flash('error', 'Kategori tidak dapat dihapus karena masih digunakan pada agenda kegiatan.');
            return;
        }

        $kategori->delete();
        session()->flash('status', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $kategoris = KalenderKategori::withCount('agendas')->orderBy('urutan')->orderBy('nama')->get();

        return view('kalenderpendidikan::livewire.kategori-manager', [
            'kategoris' => $kategoris,
        ]);
    }
}
