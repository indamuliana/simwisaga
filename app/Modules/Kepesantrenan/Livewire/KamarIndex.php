<?php

namespace App\Modules\Kepesantrenan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepesantrenan\Models\PesantrenKamar;
use App\Modules\Kepesantrenan\Models\PesantrenKamarSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Kamar & Asrama Santri')]
class KamarIndex extends Component
{
    use GuardsPermission, WithPagination;

    public $search = '';

    public $filterGender = 'all'; // all, L, P

    public $filterTahunAjaranId = null;

    // Modal Kamar Form
    public $showKamarModal = false;

    public $editingKamarId = null;

    public $nama_kamar = '';

    public $gedung = '';

    public $jenis_kelamin = 'L';

    public $kapasitas = 8;

    public $musrif_id = null;

    public $keterangan = '';

    // Modal Plotting Anggota
    public $showPlottingModal = false;

    public $activeKamar = null;

    public $searchSiswa = '';

    public $selectedSiswaId = null;

    public $selectedPeran = 'anggota';

    public $selectedNomorRanjang = '';

    protected function rules(): array
    {
        return [
            'nama_kamar' => 'required|string|max:100',
            'gedung' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'kapasitas' => 'required|integer|min:1|max:50',
            'musrif_id' => 'nullable|exists:pegawais,id',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function mount(): void
    {
        $this->guard('pesantren.kamar.lihat');

        $taAktif = TahunAjaran::where('is_active', true)->first();
        $this->filterTahunAjaranId = $taAktif?->id;
    }

    public function createKamar(): void
    {
        $this->guard('pesantren.kamar.kelola');
        $this->reset(['editingKamarId', 'nama_kamar', 'gedung', 'keterangan']);
        $this->jenis_kelamin = 'L';
        $this->kapasitas = 8;
        $this->musrif_id = null;
        $this->showKamarModal = true;
    }

    public function editKamar(int $id): void
    {
        $this->guard('pesantren.kamar.kelola');
        $kamar = PesantrenKamar::findOrFail($id);
        $this->editingKamarId = $kamar->id;
        $this->nama_kamar = $kamar->nama_kamar;
        $this->gedung = $kamar->gedung;
        $this->jenis_kelamin = $kamar->jenis_kelamin;
        $this->kapasitas = $kamar->kapasitas;
        $this->musrif_id = $kamar->musrif_id;
        $this->keterangan = $kamar->keterangan;
        $this->showKamarModal = true;
    }

    public function saveKamar(): void
    {
        $this->guard('pesantren.kamar.kelola');
        $this->validate();

        PesantrenKamar::updateOrCreate(
            ['id' => $this->editingKamarId],
            [
                'tahun_ajaran_id' => $this->filterTahunAjaranId,
                'nama_kamar' => $this->nama_kamar,
                'gedung' => $this->gedung,
                'jenis_kelamin' => $this->jenis_kelamin,
                'kapasitas' => $this->kapasitas,
                'musrif_id' => $this->musrif_id ?: null,
                'keterangan' => $this->keterangan,
            ]
        );

        $this->showKamarModal = false;
        session()->flash('message', 'Data kamar berhasil disimpan.');
    }

    public function deleteKamar(int $id): void
    {
        $this->guard('pesantren.kamar.kelola');
        PesantrenKamar::findOrFail($id)->delete();
        session()->flash('message', 'Kamar berhasil dihapus.');
    }

    public function openPlotting(int $kamarId): void
    {
        $this->activeKamar = PesantrenKamar::with(['siswas.rombel'])->findOrFail($kamarId);
        $this->reset(['searchSiswa', 'selectedSiswaId', 'selectedPeran', 'selectedNomorRanjang']);
        $this->showPlottingModal = true;
    }

    public function addSiswaToKamar(): void
    {
        $this->guard('pesantren.kamar.kelola');
        if (! $this->selectedSiswaId || ! $this->activeKamar) {
            return;
        }

        if ($this->activeKamar->terisi_count >= $this->activeKamar->kapasitas) {
            session()->flash('error', 'Kapasitas kamar sudah penuh!');

            return;
        }

        PesantrenKamarSiswa::updateOrCreate(
            [
                'kamar_id' => $this->activeKamar->id,
                'siswa_id' => $this->selectedSiswaId,
            ],
            [
                'peran' => $this->selectedPeran,
                'nomor_ranjang' => $this->selectedNomorRanjang ?: null,
            ]
        );

        $this->activeKamar->load(['siswas.rombel']);
        $this->reset(['selectedSiswaId', 'selectedPeran', 'selectedNomorRanjang', 'searchSiswa']);
        session()->flash('message', 'Santri berhasil ditambahkan ke kamar.');
    }

    public function togglePeran(?int $kamarSiswaId = null, ?int $siswaId = null): void
    {
        $this->guard('pesantren.kamar.kelola');

        $ks = null;
        if ($kamarSiswaId) {
            $ks = PesantrenKamarSiswa::find($kamarSiswaId);
        } elseif ($siswaId && $this->activeKamar) {
            $ks = PesantrenKamarSiswa::where('kamar_id', $this->activeKamar->id)->where('siswa_id', $siswaId)->first();
        }

        if (! $ks) {
            return;
        }

        $ks->peran = ($ks->peran === 'mudabbir') ? 'anggota' : 'mudabbir';
        $ks->save();

        if ($this->activeKamar) {
            $this->activeKamar->load(['siswas.rombel']);
        }
    }

    public function removeSiswaFromKamar(?int $kamarSiswaId = null, ?int $siswaId = null): void
    {
        $this->guard('pesantren.kamar.kelola');

        if ($kamarSiswaId) {
            PesantrenKamarSiswa::where('id', $kamarSiswaId)->delete();
        } elseif ($siswaId && $this->activeKamar) {
            PesantrenKamarSiswa::where('kamar_id', $this->activeKamar->id)->where('siswa_id', $siswaId)->delete();
        } else {
            return;
        }

        if ($this->activeKamar) {
            $this->activeKamar->load(['siswas.rombel']);
        }
        session()->flash('message', 'Santri dikeluarkan dari kamar.');
    }

    public function render()
    {
        $query = PesantrenKamar::with(['musrif', 'tahunAjaran'])
            ->withCount('kamarSiswas')
            ->when($this->filterTahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaranId))
            ->when($this->filterGender !== 'all', fn ($q) => $q->where('jenis_kelamin', $this->filterGender))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_kamar', 'like', "%{$this->search}%")
                        ->orWhere('gedung', 'like', "%{$this->search}%")
                        ->orWhereHas('musrif', fn ($m) => $m->where('nama_lengkap', 'like', "%{$this->search}%"));
                });
            });

        $kamars = $query->latest()->paginate(10);
        $tahunAjarans = TahunAjaran::orderByDesc('nama')->get();
        $musrifList = Pegawai::where('is_active', true)->orderBy('nama_lengkap')->get();

        // Daftar santri unggulan yang belum ada di kamar ini untuk modal plotting
        $availableSiswas = collect();
        if ($this->showPlottingModal && $this->activeKamar) {
            $existingIds = $this->activeKamar->siswas->pluck('id')->toArray();
            $availableSiswas = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
                ->where('is_active', true)
                ->where('jk', $this->activeKamar->jenis_kelamin)
                ->whereNotIn('id', $existingIds)
                ->when($this->searchSiswa, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nama', 'like', "%{$this->searchSiswa}%")
                            ->orWhere('nisn', 'like', "%{$this->searchSiswa}%")
                            ->orWhere('nipd', 'like', "%{$this->searchSiswa}%");
                    });
                })
                ->with('rombel')
                ->take(15)
                ->get();
        }

        return view('kepesantrenan::livewire.kamar-index', [
            'kamars' => $kamars,
            'tahunAjarans' => $tahunAjarans,
            'musrifList' => $musrifList,
            'availableSiswas' => $availableSiswas,
        ]);
    }
}
