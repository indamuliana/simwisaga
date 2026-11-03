<?php

namespace App\Modules\Kepesantrenan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqoh;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohPresensi;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohPresensiDetail;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Halaqoh & Presensi Santri')]
class HalaqohIndex extends Component
{
    use GuardsPermission;

    public $activeTab = 'presensi'; // 'presensi' atau 'kelompok'

    // Tab Kelompok Halaqoh
    public $showHalaqohModal = false;

    public $editingHalaqohId = null;

    public $nama_halaqoh = '';

    public $pengajar_id = null;

    public $jenis_kelamin = 'L';

    public $keterangan = '';

    // Modal Plotting Santri ke Halaqoh
    public $showPlottingModal = false;

    public $activeHalaqoh = null;

    public $searchSiswa = '';

    public $selectedSiswaId = null;

    // Tab Presensi Halaqoh
    public $selectedHalaqohId = null;

    public $presensiTanggal = '';

    public $presensiWaktuSesi = 'subuh';

    public $presensiCatatan = '';

    // Format: [ siswa_id => 'H' / 'S' / 'I' / 'A' ]
    public $attendanceMap = [];

    public $attendanceNotes = [];

    public function mount(): void
    {
        $this->guard('pesantren.halaqoh.lihat');

        $this->presensiTanggal = now()->toDateString();

        // Default pilih halaqoh pertama jika ada
        $firstHalaqoh = PesantrenHalaqoh::first();
        if ($firstHalaqoh) {
            $this->selectedHalaqohId = $firstHalaqoh->id;
            $this->loadPresensiData();
        }
    }

    public function updatedSelectedHalaqohId(): void
    {
        $this->loadPresensiData();
    }

    public function updatedPresensiTanggal(): void
    {
        $this->loadPresensiData();
    }

    public function updatedPresensiWaktuSesi(): void
    {
        $this->loadPresensiData();
    }

    public function loadPresensiData(): void
    {
        if (! $this->selectedHalaqohId) {
            return;
        }

        $halaqoh = PesantrenHalaqoh::with('siswas')->find($this->selectedHalaqohId);
        if (! $halaqoh) {
            return;
        }

        // Cari rekaman presensi jika sudah pernah disimpan
        $existing = PesantrenHalaqohPresensi::with('details')
            ->where('halaqoh_id', $this->selectedHalaqohId)
            ->where('tanggal', $this->presensiTanggal)
            ->where('waktu_sesi', $this->presensiWaktuSesi)
            ->first();

        $map = [];
        $notes = [];

        if ($existing) {
            $this->presensiCatatan = $existing->catatan ?? '';
            foreach ($existing->details as $d) {
                $map[$d->siswa_id] = $d->status;
                $notes[$d->siswa_id] = $d->catatan ?? '';
            }
        }

        // Defaultkan santri yang belum tercatat menjadi 'H' (Hadir)
        foreach ($halaqoh->siswas as $s) {
            if (! isset($map[$s->id])) {
                $map[$s->id] = 'H';
            }
        }

        $this->attendanceMap = $map;
        $this->attendanceNotes = $notes;
    }

    public function setAllStatus(string $status): void
    {
        foreach ($this->attendanceMap as $id => $val) {
            $this->attendanceMap[$id] = $status;
        }
    }

    public function savePresensi(): void
    {
        $this->guard('pesantren.halaqoh.absen');
        if (! $this->selectedHalaqohId) {
            return;
        }

        $presensi = PesantrenHalaqohPresensi::updateOrCreate(
            [
                'halaqoh_id' => $this->selectedHalaqohId,
                'tanggal' => $this->presensiTanggal,
                'waktu_sesi' => $this->presensiWaktuSesi,
            ],
            [
                'catatan' => $this->presensiCatatan ?: null,
            ]
        );

        foreach ($this->attendanceMap as $siswaId => $status) {
            PesantrenHalaqohPresensiDetail::updateOrCreate(
                [
                    'presensi_id' => $presensi->id,
                    'siswa_id' => $siswaId,
                ],
                [
                    'status' => $status,
                    'catatan' => $this->attendanceNotes[$siswaId] ?? null,
                ]
            );
        }

        session()->flash('message', 'Presensi halaqoh berhasil disimpan.');
    }

    // CRUD Halaqoh
    public function createHalaqoh(): void
    {
        $this->guard('pesantren.halaqoh.kelola');
        $this->reset(['editingHalaqohId', 'nama_halaqoh', 'keterangan']);
        $this->pengajar_id = null;
        $this->jenis_kelamin = 'L';
        $this->showHalaqohModal = true;
    }

    public function editHalaqoh(int $id): void
    {
        $this->guard('pesantren.halaqoh.kelola');
        $h = PesantrenHalaqoh::findOrFail($id);
        $this->editingHalaqohId = $h->id;
        $this->nama_halaqoh = $h->nama_halaqoh;
        $this->pengajar_id = $h->pengajar_id;
        $this->jenis_kelamin = $h->jenis_kelamin ?? 'L';
        $this->keterangan = $h->keterangan;
        $this->showHalaqohModal = true;
    }

    public function saveHalaqoh(): void
    {
        $this->guard('pesantren.halaqoh.kelola');
        $this->validate([
            'nama_halaqoh' => 'required|string|max:100',
            'pengajar_id' => 'nullable|exists:pegawais,id',
            'jenis_kelamin' => 'required|in:L,P',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $taAktif = TahunAjaran::where('is_active', true)->first();

        PesantrenHalaqoh::updateOrCreate(
            ['id' => $this->editingHalaqohId],
            [
                'tahun_ajaran_id' => $taAktif?->id ?? 1,
                'nama_halaqoh' => $this->nama_halaqoh,
                'pengajar_id' => $this->pengajar_id ?: null,
                'jenis_kelamin' => $this->jenis_kelamin,
                'keterangan' => $this->keterangan,
            ]
        );

        $this->showHalaqohModal = false;
        session()->flash('message', 'Kelompok halaqoh berhasil disimpan.');
    }

    public function deleteHalaqoh(int $id): void
    {
        $this->guard('pesantren.halaqoh.kelola');
        PesantrenHalaqoh::findOrFail($id)->delete();
        session()->flash('message', 'Halaqoh berhasil dihapus.');
    }

    public function openPlotting(int $halaqohId): void
    {
        $this->activeHalaqoh = PesantrenHalaqoh::with(['siswas.rombel'])->findOrFail($halaqohId);
        $this->reset(['searchSiswa', 'selectedSiswaId']);
        $this->showPlottingModal = true;
    }

    public function addSiswaToHalaqoh(): void
    {
        $this->guard('pesantren.halaqoh.kelola');
        if (! $this->selectedSiswaId || ! $this->activeHalaqoh) {
            return;
        }

        PesantrenHalaqohSiswa::updateOrCreate([
            'halaqoh_id' => $this->activeHalaqoh->id,
            'siswa_id' => $this->selectedSiswaId,
        ]);

        $this->activeHalaqoh->load(['siswas.rombel']);
        $this->reset(['selectedSiswaId', 'searchSiswa']);
        session()->flash('message', 'Santri berhasil dimasukkan ke halaqoh.');
    }

    public function removeSiswaFromHalaqoh(int $siswaId): void
    {
        $this->guard('pesantren.halaqoh.kelola');
        PesantrenHalaqohSiswa::where('halaqoh_id', $this->activeHalaqoh->id)
            ->where('siswa_id', $siswaId)
            ->delete();

        $this->activeHalaqoh->load(['siswas.rombel']);
        session()->flash('message', 'Santri dikeluarkan dari halaqoh.');
    }

    public function render()
    {
        $halaqohs = PesantrenHalaqoh::with(['pengajar', 'siswas'])
            ->withCount('halaqohSiswas')
            ->orderBy('nama_halaqoh')
            ->get();

        $selectedHalaqoh = $this->selectedHalaqohId ? PesantrenHalaqoh::with(['siswas.rombel', 'pengajar'])->find($this->selectedHalaqohId) : null;
        $pengajars = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();

        $availableSiswas = collect();
        if ($this->showPlottingModal && $this->activeHalaqoh) {
            $existingIds = $this->activeHalaqoh->siswas->pluck('id')->toArray();
            $availableSiswas = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
                ->where('is_active', true)
                ->where('jk', $this->activeHalaqoh->jenis_kelamin)
                ->whereNotIn('id', $existingIds)
                ->when($this->searchSiswa, function ($q) {
                    $q->where('nama', 'like', "%{$this->searchSiswa}%")
                        ->orWhere('nisn', 'like', "%{$this->searchSiswa}%");
                })
                ->take(15)
                ->get();
        }

        return view('kepesantrenan::livewire.halaqoh-index', [
            'halaqohs' => $halaqohs,
            'selectedHalaqoh' => $selectedHalaqoh,
            'pengajars' => $pengajars,
            'availableSiswas' => $availableSiswas,
        ]);
    }
}
