<?php

namespace App\Modules\Sarpras\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Sarpras\Models\SumberDana;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Master Sumber Dana Aset')]
class SumberDanaIndex extends Component
{
    use WithPagination, GuardsPermission;

    public string $search = '';

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $sumberId = null;

    public string $kode = '';
    public string $nama = '';
    public ?string $keterangan = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'kode' => 'required|string|size:4|alpha_num|uppercase|unique:sarpras_sumber_danas,kode,' . ($this->sumberId ?? 'NULL') . ',id',
            'nama' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function updatedKode($value)
    {
        $this->kode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value));
    }

    public function create()
    {
        $this->resetValidation();
        $this->isEdit = false;
        $this->sumberId = null;
        $this->kode = '';
        $this->nama = '';
        $this->keterangan = '';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->sumberId = $id;

        $s = SumberDana::findOrFail($id);
        $this->kode = $s->kode;
        $this->nama = $s->nama;
        $this->keterangan = $s->keterangan;
        $this->is_active = (bool) $s->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $this->guard($this->isEdit ? 'sarpras.sumber_dana.edit' : 'sarpras.sumber_dana.tambah');

        $this->kode = strtoupper(trim($this->kode));
        $data = $this->validate();

        if ($this->isEdit && $this->sumberId) {
            $s = SumberDana::findOrFail($this->sumberId);
            $s->update($data);
            session()->flash('message', "Sumber Dana {$s->nama} ({$s->kode}) berhasil diperbarui.");
        } else {
            $s = SumberDana::create($data);
            session()->flash('message', "Sumber Dana baru {$s->nama} ({$s->kode}) berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        $this->guard('sarpras.sumber_dana.hapus');

        $s = SumberDana::withCount('asets')->findOrFail($id);
        if ($s->asets_count > 0) {
            session()->flash('error', "Sumber Dana '{$s->nama}' tidak dapat dihapus karena masih terkait dengan {$s->asets_count} aset.");
            return;
        }

        $nama = $s->nama;
        $s->delete();
        session()->flash('message', "Sumber Dana '{$nama}' berhasil dihapus.");
    }

    public function render()
    {
        $query = SumberDana::withCount('asets');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('kode', 'like', "%{$this->search}%");
            });
        }

        $sumbers = $query->orderBy('kode')->paginate(10);

        return view('sarpras::livewire.sumber-dana-index', compact('sumbers'));
    }
}
