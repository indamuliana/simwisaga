<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Struktur Kurikulum')]
class StrukturKurikulumIndex extends Component
{
    use GuardsPermission;

    // Filter bar
    public ?int $filterTahunAjaran = null;
    public ?int $filterJurusan = null;
    public string $filterTingkat = 'X';
    public string $filterSemester = 'ganjil'; // 'ganjil' | 'genap'

    // Accordion state
    public array $expandedMapels = [];

    // Single modal state (Edit / Tambah Satuan)
    public bool $showModal = false;
    public ?int $kurikulumId = null;
    public ?int $tahun_ajaran_id = null;
    public ?int $jurusan_id = null;
    public string $tingkat = 'X';
    public ?int $mapel_id = null;
    public int $jp_ganjil = 2;
    public int $jp_genap = 2;
    public int $jp_per_minggu = 2;
    public int $urutan = 1;
    public bool $samakanJp = true;

    // Batch Allocation Modal State (Alokasi Cepat Multi-Jurusan & Multi-Tingkat)
    public bool $showBatchModal = false;
    public ?int $batch_tahun_ajaran_id = null;
    public ?int $batch_mapel_id = null;
    public array $selectedJurusans = [];
    public array $tingkatSettings = [
        'X' => ['active' => true, 'jp_ganjil' => 4, 'jp_genap' => 4, 'samakan' => true],
        'XI' => ['active' => false, 'jp_ganjil' => 4, 'jp_genap' => 4, 'samakan' => true],
        'XII' => ['active' => false, 'jp_ganjil' => 4, 'jp_genap' => 4, 'samakan' => true],
    ];

    // Duplicate Modal State
    public bool $showDuplicateModal = false;
    public ?int $sourceTahunAjaranId = null;
    public ?int $targetTahunAjaranId = null;
    public string $duplicateJurusanId = 'semua';
    public int $sourceKurikulumCount = 0;

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaran = $currentTa?->id;
        $firstJurusan = Jurusan::where('is_active', true)->first();
        $this->filterJurusan = $firstJurusan?->id;

        $currentSem = AcademicContext::currentSemester();
        $this->filterSemester = $currentSem?->semester ?? 'ganjil';
    }

    public function toggleExpand(int $kurikulumId): void
    {
        if (in_array($kurikulumId, $this->expandedMapels, true)) {
            $this->expandedMapels = array_values(array_diff($this->expandedMapels, [$kurikulumId]));
        } else {
            $this->expandedMapels[] = $kurikulumId;
        }
    }

    public function setFilterSemester(string $semester): void
    {
        if (in_array($semester, ['ganjil', 'genap'], true)) {
            $this->filterSemester = $semester;
            $this->expandedMapels = [];
        }
    }

    public function updatedFilterTahunAjaran(): void
    {
        $this->expandedMapels = [];
    }

    public function updatedFilterJurusan(): void
    {
        $this->expandedMapels = [];
    }

    public function updatedFilterTingkat(): void
    {
        $this->expandedMapels = [];
    }

    // --- BATCH ALLOCATION (ALOKASI CEPAT MULTI-JURUSAN & MULTI-TINGKAT) ---

    public function openBatchModal(): void
    {
        $this->guard('akademik.kurikulum.tambah');
        $this->batch_tahun_ajaran_id = $this->filterTahunAjaran ?? AcademicContext::currentTahunAjaran()?->id;
        $firstMapel = Mapel::where('is_active', true)->first();
        $this->batch_mapel_id = $firstMapel?->id;

        // Default: centang semua jurusan aktif
        $this->selectedJurusans = Jurusan::where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        // Default: aktifkan tingkat yang sedang dipilih di filter
        $this->tingkatSettings = [
            'X' => ['active' => $this->filterTingkat === 'X', 'jp_ganjil' => 4, 'jp_genap' => 4, 'samakan' => true],
            'XI' => ['active' => $this->filterTingkat === 'XI', 'jp_ganjil' => 4, 'jp_genap' => 4, 'samakan' => true],
            'XII' => ['active' => $this->filterTingkat === 'XII', 'jp_ganjil' => 4, 'jp_genap' => 4, 'samakan' => true],
        ];

        // Jika tidak ada yang aktif, aktifkan X
        if (! $this->tingkatSettings['X']['active'] && ! $this->tingkatSettings['XI']['active'] && ! $this->tingkatSettings['XII']['active']) {
            $this->tingkatSettings['X']['active'] = true;
        }

        $this->showBatchModal = true;
    }

    public function closeBatchModal(): void
    {
        $this->showBatchModal = false;
    }

    public function selectAllJurusans(): void
    {
        $this->selectedJurusans = Jurusan::where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function deselectAllJurusans(): void
    {
        $this->selectedJurusans = [];
    }

    public function updatedTingkatSettings($value, $key): void
    {
        // Jika user mengubah jp_ganjil dan 'samakan' aktif, otomatis samakan jp_genap
        if (str_ends_with($key, '.jp_ganjil')) {
            $parts = explode('.', $key);
            $tingkat = $parts[0] ?? null;
            if ($tingkat && ! empty($this->tingkatSettings[$tingkat]['samakan'])) {
                $this->tingkatSettings[$tingkat]['jp_genap'] = (int) $value;
            }
        }
    }

    public function toggleSamakanJp(string $tingkat): void
    {
        if (! empty($this->tingkatSettings[$tingkat]['samakan'])) {
            $this->tingkatSettings[$tingkat]['jp_genap'] = $this->tingkatSettings[$tingkat]['jp_ganjil'];
        }
    }

    public function saveBatch(): void
    {
        $this->guard('akademik.kurikulum.tambah');

        $this->validate([
            'batch_tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'batch_mapel_id' => ['required', 'exists:mapels,id'],
            'selectedJurusans' => ['required', 'array', 'min:1'],
        ], [
            'selectedJurusans.min' => 'Pilih minimal satu Jurusan.',
            'batch_mapel_id.required' => 'Pilih mata pelajaran yang akan dialokasikan.',
        ]);

        $activeTingkats = collect($this->tingkatSettings)->filter(fn ($s) => ! empty($s['aktif']) || ! empty($s['active']));
        if ($activeTingkats->isEmpty()) {
            session()->flash('error', 'Pilih minimal satu Tingkat Kelas (X, XI, atau XII) yang diaktifkan.');
            return;
        }

        foreach ($activeTingkats as $t => $setting) {
            $ganjil = (int) ($setting['jp_ganjil'] ?? 0);
            $genap = (int) ($setting['jp_genap'] ?? 0);
            if ($ganjil <= 0 && $genap <= 0) {
                session()->flash('error', "Untuk Kelas {$t}, minimal salah satu semester (Ganjil atau Genap) harus memiliki beban JP lebih dari 0.");
                return;
            }
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($activeTingkats, &$created, &$updated) {
            foreach ($this->selectedJurusans as $jurusanId) {
                foreach ($activeTingkats as $tingkat => $setting) {
                    $ganjil = (int) ($setting['jp_ganjil'] ?? 0);
                    $genap = (int) ($setting['jp_genap'] ?? 0);
                    $maxJp = max($ganjil, $genap);

                    $existing = StrukturKurikulum::where('tahun_ajaran_id', $this->batch_tahun_ajaran_id)
                        ->where('jurusan_id', (int) $jurusanId)
                        ->where('tingkat', $tingkat)
                        ->where('mapel_id', $this->batch_mapel_id)
                        ->first();

                    $urutan = $existing
                        ? $existing->urutan
                        : ((StrukturKurikulum::where('tahun_ajaran_id', $this->batch_tahun_ajaran_id)
                            ->where('jurusan_id', (int) $jurusanId)
                            ->where('tingkat', $tingkat)
                            ->max('urutan') ?? 0) + 1);

                    $res = StrukturKurikulum::updateOrCreate(
                        [
                            'tahun_ajaran_id' => $this->batch_tahun_ajaran_id,
                            'jurusan_id' => (int) $jurusanId,
                            'tingkat' => $tingkat,
                            'mapel_id' => $this->batch_mapel_id,
                        ],
                        [
                            'jp_ganjil' => $ganjil,
                            'jp_genap' => $genap,
                            'jp_per_minggu' => $maxJp,
                            'urutan' => $urutan,
                        ]
                    );

                    if ($res->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }
            }
        });

        $this->showBatchModal = false;
        $this->filterTahunAjaran = $this->batch_tahun_ajaran_id;
        session()->flash('status', "Berhasil mengalokasikan mapel! Sebanyak {$created} alokasi baru ditambahkan dan {$updated} diperbarui.");
    }

    // --- SINGLE FORM MODAL (EDIT / TAMBAH SATUAN) ---

    public function updatedJpGanjil($value): void
    {
        if ($this->samakanJp) {
            $this->jp_genap = (int) $value;
        }
    }

    public function create(): void
    {
        $this->guard('akademik.kurikulum.tambah');
        $this->reset(['kurikulumId']);
        $this->tahun_ajaran_id = $this->filterTahunAjaran ?? AcademicContext::currentTahunAjaran()?->id;
        $this->jurusan_id = $this->filterJurusan;
        $this->tingkat = $this->filterTingkat;
        $firstMapel = Mapel::where('is_active', true)->first();
        $this->mapel_id = $firstMapel?->id;
        $this->jp_ganjil = 2;
        $this->jp_genap = 2;
        $this->jp_per_minggu = 2;
        $this->samakanJp = true;
        $this->urutan = (StrukturKurikulum::where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('jurusan_id', $this->jurusan_id)
            ->where('tingkat', $this->tingkat)
            ->max('urutan') ?? 0) + 1;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.kurikulum.edit');
        $k = StrukturKurikulum::findOrFail($id);
        $this->kurikulumId = $k->id;
        $this->tahun_ajaran_id = $k->tahun_ajaran_id;
        $this->jurusan_id = $k->jurusan_id;
        $this->tingkat = $k->tingkat;
        $this->mapel_id = $k->mapel_id;
        $this->jp_ganjil = $k->jp_ganjil ?? $k->jp_per_minggu;
        $this->jp_genap = $k->jp_genap ?? $k->jp_per_minggu;
        $this->jp_per_minggu = $k->jp_per_minggu;
        $this->samakanJp = ($this->jp_ganjil === $this->jp_genap);
        $this->urutan = $k->urutan;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->kurikulumId) {
            $this->guard('akademik.kurikulum.edit');
        } else {
            $this->guard('akademik.kurikulum.tambah');
        }

        $this->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tingkat' => ['required', 'in:X,XI,XII'],
            'mapel_id' => ['required', 'exists:mapels,id'],
            'jp_ganjil' => ['required', 'integer', 'min:0', 'max:200'],
            'jp_genap' => ['required', 'integer', 'min:0', 'max:200'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        if ($this->jp_ganjil <= 0 && $this->jp_genap <= 0) {
            session()->flash('error', 'Minimal salah satu semester (Ganjil atau Genap) harus memiliki beban JP > 0.');
            return;
        }

        $maxJp = max($this->jp_ganjil, $this->jp_genap);

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'jurusan_id' => $this->jurusan_id,
            'tingkat' => $this->tingkat,
            'mapel_id' => $this->mapel_id,
            'jp_ganjil' => $this->jp_ganjil,
            'jp_genap' => $this->jp_genap,
            'jp_per_minggu' => $maxJp,
            'urutan' => $this->urutan,
        ];

        if ($this->kurikulumId) {
            StrukturKurikulum::findOrFail($this->kurikulumId)->update($data);
        } else {
            StrukturKurikulum::updateOrCreate(
                [
                    'tahun_ajaran_id' => $this->tahun_ajaran_id,
                    'jurusan_id' => $this->jurusan_id,
                    'tingkat' => $this->tingkat,
                    'mapel_id' => $this->mapel_id,
                ],
                $data
            );
        }

        $this->showModal = false;
        session()->flash('status', 'Struktur kurikulum berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $this->guard('akademik.kurikulum.hapus');
        StrukturKurikulum::findOrFail($id)->delete();
        session()->flash('status', 'Mapel telah dihapus dari struktur kurikulum.');
    }

    // --- DUPLICATE KURIKULUM MODAL ---

    public function openDuplicateModal(): void
    {
        $this->guard('akademik.kurikulum.tambah');
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->targetTahunAjaranId = $this->filterTahunAjaran ?? $currentTa?->id;

        $prevTa = TahunAjaran::where('id', '!=', $this->targetTahunAjaranId)->orderByDesc('id')->first();
        $this->sourceTahunAjaranId = $prevTa?->id;
        $this->duplicateJurusanId = 'semua';
        $this->updateSourceKurikulumCount();
        $this->showDuplicateModal = true;
    }

    public function closeDuplicateModal(): void
    {
        $this->showDuplicateModal = false;
    }

    public function updatedSourceTahunAjaranId(): void
    {
        $this->updateSourceKurikulumCount();
    }

    public function updatedDuplicateJurusanId(): void
    {
        $this->updateSourceKurikulumCount();
    }

    public function updateSourceKurikulumCount(): void
    {
        if ($this->sourceTahunAjaranId) {
            $query = StrukturKurikulum::where('tahun_ajaran_id', $this->sourceTahunAjaranId);
            if ($this->duplicateJurusanId !== 'semua') {
                $query->where('jurusan_id', (int) $this->duplicateJurusanId);
            }
            $this->sourceKurikulumCount = $query->count();
        } else {
            $this->sourceKurikulumCount = 0;
        }
    }

    public function executeDuplicate(): void
    {
        $this->guard('akademik.kurikulum.tambah');

        if (! $this->sourceTahunAjaranId || ! $this->targetTahunAjaranId) {
            session()->flash('error', 'Pilih Tahun Ajaran sumber dan tujuan dengan benar.');
            return;
        }

        if ($this->sourceTahunAjaranId === $this->targetTahunAjaranId) {
            session()->flash('error', 'Tahun Ajaran sumber dan tujuan tidak boleh sama.');
            return;
        }

        $query = StrukturKurikulum::where('tahun_ajaran_id', $this->sourceTahunAjaranId);
        if ($this->duplicateJurusanId !== 'semua') {
            $query->where('jurusan_id', (int) $this->duplicateJurusanId);
        }
        $sources = $query->get();

        if ($sources->isEmpty()) {
            session()->flash('error', 'Tidak ada data struktur kurikulum pada Tahun Ajaran sumber yang dapat disalin.');
            return;
        }

        $createdCount = 0;
        $updatedCount = 0;

        DB::transaction(function () use ($sources, &$createdCount, &$updatedCount) {
            foreach ($sources as $src) {
                $target = StrukturKurikulum::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $this->targetTahunAjaranId,
                        'jurusan_id' => $src->jurusan_id,
                        'tingkat' => $src->tingkat,
                        'mapel_id' => $src->mapel_id,
                    ],
                    [
                        'jp_ganjil' => $src->jp_ganjil ?? $src->jp_per_minggu,
                        'jp_genap' => $src->jp_genap ?? $src->jp_per_minggu,
                        'jp_per_minggu' => $src->jp_per_minggu,
                        'urutan' => $src->urutan,
                    ]
                );

                if ($target->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }
            }
        });

        $this->showDuplicateModal = false;
        $this->filterTahunAjaran = $this->targetTahunAjaranId;
        session()->flash('status', "Berhasil menyalin struktur kurikulum! Sebanyak {$createdCount} mata pelajaran baru ditambahkan dan {$updatedCount} diperbarui.");
    }

    public function render()
    {
        $items = collect();
        $totalJpGanjil = 0;
        $totalJpGenap = 0;
        $rombels = collect();
        $penugasanMap = [];
        $activeSemesterModel = null;

        if ($this->filterTahunAjaran && $this->filterJurusan && $this->filterTingkat) {
            $items = StrukturKurikulum::with('mapel.kelompokMapel')
                ->where('tahun_ajaran_id', $this->filterTahunAjaran)
                ->where('jurusan_id', $this->filterJurusan)
                ->where('tingkat', $this->filterTingkat)
                ->orderBy('urutan')
                ->get();

            $totalJpGanjil = $items->sum('jp_ganjil');
            $totalJpGenap = $items->sum('jp_genap');

            $rombels = Rombel::where('tahun_ajaran_id', $this->filterTahunAjaran)
                ->where('jurusan_id', $this->filterJurusan)
                ->where('tingkat', $this->filterTingkat)
                ->orderBy('nama')
                ->get();

            $activeSemesterModel = Semester::where('tahun_ajaran_id', $this->filterTahunAjaran)
                ->where('semester', $this->filterSemester)
                ->first();

            if ($items->isNotEmpty()) {
                $rawPenugasans = collect();
                if ($activeSemesterModel && $rombels->isNotEmpty()) {
                    $rawPenugasans = PenugasanMengajar::with('pegawai')
                        ->where('semester_id', $activeSemesterModel->id)
                        ->whereIn('mapel_id', $items->pluck('mapel_id'))
                        ->whereIn('rombel_id', $rombels->pluck('id'))
                        ->get();
                }

                $grouped = $rawPenugasans->groupBy('mapel_id');

                foreach ($items as $it) {
                    $targetJp = ($this->filterSemester === 'genap') ? (int) $it->jp_genap : (int) $it->jp_ganjil;
                    $mapelPenugasans = $grouped->get($it->mapel_id, collect())->groupBy('rombel_id');

                    $rombelList = [];
                    $fulfilledCount = 0;
                    $overCount = 0;
                    $underCount = 0;
                    $emptyCount = 0;

                    foreach ($rombels as $r) {
                        $pList = $mapelPenugasans->get($r->id, collect());
                        $assignedJp = (int) $pList->sum('jp');

                        $gurus = $pList->map(function ($p) {
                            return [
                                'pegawai_id' => $p->pegawai_id,
                                'nama' => $p->pegawai?->nama_gelar ?: ($p->pegawai?->nama_lengkap ?: 'Guru Pengampu'),
                                'nip' => $p->pegawai?->nip,
                                'jp' => (int) $p->jp,
                            ];
                        })->values()->toArray();

                        if ($targetJp === 0) {
                            $status = 'no_target';
                            $label = 'Tidak Ada Jam';
                            $badgeClass = 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700';
                        } elseif ($assignedJp === $targetJp) {
                            $status = 'fulfilled';
                            $label = 'Terpenuhi';
                            $badgeClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
                            $fulfilledCount++;
                        } elseif ($assignedJp === 0) {
                            $status = 'empty';
                            $label = 'Belum Ada Guru';
                            $badgeClass = 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800';
                            $emptyCount++;
                        } elseif ($assignedJp < $targetJp) {
                            $status = 'under';
                            $label = 'Kurang ' . ($targetJp - $assignedJp) . ' JP';
                            $badgeClass = 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800';
                            $underCount++;
                        } else {
                            $status = 'over';
                            $label = 'Kelebihan ' . ($assignedJp - $targetJp) . ' JP';
                            $badgeClass = 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800';
                            $overCount++;
                        }

                        $rombelList[$r->id] = [
                            'rombel_id' => $r->id,
                            'rombel_nama' => $r->nama,
                            'assigned_jp' => $assignedJp,
                            'target_jp' => $targetJp,
                            'status' => $status,
                            'label' => $label,
                            'badge_class' => $badgeClass,
                            'gurus' => $gurus,
                        ];
                    }

                    $totalRombels = $rombels->count();
                    if ($targetJp === 0) {
                        $aggStatus = 'no_target';
                        $aggBadge = '-';
                        $aggColor = 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
                    } elseif ($totalRombels === 0) {
                        $aggStatus = 'no_rombel';
                        $aggBadge = 'Belum Ada Rombel';
                        $aggColor = 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
                    } elseif ($fulfilledCount === $totalRombels) {
                        $aggStatus = 'fulfilled';
                        $aggBadge = "✓ Lengkap ({$fulfilledCount}/{$totalRombels} Rombel)";
                        $aggColor = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
                    } elseif ($overCount > 0) {
                        $aggStatus = 'over';
                        $aggBadge = "! Kelebihan JP ({$overCount} Rombel)";
                        $aggColor = 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800';
                    } elseif ($emptyCount === $totalRombels) {
                        $aggStatus = 'empty';
                        $aggBadge = "0/{$totalRombels} Rombel Terisi";
                        $aggColor = 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800';
                    } else {
                        $aggStatus = 'under';
                        $aggBadge = "! Kurang ({$fulfilledCount}/{$totalRombels} Lengkap)";
                        $aggColor = 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800';
                    }

                    $penugasanMap[$it->id] = [
                        'target_jp' => $targetJp,
                        'total_rombels' => $totalRombels,
                        'fulfilled_count' => $fulfilledCount,
                        'agg_status' => $aggStatus,
                        'agg_badge' => $aggBadge,
                        'agg_color' => $aggColor,
                        'rombels' => $rombelList,
                    ];
                }
            }
        }

        $tahunAjarans = TahunAjaran::orderByDesc('tgl_mulai')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get();
        $allMapels = Mapel::with('kelompokMapel')
            ->leftJoin('kelompok_mapels', 'mapels.kelompok_mapel_id', '=', 'kelompok_mapels.id')
            ->select('mapels.*')
            ->where('mapels.is_active', true)
            ->orderBy('kelompok_mapels.urutan')
            ->orderBy('mapels.urutan')
            ->get();

        return view('akademik::livewire.struktur-kurikulum-index', [
            'items' => $items,
            'totalJpGanjil' => $totalJpGanjil,
            'totalJpGenap' => $totalJpGenap,
            'tahunAjarans' => $tahunAjarans,
            'jurusans' => $jurusans,
            'allMapels' => $allMapels,
            'rombels' => $rombels,
            'activeSemesterModel' => $activeSemesterModel,
            'penugasanMap' => $penugasanMap,
        ]);
    }
}
