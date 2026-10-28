<?php

namespace App\Modules\KalenderPendidikan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\KalenderPendidikan\Models\KalenderLibur;
use App\Modules\KalenderPendidikan\Services\KalenderService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Pengaturan Hari Libur')]
class LiburManager extends Component
{
    use GuardsPermission;
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $liburId = null;

    public string $nama = '';
    public string $tgl_mulai = '';
    public string $tgl_selesai = '';
    public string $jenis = 'sekolah';
    public string $keterangan = '';
    public bool $is_kbm_libur = true;
    public $filterTahun = '';

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'jenis' => 'required|in:nasional,cuti_bersama,sekolah',
            'keterangan' => 'nullable|string|max:255',
            'is_kbm_libur' => 'boolean',
        ];
    }

    public function mount(): void
    {
        $this->filterTahun = (string) date('Y');
        $this->tgl_mulai = now()->toDateString();
        $this->tgl_selesai = now()->toDateString();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->liburId = null;
        $this->nama = '';
        $this->tgl_mulai = now()->toDateString();
        $this->tgl_selesai = now()->toDateString();
        $this->jenis = 'sekolah';
        $this->keterangan = '';
        $this->is_kbm_libur = true;
        $this->showModal = true;
    }

    public function editLibur(int $id): void
    {
        $this->resetValidation();
        $libur = KalenderLibur::findOrFail($id);
        $this->isEditing = true;
        $this->liburId = $libur->id;
        $this->nama = $libur->nama;
        $this->tgl_mulai = $libur->tgl_mulai->format('Y-m-d');
        $this->tgl_selesai = $libur->tgl_selesai->format('Y-m-d');
        $this->jenis = $libur->jenis;
        $this->keterangan = $libur->keterangan ?? '';
        $this->is_kbm_libur = (bool) $libur->is_kbm_libur;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->guard($this->isEditing ? 'kalender.libur.edit' : 'kalender.libur.tambah');

        $this->validate();
        $activeTa = TahunAjaran::active()->first();

        if ($this->isEditing && $this->liburId) {
            $libur = KalenderLibur::findOrFail($this->liburId);
            $libur->update([
                'nama' => $this->nama,
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
                'jenis' => $this->jenis,
                'keterangan' => $this->keterangan,
                'is_kbm_libur' => $this->is_kbm_libur,
            ]);
            session()->flash('status', 'Data hari libur berhasil diperbarui.');
        } else {
            KalenderLibur::create([
                'tahun_ajaran_id' => $activeTa?->id,
                'nama' => $this->nama,
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
                'jenis' => $this->jenis,
                'keterangan' => $this->keterangan,
                'is_kbm_libur' => $this->is_kbm_libur,
            ]);
            session()->flash('status', 'Hari libur baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function syncNational(KalenderService $service): void
    {
        $this->guard('kalender.libur.tambah');

        $year = (int) ($this->filterTahun ?: date('Y'));
        $count = $service->syncHariLiburNasional($year);

        session()->flash('status', "Sinkronisasi berhasil: {$count} hari libur nasional baru ditambahkan.");
    }

    public function deleteLibur(int $id): void
    {
        $this->guard('kalender.libur.hapus');

        KalenderLibur::findOrFail($id)->delete();
        session()->flash('status', 'Hari libur berhasil dihapus.');
    }

    public function render()
    {
        $query = KalenderLibur::query();

        if ($this->filterTahun) {
            $query->whereYear('tgl_mulai', $this->filterTahun);
        }

        $liburs = $query->orderBy('tgl_mulai')->get();

        return view('kalenderpendidikan::livewire.libur-manager', [
            'liburs' => $liburs,
        ]);
    }
}
