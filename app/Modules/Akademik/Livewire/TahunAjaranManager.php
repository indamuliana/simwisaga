<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tahun Ajaran & Semester')]
class TahunAjaranManager extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public string $nama = '';
    public ?string $tgl_mulai = null;
    public ?string $tgl_selesai = null;
    public bool $is_active = false;

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:20', 'unique:tahun_ajarans,nama'],
            'tgl_mulai' => ['nullable', 'date'],
            'tgl_selesai' => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
            'is_active' => ['boolean'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.tahunajaran.tambah');
        $this->reset(['nama', 'tgl_mulai', 'tgl_selesai', 'is_active']);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->guard('akademik.tahunajaran.tambah');
        $this->validate();

        if ($this->is_active) {
            TahunAjaran::query()->update(['is_active' => false]);
            Semester::query()->update(['is_active' => false]);
        }

        $ta = TahunAjaran::create([
            'nama' => $this->nama,
            'tgl_mulai' => $this->tgl_mulai ?: null,
            'tgl_selesai' => $this->tgl_selesai ?: null,
            'is_active' => $this->is_active,
        ]);

        // Otomatis buat semester Ganjil dan Genap
        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'ganjil',
            'is_active' => $this->is_active,
        ]);
        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'genap',
            'is_active' => false,
        ]);

        $this->showModal = false;
        session()->flash('status', "Tahun ajaran {$ta->nama} berhasil ditambahkan.");
    }

    public function setActiveSemester(int $semesterId): void
    {
        $this->guard('akademik.tahunajaran.edit');

        $target = Semester::with('tahunAjaran')->findOrFail($semesterId);

        // Nonaktifkan semua tahun ajaran & semester lain
        TahunAjaran::query()->update(['is_active' => false]);
        Semester::query()->update(['is_active' => false]);

        $target->update(['is_active' => true]);
        $target->tahunAjaran->update(['is_active' => true]);

        session()->flash('status', "Semester aktif berhasil diubah menjadi: {$target->label}.");
    }

    public function toggleLockNilai(int $semesterId): void
    {
        $this->guard('akademik.tahunajaran.edit');

        $sem = Semester::findOrFail($semesterId);
        $sem->nilai_locked_at = $sem->nilai_locked_at ? null : now();
        $sem->save();

        session()->flash('status', 'Status penguncian nilai diperbarui.');
    }

    public function toggleLockAbsensi(int $semesterId): void
    {
        $this->guard('akademik.tahunajaran.edit');

        $sem = Semester::findOrFail($semesterId);
        $sem->absensi_locked_at = $sem->absensi_locked_at ? null : now();
        $sem->save();

        session()->flash('status', 'Status penguncian absensi diperbarui.');
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::with('semesters')->orderBy('id', 'desc')->get();

        return view('akademik::livewire.tahun-ajaran-manager', [
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}
