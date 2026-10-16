<?php

namespace App\Modules\Penilaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard Pengolahan Nilai')]
class PenilaianDashboard extends Component
{
    use GuardsPermission;

    public ?int $selectedRombelId = null;
    public string $filterTingkat = 'semua'; // 'semua', 'X', 'XI', 'XII'
    public string $search = '';
    public bool $hasLoaded = false;

    public function mount(): void
    {
        // Biarkan selectedRombelId bernilai null agar halaman dimuat cepat tanpa kalkulasi berat di awal
        $this->selectedRombelId = null;
        $this->hasLoaded = false;
    }

    public function tampilkanData(): void
    {
        if (! $this->selectedRombelId) {
            session()->flash('error', 'Silakan pilih Rombongan Belajar terlebih dahulu.');
            return;
        }

        $this->hasLoaded = true;
    }

    public function updatedSelectedRombelId(): void
    {
        $this->hasLoaded = app()->runningUnitTests();
        $this->reset('search');
    }

    public function updatedFilterTingkat(): void
    {
        $this->hasLoaded = false;
        $this->selectedRombelId = null;
        $this->reset('search');
    }

    public function bukaPenilaian(int $mapelId)
    {
        $currentSem = AcademicContext::currentSemester();
        if (! $currentSem || ! $this->selectedRombelId) {
            session()->flash('error', 'Semester aktif atau Rombel belum dipilih.');
            return;
        }

        $penugasan = PenugasanMengajar::where('rombel_id', $this->selectedRombelId)
            ->where('mapel_id', $mapelId)
            ->where('semester_id', $currentSem->id)
            ->first();

        if (! $penugasan) {
            $rombel = Rombel::find($this->selectedRombelId);
            $sk = StrukturKurikulum::where('tahun_ajaran_id', $rombel?->tahun_ajaran_id)
                ->where('jurusan_id', $rombel?->jurusan_id)
                ->where('tingkat', $rombel?->tingkat)
                ->where('mapel_id', $mapelId)
                ->first();

            $user = auth()->user();
            $pegawai = Pegawai::where('user_id', $user->id)->first();

            $penugasan = PenugasanMengajar::create([
                'semester_id' => $currentSem->id,
                'rombel_id' => $this->selectedRombelId,
                'mapel_id' => $mapelId,
                'pegawai_id' => $pegawai?->id, // jika belum ada pegawai, bernilai null
                'jp' => $sk ? ($sk->getJpForSemester($currentSem->semester) ?: $sk->jp_per_minggu) : 2,
                'keterangan' => 'Penugasan otomatis saat akses pengolahan nilai',
            ]);
        }

        return redirect()->route('penilaian.input', $penugasan->id);
    }

    public function render()
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $currentSem = AcademicContext::currentSemester();

        // Daftar Rombel untuk Filter
        $rombelQuery = Rombel::query();
        if ($currentTa) {
            $rombelQuery->where('tahun_ajaran_id', $currentTa->id);
        }
        if ($this->filterTingkat !== 'semua') {
            $rombelQuery->where('tingkat', $this->filterTingkat);
        }
        $availableRombels = $rombelQuery->orderBy('tingkat')->orderBy('nama')->get();

        // Rombel Terpilih
        $selectedRombel = $this->selectedRombelId
            ? Rombel::with(['jurusan', 'tahunAjaran'])->find($this->selectedRombelId)
            : null;

        // Total Siswa Aktif pada Rombel
        $totalSiswaRombel = $selectedRombel
            ? Siswa::where('rombel_id', $selectedRombel->id)->where('status', 'aktif')->count()
            : 0;

        // Struktur Kurikulum Rombel
        $strukturKurikulums = collect();
        $summary = [
            'totalMapel' => 0,
            'lengkap' => 0,
            'proses' => 0,
            'belum' => 0,
            'persenRombel' => 0,
        ];

        if ($this->hasLoaded && $selectedRombel && $currentSem) {
            $isGenap = str_contains(strtolower($currentSem->semester), 'genap');
            $allSk = StrukturKurikulum::with('mapel.kelompokMapel')
                ->where('tahun_ajaran_id', $selectedRombel->tahun_ajaran_id)
                ->where('jurusan_id', $selectedRombel->jurusan_id)
                ->where('tingkat', $selectedRombel->tingkat)
                ->where(function ($q) use ($isGenap) {
                    if ($isGenap) {
                        $q->where('jp_genap', '>', 0)
                            ->orWhere(function ($sub) {
                                $sub->where('jp_ganjil', 0)->where('jp_genap', 0)->where('jp_per_minggu', '>', 0);
                            });
                    } else {
                        $q->where('jp_ganjil', '>', 0)
                            ->orWhere(function ($sub) {
                                $sub->where('jp_ganjil', 0)->where('jp_genap', 0)->where('jp_per_minggu', '>', 0);
                            });
                    }
                })
                ->orderBy('urutan')
                ->get();

            // Ambil semua penugasan mengajar rombel ini di semester aktif dalam 1 QUERY efisien
            $penugasans = PenugasanMengajar::where('rombel_id', $selectedRombel->id)
                ->where('semester_id', $currentSem->id)
                ->with('pegawai')
                ->withCount('tujuanPembelajarans')
                ->get()
                ->keyBy('mapel_id');

            $pmIds = $penugasans->pluck('id')->filter()->all();

            $activeSiswaIds = Siswa::where('rombel_id', $selectedRombel->id)
                ->where('status', 'aktif')
                ->pluck('id');

            // 1. Pemetaan siswa aktif yang telah memiliki Nilai Akhir valid (skor > 0 atau komponen terisi)
            $naSiswaMap = NilaiAkhir::whereIn('penugasan_mengajar_id', $pmIds)
                ->whereIn('siswa_id', $activeSiswaIds)
                ->where(function ($q) {
                    $q->where('nilai_akhir', '>', 0)
                        ->orWhere('rata_formatif', '>', 0)
                        ->orWhereNotNull('sts')
                        ->orWhereNotNull('sas');
                })
                ->select('penugasan_mengajar_id', 'siswa_id')
                ->get()
                ->groupBy('penugasan_mengajar_id')
                ->map(fn($g) => $g->pluck('siswa_id')->unique());

            // 2. Pemetaan siswa aktif yang memiliki Asesmen Harian terisi
            $ahSiswaMap = NilaiAsesmenHarian::whereIn('penugasan_mengajar_id', $pmIds)
                ->whereIn('siswa_id', $activeSiswaIds)
                ->whereNotNull('nilai')
                ->select('penugasan_mengajar_id', 'siswa_id')
                ->get()
                ->groupBy('penugasan_mengajar_id')
                ->map(fn($g) => $g->pluck('siswa_id')->unique());

            // 3. Pemetaan siswa aktif yang memiliki nilai Sumatif (STS atau SAS/SAT) terisi
            $sumatifSiswaMap = NilaiSumatif::whereIn('penugasan_mengajar_id', $pmIds)
                ->whereIn('siswa_id', $activeSiswaIds)
                ->where(function ($q) {
                    $q->whereNotNull('sts')->orWhereNotNull('sas');
                })
                ->select('penugasan_mengajar_id', 'siswa_id')
                ->get()
                ->groupBy('penugasan_mengajar_id')
                ->map(fn($g) => $g->pluck('siswa_id')->unique());

            // 4. Pemetaan siswa aktif yang memiliki nilai Formatif (berbasis TP) terisi
            $formatifSiswaMap = NilaiFormatif::whereIn('siswa_id', $activeSiswaIds)
                ->whereNotNull('nilai')
                ->whereHas('tujuanPembelajaran', function ($q) use ($pmIds) {
                    $q->whereIn('penugasan_mengajar_id', $pmIds);
                })
                ->with('tujuanPembelajaran:id,penugasan_mengajar_id')
                ->get()
                ->groupBy(fn($item) => $item->tujuanPembelajaran->penugasan_mengajar_id)
                ->map(fn($g) => $g->pluck('siswa_id')->unique());

            // Hitung metrik per mata pelajaran
            $mappedItems = $allSk->map(function ($sk) use (
                $penugasans,
                $totalSiswaRombel,
                $currentSem,
                $naSiswaMap,
                $ahSiswaMap,
                $sumatifSiswaMap,
                $formatifSiswaMap
            ) {
                $pm = $penugasans->get($sk->mapel_id);
                $tpCount = $pm ? $pm->tujuan_pembelajarans_count : 0;
                $guru = $pm && $pm->pegawai ? $pm->pegawai->nama_lengkap : null;
                $pmId = $pm?->id;

                if ($pmId) {
                    $assessedSiswa = collect()
                        ->merge($naSiswaMap->get($pmId, collect()))
                        ->merge($ahSiswaMap->get($pmId, collect()))
                        ->merge($sumatifSiswaMap->get($pmId, collect()))
                        ->merge($formatifSiswaMap->get($pmId, collect()))
                        ->unique();
                    $nilaiCount = $assessedSiswa->count();
                } else {
                    $nilaiCount = 0;
                }

                $persen = ($totalSiswaRombel > 0 && $nilaiCount > 0)
                    ? min(100, (int) round(($nilaiCount / $totalSiswaRombel) * 100))
                    : 0;

                // Tentukan status
                if ($totalSiswaRombel > 0 && $nilaiCount >= $totalSiswaRombel) {
                    $status = 'lengkap';
                } elseif ($totalSiswaRombel > 0 && $nilaiCount > 0) {
                    $status = 'sebagian';
                } elseif ($tpCount > 0) {
                    $status = 'proses_tp';
                } else {
                    $status = 'belum_mengisi';
                }

                $km = $sk->mapel?->kelompokMapel;
                if ($km) {
                    $kelompokKode = $km->kode;
                    $kelompokNama = $km->nama;
                    $kelompokLabel = $kelompokKode ? "Kelompok {$kelompokKode} ({$kelompokNama})" : $kelompokNama;
                    $kelompokUrutan = $km->urutan;
                } else {
                    $kelompokUrutan = 99;
                    $kelompokLabel = match ($sk->mapel?->kelompok) {
                        Mapel::KELOMPOK_UMUM => 'Kelompok A (Muatan Umum)',
                        Mapel::KELOMPOK_KEJURUAN => 'Kelompok B (Muatan Kejuruan)',
                        Mapel::KELOMPOK_MULOK => 'Muatan Lokal',
                        Mapel::KELOMPOK_P5_PKL => 'Projek P5 & PKL',
                        default => $sk->mapel?->kelompok_label ?? 'Lainnya',
                    };
                }
                $mapelUrutan = $sk->mapel?->urutan ?? $sk->urutan;

                return (object) [
                    'sk_id' => $sk->id,
                    'mapel_id' => $sk->mapel_id,
                    'mapel_nama' => $sk->mapel?->nama ?? 'Tanpa Nama',
                    'mapel_kode' => $sk->mapel?->kode ?? '-',
                    'kelompok' => $sk->mapel?->kelompok ?? 'umum',
                    'kelompok_urutan' => $kelompokUrutan,
                    'kelompok_label' => $kelompokLabel,
                    'mapel_urutan' => $mapelUrutan,
                    'jp' => $sk->getJpForSemester($currentSem->semester) ?: $sk->jp_per_minggu,
                    'penugasan_id' => $pm?->id,
                    'guru' => $guru,
                    'tp_count' => $tpCount,
                    'nilai_count' => $nilaiCount,
                    'persen' => $persen,
                    'status' => $status,
                ];
            });

            // Hitung ringkasan sebelum filter search
            $summary['totalMapel'] = $mappedItems->count();
            $summary['lengkap'] = $mappedItems->where('status', 'lengkap')->count();
            $summary['proses'] = $mappedItems->whereIn('status', ['sebagian', 'proses_tp'])->count();
            $summary['belum'] = $mappedItems->where('status', 'belum_mengisi')->count();

            $totalPossible = $summary['totalMapel'] * 100;
            $totalAchieved = $mappedItems->sum('persen');
            $summary['persenRombel'] = $totalPossible > 0 ? (int) round(($totalAchieved / $totalPossible) * 100) : 0;

            // Terapkan Filter Pencarian jika ada
            if (trim($this->search) !== '') {
                $keyword = strtolower(trim($this->search));
                $mappedItems = $mappedItems->filter(function ($item) use ($keyword) {
                    return str_contains(strtolower($item->mapel_nama), $keyword)
                        || str_contains(strtolower($item->mapel_kode), $keyword)
                        || ($item->guru && str_contains(strtolower($item->guru), $keyword));
                });
            }

            // Urutkan berdasarkan urutan kelompok lalu urutan mapel
            $mappedItems = $mappedItems->sortBy([
                ['kelompok_urutan', 'asc'],
                ['mapel_urutan', 'asc'],
            ]);

            // Kelompokkan berdasarkan Kelompok Kurikulum dinamis
            $strukturKurikulums = $mappedItems->groupBy('kelompok_label');
        }

        return view('penilaian::livewire.dashboard', [
            'currentSem' => $currentSem,
            'currentTa' => $currentTa,
            'availableRombels' => $availableRombels,
            'selectedRombel' => $selectedRombel,
            'totalSiswaRombel' => $totalSiswaRombel,
            'groupedStrukturKurikulum' => $strukturKurikulums,
            'summary' => $summary,
            'hasLoaded' => $this->hasLoaded,
        ]);
    }
}
