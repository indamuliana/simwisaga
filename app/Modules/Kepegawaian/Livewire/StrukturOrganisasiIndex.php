<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kepegawaian\Models\Jabatan;
use App\Modules\Kepegawaian\Models\JabatanPejabat;
use App\Modules\Kepegawaian\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Struktur Organisasi')]
class StrukturOrganisasiIndex extends Component
{
    use GuardsPermission;

    #[Url(as: 'tab')]
    public string $activeTab = 'daftar';

    public string $search = '';

    // Modal state
    public bool $isModalOpen = false;
    public ?int $editingJabatanId = null;

    // Form fields
    public string $nama = '';
    public string $kode = '';
    public ?int $parent_id = null;
    public float $bobot_jam = 0;
    public string $keterangan = '';
    public array $pejabatList = [];

    protected $rules = [
        'nama' => 'required|string|max:150',
        'kode' => 'nullable|string|max:30',
        'parent_id' => 'nullable|exists:jabatans,id',
        'bobot_jam' => 'required|numeric|min:0|max:999',
        'keterangan' => 'nullable|string|max:500',
        'pejabatList.*.pegawai_id' => 'nullable|exists:pegawais,id',
        'pejabatList.*.nama_pejabat' => 'nullable|string|max:150',
        'pejabatList.*.keterangan' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->guard('kepegawaian.struktur_organisasi.lihat');
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['daftar', 'bagan'])) {
            $this->activeTab = $tab;
        }
    }

    public function openCreateModal(?int $parentId = null): void
    {
        $this->guard('kepegawaian.struktur_organisasi.kelola');
        $this->resetValidation();
        $this->resetForm();
        $this->parent_id = $parentId;
        $this->addPejabatRow();
        $this->isModalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        $this->guard('kepegawaian.struktur_organisasi.kelola');
        $this->resetValidation();
        $this->resetForm();

        $jabatan = Jabatan::with('pejabats')->findOrFail($id);
        $this->editingJabatanId = $jabatan->id;
        $this->nama = $jabatan->nama;
        $this->kode = $jabatan->kode ?? '';
        $this->parent_id = $jabatan->parent_id;
        $this->bobot_jam = (float) $jabatan->bobot_jam;
        $this->keterangan = $jabatan->keterangan ?? '';

        foreach ($jabatan->pejabats as $p) {
            $this->pejabatList[] = [
                'pegawai_id' => $p->pegawai_id ? (string) $p->pegawai_id : '',
                'nama_pejabat' => $p->nama_pejabat ?? '',
                'keterangan' => $p->keterangan ?? '',
            ];
        }

        if (empty($this->pejabatList)) {
            $this->addPejabatRow();
        }

        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingJabatanId = null;
        $this->nama = '';
        $this->kode = '';
        $this->parent_id = null;
        $this->bobot_jam = 0;
        $this->keterangan = '';
        $this->pejabatList = [];
    }

    public function addPejabatRow(): void
    {
        $this->pejabatList[] = [
            'pegawai_id' => '',
            'nama_pejabat' => '',
            'keterangan' => '',
        ];
    }

    public function removePejabatRow(int $index): void
    {
        if (isset($this->pejabatList[$index])) {
            unset($this->pejabatList[$index]);
            $this->pejabatList = array_values($this->pejabatList);
        }
    }

    public function save(): void
    {
        $this->guard('kepegawaian.struktur_organisasi.kelola');

        $this->validate();

        // Pencegahan circular reference jika jabatan menginduk ke dirinya sendiri atau turunannya
        if ($this->editingJabatanId && $this->parent_id) {
            if ($this->parent_id === $this->editingJabatanId) {
                $this->addError('parent_id', 'Jabatan tidak boleh menjadi atasan untuk dirinya sendiri.');
                return;
            }

            $descendantIds = $this->getDescendantIds($this->editingJabatanId);
            if (in_array($this->parent_id, $descendantIds)) {
                $this->addError('parent_id', 'Jabatan tidak boleh memilih bawahannya sendiri sebagai atasan.');
                return;
            }
        }

        DB::transaction(function () {
            if ($this->editingJabatanId) {
                $jabatan = Jabatan::findOrFail($this->editingJabatanId);
                $jabatan->update([
                    'nama' => $this->nama,
                    'kode' => $this->kode ?: null,
                    'parent_id' => $this->parent_id ?: null,
                    'bobot_jam' => $this->bobot_jam,
                    'keterangan' => $this->keterangan ?: null,
                ]);
            } else {
                $maxUrutan = Jabatan::where('parent_id', $this->parent_id)->max('urutan') ?? 0;
                $jabatan = Jabatan::create([
                    'nama' => $this->nama,
                    'kode' => $this->kode ?: null,
                    'parent_id' => $this->parent_id ?: null,
                    'bobot_jam' => $this->bobot_jam,
                    'keterangan' => $this->keterangan ?: null,
                    'urutan' => $maxUrutan + 10,
                    'is_active' => true,
                ]);
            }

            // Sync Pejabat Pengisi
            JabatanPejabat::where('jabatan_id', $jabatan->id)->delete();

            $pUrutan = 1;
            foreach ($this->pejabatList as $p) {
                $pegawaiId = !empty($p['pegawai_id']) ? (int) $p['pegawai_id'] : null;
                $namaManual = !empty($p['nama_pejabat']) ? trim($p['nama_pejabat']) : null;

                // Hanya simpan jika pegawai_id atau nama_pejabat diisi
                if ($pegawaiId || $namaManual) {
                    JabatanPejabat::create([
                        'jabatan_id' => $jabatan->id,
                        'pegawai_id' => $pegawaiId,
                        'nama_pejabat' => $namaManual,
                        'keterangan' => !empty($p['keterangan']) ? trim($p['keterangan']) : null,
                        'urutan' => $pUrutan++,
                    ]);
                }
            }
        });

        $this->closeModal();
        session()->flash('success', 'Data jabatan berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $this->guard('kepegawaian.struktur_organisasi.kelola');

        $jabatan = Jabatan::findOrFail($id);

        if ($jabatan->children()->exists()) {
            session()->flash('error', "Jabatan '{$jabatan->nama}' tidak dapat dihapus karena masih memiliki sub-jabatan di bawahnya. Harap pindahkan atau hapus anak jabatan terlebih dahulu.");
            return;
        }

        $jabatan->delete();
        session()->flash('success', "Jabatan '{$jabatan->nama}' berhasil dihapus.");
    }

    public function moveUp(int $id): void
    {
        $this->guard('kepegawaian.struktur_organisasi.kelola');

        $current = Jabatan::findOrFail($id);
        $siblings = Jabatan::where('parent_id', $current->parent_id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $index = $siblings->search(fn($item) => $item->id === $current->id);

        if ($index > 0) {
            $prev = $siblings[$index - 1];

            // Normalize order jika nilainya sama
            if ($current->urutan === $prev->urutan) {
                foreach ($siblings as $i => $s) {
                    $s->update(['urutan' => ($i + 1) * 10]);
                }
                $current->refresh();
                $prev = $siblings[$index - 1]->fresh();
            }

            $temp = $current->urutan;
            $current->update(['urutan' => $prev->urutan]);
            $prev->update(['urutan' => $temp]);

            session()->flash('success', "Posisi jabatan '{$current->nama}' berhasil dinaikkan.");
        }
    }

    public function moveDown(int $id): void
    {
        $this->guard('kepegawaian.struktur_organisasi.kelola');

        $current = Jabatan::findOrFail($id);
        $siblings = Jabatan::where('parent_id', $current->parent_id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $index = $siblings->search(fn($item) => $item->id === $current->id);

        if ($index !== false && $index < $siblings->count() - 1) {
            $next = $siblings[$index + 1];

            // Normalize order jika nilainya sama
            if ($current->urutan === $next->urutan) {
                foreach ($siblings as $i => $s) {
                    $s->update(['urutan' => ($i + 1) * 10]);
                }
                $current->refresh();
                $next = $siblings[$index + 1]->fresh();
            }

            $temp = $current->urutan;
            $current->update(['urutan' => $next->urutan]);
            $next->update(['urutan' => $temp]);

            session()->flash('success', "Posisi jabatan '{$current->nama}' berhasil diturunkan.");
        }
    }

    public function getDescendantIds(int $jabatanId): array
    {
        $ids = [];
        $children = Jabatan::where('parent_id', $jabatanId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }
        return $ids;
    }

    /**
     * Membangun daftar hierarki pohon yang diratakan (flattened) untuk tabel.
     */
    public function getFlattenedTreeData(): array
    {
        $all = Jabatan::with(['pejabats.pegawai', 'parent'])
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        if ($this->search) {
            $search = strtolower(trim($this->search));
            $filtered = $all->filter(function ($j) use ($search) {
                if (str_contains(strtolower($j->nama), $search) || str_contains(strtolower($j->kode ?? ''), $search)) {
                    return true;
                }
                foreach ($j->pejabats as $p) {
                    if (str_contains(strtolower($p->nama_tampil), $search)) {
                        return true;
                    }
                }
                return false;
            });

            return $filtered->map(function ($j) {
                return [
                    'jabatan' => $j,
                    'level' => $j->getDepth(),
                    'is_first' => true,
                    'is_last' => true,
                ];
            })->values()->toArray();
        }

        // Jika tidak sedang mencari, tampilkan struktur pohon lengkap
        $grouped = $all->groupBy(fn($j) => $j->parent_id ?? 0);
        $flattened = [];

        $traverse = function ($parentId, $level = 0) use (&$traverse, &$flattened, $grouped) {
            if (!$grouped->has($parentId)) {
                return;
            }

            $siblings = $grouped[$parentId]->values();
            $count = $siblings->count();

            foreach ($siblings as $idx => $jabatan) {
                $flattened[] = [
                    'jabatan' => $jabatan,
                    'level' => $level,
                    'is_first' => ($idx === 0),
                    'is_last' => ($idx === $count - 1),
                ];

                $traverse($jabatan->id, $level + 1);
            }
        };

        $traverse(0, 0);

        return $flattened;
    }

    /**
     * Membangun struktur pohon bertingkat (nested tree) untuk visual bagan organigram.
     */
    public function getNestedTreeData(): array
    {
        $all = Jabatan::with(['pejabats.pegawai'])
            ->where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        $grouped = $all->groupBy(fn($j) => $j->parent_id ?? 0);

        $buildNode = function ($parentId) use (&$buildNode, $grouped) {
            if (!$grouped->has($parentId)) {
                return [];
            }

            $nodes = [];
            foreach ($grouped[$parentId] as $j) {
                $pejabats = $j->pejabats->map(function ($p) {
                    return [
                        'nama' => $p->nama_tampil,
                        'keterangan' => $p->keterangan,
                        'is_internal' => $p->isInternal(),
                        'foto' => $p->pegawai?->foto_url ?? null,
                        'nip' => $p->pegawai?->nip ?: $p->pegawai?->niy,
                    ];
                })->toArray();

                $nodes[] = [
                    'id' => $j->id,
                    'nama' => $j->nama,
                    'kode' => $j->kode,
                    'bobot_jam' => (float) $j->bobot_jam,
                    'keterangan' => $j->keterangan,
                    'pejabats' => $pejabats,
                    'children' => $buildNode($j->id),
                ];
            }

            return $nodes;
        };

        return $buildNode(0);
    }

    public function render()
    {
        $pegawaiOptions = Pegawai::where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'gelar_depan', 'gelar_belakang', 'nip', 'niy', 'jenis_gtk']);

        // Jabatan options untuk dropdown parent (kecuali diri sendiri dan turunannya jika edit)
        $excludedIds = [];
        if ($this->editingJabatanId) {
            $excludedIds = array_merge([$this->editingJabatanId], $this->getDescendantIds($this->editingJabatanId));
        }

        $parentOptions = Jabatan::whereNotIn('id', $excludedIds)
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode']);

        $treeData = $this->getFlattenedTreeData();
        $nestedTree = ($this->activeTab === 'bagan') ? $this->getNestedTreeData() : [];

        $totalJabatan = Jabatan::count();
        $totalBobotJam = (float) Jabatan::sum('bobot_jam');
        $totalPejabat = JabatanPejabat::distinct('pegawai_id')->count('pegawai_id') + JabatanPejabat::whereNull('pegawai_id')->count();

        return view('kepegawaian::livewire.struktur-organisasi-index', [
            'treeData' => $treeData,
            'nestedTree' => $nestedTree,
            'pegawaiOptions' => $pegawaiOptions,
            'parentOptions' => $parentOptions,
            'totalJabatan' => $totalJabatan,
            'totalBobotJam' => $totalBobotJam,
            'totalPejabat' => $totalPejabat,
        ]);
    }
}
