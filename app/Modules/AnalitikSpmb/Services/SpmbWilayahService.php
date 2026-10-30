<?php

namespace App\Modules\AnalitikSpmb\Services;

use App\Modules\Akademik\Models\Jurusan;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsalAlias;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpmbWilayahService
{
    /**
     * Daftar daerah yang hanya berbentuk Kabupaten (tidak ada entitas Kota)
     */
    public const KABUPATEN_ONLY = [
        'Garut', 'Cianjur', 'Ciamis', 'Subang', 'Indramayu', 'Karawang',
        'Kuningan', 'Majalengka', 'Pangandaran', 'Purwakarta', 'Sumedang',
    ];

    /**
     * Daftar daerah yang hanya berbentuk Kota (tidak ada entitas Kabupaten)
     */
    public const KOTA_ONLY = [
        'Cimahi', 'Depok', 'Banjar',
    ];

    /**
     * Daftar daerah yang memiliki dualitas (ada Kota dan Kabupaten berbeda)
     */
    public const DUALITY_REGIONS = [
        'Bogor' => ['Kota Bogor', 'Kab. Bogor'],
        'Bandung' => ['Kota Bandung', 'Kab. Bandung', 'Kab. Bandung Barat'],
        'Bekasi' => ['Kota Bekasi', 'Kab. Bekasi'],
        'Cirebon' => ['Kota Cirebon', 'Kab. Cirebon'],
        'Sukabumi' => ['Kota Sukabumi', 'Kab. Sukabumi'],
        'Tasikmalaya' => ['Kota Tasikmalaya', 'Kab. Tasikmalaya'],
    ];

    /**
     * Jalankan deteksi dan standarisasi cerdas data wilayah siswa dari database Master SMP & Sekolah Asal.
     *
     * @return array{total_scanned: int, updated_domestic: int, updated_international: int}
     */
    public function autoDetectWilayahFromMaster(): array
    {
        // 0. Normalisasikan terlebih dahulu master sekolah asal & siswa non-standar (misal 'Garut' -> 'Kab. Garut')
        $this->normalizeAllKabupatenKota();

        $siswas = Siswa::all();
        $totalScanned = $siswas->count();
        $updatedDomestic = 0;
        $updatedInternational = 0;

        // Cache Master Sekolah Asal lokal (nama_resmi & alias)
        $masterSekolahs = SpmbSekolahAsal::with('masterSmp')->get();
        $aliasMap = [];
        foreach ($masterSekolahs as $ms) {
            $aliasMap[strtolower(trim($ms->nama_resmi))] = $ms;
        }

        $allAliases = SpmbSekolahAsalAlias::with('sekolahAsal.masterSmp')->get();
        foreach ($allAliases as $al) {
            if ($al->sekolahAsal) {
                $aliasMap[strtolower(trim($al->alias_raw))] = $al->sekolahAsal;
            }
        }

        // Cache Master SMP berdasarkan kecamatan unik (misal: "Tarogong Kaler" -> Kab. Garut, Jawa Barat)
        $kecamatanMasterMap = SpmbMasterSmp::select('kecamatan', 'kabupaten', 'provinsi')
            ->groupBy('kecamatan', 'kabupaten', 'provinsi')
            ->get()
            ->groupBy(fn ($item) => strtolower(trim($item->kecamatan)));

        foreach ($siswas as $siswa) {
            $rawKecamatan = trim((string) $siswa->kecamatan);
            $rawAlamat = trim((string) $siswa->alamat);
            $rawKelurahan = trim((string) $siswa->kelurahan);
            $rawSekolah = trim((string) $siswa->sekolah_asal);

            $combinedLocation = strtolower("{$rawKecamatan} {$rawKelurahan} {$rawAlamat} {$rawSekolah}");
            $isInternational = false;

            // 1. Deteksi Luar Negeri (Sabah, Malaysia / CLC / Tawau / Sandakan / Kota Kinabalu)
            if (preg_match('/\b(tawau|sandakan|kota kinabalu|lahad datu|keningau|beaufort|clc|lc|sabah|malaysia)\b/i', $combinedLocation)) {
                $isInternational = true;
                $negara = 'Malaysia';
                $provinsi = 'Sabah';
                $kabKota = 'Tawau';

                if (str_contains($combinedLocation, 'sandakan')) {
                    $kabKota = 'Sandakan';
                } elseif (str_contains($combinedLocation, 'kota kinabalu')) {
                    $kabKota = 'Kota Kinabalu';
                } elseif (str_contains($combinedLocation, 'beaufort')) {
                    $kabKota = 'Beaufort';
                } elseif (str_contains($combinedLocation, 'lahad datu')) {
                    $kabKota = 'Lahad Datu';
                } elseif (str_contains($combinedLocation, 'keningau')) {
                    $kabKota = 'Keningau';
                }

                // Bersihkan penulisan kecamatan jika masih kotor
                $cleanKec = Str::title(preg_replace('/^kec\.?\s+/i', '', $rawKecamatan ?: $kabKota));

                $siswa->update([
                    'negara' => $negara,
                    'provinsi' => $provinsi,
                    'kabupaten_kota' => $kabKota,
                    'kecamatan' => $cleanKec,
                ]);

                $updatedInternational++;
                continue;
            }

            // 2. Deteksi Dalam Negeri
            $needsUpdate = false;
            $updates = [
                'negara' => $siswa->negara ?: 'Indonesia',
            ];

            // Normalisasi penulisan kecamatan lokal: hilangkan "Kec. "
            $cleanedKecamatan = $rawKecamatan;
            if (preg_match('/^kec\.?\s+(.*)$/i', $rawKecamatan, $m)) {
                $cleanedKecamatan = Str::title(trim($m[1]));
                $updates['kecamatan'] = $cleanedKecamatan;
                $needsUpdate = true;
            }

            // A. Cari dari Sekolah Asal yang sudah dipetakan
            $matchedMaster = $aliasMap[strtolower($rawSekolah)] ?? null;
            if ($matchedMaster) {
                $stdMasterKab = $this->standardizeKabupatenKotaName($matchedMaster->kabupaten_kota, $cleanedKecamatan, $rawSekolah);
                if (empty($siswa->kabupaten_kota) || $siswa->kabupaten_kota === 'Garut' || ($stdMasterKab && $siswa->kabupaten_kota !== $stdMasterKab)) {
                    if (! empty($stdMasterKab)) {
                        $updates['kabupaten_kota'] = $stdMasterKab;
                        $needsUpdate = true;
                    }
                }

                if (empty($siswa->provinsi)) {
                    $updates['provinsi'] = $matchedMaster->masterSmp?->provinsi ?: 'Jawa Barat';
                    $needsUpdate = true;
                }

                if (empty($cleanedKecamatan) && ! empty($matchedMaster->kecamatan)) {
                    $updates['kecamatan'] = $matchedMaster->kecamatan;
                    $cleanedKecamatan = $matchedMaster->kecamatan;
                    $needsUpdate = true;
                }
            }

            // B. Jika Kab/Kota atau Provinsi masih kosong, cari dari Master SMP via Kecamatan
            if ((empty($siswa->kabupaten_kota) || empty($siswa->provinsi)) && ! empty($cleanedKecamatan)) {
                $kecMatches = $kecamatanMasterMap->get(strtolower($cleanedKecamatan));
                if ($kecMatches && $kecMatches->count() === 1) {
                    $match = $kecMatches->first();
                    if (empty($siswa->kabupaten_kota)) {
                        $updates['kabupaten_kota'] = $match->kabupaten;
                        $needsUpdate = true;
                    }
                    if (empty($siswa->provinsi)) {
                        $updates['provinsi'] = $match->provinsi;
                        $needsUpdate = true;
                    }
                }
            }

            // C. Standarisasikan jika kabupaten_kota siswa masih mentah (misal 'Garut' atau 'Bogor' ambigu)
            $currentKab = $updates['kabupaten_kota'] ?? $siswa->kabupaten_kota;
            if (! empty($currentKab)) {
                $standardizedKab = $this->standardizeKabupatenKotaName($currentKab, $cleanedKecamatan, $rawSekolah);
                if ($standardizedKab && $standardizedKab !== $currentKab) {
                    $updates['kabupaten_kota'] = $standardizedKab;
                    $needsUpdate = true;
                }
            }

            // Fallback default untuk daerah Garut jika terbukti sekolah asalnya di Garut
            if (empty($siswa->kabupaten_kota) && (str_contains(strtolower($rawAlamat), 'garut') || str_contains(strtolower($rawSekolah), 'garut'))) {
                $updates['kabupaten_kota'] = 'Kab. Garut';
                $updates['provinsi'] = 'Jawa Barat';
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $siswa->update($updates);
                $updatedDomestic++;
            }
        }

        return [
            'total_scanned' => $totalScanned,
            'updated_domestic' => $updatedDomestic,
            'updated_international' => $updatedInternational,
        ];
    }

    /**
     * Standarisasikan penamaan Kabupaten/Kota sesuai standar resmi Kemendikbud:
     * - Kabupaten selalu berawalan 'Kab. ' (contoh: 'Kab. Garut', 'Kab. Bogor')
     * - Kota selalu berawalan 'Kota ' (contoh: 'Kota Bogor', 'Kota Bandung')
     * - 'Garut' dinormalisasi menjadi 'Kab. Garut' (karena tidak ada Kota Garut)
     * - 'Bogor' ambigu: dicek ke kecamatan / sekolah asal terhadap database Master SMP (Cibinong -> Kab. Bogor, Bogor Tengah -> Kota Bogor)
     */
    public function standardizeKabupatenKotaName(?string $rawName, ?string $kecamatan = null, ?string $sekolahAsal = null): ?string
    {
        if (empty($rawName)) {
            return null;
        }

        $trimmed = trim(preg_replace('/\s+/', ' ', $rawName));
        $lower = strtolower($trimmed);

        // 1. Cek Non-Ambigus: KABUPATEN_ONLY (Garut, Cianjur, Ciamis, dll.)
        foreach (self::KABUPATEN_ONLY as $reg) {
            $regLower = strtolower($reg);
            if ($lower === $regLower || $lower === "kab {$regLower}" || $lower === "kab. {$regLower}" || $lower === "kabupaten {$regLower}") {
                return "Kab. {$reg}";
            }
        }

        // 2. Cek Non-Ambigus: KOTA_ONLY (Cimahi, Depok, Banjar)
        foreach (self::KOTA_ONLY as $kot) {
            $kotLower = strtolower($kot);
            if ($lower === $kotLower || $lower === "kota {$kotLower}") {
                return "Kota {$kot}";
            }
        }

        // 3. Cek Duality Regions (Bogor, Bandung, Bekasi, Cirebon, Sukabumi, Tasikmalaya)
        foreach (self::DUALITY_REGIONS as $baseName => $options) {
            $baseLower = strtolower($baseName);

            // A. Sudah jelas 'Kota'
            if (str_contains($lower, 'kota') && str_contains($lower, $baseLower)) {
                return "Kota {$baseName}";
            }

            // B. Sudah jelas 'Kabupaten' / 'Kab.'
            if ((str_contains($lower, 'kab') || str_contains($lower, 'kabupaten')) && str_contains($lower, $baseLower)) {
                if ($baseName === 'Bandung' && (str_contains($lower, 'barat') || str_contains($lower, 'kbb'))) {
                    return 'Kab. Bandung Barat';
                }
                return "Kab. {$baseName}";
            }

            // C. Mentah hanya nama dasarnya (misal: 'Bogor', 'Bandung', 'Bekasi')
            if ($lower === $baseLower) {
                // Deduksi dari Kecamatan via Master SMP
                if (! empty($kecamatan)) {
                    $cleanKec = preg_replace('/^(kec\.?|kota)\s+/i', '', trim($kecamatan));
                    $masterKabs = SpmbMasterSmp::where(function ($q) use ($cleanKec) {
                        $q->where('kecamatan', 'like', $cleanKec)
                            ->orWhere('kecamatan', 'like', "%{$cleanKec}%");
                    })
                        ->where('provinsi', 'Jawa Barat')
                        ->distinct()
                        ->pluck('kabupaten');

                    $matchedDuality = $masterKabs->filter(fn ($k) => str_contains(strtolower($k), $baseLower));
                    if ($matchedDuality->count() === 1) {
                        return $matchedDuality->first();
                    }
                }

                // Deduksi dari Sekolah Asal via Master SMP
                if (! empty($sekolahAsal)) {
                    $masterSchool = SpmbMasterSmp::where('nama', 'like', '%' . trim($sekolahAsal) . '%')
                        ->where('provinsi', 'Jawa Barat')
                        ->first();
                    if ($masterSchool && in_array($masterSchool->kabupaten, $options)) {
                        return $masterSchool->kabupaten;
                    }
                }

                // Tidak bisa dipastikan otomatis -> biarkan dengan Title Case untuk diverifikasi di Tab Perbaikan
                return Str::title($trimmed);
            }
        }

        // 4. Standarisasi awalan 'Kab ' atau 'Kabupaten ' menjadi 'Kab. '
        if (preg_match('/^kabupaten\s+(.+)$/i', $trimmed, $m) || preg_match('/^kab\s+([^\.].+)$/i', $trimmed, $m)) {
            return 'Kab. ' . Str::title(trim($m[1]));
        }

        return Str::title($trimmed);
    }

    /**
     * Mengambil opsi pill button dan petunjuk cerdas dari Master SMP untuk kelompok wilayah di Tab Perbaikan.
     *
     * @return array{pills: array<string>, hint: ?string}
     */
    public function getDualityPillOptions(?string $rawKecamatan, ?string $rawKabupaten): array
    {
        $cleanKec = $rawKecamatan ? preg_replace('/^kec\.?\s+/i', '', trim($rawKecamatan)) : '';
        $cleanKab = $rawKabupaten ? trim($rawKabupaten) : '';

        $pills = [];
        $hint = null;

        // 1. Cek apakah kecamatan cocok dengan Master SMP
        if (! empty($cleanKec)) {
            $masterMatches = SpmbMasterSmp::where(function ($q) use ($cleanKec) {
                $q->where('kecamatan', 'like', $cleanKec)
                    ->orWhere('kecamatan', 'like', "%{$cleanKec}%");
            })
                ->select('kabupaten', 'provinsi')
                ->groupBy('kabupaten', 'provinsi')
                ->get();

            if ($masterMatches->count() === 1) {
                $match = $masterMatches->first();
                $hint = "Kecamatan '{$cleanKec}' resmi terdaftar di {$match->kabupaten}, {$match->provinsi} pada database Master Kemendikbud.";
                $pills[] = $match->kabupaten;

                // Jika terdaftar di Kota/Kab yang memiliki pasangan dualitas, tambahkan opsi pembandingnya
                foreach (self::DUALITY_REGIONS as $base => $dualOpts) {
                    if (str_contains(strtolower($match->kabupaten), strtolower($base))) {
                        foreach ($dualOpts as $dOpt) {
                            if (! in_array($dOpt, $pills)) {
                                $pills[] = $dOpt;
                            }
                        }
                    }
                }
            } elseif ($masterMatches->count() > 1) {
                $kabNames = $masterMatches->pluck('kabupaten')->unique()->values()->all();
                $hint = "Kecamatan '{$cleanKec}' ditemukan di beberapa wilayah: " . implode(', ', $kabNames) . ". Silakan pilih yang tepat.";
                $pills = array_merge($pills, $kabNames);
            }
        }

        // 2. Cek apakah kabupaten mentah mengindikasikan dualitas (misal 'Bogor' atau 'Bandung')
        if (! empty($cleanKab)) {
            foreach (self::DUALITY_REGIONS as $base => $dualOpts) {
                if (strtolower($cleanKab) === strtolower($base)) {
                    foreach ($dualOpts as $dOpt) {
                        if (! in_array($dOpt, $pills)) {
                            $pills[] = $dOpt;
                        }
                    }
                }
            }

            // Jika mengindikasikan Garut
            if (strtolower($cleanKab) === 'garut' && ! in_array('Kab. Garut', $pills)) {
                $pills[] = 'Kab. Garut';
            }
        }

        return [
            'pills' => array_values(array_unique($pills)),
            'hint' => $hint,
        ];
    }

    /**
     * Lakukan normalisasi massal penamaan Kabupaten/Kota di tabel spmb_sekolah_asals dan siswas.
     * Mengubah penulisan 'Garut' -> 'Kab. Garut' dan menyelaraskan seluruh penamaan.
     *
     * @return array{updated_sekolah_asals: int, updated_siswas: int}
     */
    public function normalizeAllKabupatenKota(): array
    {
        // 1. Normalisasi spmb_sekolah_asals
        $updatedSekolah = SpmbSekolahAsal::where('kabupaten_kota', 'Garut')
            ->orWhere('kabupaten_kota', 'garut')
            ->orWhere('kabupaten_kota', 'Kab Garut')
            ->update(['kabupaten_kota' => 'Kab. Garut']);

        // 2. Normalisasi siswas yang bertuliskan 'Garut'
        $updatedSiswa = Siswa::where('kabupaten_kota', 'Garut')
            ->orWhere('kabupaten_kota', 'garut')
            ->orWhere('kabupaten_kota', 'Kab Garut')
            ->update([
                'kabupaten_kota' => 'Kab. Garut',
                'provinsi' => 'Jawa Barat',
                'negara' => 'Indonesia',
            ]);

        // 3. Normalisasi siswas dengan non-ambiguous kabupaten lain atau awalan 'Kabupaten '
        $otherSiswas = Siswa::whereNotNull('kabupaten_kota')
            ->where('kabupaten_kota', '!=', '')
            ->where('kabupaten_kota', '!=', 'Kab. Garut')
            ->get();

        foreach ($otherSiswas as $s) {
            $std = $this->standardizeKabupatenKotaName($s->kabupaten_kota, $s->kecamatan, $s->sekolah_asal);
            if ($std && $std !== $s->kabupaten_kota) {
                $s->update(['kabupaten_kota' => $std]);
                $updatedSiswa++;
            }
        }

        return [
            'updated_sekolah_asals' => $updatedSekolah,
            'updated_siswas' => $updatedSiswa,
        ];
    }

    /**
     * Memperbarui data wilayah untuk sekelompok siswa berdasarkan kriteria
     */
    public function bulkUpdateWilayah(array $criteria, array $data): int
    {
        $query = Siswa::query();

        if (! empty($criteria['ids'])) {
            $query->whereIn('id', $criteria['ids']);
        } elseif (! empty($criteria['raw_kecamatan'])) {
            $query->where('kecamatan', $criteria['raw_kecamatan']);
            if (isset($criteria['raw_kelurahan'])) {
                $query->where('kelurahan', $criteria['raw_kelurahan']);
            }
        } else {
            return 0;
        }

        $updatePayload = [
            'negara' => $data['negara'] ?: 'Indonesia',
            'provinsi' => $data['provinsi'] ?: null,
            'kabupaten_kota' => $data['kabupaten_kota'] ?: null,
        ];

        if (! empty($data['kecamatan'])) {
            $updatePayload['kecamatan'] = $data['kecamatan'];
        }

        return $query->update($updatePayload);
    }

    /**
     * Mengambil analitik sebaran wilayah beserta ringkasan statistik
     */
    public function getWilayahAnalytics(array $filters = []): array
    {
        $jurusans = Jurusan::orderBy('kode')->get();

        $siswaQuery = Siswa::query()
            ->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id');

        // Filter Rombel & Status
        if (! empty($filters['filterTingkat'])) {
            $siswaQuery->where('rombels.tingkat', $filters['filterTingkat']);
        }
        if (! empty($filters['filterTahunAjaranId'])) {
            $siswaQuery->where('rombels.tahun_ajaran_id', $filters['filterTahunAjaranId']);
        }
        if (! empty($filters['filterJurusanId'])) {
            $siswaQuery->where('rombels.jurusan_id', $filters['filterJurusanId']);
        }
        if (! empty($filters['filterJk'])) {
            $siswaQuery->where('siswas.jk', $filters['filterJk']);
        }

        if (($filters['filterStatus'] ?? 'aktif') === 'aktif') {
            $siswaQuery->where('siswas.is_active', true);
        } elseif (($filters['filterStatus'] ?? '') !== 'semua' && ! empty($filters['filterStatus'])) {
            $siswaQuery->where('siswas.status', $filters['filterStatus']);
        }

        // Filter Cakupan: semua, dalam_negeri, luar_negeri
        if (($filters['filterCakupan'] ?? '') === 'dalam_negeri') {
            $siswaQuery->where(function ($q) {
                $q->whereNull('siswas.negara')
                    ->orWhere('siswas.negara', 'Indonesia');
            });
        } elseif (($filters['filterCakupan'] ?? '') === 'luar_negeri') {
            $siswaQuery->whereNotNull('siswas.negara')
                ->where('siswas.negara', '!=', 'Indonesia');
        }

        if (! empty($filters['filterProvinsi'])) {
            $siswaQuery->where('siswas.provinsi', $filters['filterProvinsi']);
        }

        if (! empty($filters['filterKabupaten'])) {
            $siswaQuery->where('siswas.kabupaten_kota', $filters['filterKabupaten']);
        }

        if (! empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $siswaQuery->where(function ($q) use ($s) {
                $q->where('siswas.kecamatan', 'like', $s)
                    ->orWhere('siswas.kabupaten_kota', 'like', $s)
                    ->orWhere('siswas.provinsi', 'like', $s)
                    ->orWhere('siswas.negara', 'like', $s)
                    ->orWhere('siswas.kelurahan', 'like', $s)
                    ->orWhere('siswas.alamat', 'like', $s);
            });
        }

        // Hitung total siswa dalam filter
        $totalSiswa = (clone $siswaQuery)->count();

        // Agregasi Statistik Ringkas
        $statNegara = (clone $siswaQuery)->whereNotNull('siswas.negara')->distinct('siswas.negara')->count('siswas.negara');
        $statProvinsi = (clone $siswaQuery)->whereNotNull('siswas.provinsi')->distinct('siswas.provinsi')->count('siswas.provinsi');
        $statKabupaten = (clone $siswaQuery)->whereNotNull('siswas.kabupaten_kota')->distinct('siswas.kabupaten_kota')->count('siswas.kabupaten_kota');
        $statLuarNegeri = (clone $siswaQuery)->whereNotNull('siswas.negara')->where('siswas.negara', '!=', 'Indonesia')->count();

        // Kueri Agregasi Per Wilayah (Negara, Provinsi, Kab/Kota, Kecamatan)
        $selects = [
            DB::raw("COALESCE(NULLIF(siswas.negara, ''), 'Indonesia') as negara"),
            DB::raw("COALESCE(NULLIF(siswas.provinsi, ''), '[Belum Diset]') as provinsi"),
            DB::raw("COALESCE(NULLIF(siswas.kabupaten_kota, ''), '[Belum Diset]') as kabupaten_kota"),
            DB::raw("COALESCE(NULLIF(siswas.kecamatan, ''), '[Belum Diset]') as kecamatan"),
            DB::raw('count(siswas.id) as total_siswa'),
            DB::raw('sum(case when siswas.jk = "L" then 1 else 0 end) as siswa_l'),
            DB::raw('sum(case when siswas.jk = "P" then 1 else 0 end) as siswa_p'),
        ];

        foreach ($jurusans as $j) {
            $selects[] = DB::raw("sum(case when rombels.jurusan_id = {$j->id} then 1 else 0 end) as jurusan_{$j->id}");
        }

        $groupQuery = (clone $siswaQuery)
            ->select($selects)
            ->groupBy('negara', 'provinsi', 'kabupaten_kota', 'kecamatan')
            ->orderByDesc('total_siswa')
            ->orderBy('provinsi')
            ->orderBy('kabupaten_kota')
            ->orderBy('kecamatan');

        $items = $groupQuery->get();

        // Hitung dominan jurusan & persentase
        $items->transform(function ($item) use ($totalSiswa, $jurusans) {
            $dominantName = '-';
            $dominantCount = 0;
            foreach ($jurusans as $j) {
                $cnt = (int) ($item->{'jurusan_' . $j->id} ?? 0);
                if ($cnt > $dominantCount) {
                    $dominantCount = $cnt;
                    $dominantName = $j->kode;
                }
            }

            $pct = $totalSiswa > 0 ? round(($item->total_siswa / $totalSiswa) * 100, 1) : 0;
            $item->percentage = $pct;
            $item->dominant_jurusan = $dominantName !== '-' ? "{$dominantName} ({$dominantCount})" : '-';

            return $item;
        });

        // Top 10 Kabupaten/Kota Leaderboard
        $topKabupatenQuery = (clone $siswaQuery)
            ->select(
                DB::raw("COALESCE(NULLIF(siswas.kabupaten_kota, ''), '[Belum Diset]') as kab_kota"),
                DB::raw("COALESCE(NULLIF(siswas.provinsi, ''), '') as prov"),
                DB::raw("COALESCE(NULLIF(siswas.negara, ''), 'Indonesia') as neg"),
                DB::raw('count(siswas.id) as total')
            )
            ->groupBy('kab_kota', 'prov', 'neg')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Daftar Siswa Luar Negeri
        $luarNegeriSiswa = (clone $siswaQuery)
            ->whereNotNull('siswas.negara')
            ->where('siswas.negara', '!=', 'Indonesia')
            ->select('siswas.id', 'siswas.nama', 'siswas.nisn', 'siswas.nipd', 'siswas.jk', 'siswas.negara', 'siswas.provinsi', 'siswas.kabupaten_kota', 'siswas.kecamatan', 'siswas.sekolah_asal', 'siswas.alamat')
            ->orderBy('siswas.negara')
            ->orderBy('siswas.kabupaten_kota')
            ->orderBy('siswas.nama')
            ->get();

        return [
            'items' => $items,
            'jurusans' => $jurusans,
            'totalSiswa' => $totalSiswa,
            'statNegara' => $statNegara,
            'statProvinsi' => $statProvinsi,
            'statKabupaten' => $statKabupaten,
            'statLuarNegeri' => $statLuarNegeri,
            'topKabupaten' => $topKabupatenQuery,
            'luarNegeriSiswa' => $luarNegeriSiswa,
        ];
    }

    /**
     * Mengambil daftar kelompok wilayah yang belum lengkap atau masih anomali untuk Tab Perbaikan
     */
    public function getAnomaliGroups(?string $search = null): Collection
    {
        $query = Siswa::query()
            ->select(
                DB::raw("COALESCE(siswas.kecamatan, '') as kecamatan"),
                DB::raw("COALESCE(siswas.kelurahan, '') as kelurahan"),
                DB::raw("COALESCE(siswas.kabupaten_kota, '') as kabupaten_kota"),
                DB::raw("COALESCE(siswas.provinsi, '') as provinsi"),
                DB::raw("COALESCE(siswas.negara, 'Indonesia') as negara"),
                DB::raw('count(siswas.id) as total_siswa'),
                DB::raw('group_concat(siswas.id) as siswa_ids')
            )
            ->where(function ($q) {
                $q->whereNull('siswas.kabupaten_kota')
                    ->orWhere('siswas.kabupaten_kota', '')
                    ->orWhereNull('siswas.provinsi')
                    ->orWhere('siswas.provinsi', '')
                    ->orWhere('siswas.kecamatan', 'like', 'Kec.%');
            })
            ->groupBy('kecamatan', 'kelurahan', 'kabupaten_kota', 'provinsi', 'negara')
            ->orderByDesc('total_siswa');

        if ($search) {
            $s = '%' . trim($search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('siswas.kecamatan', 'like', $s)
                    ->orWhere('siswas.kelurahan', 'like', $s)
                    ->orWhere('siswas.alamat', 'like', $s)
                    ->orWhere('siswas.sekolah_asal', 'like', $s);
            });
        }

        $groups = $query->get();

        // Ambil sampel nama siswa untuk setiap kelompok
        $groups->transform(function ($g) {
            $ids = array_filter(explode(',', (string) $g->siswa_ids));
            $g->sample_names = Siswa::whereIn('id', array_slice($ids, 0, 3))->pluck('nama')->toArray();
            $g->sample_school = Siswa::whereIn('id', array_slice($ids, 0, 2))->whereNotNull('sekolah_asal')->value('sekolah_asal') ?? '-';
            return $g;
        });

        return $groups;
    }
}
