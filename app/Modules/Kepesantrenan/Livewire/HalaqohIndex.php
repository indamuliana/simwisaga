<?php

namespace App\Modules\Kepesantrenan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqoh;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohPresensi;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohPresensiDetail;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenKamar;
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

    // Tab 3: Rekap Presensi Halaqoh
    public $rekapSubTab = 'siswa'; // 'siswa', 'kamar', 'rayon'

    public $rekapTanggalMulai = '';

    public $rekapTanggalSelesai = '';

    public $rekapHalaqohId = '';

    public $rekapWaktuSesi = '';

    public $rekapSearchSiswa = '';

    // Modal Detail Presensi Santri
    public $showDetailModal = false;

    public $selectedDetailSiswaId = null;

    public function mount(): void
    {
        $this->guard('pesantren.halaqoh.lihat');

        $this->presensiTanggal = now()->toDateString();
        $this->rekapTanggalMulai = now()->startOfMonth()->toDateString();
        $this->rekapTanggalSelesai = now()->toDateString();

        // Default pilih halaqoh pertama jika ada
        $firstHalaqoh = PesantrenHalaqoh::first();
        if ($firstHalaqoh) {
            $this->selectedHalaqohId = $firstHalaqoh->id;
            $this->loadPresensiData();
        }
    }

    public function setDatePreset(string $preset): void
    {
        if ($preset === 'today') {
            $this->rekapTanggalMulai = now()->toDateString();
            $this->rekapTanggalSelesai = now()->toDateString();
        } elseif ($preset === 'week') {
            $this->rekapTanggalMulai = now()->subDays(6)->toDateString();
            $this->rekapTanggalSelesai = now()->toDateString();
        } elseif ($preset === 'month') {
            $this->rekapTanggalMulai = now()->startOfMonth()->toDateString();
            $this->rekapTanggalSelesai = now()->toDateString();
        } elseif ($preset === 'last_month') {
            $this->rekapTanggalMulai = now()->subMonth()->startOfMonth()->toDateString();
            $this->rekapTanggalSelesai = now()->subMonth()->endOfMonth()->toDateString();
        }
    }

    public function openDetailSiswa(int $siswaId): void
    {
        $this->selectedDetailSiswaId = $siswaId;
        $this->showDetailModal = true;
    }

    public function closeDetailSiswa(): void
    {
        $this->showDetailModal = false;
        $this->selectedDetailSiswaId = null;
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

        // Data Rekap Presensi Halaqoh (hanya dimuat jika tab 'rekap' aktif)
        $rekapSiswas = collect();
        $rekapKamars = collect();
        $rekapRayons = collect();
        $globalStats = null;
        $globalPersen = 0;
        $detailSiswa = null;
        $detailPresensis = collect();

        if ($this->activeTab === 'rekap') {
            $startDate = $this->rekapTanggalMulai ?: now()->startOfMonth()->toDateString();
            $endDate = $this->rekapTanggalSelesai ?: now()->toDateString();
            if ($startDate > $endDate) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }

            $baseDetailQuery = PesantrenHalaqohPresensiDetail::query()
                ->whereHas('presensi', function ($q) use ($startDate, $endDate) {
                    $q->whereDate('tanggal', '>=', $startDate)
                        ->whereDate('tanggal', '<=', $endDate);
                    if ($this->rekapHalaqohId) {
                        $q->where('halaqoh_id', $this->rekapHalaqohId);
                    }
                    if ($this->rekapWaktuSesi) {
                        $q->where('waktu_sesi', $this->rekapWaktuSesi);
                    }
                });

            // Global KPI
            $globalStats = (clone $baseDetailQuery)
                ->selectRaw("
                    COUNT(*) as total_records,
                    COUNT(DISTINCT presensi_id) as total_pertemuan,
                    SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as total_h,
                    SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as total_s,
                    SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as total_i,
                    SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as total_a
                ")
                ->first();

            $globalPersen = ($globalStats && $globalStats->total_records > 0)
                ? round(($globalStats->total_h / $globalStats->total_records) * 100, 1)
                : 0;

            // Stats per Santri (keyed by siswa_id)
            $siswaStats = (clone $baseDetailQuery)
                ->selectRaw("
                    siswa_id,
                    COUNT(*) as total_sesi,
                    SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as total_h,
                    SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as total_s,
                    SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as total_i,
                    SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as total_a
                ")
                ->groupBy('siswa_id')
                ->get()
                ->keyBy('siswa_id');

            // 1. Rekap Per Siswa
            $rekapSiswas = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
                ->where('is_active', true)
                ->with(['rombel', 'rayon', 'pesantrenKamars', 'pesantrenHalaqohs'])
                ->when($this->rekapSearchSiswa, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nama', 'like', "%{$this->rekapSearchSiswa}%")
                            ->orWhere('nisn', 'like', "%{$this->rekapSearchSiswa}%");
                    });
                })
                ->when($this->rekapHalaqohId, function ($q) {
                    $q->whereHas('pesantrenHalaqohs', fn ($h) => $h->where('pesantren_halaqohs.id', $this->rekapHalaqohId));
                })
                ->orderBy('nama')
                ->get()
                ->map(function ($s) use ($siswaStats) {
                    $st = $siswaStats[$s->id] ?? null;
                    $totalSesi = $st ? (int) $st->total_sesi : 0;
                    $h = $st ? (int) $st->total_h : 0;
                    $s_count = $st ? (int) $st->total_s : 0;
                    $i = $st ? (int) $st->total_i : 0;
                    $a = $st ? (int) $st->total_a : 0;
                    $persen = $totalSesi > 0 ? round(($h / $totalSesi) * 100, 1) : 0;

                    return (object) [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'nisn' => $s->nisn,
                        'rombel' => $s->rombel?->nama ?? '-',
                        'rayon' => $s->rayon?->nama ?? '-',
                        'kamar' => $s->pesantrenKamars->first()?->nama_kamar ?? '-',
                        'halaqoh' => $s->pesantrenHalaqohs->first()?->nama_halaqoh ?? '-',
                        'total_sesi' => $totalSesi,
                        'h' => $h,
                        's' => $s_count,
                        'i' => $i,
                        'a' => $a,
                        'persen' => $persen,
                    ];
                });

            // 2. Rekap Per Kamar
            $rekapKamars = PesantrenKamar::with(['musrif', 'siswas' => function ($q) {
                $q->where('is_active', true)->whereHas('program', fn ($p) => $p->where('kode', 'UNG'));
            }])
                ->orderBy('nama_kamar')
                ->get()
                ->map(function ($k) use ($siswaStats) {
                    $siswaIds = $k->siswas->pluck('id');
                    $totalSantri = $siswaIds->count();
                    $totalSesi = 0;
                    $h = 0;
                    $s_count = 0;
                    $i = 0;
                    $a = 0;

                    foreach ($siswaIds as $sid) {
                        if (isset($siswaStats[$sid])) {
                            $st = $siswaStats[$sid];
                            $totalSesi += (int) $st->total_sesi;
                            $h += (int) $st->total_h;
                            $s_count += (int) $st->total_s;
                            $i += (int) $st->total_i;
                            $a += (int) $st->total_a;
                        }
                    }

                    $persen = $totalSesi > 0 ? round(($h / $totalSesi) * 100, 1) : 0;

                    return (object) [
                        'id' => $k->id,
                        'nama_kamar' => $k->nama_kamar,
                        'gedung' => $k->gedung ?? '-',
                        'musrif' => $k->musrif?->nama_lengkap ?? '-',
                        'total_santri' => $totalSantri,
                        'total_sesi' => $totalSesi,
                        'h' => $h,
                        's' => $s_count,
                        'i' => $i,
                        'a' => $a,
                        'persen' => $persen,
                    ];
                });

            // 3. Rekap Per Rayon
            $rekapRayons = Rayon::with(['pembimbing', 'siswas' => function ($q) {
                $q->where('is_active', true)->whereHas('program', fn ($p) => $p->where('kode', 'UNG'));
            }])
                ->orderBy('nama')
                ->get()
                ->map(function ($r) use ($siswaStats) {
                    $siswaIds = $r->siswas->pluck('id');
                    $totalSantri = $siswaIds->count();
                    $totalSesi = 0;
                    $h = 0;
                    $s_count = 0;
                    $i = 0;
                    $a = 0;

                    foreach ($siswaIds as $sid) {
                        if (isset($siswaStats[$sid])) {
                            $st = $siswaStats[$sid];
                            $totalSesi += (int) $st->total_sesi;
                            $h += (int) $st->total_h;
                            $s_count += (int) $st->total_s;
                            $i += (int) $st->total_i;
                            $a += (int) $st->total_a;
                        }
                    }

                    $persen = $totalSesi > 0 ? round(($h / $totalSesi) * 100, 1) : 0;

                    return (object) [
                        'id' => $r->id,
                        'nama' => $r->nama,
                        'pembimbing' => $r->pembimbing?->name ?? ($r->pembimbing?->nama_lengkap ?? '-'),
                        'total_santri' => $totalSantri,
                        'total_sesi' => $totalSesi,
                        'h' => $h,
                        's' => $s_count,
                        'i' => $i,
                        'a' => $a,
                        'persen' => $persen,
                    ];
                });

            // Detail Siswa jika modal aktif
            if ($this->showDetailModal && $this->selectedDetailSiswaId) {
                $detailSiswa = Siswa::with(['rombel', 'rayon', 'pesantrenKamars', 'pesantrenHalaqohs'])->find($this->selectedDetailSiswaId);
                $detailPresensis = PesantrenHalaqohPresensiDetail::where('siswa_id', $this->selectedDetailSiswaId)
                    ->whereHas('presensi', function ($q) use ($startDate, $endDate) {
                        $q->whereDate('tanggal', '>=', $startDate)
                            ->whereDate('tanggal', '<=', $endDate);
                        if ($this->rekapHalaqohId) {
                            $q->where('halaqoh_id', $this->rekapHalaqohId);
                        }
                        if ($this->rekapWaktuSesi) {
                            $q->where('waktu_sesi', $this->rekapWaktuSesi);
                        }
                    })
                    ->with(['presensi.halaqoh'])
                    ->get()
                    ->sortByDesc(fn ($d) => $d->presensi?->tanggal?->timestamp);
            }
        }

        return view('kepesantrenan::livewire.halaqoh-index', [
            'halaqohs' => $halaqohs,
            'selectedHalaqoh' => $selectedHalaqoh,
            'pengajars' => $pengajars,
            'availableSiswas' => $availableSiswas,
            'rekapSiswas' => $rekapSiswas,
            'rekapKamars' => $rekapKamars,
            'rekapRayons' => $rekapRayons,
            'globalStats' => $globalStats,
            'globalPersen' => $globalPersen,
            'detailSiswa' => $detailSiswa,
            'detailPresensis' => $detailPresensis,
        ]);
    }
}
