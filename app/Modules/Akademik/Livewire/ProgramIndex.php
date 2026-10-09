<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Program;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Setting Program Pendidikan')]
class ProgramIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $programId = null;
    public string $kode = '';
    public string $nama = '';
    public ?string $deskripsi = null;
    public bool $is_active = true;
    public string $search = '';

    protected function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:20', Rule::unique('programs', 'kode')->ignore($this->programId)],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    protected $messages = [
        'kode.required' => 'Kode program wajib diisi.',
        'kode.unique' => 'Kode program ini sudah terdaftar.',
        'nama.required' => 'Nama program wajib diisi.',
    ];

    public function create(): void
    {
        $this->guard('akademik.program.tambah');
        $this->reset(['programId', 'kode', 'nama', 'deskripsi']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.program.edit');
        $p = Program::findOrFail($id);
        $this->programId = $p->id;
        $this->kode = $p->kode;
        $this->nama = $p->nama;
        $this->deskripsi = $p->deskripsi;
        $this->is_active = (bool) $p->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->programId) {
            $this->guard('akademik.program.edit');
        } else {
            $this->guard('akademik.program.tambah');
        }

        $this->validate();

        $data = [
            'kode' => strtoupper(trim($this->kode)),
            'nama' => trim($this->nama),
            'deskripsi' => $this->deskripsi ? trim($this->deskripsi) : null,
            'is_active' => $this->is_active,
        ];

        if ($this->programId) {
            Program::findOrFail($this->programId)->update($data);
            $msg = "Program {$data['nama']} berhasil diperbarui.";
        } else {
            Program::create($data);
            $msg = "Program baru {$data['nama']} berhasil ditambahkan.";
        }

        $this->showModal = false;
        session()->flash('status', $msg);
    }

    public function toggleActive(int $id): void
    {
        $this->guard('akademik.program.edit');
        $p = Program::findOrFail($id);
        $p->is_active = !$p->is_active;
        $p->save();

        $statusStr = $p->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('status', "Status Program {$p->nama} berhasil {$statusStr}.");
    }

    public function delete(int $id): void
    {
        $this->guard('akademik.program.hapus');
        $program = Program::withCount('siswas')->findOrFail($id);

        if ($program->siswas_count > 0) {
            session()->flash('error', "Program '{$program->nama}' tidak dapat dihapus karena masih digunakan oleh {$program->siswas_count} siswa. Silakan ubah status program menjadi Nonaktif jika tidak lagi digunakan.");
            return;
        }

        $nama = $program->nama;
        $program->delete();
        session()->flash('status', "Program {$nama} berhasil dihapus.");
    }

    public function render()
    {
        $query = Program::withCount('siswas');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode', 'like', "%{$this->search}%")
                    ->orWhere('nama', 'like', "%{$this->search}%")
                    ->orWhere('deskripsi', 'like', "%{$this->search}%");
            });
        }

        $programs = $query->orderBy('kode')->get();

        return view('akademik::livewire.program-index', [
            'programs' => $programs,
        ]);
    }
}
