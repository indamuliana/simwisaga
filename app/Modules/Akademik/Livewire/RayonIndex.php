<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Pengelolaan Rayon')]
class RayonIndex extends Component
{
    use GuardsPermission;

    // Filter pencarian tabel utama
    public string $search = '';

    // Modal Form (Tambah / Edit)
    public bool $showModal = false;
    public ?int $rayonId = null;
    public string $nama = '';
    public ?string $wilayah_cakupan = null;
    public ?int $pembimbing_id = null;
    public ?string $keterangan = null;

    // Modal Detail (Lihat)
    public bool $showDetailModal = false;
    public ?int $detailRayonId = null;
    public string $searchDetailSiswa = '';
    public ?int $filterDetailRombel = null;

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
        $this->resetValidation();
        $this->showDetailModal = false;
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
        $this->resetValidation();
        $this->showDetailModal = false;
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
            'nama' => trim($this->nama),
            'wilayah_cakupan' => $this->wilayah_cakupan ? trim($this->wilayah_cakupan) : null,
            'pembimbing_id' => $this->pembimbing_id ?: null,
            'keterangan' => $this->keterangan ? trim($this->keterangan) : null,
        ];

        if ($this->rayonId) {
            Rayon::findOrFail($this->rayonId)->update($data);
        } else {
            Rayon::create($data);
        }

        $this->showModal = false;
        session()->flash('status', "Rayon {$data['nama']} berhasil disimpan.");
    }

    public function showDetail(int $id): void
    {
        $this->guard('akademik.rayon.lihat');
        $this->detailRayonId = $id;
        $this->searchDetailSiswa = '';
        $this->filterDetailRombel = null;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailRayonId = null;
        $this->searchDetailSiswa = '';
        $this->filterDetailRombel = null;
    }

    public function delete(int $id): void
    {
        $this->guard('akademik.rayon.hapus');
        $rayon = Rayon::withCount('siswas')->findOrFail($id);

        if ($rayon->siswas_count > 0) {
            session()->flash('error', "Rayon '{$rayon->nama}' tidak dapat dihapus karena masih memiliki {$rayon->siswas_count} siswa terdaftar. Silakan pindahkan siswa ke rayon lain terlebih dahulu.");
            return;
        }

        $nama = $rayon->nama;
        $rayon->delete();

        if ($this->detailRayonId === $id) {
            $this->closeDetail();
        }

        session()->flash('status', "Rayon '{$nama}' berhasil dihapus.");
    }

    public function getDetailRayonProperty(): ?Rayon
    {
        if (! $this->detailRayonId) {
            return null;
        }

        return Rayon::with('pembimbing')->withCount('siswas')->find($this->detailRayonId);
    }

    public function getDetailStatsProperty(): array
    {
        if (! $this->detailRayonId) {
            return ['total' => 0, 'laki' => 0, 'perempuan' => 0, 'rombels' => []];
        }

        $allSiswas = Siswa::where('rayon_id', $this->detailRayonId)
            ->with('rombel')
            ->get(['id', 'nama', 'jk', 'rombel_id']);

        $total = $allSiswas->count();
        $laki = $allSiswas->where('jk', 'L')->count();
        $perempuan = $allSiswas->where('jk', 'P')->count();

        $rombels = $allSiswas->groupBy('rombel_id')->map(function ($group) {
            $first = $group->first();
            return [
                'id' => $first?->rombel_id,
                'nama' => $first?->rombel?->nama ?? 'Tanpa Rombel',
                'count' => $group->count(),
            ];
        })->values()->sortBy('nama')->all();

        return [
            'total' => $total,
            'laki' => $laki,
            'perempuan' => $perempuan,
            'rombels' => $rombels,
        ];
    }

    public function getDetailSiswasProperty()
    {
        if (! $this->detailRayonId) {
            return collect();
        }

        return Siswa::query()
            ->where('rayon_id', $this->detailRayonId)
            ->with('rombel')
            ->when($this->searchDetailSiswa, function ($q) {
                $term = '%' . trim($this->searchDetailSiswa) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama', 'like', $term)
                        ->orWhere('nisn', 'like', $term)
                        ->orWhere('nipd', 'like', $term);
                });
            })
            ->when($this->filterDetailRombel, fn ($q) => $q->where('rombel_id', $this->filterDetailRombel))
            ->orderBy('nama')
            ->get();
    }

    public function render()
    {
        $query = Rayon::with('pembimbing')->withCount('siswas');

        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama', 'like', $term)
                    ->orWhere('wilayah_cakupan', 'like', $term)
                    ->orWhere('keterangan', 'like', $term)
                    ->orWhereHas('pembimbing', fn ($pq) => $pq->where('name', 'like', $term));
            });
        }

        $rayons = $query->orderBy('nama')->get();

        $gurus = User::where(function ($q) {
            $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['Guru', 'Operator', 'Tata Usaha', 'Kepala Sekolah']))
                ->orWhereDoesntHave('roles');
            if ($this->pembimbing_id) {
                $q->orWhere('id', $this->pembimbing_id);
            }
        })
        ->orderBy('name')
        ->get();

        return view('akademik::livewire.rayon-index', [
            'rayons' => $rayons,
            'gurus' => $gurus,
            'detailRayon' => $this->detailRayon,
            'detailStats' => $this->detailStats,
            'detailSiswas' => $this->detailSiswas,
        ]);
    }
}
