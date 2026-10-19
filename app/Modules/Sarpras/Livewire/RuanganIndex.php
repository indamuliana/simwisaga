<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Ruangan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Master Ruangan')]
class RuanganIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $ruanganId = null;

    public string $kode_ruangan = '';
    public string $nama_ruangan = '';
    public ?string $lokasi = '';
    public ?string $keterangan = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'kode_ruangan' => 'required|string|max:50|unique:sarpras_ruangans,kode_ruangan,' . ($this->ruanganId ?? 'NULL') . ',id',
            'nama_ruangan' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->resetValidation();
        $this->isEdit = false;
        $this->ruanganId = null;
        $this->kode_ruangan = '';
        $this->nama_ruangan = '';
        $this->lokasi = '';
        $this->keterangan = '';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->ruanganId = $id;

        $r = Ruangan::findOrFail($id);
        $this->kode_ruangan = $r->kode_ruangan;
        $this->nama_ruangan = $r->nama_ruangan;
        $this->lokasi = $r->lokasi;
        $this->keterangan = $r->keterangan;
        $this->is_active = (bool) $r->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->isEdit && $this->ruanganId) {
            $r = Ruangan::findOrFail($this->ruanganId);
            $r->update($data);
            session()->flash('message', "Ruangan {$r->nama_ruangan} berhasil diperbarui.");
        } else {
            $r = Ruangan::create($data);
            session()->flash('message', "Ruangan {$r->nama_ruangan} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $r = Ruangan::withCount('asets')->findOrFail($id);
        if ($r->asets_count > 0) {
            session()->flash('error', "Ruangan '{$r->nama_ruangan}' tidak dapat dihapus karena masih menampung {$r->asets_count} aset.");
            return;
        }

        $nama = $r->nama_ruangan;
        $r->delete();
        session()->flash('message', "Ruangan '{$nama}' berhasil dihapus.");
    }

    public function render()
    {
        $query = Ruangan::withCount('asets');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_ruangan', 'like', "%{$this->search}%")
                  ->orWhere('kode_ruangan', 'like', "%{$this->search}%")
                  ->orWhere('lokasi', 'like', "%{$this->search}%");
            });
        }

        $ruangans = $query->orderBy('nama_ruangan')->paginate(10);

        return view('sarpras::livewire.ruangan-index', compact('ruangans'));
    }
}
