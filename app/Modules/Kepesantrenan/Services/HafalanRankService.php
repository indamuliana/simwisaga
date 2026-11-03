<?php

namespace App\Modules\Kepesantrenan\Services;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kepesantrenan\Models\PesantrenHafalanSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenKamar;
use App\Modules\Kepesantrenan\Models\QuranSurah;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HafalanRankService
{
    public const AYAT_PER_JUZ = 207.87; // 6236 ayat / 30 juz

    public const TARGET_SEKOLAH_JUZ = 3.0;

    /**
     * Konversi jumlah juz ke tier Mobile Legends (MLBB).
     */
    public static function getRankFromJuz(float $juz): array
    {
        if ($juz >= 7.0) {
            return [
                'tier' => 'Mythical Immortal',
                'roman' => '7+ Juz',
                'name' => 'Mythical Immortal',
                'bg_gradient' => 'from-purple-900 via-indigo-900 to-rose-950',
                'gradient_style' => 'linear-gradient(135deg, #3b0764 0%, #4c0519 50%, #0f172a 100%)',
                'badge_color' => 'text-amber-300 border-amber-400 bg-amber-950/70',
                'badge_style' => 'background: #3b0764; color: #fde047; border: 1px solid #ca8a04;',
                'icon' => 'crown-immortal',
                'color' => '#f59e0b',
            ];
        }

        if ($juz >= 5.0) {
            return [
                'tier' => 'Mythical Glory',
                'roman' => '5-6.9 Juz',
                'name' => 'Mythical Glory',
                'bg_gradient' => 'from-red-900 via-rose-800 to-amber-900',
                'gradient_style' => 'linear-gradient(135deg, #881337 0%, #be123c 50%, #0f172a 100%)',
                'badge_color' => 'text-rose-300 border-rose-500 bg-rose-950/70',
                'badge_style' => 'background: #881337; color: #fda4af; border: 1px solid #f43f5e;',
                'icon' => 'crown-glory',
                'color' => '#fb7185',
            ];
        }

        if ($juz >= 4.0) {
            return [
                'tier' => 'Mythical Honor',
                'roman' => '4-4.9 Juz',
                'name' => 'Mythical Honor',
                'bg_gradient' => 'from-amber-800 via-orange-700 to-yellow-800',
                'gradient_style' => 'linear-gradient(135deg, #7c2d12 0%, #c2410c 50%, #0f172a 100%)',
                'badge_color' => 'text-amber-200 border-amber-400 bg-amber-900/60',
                'badge_style' => 'background: #7c2d12; color: #fed7aa; border: 1px solid #f97316;',
                'icon' => 'shield-honor',
                'color' => '#fbbf24',
            ];
        }

        if ($juz >= 3.0) {
            return [
                'tier' => 'Mythic',
                'roman' => 'Standar 3 Juz',
                'name' => 'Mythic',
                'bg_gradient' => 'from-emerald-800 via-teal-700 to-cyan-900',
                'gradient_style' => 'linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f172a 100%)',
                'badge_color' => 'text-emerald-300 border-emerald-400 bg-emerald-950/70',
                'badge_style' => 'background: #064e3b; color: #6ee7b7; border: 1px solid #10b981;',
                'icon' => 'shield-mythic',
                'color' => '#34d399',
            ];
        }

        if ($juz >= 2.5) {
            return [
                'tier' => 'Legend',
                'roman' => '2.5-2.9 Juz',
                'name' => 'Legend',
                'bg_gradient' => 'from-fuchsia-900 via-purple-800 to-indigo-950',
                'gradient_style' => 'linear-gradient(135deg, #4c0519 0%, #831843 50%, #3b0764 100%)',
                'badge_color' => 'text-fuchsia-300 border-fuchsia-400 bg-fuchsia-950/60',
                'badge_style' => 'background: #500724; color: #f472b6; border: 1px solid #ec4899;',
                'icon' => 'shield-legend',
                'color' => '#f472b6',
            ];
        }

        if ($juz >= 2.0) {
            return [
                'tier' => 'Epic',
                'roman' => '2.0-2.4 Juz',
                'name' => 'Epic',
                'bg_gradient' => 'from-teal-900 via-emerald-800 to-cyan-950',
                'gradient_style' => 'linear-gradient(135deg, #042f2e 0%, #0f766e 50%, #0f172a 100%)',
                'badge_color' => 'text-teal-300 border-teal-400 bg-teal-950/60',
                'badge_style' => 'background: #042f2e; color: #5eead4; border: 1px solid #14b8a6;',
                'icon' => 'shield-epic',
                'color' => '#2dd4bf',
            ];
        }

        if ($juz >= 1.5) {
            return [
                'tier' => 'Grandmaster',
                'roman' => '1.5-1.9 Juz',
                'name' => 'Grandmaster',
                'bg_gradient' => 'from-sky-900 via-blue-800 to-slate-900',
                'gradient_style' => 'linear-gradient(135deg, #082f49 0%, #0369a1 50%, #0f172a 100%)',
                'badge_color' => 'text-sky-300 border-sky-400 bg-sky-950/60',
                'badge_style' => 'background: #082f49; color: #7dd3fc; border: 1px solid #0284c7;',
                'icon' => 'shield-gm',
                'color' => '#38bdf8',
            ];
        }

        if ($juz >= 1.0) {
            return [
                'tier' => 'Master',
                'roman' => '1.0-1.4 Juz',
                'name' => 'Master',
                'bg_gradient' => 'from-amber-950 via-yellow-900 to-stone-900',
                'gradient_style' => 'linear-gradient(135deg, #422006 0%, #713f12 50%, #0f172a 100%)',
                'badge_color' => 'text-yellow-400 border-yellow-500 bg-yellow-950/60',
                'badge_style' => 'background: #451a03; color: #fde047; border: 1px solid #eab308;',
                'icon' => 'shield-master',
                'color' => '#fde047',
            ];
        }

        if ($juz >= 0.5) {
            return [
                'tier' => 'Elite',
                'roman' => '0.5-0.9 Juz',
                'name' => 'Elite',
                'bg_gradient' => 'from-slate-800 via-zinc-700 to-stone-800',
                'gradient_style' => 'linear-gradient(135deg, #1e293b 0%, #334155 50%, #0f172a 100%)',
                'badge_color' => 'text-slate-200 border-slate-400 bg-slate-800/80',
                'badge_style' => 'background: #1e293b; color: #e2e8f0; border: 1px solid #64748b;',
                'icon' => 'shield-elite',
                'color' => '#cbd5e1',
            ];
        }

        return [
            'tier' => 'Warrior',
            'roman' => '< 0.5 Juz',
            'name' => 'Warrior',
            'bg_gradient' => 'from-stone-800 via-orange-950 to-stone-900',
            'gradient_style' => 'linear-gradient(135deg, #292524 0%, #572007 50%, #0f172a 100%)',
            'badge_color' => 'text-amber-500 border-amber-700 bg-stone-900/80',
            'badge_style' => 'background: #292524; color: #fbbf24; border: 1px solid #d97706;',
            'icon' => 'shield-warrior',
            'color' => '#fbbf24',
        ];
    }

    /**
     * Hitung ringkasan hafalan satu orang santri.
     */
    public function getSiswaStats(int $siswaId): array
    {
        $totalAyat = PesantrenHafalanSiswa::where('siswa_id', $siswaId)->count();
        $juz = round($totalAyat / self::AYAT_PER_JUZ, 2);
        $rank = self::getRankFromJuz($juz);

        $surahIds = PesantrenHafalanSiswa::where('siswa_id', $siswaId)
            ->distinct()
            ->pluck('surah_id');

        $suratTuntasCount = 0;
        if ($surahIds->isNotEmpty()) {
            $surahs = QuranSurah::whereIn('nomor_surah', $surahIds)->get();
            foreach ($surahs as $s) {
                $ayatMemorized = PesantrenHafalanSiswa::where('siswa_id', $siswaId)
                    ->where('surah_id', $s->nomor_surah)
                    ->count();
                if ($ayatMemorized >= $s->jumlah_ayat) {
                    $suratTuntasCount++;
                }
            }
        }

        $persenTarget = min(100, round(($juz / self::TARGET_SEKOLAH_JUZ) * 100, 1));

        return [
            'total_ayat' => $totalAyat,
            'juz' => $juz,
            'surat_berjalan_count' => $surahIds->count(),
            'surat_tuntas_count' => $suratTuntasCount,
            'target_persen' => $persenTarget,
            'is_target_achieved' => $juz >= self::TARGET_SEKOLAH_JUZ,
            'rank' => $rank,
        ];
    }

    /**
     * Leaderboard santri unggulan.
     */
    public function getLeaderboard(?int $kamarId = null, ?int $rayonId = null, ?string $search = null, int $limit = 100): Collection
    {
        $query = Siswa::whereHas('program', function ($q) {
            $q->where('kode', 'UNG');
        })->where('is_active', true);

        if ($kamarId) {
            $query->whereHas('pesantrenKamars', fn ($q) => $q->where('pesantren_kamars.id', $kamarId));
        }

        if ($rayonId) {
            $query->where('rayon_id', $rayonId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nipd', 'like', "%{$search}%");
            });
        }

        $siswas = $query->with([
            'rombel',
            'rayon',
            'pesantrenKamars',
            'pesantrenHalaqohs',
        ])->get();

        // Hitung total ayat masing-masing santri sekaligus
        $ayatCounts = PesantrenHafalanSiswa::whereIn('siswa_id', $siswas->pluck('id'))
            ->select('siswa_id', DB::raw('count(*) as total'))
            ->groupBy('siswa_id')
            ->pluck('total', 'siswa_id');

        $result = $siswas->map(function ($s) use ($ayatCounts) {
            $ayat = $ayatCounts[$s->id] ?? 0;
            $juz = round($ayat / self::AYAT_PER_JUZ, 2);
            $rank = self::getRankFromJuz($juz);
            $persen = min(100, round(($juz / self::TARGET_SEKOLAH_JUZ) * 100, 1));

            return (object) [
                'siswa' => $s,
                'total_ayat' => $ayat,
                'juz' => $juz,
                'rank' => $rank,
                'persen_target' => $persen,
                'kamar' => $s->pesantrenKamars->first()?->nama_kamar ?? '-',
                'halaqoh' => $s->pesantrenHalaqohs->first()?->nama_halaqoh ?? '-',
            ];
        })->sortByDesc('total_ayat')->values();

        // Tambahkan nomor urut peringkat (ranking)
        $ranked = $result->map(function ($item, $index) {
            $item->ranking = $index + 1;

            return $item;
        });

        return $ranked->take($limit);
    }

    /**
     * Rekap statistik keseluruhan & per kelompok (Kamar & Rayon).
     */
    public function getRekapLaporan(): array
    {
        $siswasUnggulan = Siswa::whereHas('program', fn ($q) => $q->where('kode', 'UNG'))
            ->where('is_active', true)
            ->pluck('id');

        $totalSantri = $siswasUnggulan->count();

        $ayatCounts = PesantrenHafalanSiswa::whereIn('siswa_id', $siswasUnggulan)
            ->select('siswa_id', DB::raw('count(*) as total'))
            ->groupBy('siswa_id')
            ->pluck('total', 'siswa_id');

        $totalAyatAll = $ayatCounts->sum();
        $totalJuzAll = round($totalAyatAll / self::AYAT_PER_JUZ, 1);
        $mencapaiMythic = $ayatCounts->filter(fn ($c) => ($c / self::AYAT_PER_JUZ) >= self::TARGET_SEKOLAH_JUZ)->count();

        // Rekap per Kamar
        $kamars = PesantrenKamar::with(['musrif', 'siswas' => function ($q) {
            $q->where('is_active', true);
        }])->get()->map(function ($k) use ($ayatCounts) {
            $siswaIds = $k->siswas->pluck('id');
            $kamarAyat = $siswaIds->map(fn ($id) => $ayatCounts[$id] ?? 0)->sum();
            $count = $siswaIds->count();
            $avgJuz = $count > 0 ? round(($kamarAyat / $count) / self::AYAT_PER_JUZ, 2) : 0;
            $tuntas = $siswaIds->filter(fn ($id) => (($ayatCounts[$id] ?? 0) / self::AYAT_PER_JUZ) >= self::TARGET_SEKOLAH_JUZ)->count();

            return [
                'nama' => $k->nama_kamar,
                'gedung' => $k->gedung ?? '-',
                'gender' => $k->jenis_kelamin === 'L' ? 'Putra' : 'Putri',
                'musrif' => $k->musrif?->nama_lengkap ?? 'Belum ditentukan',
                'jumlah_santri' => $count,
                'total_ayat' => $kamarAyat,
                'rata_juz' => $avgJuz,
                'santri_tuntas' => $tuntas,
            ];
        });

        // Rekap per Rayon (hanya santri unggulan)
        $rayons = Rayon::with(['siswas' => function ($q) {
            $q->whereHas('program', fn ($p) => $p->where('kode', 'UNG'))->where('is_active', true);
        }])->get()->filter(fn ($r) => $r->siswas->isNotEmpty())->map(function ($r) use ($ayatCounts) {
            $siswaIds = $r->siswas->pluck('id');
            $rayonAyat = $siswaIds->map(fn ($id) => $ayatCounts[$id] ?? 0)->sum();
            $count = $siswaIds->count();
            $avgJuz = $count > 0 ? round(($rayonAyat / $count) / self::AYAT_PER_JUZ, 2) : 0;
            $tuntas = $siswaIds->filter(fn ($id) => (($ayatCounts[$id] ?? 0) / self::AYAT_PER_JUZ) >= self::TARGET_SEKOLAH_JUZ)->count();

            return [
                'nama' => $r->nama,
                'jumlah_santri' => $count,
                'total_ayat' => $rayonAyat,
                'rata_juz' => $avgJuz,
                'santri_tuntas' => $tuntas,
            ];
        })->values();

        return [
            'summary' => [
                'total_santri' => $totalSantri,
                'total_ayat' => $totalAyatAll,
                'total_juz' => $totalJuzAll,
                'rata_juz' => $totalSantri > 0 ? round($totalJuzAll / $totalSantri, 2) : 0,
                'santri_mythic' => $mencapaiMythic,
                'persen_mythic' => $totalSantri > 0 ? round(($mencapaiMythic / $totalSantri) * 100, 1) : 0,
            ],
            'kamars' => $kamars,
            'rayons' => $rayons,
        ];
    }
}
