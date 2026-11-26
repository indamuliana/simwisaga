<?php

namespace App\Modules\Absensi\Services;

use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Models\School;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardAbsensiService
{
    public function resolveAccessScope(?User $user): array
    {
        if (! $user) {
            return ['all' => false, 'rombel_ids' => [], 'rayon_ids' => []];
        }

        if ($user->isSuperAdmin() || $user->hasRole(['Kesiswaan', 'Kepala Sekolah', 'Kurikulum', 'Operator'])) {
            return ['all' => true, 'rombel_ids' => [], 'rayon_ids' => []];
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

        return [
            'all' => false,
            'rombel_ids' => array_values(array_unique(array_map('intval', $rombelIds))),
            'rayon_ids' => array_values(array_unique(array_map('intval', $rayonIds))),
        ];
    }

    public function buildDashboardData(?User $user, array $filters): array
    {
        $scope = $this->resolveAccessScope($user);
        $tanggal = $this->normalizeDate($filters['tanggal'] ?? null);
        $chartRange = $filters['chart_range'] ?? '7d';
        $tahunAjaranId = AcademicContext::currentTahunAjaran()?->id;
        $semester = AcademicContext::currentSemester() ?? Semester::orderByDesc('id')->first();

        $siswaRows = $this->filteredSiswaQuery($scope, $filters)
            ->get(['id', 'rombel_id', 'rayon_id']);

        $siswaIds = $siswaRows->pluck('id')->all();
        $totalSiswa = count($siswaIds);

        [$waliMap, $pembimbingMap] = $this->loadStaffMaps($tahunAjaranId);

        $kpi = $this->buildKpi($tanggal, $siswaIds, $totalSiswa);
        $chart = $this->buildChartAndTopAbsent($tanggal, $chartRange, $semester, $siswaIds, $siswaRows);
        $progressRombel = $this->buildProgressPerRombel($tanggal, $siswaRows, $waliMap);
        $progressRayon = $this->buildProgressPerRayon($tanggal, $siswaRows, $pembimbingMap);
        $ketidakhadiran = $this->buildAbsenceAndDispenTables($tanggal, $siswaIds, $waliMap, $pembimbingMap);
        $earlyWarning = $this->buildEarlyWarning($tanggal, $semester, $siswaIds, $waliMap, $pembimbingMap);
        $waSummary = $this->buildWhatsappSummary($tanggal, $kpi, $progressRombel);
        $waSummaryRayon = $this->buildWhatsappByRayon($tanggal, $ketidakhadiran);
        $waSummaryRombel = $this->buildWhatsappByRombel($tanggal, $ketidakhadiran);

        return [
            'scope' => $scope,
            'tanggal' => $tanggal,
            'kpi' => $kpi,
            'chart' => $chart['chart'],
            'top_rombel_absent' => $chart['top_rombel'],
            'top_rayon_absent' => $chart['top_rayon'],
            'progress_rombel' => $progressRombel,
            'progress_rayon' => $progressRayon,
            'daftar_alpa' => $ketidakhadiran['alpa'],
            'daftar_izin' => $ketidakhadiran['izin'],
            'daftar_sakit' => $ketidakhadiran['sakit'],
            'daftar_dispen' => $ketidakhadiran['dispen'],
            'alert_beruntun' => $earlyWarning['beruntun'],
            'alert_sp' => $earlyWarning['sp'],
            'wa_summary' => $waSummary,
            'wa_summary_rayon' => $waSummaryRayon,
            'wa_summary_rombel' => $waSummaryRombel,
        ];
    }

    public function getFilterOptions(?User $user): array
    {
        $scope = $this->resolveAccessScope($user);
        $activeTa = AcademicContext::currentTahunAjaran();

        if ($scope['all']) {
            $rombels = Rombel::when($activeTa, fn (Builder $q) => $q->where('tahun_ajaran_id', $activeTa->id))
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->get(['id', 'nama', 'tingkat']);

            if ($rombels->isEmpty()) {
                $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'tingkat']);
            }

            $rayons = Rayon::orderBy('nama')->get(['id', 'nama']);
        } else {
            $base = $this->baseScopedSiswaQuery($scope);
            $rombelIds = (clone $base)->whereNotNull('rombel_id')->distinct()->pluck('rombel_id')
                ->merge($scope['rombel_ids'])->unique()->values();
            $rayonIds = (clone $base)->whereNotNull('rayon_id')->distinct()->pluck('rayon_id')
                ->merge($scope['rayon_ids'])->unique()->values();

            $rombels = $rombelIds->isEmpty()
                ? collect()
                : Rombel::whereIn('id', $rombelIds)->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'tingkat']);
            $rayons = $rayonIds->isEmpty()
                ? collect()
                : Rayon::whereIn('id', $rayonIds)->orderBy('nama')->get(['id', 'nama']);
        }

        $tingkats = $rombels->pluck('tingkat')->filter()->unique()->values();

        return [
            'rombels' => $rombels,
            'rayons' => $rayons,
            'tingkats' => $tingkats,
            'has_full_access' => $scope['all'],
        ];
    }

    public function formatWaNumber(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (! $clean || strlen($clean) < 9) {
            return null;
        }

        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62'.$clean;
        }

        return $clean;
    }

    protected function baseScopedSiswaQuery(array $scope): Builder
    {
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

    protected function filteredSiswaQuery(array $scope, array $filters): Builder
    {
        $rombelId = $filters['rombel_id'] ?? '';
        $rayonId = $filters['rayon_id'] ?? '';
        $tingkat = $filters['tingkat'] ?? '';
        $gender = $filters['gender'] ?? '';

        return $this->baseScopedSiswaQuery($scope)
            ->when($rombelId !== '' && $rombelId !== null, fn (Builder $q) => $q->where('rombel_id', (int) $rombelId))
            ->when($rayonId !== '' && $rayonId !== null, fn (Builder $q) => $q->where('rayon_id', (int) $rayonId))
            ->when($tingkat !== '' && $tingkat !== null, fn (Builder $q) => $q->whereHas('rombel', fn (Builder $rq) => $rq->where('tingkat', $tingkat)))
            ->when($gender !== '' && $gender !== null, fn (Builder $q) => $q->whereRaw('UPPER(jk) = ?', [strtoupper((string) $gender)]));
    }

    protected function buildKpi(string $tanggal, array $siswaIds, int $totalSiswa): array
    {
        $counts = $this->countStatusesForDate($tanggal, $siswaIds);
        $sudahAbsen = array_sum($counts);
        $belumAbsen = max(0, $totalSiswa - $sudahAbsen);
        $dispenTotal = $counts['dispen_cbt_plh'] + $counts['dispen_cbt_kwh'] + $counts['dispen_lainnya'];
        $hadirStatistik = $counts['hadir'] + $dispenTotal;
        $tidakHadir = $counts['sakit'] + $counts['izin'] + $counts['alpa'];

        $persenHadir = $sudahAbsen > 0 ? round(($hadirStatistik / $sudahAbsen) * 100, 1) : 0.0;
        $persenInput = $totalSiswa > 0 ? round(($sudahAbsen / $totalSiswa) * 100, 1) : 0.0;

        // Delta vs hari sekolah terakhir yang tercatat sebelum tanggal fokus
        $deltaPersenHadir = null;
        $prevDateLabel = null;

        if (! empty($siswaIds)) {
            $prevDateRaw = AbsensiHarian::where('tanggal', '<', $tanggal)
                ->whereIn('siswa_id', $siswaIds)
                ->max('tanggal');

            if ($prevDateRaw) {
                $prevDate = Carbon::parse($prevDateRaw)->toDateString();
                $prevCounts = $this->countStatusesForDate($prevDate, $siswaIds);
                $prevTotal = array_sum($prevCounts);
                if ($prevTotal > 0) {
                    $prevHadirStat = $prevCounts['hadir']
                        + $prevCounts['dispen_cbt_plh']
                        + $prevCounts['dispen_cbt_kwh']
                        + $prevCounts['dispen_lainnya'];
                    $prevPersen = round(($prevHadirStat / $prevTotal) * 100, 1);
                    if ($sudahAbsen > 0) {
                        $deltaPersenHadir = round($persenHadir - $prevPersen, 1);
                    }
                    $prevDateLabel = Carbon::parse($prevDate)->translatedFormat('d M');
                }
            }
        }

        return [
            'total_siswa' => $totalSiswa,
            'sudah_absen' => $sudahAbsen,
            'belum_absen' => $belumAbsen,
            'hadir' => $counts['hadir'],
            'hadir_statistik' => $hadirStatistik,
            'sakit' => $counts['sakit'],
            'izin' => $counts['izin'],
            'alpa' => $counts['alpa'],
            'tidak_hadir' => $tidakHadir,
            'dispen_cbt_plh' => $counts['dispen_cbt_plh'],
            'dispen_cbt_kwh' => $counts['dispen_cbt_kwh'],
            'dispen_lainnya' => $counts['dispen_lainnya'],
            'dispen_total' => $dispenTotal,
            'persen_hadir' => $persenHadir,
            'persen_input' => $persenInput,
            'delta_persen_hadir' => $deltaPersenHadir,
            'prev_date_label' => $prevDateLabel,
        ];
    }

    protected function countStatusesForDate(string $tanggal, array $siswaIds): array
    {
        $counts = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'dispen_cbt_plh' => 0,
            'dispen_cbt_kwh' => 0,
            'dispen_lainnya' => 0,
        ];

        if (empty($siswaIds)) {
            return $counts;
        }

        $rows = AbsensiHarian::where('tanggal', $tanggal)
            ->whereIn('siswa_id', $siswaIds)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        foreach ($rows as $row) {
            $statusVal = $row->status instanceof StatusAbsensi ? $row->status->value : (string) $row->status;
            $key = match ($statusVal) {
                StatusAbsensi::HADIR->value => 'hadir',
                StatusAbsensi::SAKIT->value => 'sakit',
                StatusAbsensi::IZIN->value => 'izin',
                StatusAbsensi::ALPA->value => 'alpa',
                StatusAbsensi::DISPEN_CBT_PLH->value => 'dispen_cbt_plh',
                StatusAbsensi::DISPEN_CBT_KWH->value => 'dispen_cbt_kwh',
                StatusAbsensi::DISPEN_LAINNYA->value => 'dispen_lainnya',
                default => null,
            };
            if ($key !== null) {
                $counts[$key] += (int) $row->total;
            }
        }

        return $counts;
    }

    protected function buildChartAndTopAbsent(
        string $tanggal,
        string $chartRange,
        ?Semester $semester,
        array $siswaIds,
        Collection $siswaRows
    ): array {
        $end = Carbon::parse($tanggal)->startOfDay();
        $start = match ($chartRange) {
            '14d' => $end->copy()->subDays(13),
            '30d' => $end->copy()->subDays(29),
            'bulan_ini' => $end->copy()->startOfMonth(),
            'semester' => null,
            default => $end->copy()->subDays(6),
        };

        $emptyChart = [
            'categories' => [],
            'series' => [
                'H' => [],
                'S' => [],
                'I' => [],
                'A' => [],
                'Dispen' => [],
            ],
            'range_label' => '',
        ];

        if (empty($siswaIds)) {
            return ['chart' => $emptyChart, 'top_rombel' => [], 'top_rayon' => []];
        }

        $query = AbsensiHarian::whereIn('siswa_id', $siswaIds)
            ->where('tanggal', '<=', $end->toDateString());

        if ($chartRange === 'semester' && $semester) {
            $query->where('semester_id', $semester->id);
            $rangeLabel = 'Semester '.ucfirst((string) $semester->semester);
        } else {
            $startDate = ($start ?? $end->copy()->subDays(6))->toDateString();
            $query->where('tanggal', '>=', $startDate);
            $rangeLabel = Carbon::parse($startDate)->translatedFormat('d M').' - '.$end->translatedFormat('d M Y');
        }

        $raw = (clone $query)
            ->select('tanggal', 'status', DB::raw('COUNT(*) as total'))
            ->groupBy('tanggal', 'status')
            ->orderBy('tanggal')
            ->get();

        $dates = $raw->pluck('tanggal')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($dates)) {
            $fallbackStart = ($start ?? $end->copy()->subDays(6));
            $cursor = $fallbackStart->copy();
            $limit = 0;
            while ($cursor->lte($end) && $limit < 14) {
                if (! $cursor->isWeekend()) {
                    $dates[] = $cursor->toDateString();
                    $limit++;
                }
                $cursor->addDay();
            }
            if (empty($dates)) {
                $dates = [$end->toDateString()];
            }
        }

        $indexByDate = array_flip($dates);
        $countDates = count($dates);

        $series = [
            'H' => array_fill(0, $countDates, 0),
            'S' => array_fill(0, $countDates, 0),
            'I' => array_fill(0, $countDates, 0),
            'A' => array_fill(0, $countDates, 0),
            'Dispen' => array_fill(0, $countDates, 0),
        ];

        foreach ($raw as $row) {
            $dStr = Carbon::parse($row->tanggal)->toDateString();
            if (! isset($indexByDate[$dStr])) {
                continue;
            }
            $idx = $indexByDate[$dStr];
            $statusVal = $row->status instanceof StatusAbsensi ? $row->status->value : (string) $row->status;
            $tot = (int) $row->total;

            match ($statusVal) {
                StatusAbsensi::HADIR->value => $series['H'][$idx] += $tot,
                StatusAbsensi::SAKIT->value => $series['S'][$idx] += $tot,
                StatusAbsensi::IZIN->value => $series['I'][$idx] += $tot,
                StatusAbsensi::ALPA->value => $series['A'][$idx] += $tot,
                StatusAbsensi::DISPEN_CBT_PLH->value,
                StatusAbsensi::DISPEN_CBT_KWH->value,
                StatusAbsensi::DISPEN_LAINNYA->value => $series['Dispen'][$idx] += $tot,
                default => null,
            };
        }

        $categories = array_map(
            fn (string $d) => Carbon::parse($d)->translatedFormat('d M'),
            $dates
        );

        // Top 5 Rombel & Top 5 Rayon ketidakhadiran (S + I + A) pada periode grafik
        $absentStatuses = [
            StatusAbsensi::ALPA->value,
            StatusAbsensi::IZIN->value,
            StatusAbsensi::SAKIT->value,
        ];

        $absentRows = (clone $query)
            ->whereIn('status', $absentStatuses)
            ->get(['siswa_id', 'rombel_id', 'status']);

        $siswaRayonMap = $siswaRows->pluck('rayon_id', 'id')->all();
        $siswaRombelMap = $siswaRows->pluck('rombel_id', 'id')->all();

        $rombelAgg = [];
        $rayonAgg = [];

        foreach ($absentRows as $r) {
            $statusVal = $r->status instanceof StatusAbsensi ? $r->status->value : (string) $r->status;
            $sKey = match ($statusVal) {
                StatusAbsensi::ALPA->value => 'a',
                StatusAbsensi::IZIN->value => 'i',
                StatusAbsensi::SAKIT->value => 's',
                default => null,
            };
            if (! $sKey) {
                continue;
            }

            $rId = $r->rombel_id ?: ($siswaRombelMap[$r->siswa_id] ?? null);
            if ($rId) {
                $rombelAgg[$rId] ??= ['id' => (int) $rId, 'total' => 0, 'a' => 0, 'i' => 0, 's' => 0];
                $rombelAgg[$rId]['total']++;
                $rombelAgg[$rId][$sKey]++;
            }

            $ryId = $siswaRayonMap[$r->siswa_id] ?? null;
            if ($ryId) {
                $rayonAgg[$ryId] ??= ['id' => (int) $ryId, 'total' => 0, 'a' => 0, 'i' => 0, 's' => 0];
                $rayonAgg[$ryId]['total']++;
                $rayonAgg[$ryId][$sKey]++;
            }
        }

        usort($rombelAgg, fn ($x, $y) => ($y['total'] <=> $x['total']) ?: ($y['a'] <=> $x['a']));
        usort($rayonAgg, fn ($x, $y) => ($y['total'] <=> $x['total']) ?: ($y['a'] <=> $x['a']));

        $topRombelSlice = array_slice($rombelAgg, 0, 5);
        $topRayonSlice = array_slice($rayonAgg, 0, 5);

        $rombelNames = ! empty($topRombelSlice)
            ? Rombel::whereIn('id', array_column($topRombelSlice, 'id'))->pluck('nama', 'id')->all()
            : [];
        $rayonNames = ! empty($topRayonSlice)
            ? Rayon::whereIn('id', array_column($topRayonSlice, 'id'))->pluck('nama', 'id')->all()
            : [];

        $topRombel = array_map(fn ($item) => [
            'id' => $item['id'],
            'nama' => $rombelNames[$item['id']] ?? '-',
            'total' => $item['total'],
            'a' => $item['a'],
            'i' => $item['i'],
            's' => $item['s'],
        ], $topRombelSlice);

        $topRayon = array_map(fn ($item) => [
            'id' => $item['id'],
            'nama' => $rayonNames[$item['id']] ?? '-',
            'total' => $item['total'],
            'a' => $item['a'],
            'i' => $item['i'],
            's' => $item['s'],
        ], $topRayonSlice);

        return [
            'chart' => [
                'categories' => $categories,
                'series' => $series,
                'range_label' => $rangeLabel,
            ],
            'top_rombel' => $topRombel,
            'top_rayon' => $topRayon,
        ];
    }

    protected function buildProgressPerRombel(string $tanggal, Collection $siswaRows, array $waliMap): array
    {
        if ($siswaRows->isEmpty()) {
            return [
                'items' => [],
                'summary' => ['total' => 0, 'lengkap' => 0, 'parsial' => 0, 'belum' => 0],
            ];
        }

        $siswaIds = $siswaRows->pluck('id')->all();
        $recordedSiswaIds = AbsensiHarian::where('tanggal', $tanggal)
            ->whereIn('siswa_id', $siswaIds)
            ->pluck('siswa_id')
            ->flip()
            ->all();

        $grouped = $siswaRows->filter(fn ($s) => ! empty($s->rombel_id))->groupBy('rombel_id');
        $rombelIds = $grouped->keys()->all();
        $rombels = Rombel::whereIn('id', $rombelIds)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'nama', 'tingkat', 'wali_kelas_id'])
            ->keyBy('id');

        $tglLabel = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $items = [];
        $summary = ['total' => 0, 'lengkap' => 0, 'parsial' => 0, 'belum' => 0];

        foreach ($rombels as $rombelId => $rombel) {
            $students = $grouped->get($rombelId, collect());
            $total = $students->count();
            $sudah = $students->filter(fn ($s) => isset($recordedSiswaIds[$s->id]))->count();
            $belum = max(0, $total - $sudah);
            $persen = $total > 0 ? round(($sudah / $total) * 100, 1) : 0.0;

            $summary['total']++;
            if ($belum === 0) {
                $summary['lengkap']++;
                $state = 'lengkap';
                $statusText = 'Lengkap (100%)';
            } elseif ($sudah === 0) {
                $summary['belum']++;
                $state = 'belum';
                $statusText = "Belum diabsen ({$belum} siswa)";
            } else {
                $summary['parsial']++;
                $state = 'parsial';
                $statusText = "{$belum} siswa belum diabsen";
            }

            $wali = $waliMap[$rombelId] ?? ['nama' => '-', 'no_hp' => null];
            $waNum = $this->formatWaNumber($wali['no_hp'] ?? null);
            $waReminderUrl = null;
            if ($waNum && $belum > 0) {
                $msg = "Assalamu'alaikum Bapak/Ibu {$wali['nama']}, mohon bantuan untuk melengkapi absensi harian rombel {$rombel->nama} tanggal {$tglLabel}. Saat ini masih ada {$belum} dari {$total} siswa yang belum tercatat. Terima kasih.";
                $waReminderUrl = 'https://wa.me/'.$waNum.'?text='.rawurlencode($msg);
            }

            $items[] = [
                'id' => $rombelId,
                'nama' => $rombel->nama,
                'tingkat' => $rombel->tingkat,
                'total_siswa' => $total,
                'sudah_absen' => $sudah,
                'belum_absen' => $belum,
                'persen' => $persen,
                'state' => $state,
                'status_text' => $statusText,
                'pj_nama' => $wali['nama'] ?? '-',
                'pj_wa_url' => $waReminderUrl,
                'input_url' => route('absensi.harian', ['tanggal' => $tanggal, 'rombel_id' => $rombelId]),
            ];
        }

        usort($items, function ($a, $b) {
            $aIncomplete = $a['belum_absen'] > 0 ? 0 : 1;
            $bIncomplete = $b['belum_absen'] > 0 ? 0 : 1;

            return ($aIncomplete <=> $bIncomplete)
                ?: ($a['persen'] <=> $b['persen'])
                ?: ($b['belum_absen'] <=> $a['belum_absen'])
                ?: strcmp($a['nama'], $b['nama']);
        });

        return ['items' => $items, 'summary' => $summary];
    }

    protected function buildProgressPerRayon(string $tanggal, Collection $siswaRows, array $pembimbingMap): array
    {
        if ($siswaRows->isEmpty()) {
            return [
                'items' => [],
                'summary' => ['total' => 0, 'lengkap' => 0, 'parsial' => 0, 'belum' => 0],
            ];
        }

        $siswaIds = $siswaRows->pluck('id')->all();
        $recordedSiswaIds = AbsensiHarian::where('tanggal', $tanggal)
            ->whereIn('siswa_id', $siswaIds)
            ->pluck('siswa_id')
            ->flip()
            ->all();

        $grouped = $siswaRows->filter(fn ($s) => ! empty($s->rayon_id))->groupBy('rayon_id');
        $rayonIds = $grouped->keys()->all();
        $rayons = Rayon::whereIn('id', $rayonIds)
            ->orderBy('nama')
            ->get(['id', 'nama', 'pembimbing_id'])
            ->keyBy('id');

        $tglLabel = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $items = [];
        $summary = ['total' => 0, 'lengkap' => 0, 'parsial' => 0, 'belum' => 0];

        foreach ($rayons as $rayonId => $rayon) {
            $students = $grouped->get($rayonId, collect());
            $total = $students->count();
            $sudah = $students->filter(fn ($s) => isset($recordedSiswaIds[$s->id]))->count();
            $belum = max(0, $total - $sudah);
            $persen = $total > 0 ? round(($sudah / $total) * 100, 1) : 0.0;

            $summary['total']++;
            if ($belum === 0) {
                $summary['lengkap']++;
                $state = 'lengkap';
                $statusText = 'Lengkap (100%)';
            } elseif ($sudah === 0) {
                $summary['belum']++;
                $state = 'belum';
                $statusText = "Belum diabsen ({$belum} siswa)";
            } else {
                $summary['parsial']++;
                $state = 'parsial';
                $statusText = "{$belum} siswa belum diabsen";
            }

            $pembimbing = $pembimbingMap[$rayonId] ?? ['nama' => '-', 'no_hp' => null];
            $waNum = $this->formatWaNumber($pembimbing['no_hp'] ?? null);
            $waReminderUrl = null;
            if ($waNum && $belum > 0) {
                $msg = "Assalamu'alaikum Bapak/Ibu {$pembimbing['nama']}, mohon bantuan untuk melengkapi absensi harian rayon {$rayon->nama} tanggal {$tglLabel}. Saat ini masih ada {$belum} dari {$total} siswa yang belum tercatat. Terima kasih.";
                $waReminderUrl = 'https://wa.me/'.$waNum.'?text='.rawurlencode($msg);
            }

            $items[] = [
                'id' => $rayonId,
                'nama' => $rayon->nama,
                'total_siswa' => $total,
                'sudah_absen' => $sudah,
                'belum_absen' => $belum,
                'persen' => $persen,
                'state' => $state,
                'status_text' => $statusText,
                'pj_nama' => $pembimbing['nama'] ?? '-',
                'pj_wa_url' => $waReminderUrl,
                'input_url' => route('absensi.harian', ['tanggal' => $tanggal, 'rayon_id' => $rayonId]),
            ];
        }

        usort($items, function ($a, $b) {
            $aIncomplete = $a['belum_absen'] > 0 ? 0 : 1;
            $bIncomplete = $b['belum_absen'] > 0 ? 0 : 1;

            return ($aIncomplete <=> $bIncomplete)
                ?: ($a['persen'] <=> $b['persen'])
                ?: ($b['belum_absen'] <=> $a['belum_absen'])
                ?: strcmp($a['nama'], $b['nama']);
        });

        return ['items' => $items, 'summary' => $summary];
    }

    protected function buildAbsenceAndDispenTables(
        string $tanggal,
        array $siswaIds,
        array $waliMap,
        array $pembimbingMap
    ): array {
        $result = ['alpa' => [], 'izin' => [], 'sakit' => [], 'dispen' => []];

        if (empty($siswaIds)) {
            return $result;
        }

        $records = AbsensiHarian::where('tanggal', $tanggal)
            ->whereIn('siswa_id', $siswaIds)
            ->where('status', '!=', StatusAbsensi::HADIR->value)
            ->with([
                'siswa:id,nama,nipd,nisn,jk,hp,telepon,rombel_id,rayon_id',
                'siswa.rombel:id,nama,wali_kelas_id',
                'siswa.rayon:id,nama,pembimbing_id',
                'siswa.orangTuas:id,siswa_id,hubungan,nama,no_hp',
            ])
            ->get()
            ->sortBy(fn ($r) => $r->siswa?->nama ?? '')
            ->values();

        foreach ($records as $rec) {
            $siswa = $rec->siswa;
            if (! $siswa) {
                continue;
            }

            $enum = $rec->status instanceof StatusAbsensi ? $rec->status : StatusAbsensi::tryFrom((string) $rec->status);
            if (! $enum || $enum === StatusAbsensi::HADIR) {
                continue;
            }

            $row = $this->formatStudentWithContacts(
                $siswa,
                $enum,
                $tanggal,
                $rec->keterangan,
                $waliMap,
                $pembimbingMap
            );

            match ($enum) {
                StatusAbsensi::ALPA => $result['alpa'][] = $row,
                StatusAbsensi::IZIN => $result['izin'][] = $row,
                StatusAbsensi::SAKIT => $result['sakit'][] = $row,
                StatusAbsensi::DISPEN_CBT_PLH,
                StatusAbsensi::DISPEN_CBT_KWH,
                StatusAbsensi::DISPEN_LAINNYA => $result['dispen'][] = $row,
                default => null,
            };
        }

        return $result;
    }

    protected function buildEarlyWarning(
        string $tanggal,
        ?Semester $semester,
        array $siswaIds,
        array $waliMap,
        array $pembimbingMap
    ): array {
        if (empty($siswaIds)) {
            return ['beruntun' => [], 'sp' => []];
        }

        // 1. Alpa / Sakit beruntun >= 2 hari sekolah terakhir yang tercatat hingga tanggal fokus
        $recentDates = AbsensiHarian::where('tanggal', '<=', $tanggal)
            ->whereIn('siswa_id', $siswaIds)
            ->distinct()
            ->orderByDesc('tanggal')
            ->limit(10)
            ->pluck('tanggal')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->values()
            ->all();

        $streakMap = [];
        if (count($recentDates) >= 2) {
            $recentRecords = AbsensiHarian::whereIn('tanggal', $recentDates)
                ->whereIn('siswa_id', $siswaIds)
                ->whereIn('status', [StatusAbsensi::ALPA->value, StatusAbsensi::SAKIT->value])
                ->get(['tanggal', 'siswa_id', 'status', 'keterangan'])
                ->groupBy('siswa_id');

            foreach ($recentRecords as $sId => $recs) {
                $byDate = $recs->keyBy(fn ($r) => Carbon::parse($r->tanggal)->toDateString());
                $latestDate = $recentDates[0];
                if (! $byDate->has($latestDate)) {
                    continue;
                }

                $firstRec = $byDate->get($latestDate);
                $firstStatus = $firstRec->status instanceof StatusAbsensi ? $firstRec->status : StatusAbsensi::tryFrom((string) $firstRec->status);
                if (! $firstStatus) {
                    continue;
                }

                $streak = 0;
                $datesInStreak = [];
                foreach ($recentDates as $d) {
                    if (! $byDate->has($d)) {
                        break;
                    }
                    $st = $byDate->get($d)->status;
                    $stEnum = $st instanceof StatusAbsensi ? $st : StatusAbsensi::tryFrom((string) $st);
                    if ($stEnum !== $firstStatus) {
                        break;
                    }
                    $streak++;
                    $datesInStreak[] = $d;
                }

                if ($streak >= 2) {
                    $streakMap[(int) $sId] = [
                        'streak' => $streak,
                        'status' => $firstStatus,
                        'keterangan' => $firstRec->keterangan,
                        'since' => end($datesInStreak),
                    ];
                }
            }
        }

        // 2. Akumulasi Alpa tinggi semester aktif (>= 3 SP1, >= 6 SP2, >= 9 SP3)
        $spRaw = collect();
        if ($semester) {
            $spRaw = AbsensiHarian::where('semester_id', $semester->id)
                ->whereIn('siswa_id', $siswaIds)
                ->where('status', StatusAbsensi::ALPA->value)
                ->select('siswa_id', DB::raw('COUNT(*) as total_alpa'))
                ->groupBy('siswa_id')
                ->having('total_alpa', '>=', 3)
                ->orderByDesc('total_alpa')
                ->limit(15)
                ->get();
        }

        $warningSiswaIds = array_values(array_unique(array_merge(
            array_keys($streakMap),
            $spRaw->pluck('siswa_id')->map(fn ($id) => (int) $id)->all()
        )));

        $siswas = empty($warningSiswaIds)
            ? collect()
            : Siswa::whereIn('id', $warningSiswaIds)
                ->with([
                    'rombel:id,nama,wali_kelas_id',
                    'rayon:id,nama,pembimbing_id',
                    'orangTuas:id,siswa_id,hubungan,nama,no_hp',
                ])
                ->get(['id', 'nama', 'nipd', 'nisn', 'jk', 'hp', 'telepon', 'rombel_id', 'rayon_id'])
                ->keyBy('id');

        $beruntun = [];
        foreach ($streakMap as $sId => $info) {
            $siswa = $siswas->get($sId);
            if (! $siswa) {
                continue;
            }
            $row = $this->formatStudentWithContacts(
                $siswa,
                $info['status'],
                $tanggal,
                $info['keterangan'],
                $waliMap,
                $pembimbingMap,
                "tercatat {$info['status']->fullLabel()} selama {$info['streak']} hari sekolah berturut-turut"
            );
            $row['streak'] = $info['streak'];
            $row['since_label'] = Carbon::parse($info['since'])->translatedFormat('d M');
            $beruntun[] = $row;
        }
        usort($beruntun, fn ($a, $b) => ($b['streak'] <=> $a['streak']) ?: strcmp($a['nama'], $b['nama']));

        $sp = [];
        foreach ($spRaw as $r) {
            $siswa = $siswas->get((int) $r->siswa_id);
            if (! $siswa) {
                continue;
            }
            $totalAlpa = (int) $r->total_alpa;
            $spLevel = $totalAlpa >= 9 ? 'SP 3' : ($totalAlpa >= 6 ? 'SP 2' : 'SP 1');
            $row = $this->formatStudentWithContacts(
                $siswa,
                StatusAbsensi::ALPA,
                $tanggal,
                null,
                $waliMap,
                $pembimbingMap,
                "telah mencapai {$totalAlpa} kali Alpa pada semester ini ({$spLevel})"
            );
            $row['total_alpa'] = $totalAlpa;
            $row['sp_level'] = $spLevel;
            $sp[] = $row;
        }

        return [
            'beruntun' => array_slice($beruntun, 0, 15),
            'sp' => $sp,
        ];
    }

    protected function formatStudentWithContacts(
        Siswa $siswa,
        StatusAbsensi $status,
        string $tanggal,
        ?string $keterangan,
        array $waliMap,
        array $pembimbingMap,
        ?string $customReason = null
    ): array {
        $rombelNama = $siswa->rombel?->nama ?? '-';
        $rayonNama = $siswa->rayon?->nama ?? '-';
        $pembimbing = $siswa->rayon_id ? ($pembimbingMap[$siswa->rayon_id] ?? ['nama' => '-', 'no_hp' => null]) : ['nama' => '-', 'no_hp' => null];
        $waliKelas = $siswa->rombel_id ? ($waliMap[$siswa->rombel_id] ?? ['nama' => '-', 'no_hp' => null]) : ['nama' => '-', 'no_hp' => null];

        $orangTuas = $siswa->orangTuas;
        $ayah = $orangTuas->firstWhere('hubungan', 'ayah');
        $ibu = $orangTuas->firstWhere('hubungan', 'ibu');
        $wali = $orangTuas->firstWhere('hubungan', 'wali');

        $tglLabel = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $reasonText = $customReason ?: ("tercatat {$status->fullLabel()} pada tanggal {$tglLabel}".($keterangan ? " ({$keterangan})" : ''));

        $makeContact = function (string $role, ?string $nama, ?string $rawPhone, string $message): array {
            $cleanWa = $this->formatWaNumber($rawPhone);

            return [
                'role' => $role,
                'nama' => $nama && trim($nama) !== '' ? trim($nama) : '-',
                'no_hp' => $rawPhone && trim($rawPhone) !== '' ? trim($rawPhone) : null,
                'wa_url' => $cleanWa ? 'https://wa.me/'.$cleanWa.'?text='.rawurlencode($message) : null,
            ];
        };

        $msgSiswa = "Assalamu'alaikum {$siswa->nama}, dari pihak sekolah mengonfirmasi bahwa kamu {$reasonText}. Mohon konfirmasi kabarnya ya.";
        $msgOrtu = fn (string $sapaan, ?string $nama) => "Assalamu'alaikum Bapak/Ibu ".($nama ?: $sapaan).", kami dari pihak sekolah menginformasikan bahwa ananda {$siswa->nama} ({$rombelNama}) {$reasonText}. Mohon informasi dan konfirmasinya. Terima kasih.";
        $msgStaff = fn (string $jabatan, ?string $nama) => "Assalamu'alaikum Bapak/Ibu ".($nama ?: $jabatan).", izin berkoordinasi terkait siswa binaan {$siswa->nama} ({$rombelNama} / {$rayonNama}) yang {$reasonText}. Terima kasih.";

        $contacts = [
            $makeContact('Siswa', $siswa->nama, $siswa->hp ?: $siswa->telepon, $msgSiswa),
            $makeContact('Ayah', $ayah?->nama, $ayah?->no_hp, $msgOrtu('Ayah', $ayah?->nama)),
            $makeContact('Ibu', $ibu?->nama, $ibu?->no_hp, $msgOrtu('Ibu', $ibu?->nama)),
            $makeContact('Wali', $wali?->nama, $wali?->no_hp, $msgOrtu('Wali', $wali?->nama)),
            $makeContact('Pembimbing Rayon', $pembimbing['nama'], $pembimbing['no_hp'], $msgStaff('Pembimbing Rayon', $pembimbing['nama'])),
            $makeContact('Wali Kelas', $waliKelas['nama'], $waliKelas['no_hp'], $msgStaff('Wali Kelas', $waliKelas['nama'])),
        ];

        $availableContactsCount = count(array_filter($contacts, fn ($c) => ! empty($c['wa_url'])));

        return [
            'siswa_id' => $siswa->id,
            'nama' => $siswa->nama,
            'nipd' => $siswa->nipd ?: ($siswa->nisn ?: '-'),
            'rombel_nama' => $rombelNama,
            'rayon_nama' => $rayonNama,
            'pembimbing_rayon_nama' => $pembimbing['nama'] ?? '-',
            'wali_kelas_nama' => $waliKelas['nama'] ?? '-',
            'status_value' => $status->value,
            'status_label' => $status->label(),
            'status_full_label' => $status->fullLabel(),
            'keterangan' => $keterangan ?: '',
            'contacts' => $contacts,
            'available_contacts_count' => $availableContactsCount,
        ];
    }

    /**
     * Memuat peta Wali Kelas (per rombel_id) dan Pembimbing Rayon (per rayon_id) tanpa N+1 query.
     *
     * @return array{0: array<int, array{nama: string, no_hp: ?string}>, 1: array<int, array{nama: string, no_hp: ?string}>}
     */
    protected function loadStaffMaps(?int $tahunAjaranId): array
    {
        $waliMap = [];
        $pembimbingMap = [];

        // 1. Dari tabel Rombel & Rayon langsung (fallback)
        $rombels = Rombel::with('waliKelas.pegawai:id,user_id,nama_lengkap,gelar_depan,gelar_belakang,no_hp')
            ->get(['id', 'wali_kelas_id']);

        foreach ($rombels as $r) {
            if ($r->waliKelas) {
                $peg = $r->waliKelas->pegawai;
                $waliMap[$r->id] = [
                    'nama' => $peg?->nama_gelar ?: $r->waliKelas->name,
                    'no_hp' => $peg?->no_hp,
                ];
            }
        }

        $rayons = Rayon::with('pembimbing.pegawai:id,user_id,nama_lengkap,gelar_depan,gelar_belakang,no_hp')
            ->get(['id', 'pembimbing_id']);

        foreach ($rayons as $ry) {
            if ($ry->pembimbing) {
                $peg = $ry->pembimbing->pegawai;
                $pembimbingMap[$ry->id] = [
                    'nama' => $peg?->nama_gelar ?: $ry->pembimbing->name,
                    'no_hp' => $peg?->no_hp,
                ];
            }
        }

        // 2. Prioritaskan penugasan resmi tahun ajaran aktif dari modul Kepegawaian
        $penugasanWali = PenugasanWaliKelas::with('pegawai:id,nama_lengkap,gelar_depan,gelar_belakang,no_hp')
            ->when($tahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->get(['rombel_id', 'pegawai_id']);

        foreach ($penugasanWali as $pw) {
            if ($pw->pegawai) {
                $waliMap[$pw->rombel_id] = [
                    'nama' => $pw->pegawai->nama_gelar,
                    'no_hp' => $pw->pegawai->no_hp,
                ];
            }
        }

        $penugasanRayon = PenugasanPembimbingRayon::with('pegawai:id,nama_lengkap,gelar_depan,gelar_belakang,no_hp')
            ->when($tahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->get(['rayon_id', 'pegawai_id']);

        foreach ($penugasanRayon as $pr) {
            if ($pr->pegawai) {
                $pembimbingMap[$pr->rayon_id] = [
                    'nama' => $pr->pegawai->nama_gelar,
                    'no_hp' => $pr->pegawai->no_hp,
                ];
            }
        }

        return [$waliMap, $pembimbingMap];
    }

    protected function buildWhatsappSummary(string $tanggal, array $kpi, array $progressRombel): string
    {
        $sekolah = rescue(fn () => School::current()?->nama, 'Sekolah');
        $tglLabel = Carbon::parse($tanggal)->translatedFormat('l, d F Y');

        $lines = [
            "*LAPORAN ABSENSI HARIAN*",
            $sekolah,
            "Tanggal: {$tglLabel}",
            '',
            "Total Siswa: *{$kpi['total_siswa']}*",
            "Sudah Diabsen: *{$kpi['sudah_absen']}* ({$kpi['persen_input']}%)",
            "Belum Diabsen: *{$kpi['belum_absen']}*",
            '',
            "• H (Hadir): *{$kpi['hadir']}*",
            "• Dispen (PLH/KWH/Lain): *{$kpi['dispen_total']}*",
            "• S (Sakit): *{$kpi['sakit']}*",
            "• I (Izin): *{$kpi['izin']}*",
            "• A (Alpa): *{$kpi['alpa']}*",
            '',
            "Persentase Kehadiran: *{$kpi['persen_hadir']}%*",
        ];

        $incomplete = array_filter($progressRombel['items'], fn ($item) => $item['belum_absen'] > 0);
        if (! empty($incomplete)) {
            $lines[] = '';
            $lines[] = '*Rombel Belum Tuntas Diabsen:*';
            foreach (array_slice($incomplete, 0, 10) as $item) {
                $lines[] = "- {$item['nama']}: {$item['belum_absen']} siswa belum diabsen ({$item['persen']}%)";
            }
            if (count($incomplete) > 10) {
                $lines[] = '- dan '.(count($incomplete) - 10).' rombel lainnya.';
            }
        } else {
            $lines[] = '';
            $lines[] = '✅ Seluruh rombel sudah 100% diabsen.';
        }

        return implode("\n", $lines);
    }

    protected function buildWhatsappByRayon(string $tanggal, array $ketidakhadiran): string
    {
        $sekolah = rescue(fn () => School::current()?->nama, 'Sekolah');
        $tglLabel = Carbon::parse($tanggal)->translatedFormat('l, d F Y');

        $absentAll = array_merge(
            $ketidakhadiran['alpa'],
            $ketidakhadiran['izin'],
            $ketidakhadiran['sakit']
        );

        $lines = [
            '*REKAP KETIDAKHADIRAN PER RAYON*',
            $sekolah,
            "Tanggal: {$tglLabel}",
            'Total Tidak Hadir: *'.count($absentAll).'* (A: '.count($ketidakhadiran['alpa']).', I: '.count($ketidakhadiran['izin']).', S: '.count($ketidakhadiran['sakit']).')',
        ];

        if (empty($absentAll)) {
            $lines[] = '';
            $lines[] = '✅ Tidak ada siswa yang tidak hadir (Alpa/Izin/Sakit) pada tanggal ini.';

            return implode("\n", $lines);
        }

        $grouped = [];
        foreach ($absentAll as $item) {
            $key = $item['rayon_nama'] ?: 'Tanpa Rayon';
            $grouped[$key]['pembimbing'] = $item['pembimbing_rayon_nama'] ?? '-';
            $grouped[$key]['items'][] = $item;
        }
        ksort($grouped);

        foreach ($grouped as $rayonNama => $group) {
            $lines[] = '';
            $pembimbingText = ($group['pembimbing'] && $group['pembimbing'] !== '-') ? " (Pembimbing: {$group['pembimbing']})" : '';
            $lines[] = "*{$rayonNama}*{$pembimbingText}";

            $items = $group['items'];
            usort($items, fn ($a, $b) => strcmp($a['nama'], $b['nama']));

            foreach ($items as $idx => $s) {
                $no = $idx + 1;
                $ket = $s['keterangan'] !== '' ? " - {$s['keterangan']}" : '';
                $lines[] = "{$no}. {$s['nama']} ({$s['rombel_nama']}) [{$s['status_full_label']}]{$ket}";
            }
        }

        return implode("\n", $lines);
    }

    protected function buildWhatsappByRombel(string $tanggal, array $ketidakhadiran): string
    {
        $sekolah = rescue(fn () => School::current()?->nama, 'Sekolah');
        $tglLabel = Carbon::parse($tanggal)->translatedFormat('l, d F Y');

        $absentAll = array_merge(
            $ketidakhadiran['alpa'],
            $ketidakhadiran['izin'],
            $ketidakhadiran['sakit']
        );

        $lines = [
            '*REKAP KETIDAKHADIRAN PER ROMBEL*',
            $sekolah,
            "Tanggal: {$tglLabel}",
            'Total Tidak Hadir: *'.count($absentAll).'* (A: '.count($ketidakhadiran['alpa']).', I: '.count($ketidakhadiran['izin']).', S: '.count($ketidakhadiran['sakit']).')',
        ];

        if (empty($absentAll)) {
            $lines[] = '';
            $lines[] = '✅ Tidak ada siswa yang tidak hadir (Alpa/Izin/Sakit) pada tanggal ini.';

            return implode("\n", $lines);
        }

        $grouped = [];
        foreach ($absentAll as $item) {
            $key = $item['rombel_nama'] ?: 'Tanpa Rombel';
            $grouped[$key]['wali_kelas'] = $item['wali_kelas_nama'] ?? '-';
            $grouped[$key]['items'][] = $item;
        }
        ksort($grouped);

        foreach ($grouped as $rombelNama => $group) {
            $lines[] = '';
            $waliText = ($group['wali_kelas'] && $group['wali_kelas'] !== '-') ? " (Wali Kelas: {$group['wali_kelas']})" : '';
            $lines[] = "*{$rombelNama}*{$waliText}";

            $items = $group['items'];
            usort($items, fn ($a, $b) => strcmp($a['nama'], $b['nama']));

            foreach ($items as $idx => $s) {
                $no = $idx + 1;
                $ket = $s['keterangan'] !== '' ? " - {$s['keterangan']}" : '';
                $lines[] = "{$no}. {$s['nama']} ({$s['rayon_nama']}) [{$s['status_full_label']}]{$ket}";
            }
        }

        return implode("\n", $lines);
    }

    protected function normalizeDate(mixed $value): string
    {
        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value)->toDateString();
            } catch (\Throwable) {
            }
        }

        return now()->toDateString();
    }
}
