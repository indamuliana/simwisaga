<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\KelompokMapel;
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

    // Tab navigasi: 'mapel' atau 'kelompok'
    public string $activeTab = 'mapel';

    // State untuk Form Mata Pelajaran
    public bool $showModal = false;
    public ?int $mapelId = null;
    public string $kode = '';
    public string $nama = '';
    public ?int $kelompok_mapel_id = null;
    public string $kelompok = Mapel::KELOMPOK_UMUM;
    public int $kkm_default = 75;
    public int $urutan = 1;
    public bool $is_active = true;

    public string $search = '';
    public string $filterKelompok = '';

    // State untuk Form Kelompok Mapel
    public bool $showKelompokModal = false;
    public ?int $kelompokId = null;
    public string $kelompokKode = '';
    public string $kelompokNama = '';
    public int $kelompokUrutan = 1;
    public bool $kelompokIsActive = true;
    public string $searchKelompok = '';

    protected function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:30', Rule::unique('mapels', 'kode')->ignore($this->mapelId)],
            'nama' => ['required', 'string', 'max:150'],
            'kelompok_mapel_id' => ['required', 'exists:kelompok_mapels,id'],
            'kkm_default' => ['required', 'integer', 'min:0', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedKelompokMapelId($value): void
    {
        if ($value && ! $this->mapelId) {
            $this->urutan = (Mapel::where('kelompok_mapel_id', $value)->max('urutan') ?? 0) + 1;
        }
    }

    // --- CRUD MATA PELAJARAN ---

    public function create(): void
    {
        $this->guard('akademik.mapel.tambah');
        $this->reset(['mapelId', 'kode', 'nama']);

        $defaultGroup = $this->filterKelompok
            ? KelompokMapel::find($this->filterKelompok)
            : KelompokMapel::where('is_active', true)->orderBy('urutan')->first();

        $this->kelompok_mapel_id = $defaultGroup?->id;
        $this->kelompok = Mapel::KELOMPOK_UMUM;
        $this->kkm_default = 75;
        $this->urutan = (Mapel::where('kelompok_mapel_id', $this->kelompok_mapel_id)->max('urutan') ?? 0) + 1;
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
        $this->kelompok_mapel_id = $m->kelompok_mapel_id;
        $this->kelompok = $m->kelompok ?? Mapel::KELOMPOK_UMUM;
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

        $km = KelompokMapel::find($this->kelompok_mapel_id);
        $legacyKelompok = match (strtoupper($km?->kode ?? '')) {
            'A' => Mapel::KELOMPOK_UMUM,
            'B' => Mapel::KELOMPOK_KEJURUAN,
            'C' => Mapel::KELOMPOK_MULOK,
            'D' => Mapel::KELOMPOK_P5_PKL,
            default => Mapel::KELOMPOK_UMUM,
        };

        $data = [
            'kode' => strtoupper(trim($this->kode)),
            'nama' => $this->nama,
            'kelompok_mapel_id' => $this->kelompok_mapel_id,
            'kelompok' => $legacyKelompok,
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

    // --- CRUD KELOMPOK MAPEL ---

    public function createKelompok(): void
    {
        $this->guard('akademik.mapel.tambah');
        $this->reset(['kelompokId', 'kelompokKode', 'kelompokNama']);
        $this->kelompokUrutan = (KelompokMapel::max('urutan') ?? 0) + 1;
        $this->kelompokIsActive = true;
        $this->showKelompokModal = true;
    }

    public function editKelompok(int $id): void
    {
        $this->guard('akademik.mapel.edit');
        $km = KelompokMapel::findOrFail($id);
        $this->kelompokId = $km->id;
        $this->kelompokKode = $km->kode;
        $this->kelompokNama = $km->nama;
        $this->kelompokUrutan = $km->urutan;
        $this->kelompokIsActive = $km->is_active;
        $this->showKelompokModal = true;
    }

    public function saveKelompok(): void
    {
        if ($this->kelompokId) {
            $this->guard('akademik.mapel.edit');
        } else {
            $this->guard('akademik.mapel.tambah');
        }

        $this->validate([
            'kelompokKode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('kelompok_mapels', 'kode')->ignore($this->kelompokId),
            ],
            'kelompokNama' => ['required', 'string', 'max:150'],
            'kelompokUrutan' => ['required', 'integer', 'min:1'],
            'kelompokIsActive' => ['boolean'],
        ]);

        $data = [
            'kode' => strtoupper(trim($this->kelompokKode)),
            'nama' => trim($this->kelompokNama),
            'urutan' => $this->kelompokUrutan,
            'is_active' => $this->kelompokIsActive,
        ];

        if ($this->kelompokId) {
            KelompokMapel::findOrFail($this->kelompokId)->update($data);
        } else {
            KelompokMapel::create($data);
        }

        $this->showKelompokModal = false;
        session()->flash('status', "Kelompok Mapel {$data['nama']} berhasil disimpan.");
    }

    public function deleteKelompok(int $id): void
    {
        $this->guard('akademik.mapel.hapus');
        $km = KelompokMapel::findOrFail($id);

        if (Mapel::where('kelompok_mapel_id', $id)->exists()) {
            session()->flash('error', "Kelompok '{$km->nama}' tidak dapat dihapus karena masih digunakan oleh mata pelajaran.");
            return;
        }

        $nama = $km->nama;
        $km->delete();
        session()->flash('status', "Kelompok Mapel {$nama} berhasil dihapus.");
    }

    public function render()
    {
        // Query Mapel
        $mapelQuery = Mapel::with('kelompokMapel')
            ->leftJoin('kelompok_mapels', 'mapels.kelompok_mapel_id', '=', 'kelompok_mapels.id')
            ->select('mapels.*');

        if ($this->search) {
            $mapelQuery->where(fn ($q) => $q->where('mapels.nama', 'like', "%{$this->search}%")
                ->orWhere('mapels.kode', 'like', "%{$this->search}%"));
        }

        if ($this->filterKelompok) {
            $mapelQuery->where('mapels.kelompok_mapel_id', $this->filterKelompok);
        }

        $mapels = $mapelQuery
            ->orderBy('kelompok_mapels.urutan')
            ->orderBy('mapels.urutan')
            ->get();

        // Query Kelompok Mapel
        $kelompokQuery = KelompokMapel::withCount('mapels');

        if ($this->searchKelompok) {
            $kelompokQuery->where(fn ($q) => $q->where('nama', 'like', "%{$this->searchKelompok}%")
                ->orWhere('kode', 'like', "%{$this->searchKelompok}%"));
        }

        $kelompokMapels = $kelompokQuery->orderBy('urutan')->get();
        $activeKelompokList = KelompokMapel::where('is_active', true)->orderBy('urutan')->get();

        return view('akademik::livewire.mapel-index', [
            'mapels' => $mapels,
            'kelompokMapels' => $kelompokMapels,
            'activeKelompokList' => $activeKelompokList,
        ]);
    }
}
