<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Mapel;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Mata Pelajaran (Mapel)')]
class MapelIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $mapelId = null;
    public string $kode = '';
    public string $nama = '';
    public string $kelompok = Mapel::KELOMPOK_UMUM;
    public int $kkm_default = 75;
    public int $urutan = 1;
    public bool $is_active = true;

    public string $search = '';
    public string $filterKelompok = '';

    protected function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:30', Rule::unique('mapels', 'kode')->ignore($this->mapelId)],
            'nama' => ['required', 'string', 'max:150'],
            'kelompok' => ['required', Rule::in(array_keys(Mapel::DAFTAR_KELOMPOK))],
            'kkm_default' => ['required', 'integer', 'min:0', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.mapel.tambah');
        $this->reset(['mapelId', 'kode', 'nama']);
        $this->kelompok = $this->filterKelompok ?: Mapel::KELOMPOK_UMUM;
        $this->kkm_default = 75;
        $this->urutan = (Mapel::max('urutan') ?? 0) + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.mapel.edit');
        $m = Mapel::findOrFail($id);
        $this->mapelId = $m->id;
        $this->kode = $m->kode;
        $this->nama = $m->nama;
        $this->kelompok = $m->kelompok;
        $this->kkm_default = $m->kkm_default;
        $this->urutan = $m->urutan;
        $this->is_active = $m->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->mapelId) {
            $this->guard('akademik.mapel.edit');
        } else {
            $this->guard('akademik.mapel.tambah');
        }

        $this->validate();

        $data = [
            'kode' => strtoupper(trim($this->kode)),
            'nama' => $this->nama,
            'kelompok' => $this->kelompok,
            'kkm_default' => $this->kkm_default,
            'urutan' => $this->urutan,
            'is_active' => $this->is_active,
        ];

        if ($this->mapelId) {
            Mapel::findOrFail($this->mapelId)->update($data);
        } else {
            Mapel::create($data);
        }

        $this->showModal = false;
        session()->flash('status', "Mata pelajaran {$data['nama']} berhasil disimpan.");
    }

    public function delete(int $id): void
    {
        $this->guard('akademik.mapel.hapus');
        $m = Mapel::findOrFail($id);
        $nama = $m->nama;
        $m->delete();
        session()->flash('status', "Mata pelajaran {$nama} berhasil dihapus.");
    }

    public function render()
    {
        $query = Mapel::query();

        if ($this->search) {
            $query->where(fn ($q) => $q->where('nama', 'like', "%{$this->search}%")
                ->orWhere('kode', 'like', "%{$this->search}%"));
        }

        if ($this->filterKelompok) {
            $query->where('kelompok', $this->filterKelompok);
        }

        $mapels = $query->orderBy('kelompok')->orderBy('urutan')->get();

        return view('akademik::livewire.mapel-index', [
            'mapels' => $mapels,
            'daftarKelompok' => Mapel::DAFTAR_KELOMPOK,
        ]);
    }
}
