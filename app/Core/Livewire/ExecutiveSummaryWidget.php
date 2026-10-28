<?php

namespace App\Core\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Absensi\Models\AbsensiHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusAbsensi;

#[Lazy]
class ExecutiveSummaryWidget extends Component
{
    public function render()
    {
        // 1. Demografi
        $siswaLaki = Siswa::where('status', 'aktif')->where('jk', 'L')->count();
        $siswaPerempuan = Siswa::where('status', 'aktif')->where('jk', 'P')->count();
        $totalGuru = Pegawai::where('is_active', true)->where('jenis_gtk', 'like', '%Guru%')->count();
        $totalPegawaiNonGuru = Pegawai::where('is_active', true)->where('jenis_gtk', 'not like', '%Guru%')->count();

        if ($totalGuru === 0 && $totalPegawaiNonGuru === 0) {
            $totalGuru = Pegawai::where('is_active', true)->count(); // fallback
        }

        // 2. Kehadiran (7 Hari Terakhir)
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $absensiRaw = AbsensiHarian::select('tanggal', 'status', DB::raw('count(*) as total'))
            ->where('tanggal', '>=', $sevenDaysAgo)
            ->groupBy('tanggal', 'status')
            ->get();
            
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = Carbon::today()->subDays($i)->format('Y-m-d');
        }
        
        $kehadiranData = [
            'Hadir' => array_fill(0, 7, 0),
            'Sakit' => array_fill(0, 7, 0),
            'Izin' => array_fill(0, 7, 0),
            'Alpa' => array_fill(0, 7, 0),
        ];

        foreach ($absensiRaw as $abs) {
            $dateIndex = array_search($abs->tanggal->format('Y-m-d'), $dates);
            if ($dateIndex !== false) {
                // Ensure enum resolving if necessary
                $statusKey = $abs->status instanceof StatusAbsensi ? $abs->status->value : $abs->status;
                
                // Map enum values if they differ slightly
                if (in_array($statusKey, ['Hadir', 'Sakit', 'Izin', 'Alpa'])) {
                    $kehadiranData[$statusKey][$dateIndex] = (int) $abs->total;
                } elseif (in_array($statusKey, ['Dispen_CBT_PLH', 'Dispen_CBT_KWH'])) {
                    $kehadiranData['Hadir'][$dateIndex] += (int) $abs->total; // Dispen counts as Hadir in this summary
                }
            }
        }

        // 3. Akademik (Rata-rata Nilai Akhir per Mata Pelajaran - Top 7)
        $akademikRaw = DB::table('nilai_akhirs')
            ->join('penugasan_mengajars', 'nilai_akhirs.penugasan_mengajar_id', '=', 'penugasan_mengajars.id')
            ->join('mapels', 'penugasan_mengajars.mapel_id', '=', 'mapels.id')
            ->select('mapels.nama as mapel', DB::raw('AVG(nilai_akhirs.nilai_akhir) as rata_rata'))
            ->groupBy('mapels.id', 'mapels.nama')
            ->orderByDesc('rata_rata')
            ->limit(7)
            ->get();

        $mapelLabels = $akademikRaw->pluck('mapel')->toArray();
        $mapelScores = $akademikRaw->pluck('rata_rata')->map(fn($v) => round((float)$v, 2))->toArray();

        // Fallback jika belum ada data nilai
        if (empty($mapelLabels)) {
            $mapelLabels = ['Belum ada data'];
            $mapelScores = [0];
        }

        // 4. Data Statis (Demo): Bimbingan Konseling
        $konselingDemo = [
            'categories' => ['Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'series' => [
                ['name' => 'Pelanggaran', 'data' => [12, 19, 15, 8, 5, 2]],
                ['name' => 'Prestasi', 'data' => [2, 5, 8, 12, 14, 20]]
            ]
        ];

        // 5. Data Statis (Demo): Pembayaran
        $pembayaranDemo = [
            'labels' => ['Lunas', 'Belum Lunas', 'Menunggak'],
            'series' => [65, 25, 10] // Persentase
        ];

        return view('livewire.executive-summary-widget', [
            'demografi' => [
                'siswaLaki' => $siswaLaki,
                'siswaPerempuan' => $siswaPerempuan,
                'guru' => $totalGuru,
                'tendik' => $totalPegawaiNonGuru,
            ],
            'kehadiran' => [
                'categories' => array_map(fn($d) => Carbon::parse($d)->translatedFormat('d M'), $dates),
                'series' => $kehadiranData,
            ],
            'akademik' => [
                'categories' => $mapelLabels,
                'series' => $mapelScores,
            ],
            'konselingDemo' => $konselingDemo,
            'pembayaranDemo' => $pembayaranDemo,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="space-y-6 animate-pulse">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="card h-80 bg-surface-hover rounded-xl border border-border"></div>
                <div class="card h-80 bg-surface-hover rounded-xl border border-border"></div>
                <div class="card h-80 bg-surface-hover rounded-xl border border-border"></div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card h-80 bg-surface-hover rounded-xl border border-border"></div>
                <div class="card h-80 bg-surface-hover rounded-xl border border-border"></div>
            </div>
        </div>
        HTML;
    }
}
