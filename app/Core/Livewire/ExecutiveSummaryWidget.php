<?php

namespace App\Core\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Penilaian\Models\NilaiAkhir;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusAbsensi;

#[Lazy]
class ExecutiveSummaryWidget extends Component
{
    public function render()
    {
        $semester = AcademicContext::currentSemester();
        $semesterId = $semester?->id;
        $tahunAjaran = AcademicContext::currentTahunAjaran();
        $tahunAjaranNama = $tahunAjaran?->nama ?? '-';
        $semesterNama = $semester ? ucfirst($semester->semester) : '-';

        // 1. Demografi Siswa & GTK
        $siswaLaki = Siswa::where('status', 'aktif')->where('jk', 'L')->count();
        $siswaPerempuan = Siswa::where('status', 'aktif')->where('jk', 'P')->count();
        $totalSiswa = $siswaLaki + $siswaPerempuan;

        $totalGuru = Pegawai::where('is_active', true)->where('jenis_gtk', 'like', '%Guru%')->count();
        $totalTendik = Pegawai::where('is_active', true)->where('jenis_gtk', 'not like', '%Guru%')->count();
        $totalGTK = $totalGuru + $totalTendik;

        if ($totalGTK === 0) {
            $totalGTK = Pegawai::where('is_active', true)->count();
            $totalGuru = $totalGTK;
        }

        // 2. Kehadiran Hari Ini (atau Hari Efektif Terakhir yang Tercatat)
        $todayStr = Carbon::today()->format('Y-m-d');
        $hasToday = AbsensiHarian::whereDate('tanggal', $todayStr)->exists();

        if ($hasToday) {
            $targetTanggal = $todayStr;
            $isToday = true;
            $tanggalLabel = 'Hari ini (' . Carbon::today()->translatedFormat('d M Y') . ')';
        } else {
            $latestRecorded = AbsensiHarian::whereDate('tanggal', '<=', $todayStr)->max('tanggal');
            $targetTanggal = $latestRecorded ? Carbon::parse($latestRecorded)->format('Y-m-d') : $todayStr;
            $isToday = false;
            $tanggalLabel = $latestRecorded ? Carbon::parse($latestRecorded)->translatedFormat('d M Y') : 'Belum Ada Presensi';
        }

        $absensiTarget = AbsensiHarian::whereDate('tanggal', $targetTanggal)->get();
        $hadirCount = 0;
        $sakitCount = 0;
        $izinCount = 0;
        $alpaCount = 0;

        foreach ($absensiTarget as $item) {
            $statusVal = $item->status instanceof StatusAbsensi ? $item->status : StatusAbsensi::tryFrom((string)$item->status);
            if ($statusVal && $statusVal->isHadirStatistik()) {
                $hadirCount++;
            } elseif ($statusVal === StatusAbsensi::SAKIT) {
                $sakitCount++;
            } elseif ($statusVal === StatusAbsensi::IZIN) {
                $izinCount++;
            } elseif ($statusVal === StatusAbsensi::ALPA) {
                $alpaCount++;
            }
        }

        $totalTargetAbsensi = $hadirCount + $sakitCount + $izinCount + $alpaCount;
        $persenKehadiran = $totalTargetAbsensi > 0 ? round(($hadirCount / $totalTargetAbsensi) * 100, 1) : 0;

        // 3. Tren Kehadiran (7 Hari Sekolah Efektif Terakhir yang Tercatat)
        $sevenDates = AbsensiHarian::whereDate('tanggal', '<=', $todayStr)
            ->distinct()
            ->orderByDesc('tanggal')
            ->limit(7)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->reverse()
            ->values()
            ->toArray();

        // Fallback jika database belum ada data absensi
        if (empty($sevenDates)) {
            for ($i = 6; $i >= 0; $i--) {
                $sevenDates[] = Carbon::today()->subDays($i)->format('Y-m-d');
            }
        }

        $kehadiranRaw = AbsensiHarian::whereIn('tanggal', $sevenDates)
            ->select('tanggal', 'status', DB::raw('count(*) as total'))
            ->groupBy('tanggal', 'status')
            ->get();

        $kehadiranSeries = [
            'Hadir' => array_fill(0, count($sevenDates), 0),
            'Sakit' => array_fill(0, count($sevenDates), 0),
            'Izin' => array_fill(0, count($sevenDates), 0),
            'Alpa' => array_fill(0, count($sevenDates), 0),
        ];

        foreach ($kehadiranRaw as $row) {
            $dIndex = array_search(Carbon::parse($row->tanggal)->format('Y-m-d'), $sevenDates);
            if ($dIndex !== false) {
                $statusVal = $row->status instanceof StatusAbsensi ? $row->status : StatusAbsensi::tryFrom((string)$row->status);
                if ($statusVal && $statusVal->isHadirStatistik()) {
                    $kehadiranSeries['Hadir'][$dIndex] += (int) $row->total;
                } elseif ($statusVal === StatusAbsensi::SAKIT) {
                    $kehadiranSeries['Sakit'][$dIndex] += (int) $row->total;
                } elseif ($statusVal === StatusAbsensi::IZIN) {
                    $kehadiranSeries['Izin'][$dIndex] += (int) $row->total;
                } elseif ($statusVal === StatusAbsensi::ALPA) {
                    $kehadiranSeries['Alpa'][$dIndex] += (int) $row->total;
                }
            }
        }

        $kehadiranCategories = array_map(fn($d) => Carbon::parse($d)->translatedFormat('d M'), $sevenDates);

        // 4. Kesiapan Rapor 9M (Data Nyata Operasional)
        $penugasans = PenugasanMengajar::with('rombel')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->get();

        $totalPenugasan = $penugasans->count();
        $penugasanIdsWith9m = NilaiAkhir::whereIn('penugasan_mengajar_id', $penugasans->pluck('id'))
            ->whereNotNull('data_9m')
            ->distinct()
            ->pluck('penugasan_mengajar_id')
            ->flip();

        $totalTuntas = $penugasanIdsWith9m->count();
        $persenKesiapan9m = $totalPenugasan > 0 ? round(($totalTuntas / $totalPenugasan) * 100, 1) : 0;

        $rombelGroups = $penugasans->groupBy(fn($p) => $p->rombel?->nama ?? 'Umum')
            ->map(function ($group, $rombelNama) use ($penugasanIdsWith9m) {
                $total = $group->count();
                $tuntas = $group->filter(fn($p) => $penugasanIdsWith9m->has($p->id))->count();
                $persen = $total > 0 ? round(($tuntas / $total) * 100, 1) : 0;
                return [
                    'rombel' => $rombelNama,
                    'total' => $total,
                    'tuntas' => $tuntas,
                    'persen' => $persen,
                ];
            })
            ->sortBy('rombel')
            ->values();

        $chartRaporCategories = $rombelGroups->pluck('rombel')->toArray();
        $chartRaporSeries = $rombelGroups->pluck('persen')->toArray();
        $chartRaporDetails = $rombelGroups->map(fn($r) => $r['tuntas'] . '/' . $r['total'] . ' Mapel')->toArray();

        // Fallback jika belum ada data rombel/penugasan
        if (empty($chartRaporCategories)) {
            $chartRaporCategories = ['Semua Rombel'];
            $chartRaporSeries = [0];
            $chartRaporDetails = ['0/0 Mapel'];
        }

        return view('livewire.executive-summary-widget', [
            'tahunAjaranNama' => $tahunAjaranNama,
            'semesterNama' => $semesterNama,
            'demografi' => [
                'totalSiswa' => $totalSiswa,
                'siswaLaki' => $siswaLaki,
                'siswaPerempuan' => $siswaPerempuan,
                'totalGTK' => $totalGTK,
                'guru' => $totalGuru,
                'tendik' => $totalTendik,
            ],
            'kehadiranHariIni' => [
                'persen' => $persenKehadiran,
                'tanggalLabel' => $tanggalLabel,
                'isToday' => $isToday,
                'hadir' => $hadirCount,
                'sakit' => $sakitCount,
                'izin' => $izinCount,
                'alpa' => $alpaCount,
                'total' => $totalTargetAbsensi,
            ],
            'kehadiranTren' => [
                'categories' => $kehadiranCategories,
                'series' => $kehadiranSeries,
            ],
            'rapor9m' => [
                'persen' => $persenKesiapan9m,
                'totalPenugasan' => $totalPenugasan,
                'totalTuntas' => $totalTuntas,
                'categories' => $chartRaporCategories,
                'series' => $chartRaporSeries,
                'details' => $chartRaporDetails,
                'rombelGroups' => $rombelGroups,
            ],
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="space-y-6 animate-pulse">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="h-28 bg-surface-hover rounded-xl border border-border"></div>
                <div class="h-28 bg-surface-hover rounded-xl border border-border"></div>
                <div class="h-28 bg-surface-hover rounded-xl border border-border"></div>
                <div class="h-28 bg-surface-hover rounded-xl border border-border"></div>
            </div>
            <div class="space-y-6">
                <div class="h-72 bg-surface-hover rounded-xl border border-border"></div>
                <div class="h-72 bg-surface-hover rounded-xl border border-border"></div>
            </div>
        </div>
        HTML;
    }
}
