<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Plotting Rayon Massal')]
class PlottingRayonMassal extends Component
{
    use GuardsPermission, WithPagination;

    // Filters
    public string $filterStatusRayon = 'belum_ada'; // 'belum_ada' atau 'semua'
    public string $filterKecamatan = '';
    public string $filterKelurahan = '';
    public ?int $filterRombel = null;
    public string $search = '';

    // Selection
    public array $selectedSiswaIds = [];
    public bool $selectAll = false;

    // Target Rayon
    public ?int $targetRayonId = null;

    public function updatedFilterStatusRayon(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterKecamatan(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterKelurahan(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedFilterRombel(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->reset(['selectedSiswaIds', 'selectAll']);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedSiswaIds = $this->getQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedSiswaIds = [];
        }
    }

    protected function getQuery()
    {
        $query = Siswa::query();

        if ($this->filterStatusRayon === 'belum_ada') {
            $query->whereNull('rayon_id');
        }

        if ($this->filterKecamatan) {
            $query->where('kecamatan', $this->filterKecamatan);
        }

        if ($this->filterKelurahan) {
            $query->where('kelurahan', $this->filterKelurahan);
        }

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    public function applyPlotting(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->selectedSiswaIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk di-plot ke rayon.');
            return;
        }

        if (!$this->targetRayonId) {
            session()->flash('error', 'Silakan pilih Rayon tujuan.');
            return;
        }

        $rayon = Rayon::findOrFail($this->targetRayonId);
        $count = count($this->selectedSiswaIds);

        DB::transaction(function () use ($rayon) {
            foreach ($this->selectedSiswaIds as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if ($siswa) {
                    $siswa->update(['rayon_id' => $rayon->id]);
                    RayonSiswa::create([
                        'siswa_id' => $siswa->id,
                        'rayon_id' => $rayon->id,
                        'tgl_mulai' => now(),
                        'alasan_pindah' => 'Plotting Rayon Massal',
                    ]);
                }
            }
        });

        $this->reset(['selectedSiswaIds', 'selectAll']);
        session()->flash('status', "Berhasil! Sebanyak {$count} siswa berhasil dialokasikan ke rayon {$rayon->nama}.");
    }

    public function render()
    {
        $query = $this->getQuery()->with(['rombel', 'rayon']);
        $siswas = $query->orderBy('kecamatan')->orderBy('kelurahan')->orderBy('nama')->paginate(25);

        $rayons = Rayon::orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();

        $kecamatans = Siswa::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $kelurahans = collect();
        if ($this->filterKecamatan) {
            $kelurahans = Siswa::where('kecamatan', $this->filterKecamatan)
                ->whereNotNull('kelurahan')
                ->where('kelurahan', '!=', '')
                ->distinct()
                ->orderBy('kelurahan')
                ->pluck('kelurahan');
        }

        return view('kesiswaan::livewire.plotting-rayon-massal', [
            'siswas' => $siswas,
            'rayons' => $rayons,
            'rombels' => $rombels,
            'kecamatans' => $kecamatans,
            'kelurahans' => $kelurahans,
        ]);
    }
}
