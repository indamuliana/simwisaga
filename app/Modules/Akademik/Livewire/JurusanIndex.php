<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kompetensi Keahlian (Jurusan)')]
class JurusanIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $jurusanId = null;
    public string $kode = '';
    public string $nama = '';
    public string $kode_alias_text = '';
    public ?string $deskripsi = null;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:20', Rule::unique('jurusans', 'kode')->ignore($this->jurusanId)],
            'nama' => ['required', 'string', 'max:150'],
            'kode_alias_text' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.jurusan.tambah');
        $this->reset(['jurusanId', 'kode', 'nama', 'kode_alias_text', 'deskripsi']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.jurusan.edit');
        $j = Jurusan::findOrFail($id);
        $this->jurusanId = $j->id;
        $this->kode = $j->kode;
        $this->nama = $j->nama;
        $this->kode_alias_text = implode(', ', $j->kode_alias ?? []);
        $this->deskripsi = $j->deskripsi;
        $this->is_active = $j->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->jurusanId) {
            $this->guard('akademik.jurusan.edit');
        } else {
            $this->guard('akademik.jurusan.tambah');
        }

        $this->validate();

        $aliases = array_filter(array_map('trim', explode(',', $this->kode_alias_text)));

        $data = [
            'kode' => strtoupper(trim($this->kode)),
            'nama' => $this->nama,
            'kode_alias' => array_values($aliases),
            'deskripsi' => $this->deskripsi ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->jurusanId) {
            Jurusan::findOrFail($this->jurusanId)->update($data);
        } else {
            Jurusan::create($data);
        }

        $this->showModal = false;
        session()->flash('status', "Data jurusan {$data['kode']} berhasil disimpan.");
    }

    public function render()
    {
        $jurusans = Jurusan::withCount('rombels')->get();

        return view('akademik::livewire.jurusan-index', [
            'jurusans' => $jurusans,
        ]);
    }
}
