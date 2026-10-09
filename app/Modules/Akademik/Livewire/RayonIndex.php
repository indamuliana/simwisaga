<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Pengelolaan Rayon')]
class RayonIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $rayonId = null;
    public string $nama = '';
    public ?string $wilayah_cakupan = null;
    public ?int $pembimbing_id = null;
    public ?string $keterangan = null;

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:50', Rule::unique('rayons', 'nama')->ignore($this->rayonId)],
            'wilayah_cakupan' => ['nullable', 'string', 'max:255'],
            'pembimbing_id' => ['nullable', 'exists:users,id'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.rayon.tambah');
        $this->reset(['rayonId', 'nama', 'wilayah_cakupan', 'pembimbing_id', 'keterangan']);
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.rayon.edit');
        $r = Rayon::findOrFail($id);
        $this->rayonId = $r->id;
        $this->nama = $r->nama;
        $this->wilayah_cakupan = $r->wilayah_cakupan;
        $this->pembimbing_id = $r->pembimbing_id;
        $this->keterangan = $r->keterangan;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->rayonId) {
            $this->guard('akademik.rayon.edit');
        } else {
            $this->guard('akademik.rayon.tambah');
        }

        $this->validate();

        $data = [
            'nama' => $this->nama,
            'wilayah_cakupan' => $this->wilayah_cakupan ?: null,
            'pembimbing_id' => $this->pembimbing_id ?: null,
            'keterangan' => $this->keterangan ?: null,
        ];

        if ($this->rayonId) {
            Rayon::findOrFail($this->rayonId)->update($data);
        } else {
            Rayon::create($data);
        }

        $this->showModal = false;
        session()->flash('status', "Rayon {$data['nama']} berhasil disimpan.");
    }

    public function render()
    {
        $rayons = Rayon::with('pembimbing')->orderBy('nama')->get();
        $gurus = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Guru', 'Operator', 'Tata Usaha']))
            ->orWhereDoesntHave('roles')
            ->orderBy('name')
            ->get();

        return view('akademik::livewire.rayon-index', [
            'rayons' => $rayons,
            'gurus' => $gurus,
        ]);
    }
}
