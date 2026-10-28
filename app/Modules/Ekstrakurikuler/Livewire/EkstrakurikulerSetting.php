<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use App\Core\Concerns\GuardsPermission;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Models\User;

class EkstrakurikulerSetting extends Component
{
    use GuardsPermission;
    public $search = '';
    public $filterKategori = 'all'; // all, wajib, pilihan

    public $showModal = false;
    public $editingId = null;

    // Form fields
    public $nama = '';
    public $pembina_id = null;
    public $instruktur_user_id = null;
    public $instruktur_nama = '';
    public $is_wajib = false;
    public $is_active = true;
    public $hari_latihan = 'Jumat';
    public $jam_mulai = '15:30';
    public $jam_selesai = '17:00';
    public $tempat = 'Lapangan / Ruang Praktik';
    public $bobot_presensi = 30;
    public $bobot_penilaian = 70;
    public $threshold_a = 90;
    public $threshold_b = 80;
    public $threshold_c = 70;
    public $deskripsi = '';

    protected $rules = [
        'nama' => 'required|string|max:150',
        'pembina_id' => 'nullable|exists:pegawais,id',
        'instruktur_user_id' => 'nullable|exists:users,id',
        'instruktur_nama' => 'nullable|string|max:150',
        'is_wajib' => 'boolean',
        'is_active' => 'boolean',
        'hari_latihan' => 'nullable|string|max:50',
        'jam_mulai' => 'nullable',
        'jam_selesai' => 'nullable',
        'tempat' => 'nullable|string|max:100',
        'bobot_presensi' => 'required|integer|min:0|max:100',
        'bobot_penilaian' => 'required|integer|min:0|max:100',
        'threshold_a' => 'required|integer|min:1|max:100',
        'threshold_b' => 'required|integer|min:1|max:100',
        'threshold_c' => 'required|integer|min:1|max:100',
        'deskripsi' => 'nullable|string',
    ];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasRole(['Kesiswaan', 'Kurikulum', 'Operator']) && !$user->can('ekskul.setting'))) {
            abort(403, 'Akses terbatas: Pengaturan Ekstrakurikuler hanya dapat diakses oleh Administrator atau Tim Kesiswaan.');
        }
    }

    public function create()
    {
        $this->reset([
            'editingId', 'nama', 'pembina_id', 'instruktur_user_id', 'instruktur_nama',
            'is_wajib', 'is_active', 'hari_latihan', 'jam_mulai', 'jam_selesai',
            'tempat', 'bobot_presensi', 'bobot_penilaian', 'threshold_a', 'threshold_b',
            'threshold_c', 'deskripsi'
        ]);
        $this->is_active = true;
        $this->hari_latihan = 'Jumat';
        $this->jam_mulai = '15:30';
        $this->jam_selesai = '17:00';
        $this->tempat = 'Lapangan / Ruang Praktik';
        $this->bobot_presensi = 30;
        $this->bobot_penilaian = 70;
        $this->threshold_a = 90;
        $this->threshold_b = 80;
        $this->threshold_c = 70;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);
        $this->editingId = $ekskul->id;
        $this->nama = $ekskul->nama;
        $this->pembina_id = $ekskul->pembina_id;
        $this->instruktur_user_id = $ekskul->instruktur_user_id;
        $this->instruktur_nama = $ekskul->instruktur_nama ?? '';
        $this->is_wajib = (bool) $ekskul->is_wajib;
        $this->is_active = (bool) $ekskul->is_active;
        $this->hari_latihan = $ekskul->hari_latihan ?? 'Jumat';
        $this->jam_mulai = $ekskul->jam_mulai ? substr($ekskul->jam_mulai, 0, 5) : '15:30';
        $this->jam_selesai = $ekskul->jam_selesai ? substr($ekskul->jam_selesai, 0, 5) : '17:00';
        $this->tempat = $ekskul->tempat ?? '';
        $this->bobot_presensi = $ekskul->bobot_presensi ?? 30;
        $this->bobot_penilaian = $ekskul->bobot_penilaian ?? 70;
        $this->threshold_a = $ekskul->threshold_a ?? 90;
        $this->threshold_b = $ekskul->threshold_b ?? 80;
        $this->threshold_c = $ekskul->threshold_c ?? 70;
        $this->deskripsi = $ekskul->deskripsi ?? '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->guard('ekstrakurikuler.ekskul.edit');

        $this->validate();

        $data = [
            'nama' => $this->nama,
            'pembina_id' => $this->pembina_id ?: null,
            'instruktur_user_id' => $this->instruktur_user_id ?: null,
            'instruktur_nama' => $this->instruktur_nama ?: null,
            'is_wajib' => $this->is_wajib,
            'is_active' => $this->is_active,
            'hari_latihan' => $this->hari_latihan,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'tempat' => $this->tempat,
            'bobot_presensi' => $this->bobot_presensi,
            'bobot_penilaian' => $this->bobot_penilaian,
            'threshold_a' => $this->threshold_a,
            'threshold_b' => $this->threshold_b,
            'threshold_c' => $this->threshold_c,
            'deskripsi' => $this->deskripsi,
        ];

        if ($this->editingId) {
            $ekskul = Ekstrakurikuler::findOrFail($this->editingId);
            $ekskul->update($data);
            session()->flash('message', "Pengaturan ekstrakurikuler {$ekskul->nama} berhasil diperbarui.");
        } else {
            $ekskul = Ekstrakurikuler::create($data);
            session()->flash('message', "Ekstrakurikuler {$ekskul->nama} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function toggleStatus($id)
    {
        $this->guard('ekstrakurikuler.ekskul.edit');

        $ekskul = Ekstrakurikuler::findOrFail($id);
        $ekskul->is_active = !$ekskul->is_active;
        $ekskul->save();

        session()->flash('message', "Status ekskul {$ekskul->nama} diubah menjadi " . ($ekskul->is_active ? 'Aktif' : 'Non-Aktif') . '.');
    }

    public function delete($id)
    {
        $this->guard('ekstrakurikuler.ekskul.hapus');

        $ekskul = Ekstrakurikuler::findOrFail($id);
        $nama = $ekskul->nama;
        $ekskul->delete();

        session()->flash('message', "Ekstrakurikuler {$nama} berhasil dihapus.");
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = Ekstrakurikuler::with(['pembina', 'instrukturUser']);

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        if ($this->filterKategori === 'wajib') {
            $query->where('is_wajib', true);
        } elseif ($this->filterKategori === 'pilihan') {
            $query->where('is_wajib', false);
        }

        $ekskuls = $query->orderByDesc('is_wajib')->orderBy('nama')->get();

        $pegawais = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();
        $instrukturUsers = User::role('Instruktur Ekskul')->where('is_active', true)->orderBy('name')->get();

        return view('ekstrakurikuler::livewire.setting', [
            'ekskuls' => $ekskuls,
            'pegawais' => $pegawais,
            'instrukturUsers' => $instrukturUsers,
        ]);
    }
}
