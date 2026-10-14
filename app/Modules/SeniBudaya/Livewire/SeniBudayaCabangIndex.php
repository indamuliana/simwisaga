<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\Kepegawaian\Models\Pegawai;

#[Layout('components.layouts.app')]
#[Title('Pengaturan Cabang Seni Budaya')]
class SeniBudayaCabangIndex extends Component
{
    public $cabangs;
    public $pegawais = [];

    // Form Modal State
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $kode = '';
    public string $nama = '';
    public ?int $guru_id = null;
    public ?string $instruktur_nama = null;
    public int $kkm = 75;
    public int $bobot_presensi = 30;
    public int $bobot_penilaian = 70;
    public ?string $hari_latihan = '';
    public ?string $jam_mulai = null;
    public ?string $jam_selesai = null;
    public ?string $tempat = '';
    public ?string $deskripsi = '';
    public bool $is_active = true;

    protected function rules()
    {
        return [
            'kode' => 'required|string|max:50|unique:seni_budayas,kode,' . ($this->editingId ?? 'NULL') . ',id',
            'nama' => 'required|string|max:150',
            'guru_id' => 'nullable|exists:pegawais,id',
            'instruktur_nama' => 'nullable|string|max:150',
            'kkm' => 'required|integer|min:0|max:100',
            'bobot_presensi' => 'required|integer|min:0|max:100',
            'bobot_penilaian' => 'required|integer|min:0|max:100',
            'hari_latihan' => 'nullable|string|max:50',
            'tempat' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->cabangs = SeniBudaya::with('guru')->orderBy('nama')->get();
        $this->pegawais = Pegawai::orderBy('nama_lengkap')->get();
    }

    public function openCreateModal()
    {
        $this->reset([
            'editingId', 'kode', 'nama', 'guru_id', 'instruktur_nama',
            'hari_latihan', 'jam_mulai', 'jam_selesai', 'tempat', 'deskripsi'
        ]);
        $this->kkm = 75;
        $this->bobot_presensi = 30;
        $this->bobot_penilaian = 70;
        $this->is_active = true;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $cabang = SeniBudaya::findOrFail($id);
        $this->editingId = $cabang->id;
        $this->kode = $cabang->kode;
        $this->nama = $cabang->nama;
        $this->guru_id = $cabang->guru_id;
        $this->instruktur_nama = $cabang->instruktur_nama;
        $this->kkm = $cabang->kkm;
        $this->bobot_presensi = $cabang->bobot_presensi;
        $this->bobot_penilaian = $cabang->bobot_penilaian;
        $this->hari_latihan = $cabang->hari_latihan;
        $this->jam_mulai = $cabang->jam_mulai;
        $this->jam_selesai = $cabang->jam_selesai;
        $this->tempat = $cabang->tempat;
        $this->deskripsi = $cabang->deskripsi;
        $this->is_active = (bool) $cabang->is_active;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editingId) {
            $cabang = SeniBudaya::findOrFail($this->editingId);
            $cabang->update($validated);
            session()->flash('message', "Cabang '{$cabang->nama}' berhasil diperbarui.");
        } else {
            $cabang = SeniBudaya::create($validated);
            session()->flash('message', "Cabang baru '{$cabang->nama}' berhasil ditambahkan.");
        }

        $this->showModal = false;
        $this->loadData();
    }

    public function toggleActive(int $id)
    {
        $cabang = SeniBudaya::findOrFail($id);
        $cabang->update(['is_active' => !$cabang->is_active]);
        $this->loadData();
        session()->flash('message', "Status aktif cabang '{$cabang->nama}' berhasil diubah.");
    }

    public function delete(int $id)
    {
        $cabang = SeniBudaya::withCount(['anggotas', 'tps'])->findOrFail($id);

        if ($cabang->anggotas_count > 0) {
            session()->flash('error', "Cabang '{$cabang->nama}' tidak dapat dihapus karena masih memiliki {$cabang->anggotas_count} siswa terdaftar. Silakan nonaktifkan cabang jika sudah tidak digunakan.");
            return;
        }

        $name = $cabang->nama;
        $cabang->delete();
        $this->loadData();
        session()->flash('message', "Cabang '{$name}' berhasil dihapus.");
    }

    public function render()
    {
        return view('senibudaya::livewire.cabang-index');
    }
}
