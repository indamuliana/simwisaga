<?php

namespace App\Modules\Absensi\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use App\Modules\Kesiswaan\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AbsensiHarianIndex extends Component
{
    use GuardsPermission;

    public $tanggal;
    public $rombel_id = '';
    public $gender = '';
    public $rayon_id = '';
    public $search = '';

    public array $absensiData = [];
    public array $dirtySiswaIds = [];
    public bool $isEditingPastDate = false;

    protected ?array $resolvedScope = null;

    public function mount(): void
    {
        $reqTanggal = request()->query('tanggal');
        $this->tanggal = $this->isValidDate($reqTanggal)
            ? Carbon::parse($reqTanggal)->toDateString()
            : now()->toDateString();

        $reqRombel = request()->query('rombel_id');
        if ($reqRombel !== null && $reqRombel !== '' && ctype_digit((string) $reqRombel)) {
            $this->rombel_id = (string) $reqRombel;
        }

        $reqRayon = request()->query('rayon_id');
        if ($reqRayon !== null && $reqRayon !== '' && ctype_digit((string) $reqRayon)) {
            $this->rayon_id = (string) $reqRayon;
        }

        $this->loadData();
    }

    public function updatedRombelId(): void
    {
        $this->dirtySiswaIds = [];
        $this->loadData();
    }

    public function updatedTanggal(): void
    {
        if (! $this->isValidDate($this->tanggal)) {
            $this->tanggal = now()->toDateString();
        }

        $this->isEditingPastDate = false;
        $this->dirtySiswaIds = [];
        $this->loadData();
    }

    public function updatedAbsensiData($value, $key): void
    {
        $siswaId = (int) explode('.', (string) $key)[0];
        if ($siswaId > 0) {
            $this->dirtySiswaIds[$siswaId] = true;
        }
    }

    public function getCanInputProperty(): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->isSuperAdmin() || $user->can('absensi.harian.input'));
    }

    public function getIsPastDateProperty(): bool
    {
        if (! $this->isValidDate($this->tanggal)) {
            return false;
        }

        return Carbon::parse($this->tanggal)->startOfDay()->lt(Carbon::today());
    }

    public function getIsEditableProperty(): bool
    {
        if (! $this->canInput) {
            return false;
        }

        return ! $this->isPastDate || $this->isEditingPastDate;
    }

    public function getHasFullAccessProperty(): bool
    {
        return $this->resolveAccessScope()['all'];
    }

    public function mulaiEditAbsen(): void
    {
        $this->guard('absensi.harian.input');

        $this->isEditingPastDate = true;
        $this->dirtySiswaIds = [];
        $this->loadData();
    }

    public function batalEditAbsen(): void
    {
        $this->isEditingPastDate = false;
        $this->dirtySiswaIds = [];
        $this->loadData();
    }

    public function loadData(): void
    {
        if (! $this->isValidDate($this->tanggal)) {
            $this->absensiData = [];

            return;
        }

        $siswaIds = $this->baseSiswaQuery()
            ->when($this->rombel_id !== '', fn (Builder $q) => $q->inRombel((int) $this->rombel_id))
            ->pluck('id');

        if ($siswaIds->isEmpty()) {
            $this->absensiData = [];

            return;
        }

        $absensis = AbsensiHarian::where('tanggal', $this->tanggal)
            ->whereIn('siswa_id', $siswaIds)
            ->get(['siswa_id', 'status', 'keterangan'])
            ->keyBy('siswa_id');

        $this->absensiData = [];
        foreach ($siswaIds as $siswaId) {
            $rec = $absensis->get($siswaId);
            $statusVal = $rec?->status instanceof StatusAbsensi ? $rec->status->value : ($rec?->status ?: null);

            $this->absensiData[$siswaId] = [
                'status' => $statusVal,
                'keterangan' => $rec?->keterangan ?? '',
            ];
        }
    }

    public function getFilteredSiswas(): Collection
    {
        $castType = DB::getDriverName() === 'sqlite' ? 'INTEGER' : 'UNSIGNED';
        $term = trim((string) $this->search);

        return $this->baseSiswaQuery()
            ->select(['id', 'nama', 'nipd', 'nisn', 'jk', 'rombel_id', 'rayon_id'])
            ->with(['rayon:id,nama', 'rombel:id,nama'])
            ->when($this->rombel_id !== '', fn (Builder $q) => $q->inRombel((int) $this->rombel_id))
            ->when($this->rayon_id !== '', fn (Builder $q) => $q->where('rayon_id', (int) $this->rayon_id))
            ->when($this->gender !== '', fn (Builder $q) => $q->whereRaw('UPPER(jk) = ?', [strtoupper((string) $this->gender)]))
            ->when($term !== '', function (Builder $q) use ($term) {
                $like = '%'.strtolower($term).'%';
                $q->where(function (Builder $sq) use ($like) {
                    $sq->whereRaw('LOWER(nama) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(nipd, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(nisn, "")) LIKE ?', [$like])
                        ->orWhereHas('rombel', fn (Builder $rq) => $rq->whereRaw('LOWER(nama) LIKE ?', [$like]));
                });
            })
            ->orderByRaw("CAST(nipd AS {$castType}) ASC, nipd ASC")
            ->get();
    }

    public function getStatistikProperty(): array
    {
        $scopedIds = $this->baseSiswaQuery()->pluck('id');
        $totalSiswa = $scopedIds->count();

        $counts = [
            'total' => $totalSiswa,
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'dispen_cbt_plh' => 0,
            'dispen_cbt_kwh' => 0,
            'dispen_lainnya' => 0,
            'belum_absen' => $totalSiswa,
        ];

        if ($totalSiswa === 0 || ! $this->isValidDate($this->tanggal)) {
            return $counts;
        }

        $statusKeyMap = [
            StatusAbsensi::HADIR->value => 'hadir',
            StatusAbsensi::SAKIT->value => 'sakit',
            StatusAbsensi::IZIN->value => 'izin',
            StatusAbsensi::ALPA->value => 'alpa',
            StatusAbsensi::DISPEN_CBT_PLH->value => 'dispen_cbt_plh',
            StatusAbsensi::DISPEN_CBT_KWH->value => 'dispen_cbt_kwh',
            StatusAbsensi::DISPEN_LAINNYA->value => 'dispen_lainnya',
        ];

        $dbGrouped = AbsensiHarian::where('tanggal', $this->tanggal)
            ->whereIn('siswa_id', $scopedIds)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        foreach ($dbGrouped as $row) {
            $statusVal = $row->status instanceof StatusAbsensi ? $row->status->value : (string) $row->status;
            if (isset($statusKeyMap[$statusVal])) {
                $counts[$statusKeyMap[$statusVal]] += (int) $row->total;
            }
        }

        // Terapkan delta hanya untuk baris yang sedang diubah di sesi ini (belum disimpan)
        $dirtyIds = array_values(array_intersect(
            array_map('intval', array_keys($this->dirtySiswaIds)),
            $scopedIds->all()
        ));

        if (! empty($dirtyIds)) {
            $existingDirty = AbsensiHarian::where('tanggal', $this->tanggal)
                ->whereIn('siswa_id', $dirtyIds)
                ->get(['siswa_id', 'status'])
                ->keyBy('siswa_id');

            foreach ($dirtyIds as $siswaId) {
                $oldRec = $existingDirty->get($siswaId);
                $oldStatus = $oldRec?->status instanceof StatusAbsensi ? $oldRec->status->value : ($oldRec?->status ?: null);
                $newStatus = $this->absensiData[$siswaId]['status'] ?? null;

                if ($oldStatus === $newStatus) {
                    continue;
                }

                if ($oldStatus && isset($statusKeyMap[$oldStatus])) {
                    $counts[$statusKeyMap[$oldStatus]] = max(0, $counts[$statusKeyMap[$oldStatus]] - 1);
                }
                if ($newStatus && isset($statusKeyMap[$newStatus])) {
                    $counts[$statusKeyMap[$newStatus]]++;
                }
            }
        }

        $totalSudahAbsen = $counts['hadir']
            + $counts['sakit']
            + $counts['izin']
            + $counts['alpa']
            + $counts['dispen_cbt_plh']
            + $counts['dispen_cbt_kwh']
            + $counts['dispen_lainnya'];

        $counts['belum_absen'] = max(0, $totalSiswa - $totalSudahAbsen);

        return $counts;
    }

    public function tandaiSemuaHadir(): void
    {
        $this->guard('absensi.harian.input');

        if (! $this->isEditable) {
            abort(403, 'Klik tombol Edit Absen terlebih dahulu untuk mengubah absensi tanggal lampau.');
        }

        $filtered = $this->getFilteredSiswas();
        foreach ($filtered as $siswa) {
            if (empty($this->absensiData[$siswa->id]['status'])) {
                $this->absensiData[$siswa->id] = [
                    'status' => StatusAbsensi::HADIR->value,
                    'keterangan' => $this->absensiData[$siswa->id]['keterangan'] ?? '',
                ];
                $this->dirtySiswaIds[$siswa->id] = true;
            }
        }
    }

    public function resetStatus(int $siswaId): void
    {
        $this->guard('absensi.harian.input');

        if (! $this->isEditable) {
            abort(403, 'Klik tombol Edit Absen terlebih dahulu untuk mengubah absensi tanggal lampau.');
        }

        if (! $this->baseSiswaQuery()->where('id', $siswaId)->exists()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah absensi siswa di luar binaan Anda.');
        }

        $this->absensiData[$siswaId] = [
            'status' => null,
            'keterangan' => '',
        ];
        $this->dirtySiswaIds[$siswaId] = true;
    }

    public function save(): void
    {
        $this->guard('absensi.harian.input');

        if (! $this->isEditable) {
            abort(403, 'Klik tombol Edit Absen terlebih dahulu untuk mengubah absensi tanggal lampau.');
        }

        $this->validate([
            'tanggal' => ['required', 'date'],
            'rombel_id' => ['nullable', 'exists:rombels,id'],
            'absensiData.*.status' => ['nullable', Rule::enum(StatusAbsensi::class)],
            'absensiData.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $semesterId = AcademicContext::currentSemester()?->id
            ?? Semester::orderByDesc('id')->first()?->id;

        if (! $semesterId) {
            $this->addError('tanggal', 'Semester aktif belum tersedia. Silakan atur semester terlebih dahulu.');

            return;
        }

        $dirtyIds = array_values(array_unique(array_filter(
            array_map('intval', array_keys($this->dirtySiswaIds)),
            fn (int $id) => $id > 0 && array_key_exists($id, $this->absensiData)
        )));

        if (empty($dirtyIds)) {
            if ($this->isPastDate) {
                $this->isEditingPastDate = false;
            }
            session()->flash('message', 'Tidak ada perubahan absensi untuk disimpan.');

            return;
        }

        $allowedSiswas = $this->baseSiswaQuery()
            ->whereIn('id', $dirtyIds)
            ->get(['id', 'nama', 'rombel_id'])
            ->keyBy('id');

        if ($allowedSiswas->count() !== count($dirtyIds)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah absensi siswa di luar binaan Anda.');
        }

        $toUpsert = [];
        $toDeleteIds = [];
        $skippedNoRombel = [];
        $now = now();
        $userId = auth()->id();

        foreach ($dirtyIds as $siswaId) {
            $data = $this->absensiData[$siswaId] ?? [];
            $status = $data['status'] ?? null;

            if (empty($status)) {
                $toDeleteIds[] = $siswaId;

                continue;
            }

            $siswa = $allowedSiswas->get($siswaId);
            if (! $siswa || ! $siswa->rombel_id) {
                $skippedNoRombel[] = $siswa?->nama ?? "ID #{$siswaId}";

                continue;
            }

            $ket = isset($data['keterangan']) ? trim((string) $data['keterangan']) : '';

            $toUpsert[] = [
                'tanggal' => $this->tanggal,
                'siswa_id' => $siswaId,
                'rombel_id' => $siswa->rombel_id,
                'semester_id' => $semesterId,
                'status' => $status,
                'keterangan' => $ket !== '' ? $ket : null,
                'diinput_oleh' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($toUpsert, $toDeleteIds) {
            if (! empty($toDeleteIds)) {
                AbsensiHarian::where('tanggal', $this->tanggal)
                    ->whereIn('siswa_id', $toDeleteIds)
                    ->delete();
            }

            if (! empty($toUpsert)) {
                DB::table('absensi_harians')->upsert(
                    $toUpsert,
                    ['tanggal', 'siswa_id'],
                    ['rombel_id', 'semester_id', 'status', 'keterangan', 'diinput_oleh', 'updated_at']
                );
            }
        });

        $this->dirtySiswaIds = [];
        if ($this->isPastDate) {
            $this->isEditingPastDate = false;
        }
        $this->loadData();

        if (! empty($skippedNoRombel)) {
            session()->flash(
                'warning',
                'Sebagian siswa dilewati karena belum memiliki rombel: '.implode(', ', array_slice($skippedNoRombel, 0, 5))
                .(count($skippedNoRombel) > 5 ? ' dan lainnya.' : '.')
            );
        }

        if (! empty($toUpsert) || ! empty($toDeleteIds)) {
            session()->flash('message', 'Absensi harian berhasil disimpan.');
        }
    }

    protected function resolveAccessScope(): array
    {
        if ($this->resolvedScope !== null) {
            return $this->resolvedScope;
        }

        $user = auth()->user();
        if (! $user) {
            return $this->resolvedScope = ['all' => false, 'rombel_ids' => [], 'rayon_ids' => []];
        }

        if ($user->isSuperAdmin() || $user->hasRole(['Kesiswaan', 'Operator'])) {
            return $this->resolvedScope = ['all' => true, 'rombel_ids' => [], 'rayon_ids' => []];
        }

        $tahunAjaranId = AcademicContext::currentTahunAjaran()?->id;
        $pegawai = $user->pegawai;

        $rombelIds = [];
        $rayonIds = [];

        if ($pegawai) {
            $rombelIds = array_merge(
                $rombelIds,
                PenugasanWaliKelas::where('pegawai_id', $pegawai->id)
                    ->when($tahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
                    ->pluck('rombel_id')
                    ->all()
            );

            $rayonIds = array_merge(
                $rayonIds,
                PenugasanPembimbingRayon::where('pegawai_id', $pegawai->id)
                    ->when($tahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
                    ->pluck('rayon_id')
                    ->all()
            );
        }

        $ownerIds = array_values(array_unique(array_filter([$user->id, $pegawai?->id])));

        if (! empty($ownerIds)) {
            $rombelIds = array_merge(
                $rombelIds,
                Rombel::whereIn('wali_kelas_id', $ownerIds)
                    ->when($tahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
                    ->pluck('id')
                    ->all()
            );

            $rayonIds = array_merge(
                $rayonIds,
                Rayon::whereIn('pembimbing_id', $ownerIds)
                    ->pluck('id')
                    ->all()
            );
        }

        return $this->resolvedScope = [
            'all' => false,
            'rombel_ids' => array_values(array_unique(array_map('intval', $rombelIds))),
            'rayon_ids' => array_values(array_unique(array_map('intval', $rayonIds))),
        ];
    }

    protected function baseSiswaQuery(): Builder
    {
        $scope = $this->resolveAccessScope();
        $query = Siswa::where('status', 'aktif');

        if (! $scope['all']) {
            if (empty($scope['rombel_ids']) && empty($scope['rayon_ids'])) {
                return $query->whereRaw('1 = 0');
            }

            $query->where(function (Builder $q) use ($scope) {
                if (! empty($scope['rombel_ids'])) {
                    $q->orWhereIn('rombel_id', $scope['rombel_ids']);
                }
                if (! empty($scope['rayon_ids'])) {
                    $q->orWhereIn('rayon_id', $scope['rayon_ids']);
                }
            });
        }

        return $query;
    }

    protected function getAvailableRombels(): Collection
    {
        $scope = $this->resolveAccessScope();
        $activeTa = AcademicContext::currentTahunAjaran();

        if ($scope['all']) {
            $rombels = Rombel::when($activeTa, fn (Builder $q) => $q->where('tahun_ajaran_id', $activeTa->id))
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'nama', 'tingkat']);

            if ($rombels->isEmpty()) {
                $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'tingkat']);
            }

            return $rombels;
        }

        $scopedRombelIds = $this->baseSiswaQuery()
            ->whereNotNull('rombel_id')
            ->distinct()
            ->pluck('rombel_id')
            ->merge($scope['rombel_ids'])
            ->unique()
            ->values();

        if ($scopedRombelIds->isEmpty()) {
            return collect();
        }

        return Rombel::whereIn('id', $scopedRombelIds)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'nama', 'tingkat']);
    }

    protected function getAvailableRayons(): Collection
    {
        $scope = $this->resolveAccessScope();

        if ($scope['all']) {
            return Rayon::orderBy('nama')->get(['id', 'nama']);
        }

        $scopedRayonIds = $this->baseSiswaQuery()
            ->whereNotNull('rayon_id')
            ->distinct()
            ->pluck('rayon_id')
            ->merge($scope['rayon_ids'])
            ->unique()
            ->values();

        if ($scopedRayonIds->isEmpty()) {
            return collect();
        }

        return Rayon::whereIn('id', $scopedRayonIds)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    protected function isValidDate(mixed $value): bool
    {
        if (! is_string($value) || trim($value) === '') {
            return false;
        }

        try {
            Carbon::parse($value);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function render()
    {
        $statuses = StatusAbsensi::cases();
        $siswas = $this->getFilteredSiswas();
        $statistik = $this->statistik;
        $rombels = $this->getAvailableRombels();
        $rayons = $this->getAvailableRayons();

        return view('absensi::livewire.absensi-harian-index', [
            'statuses' => $statuses,
            'siswas' => $siswas,
            'statistik' => $statistik,
            'rombels' => $rombels,
            'rayons' => $rayons,
            'canInput' => $this->canInput,
            'isPastDate' => $this->isPastDate,
            'isEditable' => $this->isEditable,
            'hasFullAccess' => $this->hasFullAccess,
        ]);
    }
}
