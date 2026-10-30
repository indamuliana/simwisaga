<?php

namespace App\Modules\AnalitikSpmb\Services;

use App\Modules\AnalitikSpmb\Models\SpmbIgnoredDuplicate;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsalAlias;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpmbDataCleansingService
{
    /**
     * Mengambil daftar teks unik sekolah asal dari tabel siswa beserta statistik pemetaan.
     */
    public function getUniqueSchoolStrings(?string $search = null, ?string $filterMapping = null): Collection
    {
        $rawSchools = Siswa::query()
            ->whereNotNull('sekolah_asal')
            ->where('sekolah_asal', '!=', '')
            ->select(
                'sekolah_asal',
                DB::raw('count(*) as total_siswa'),
                DB::raw('sum(case when is_active = 1 then 1 else 0 end) as total_aktif')
            )
            ->groupBy('sekolah_asal')
            ->orderByDesc('total_siswa')
            ->get();

        // Ambil pemetaan alias yang ada
        $aliases = SpmbSekolahAsalAlias::with('sekolahAsal')->get()->keyBy(function ($item) {
            return strtolower(trim($item->alias_raw));
        });

        $result = $rawSchools->map(function ($item) use ($aliases) {
            $key = strtolower(trim($item->sekolah_asal));
            $mappedAlias = $aliases->get($key);
            $master = $mappedAlias?->sekolahAsal;

            return (object) [
                'raw_name' => $item->sekolah_asal,
                'total_siswa' => (int) $item->total_siswa,
                'total_aktif' => (int) $item->total_aktif,
                'is_mapped' => (bool) $master,
                'master_id' => $master?->id,
                'master_name' => $master?->nama_resmi,
                'master_npsn' => $master?->npsn,
                'master_tier' => $master?->tier,
                'master_jenis' => $master?->jenis,
                'master_catatan' => $master?->catatan,
            ];
        });

        // Filter search jika ada
        if ($search) {
            $s = strtolower(trim($search));
            $result = $result->filter(function ($item) use ($s) {
                return str_contains(strtolower($item->raw_name), $s)
                    || ($item->master_name && str_contains(strtolower($item->master_name), $s))
                    || ($item->master_npsn && str_contains(strtolower($item->master_npsn), $s))
                    || ($item->master_catatan && str_contains(strtolower($item->master_catatan), $s));
            });
        }

        // Filter status pemetaan jika ada
        if ($filterMapping === 'mapped') {
            $result = $result->filter(fn ($item) => $item->is_mapped);
        } elseif ($filterMapping === 'unmapped') {
            $result = $result->filter(fn ($item) => ! $item->is_mapped);
        }

        return $result->values();
    }

    /**
     * Mengambil daftar siswa yang terhubung dengan salah satu dari teks sekolah asal sumber.
     */
    public function getAffectedStudents(array $schoolNames): Collection
    {
        if (empty($schoolNames)) {
            return collect();
        }

        return Siswa::with(['rombel.jurusan'])
            ->whereIn('sekolah_asal', $schoolNames)
            ->orderBy('is_active', 'desc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    /**
     * Deteksi kelompok nama sekolah asal yang berpotensi merupakan entitas yang sama (duplikat).
     */
    public function detectPotentialDuplicates(): array
    {
        $allUnique = Siswa::query()
            ->whereNotNull('sekolah_asal')
            ->where('sekolah_asal', '!=', '')
            ->select('sekolah_asal', DB::raw('count(*) as total_siswa'))
            ->groupBy('sekolah_asal')
            ->get();

        if ($allUnique->count() < 2) {
            return [];
        }

        // Siapkan struktur core name untuk pencocokan kemiripan
        $items = $allUnique->map(function ($item) {
            $cleanCore = $this->extractCoreName($item->sekolah_asal);

            return [
                'raw_name' => $item->sekolah_asal,
                'core' => $cleanCore,
                'total_siswa' => (int) $item->total_siswa,
            ];
        })->values()->toArray();

        $clusters = [];
        $visited = [];
        $ignoredHashes = SpmbIgnoredDuplicate::pluck('pair_hash')->flip()->toArray();

        $count = count($items);
        for ($i = 0; $i < $count; $i++) {
            if (isset($visited[$i])) {
                continue;
            }

            $group = [$items[$i]];
            $coreA = $items[$i]['core'];

            if (strlen($coreA) < 3) {
                continue;
            }

            for ($j = $i + 1; $j < $count; $j++) {
                if (isset($visited[$j])) {
                    continue;
                }

                $pairHash = SpmbIgnoredDuplicate::hashPair($items[$i]['raw_name'], $items[$j]['raw_name']);
                if (isset($ignoredHashes[$pairHash])) {
                    continue;
                }

                $coreB = $items[$j]['core'];
                if (strlen($coreB) < 3) {
                    continue;
                }

                if ($this->isSimilarSchool($coreA, $coreB, $items[$i]['raw_name'], $items[$j]['raw_name'])) {
                    $group[] = $items[$j];
                    $visited[$j] = true;
                }
            }

            if (count($group) > 1) {
                $visited[$i] = true;

                // Urutkan anggota grup berdasarkan jumlah siswa terbanyak (sebagai kandidat target utama)
                usort($group, fn ($a, $b) => $b['total_siswa'] <=> $a['total_siswa']);

                $suggestedTarget = $group[0]['raw_name'];
                $totalSiswaCluster = array_sum(array_column($group, 'total_siswa'));

                $clusters[] = [
                    'suggested_target' => $suggestedTarget,
                    'total_siswa' => $totalSiswaCluster,
                    'items' => $group,
                ];
            }
        }

        // Urutkan cluster berdasarkan total siswa terbesar
        usort($clusters, fn ($a, $b) => $b['total_siswa'] <=> $a['total_siswa']);

        return array_slice($clusters, 0, 15); // Ambil top 15 rekomendasi
    }

    /**
     * Memeriksa apakah dua nama sekolah tergolong mirip/duplikat.
     */
    protected function isSimilarSchool(string $coreA, string $coreB, string $rawA, string $rawB): bool
    {
        // 0. Cek perbedaan nomor urut sekolah (misal: SMPN 1 Leles vs SMPN 2 Leles)
        // Jika kedua sekolah memiliki angka urut yang berbeda, dipastikan BUKAN sekolah yang sama!
        preg_match_all('/\b(\d+)\b/', $rawA, $matchesA);
        preg_match_all('/\b(\d+)\b/', $rawB, $matchesB);
        $numsA = $matchesA[1] ?? [];
        $numsB = $matchesB[1] ?? [];
        if (! empty($numsA) && ! empty($numsB)) {
            $firstNumA = $numsA[0];
            $firstNumB = $numsB[0];
            if ($firstNumA !== $firstNumB) {
                return false;
            }
        }

        // Cek jika satu adalah Negeri (SMPN/MTsN) dan satu lagi adalah swasta yayasan spesifik
        $isNegeriA = (bool) preg_match('/\b(smpn|mtsn|negeri)\b/i', $rawA);
        $isNegeriB = (bool) preg_match('/\b(smpn|mtsn|negeri)\b/i', $rawB);
        $hasYayasanA = (bool) preg_match('/\b(muhammadiyah|persis|islam terpadu|maarif|ma\'arif|pgri)\b/i', $rawA);
        $hasYayasanB = (bool) preg_match('/\b(muhammadiyah|persis|islam terpadu|maarif|ma\'arif|pgri)\b/i', $rawB);

        if (($isNegeriA && ! $hasYayasanA && $hasYayasanB) || ($isNegeriB && ! $hasYayasanB && $hasYayasanA)) {
            return false;
        }

        // Cek jika satu MTs dan satu SMP
        $isMtsA = (bool) preg_match('/\b(mts|mtsn|madrasah)\b/i', $rawA);
        $isMtsB = (bool) preg_match('/\b(mts|mtsn|madrasah)\b/i', $rawB);
        $isSmpA = (bool) preg_match('/\b(smp|smpn)\b/i', $rawA);
        $isSmpB = (bool) preg_match('/\b(smp|smpn)\b/i', $rawB);

        if ($isMtsA && $isSmpB && ! $isMtsB) {
            return false;
        }
        if ($isMtsB && $isSmpA && ! $isMtsA) {
            return false;
        }

        // 1. Core sama persis (misal: "smp baitul hikmah" vs "smpn baitul hikmah")
        if ($coreA === $coreB) {
            return true;
        }

        // 2. Salah satu core mengandung core lainnya jika panjang core minimal 5 karakter
        // Contoh: "baitul hikmah" dan "baitul hikmah tarogong kaler"
        if (strlen($coreA) >= 5 && strlen($coreB) >= 5) {
            if (str_contains($coreA, $coreB) || str_contains($coreB, $coreA)) {
                return true;
            }
        }

        // 3. Similar text percentage >= 82%
        similar_text($coreA, $coreB, $percent);
        if ($percent >= 82 && strlen($coreA) >= 6 && strlen($coreB) >= 6) {
            return true;
        }

        // 4. Levenshtein distance kecil untuk nama panjang
        $len = max(strlen($coreA), strlen($coreB));
        if ($len >= 8 && levenshtein($coreA, $coreB) <= 2) {
            return true;
        }

        return false;
    }

    /**
     * Tandai bahwa dua nama sekolah asal adalah sekolah yang berbeda (bukan duplikat).
     */
    public function markAsDifferent(string $rawA, string $rawB): void
    {
        SpmbIgnoredDuplicate::ignorePair($rawA, $rawB);
    }

    /**
     * Tandai seluruh pasangan di dalam kelompok/cluster sebagai sekolah yang berbeda.
     */
    public function markClusterAsDifferent(array $schoolNames): int
    {
        $count = count($schoolNames);
        $added = 0;
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                SpmbIgnoredDuplicate::ignorePair($schoolNames[$i], $schoolNames[$j]);
                $added++;
            }
        }

        return $added;
    }

    /**
     * Batalkan tanda berbeda untuk pasangan tertentu.
     */
    public function unmarkDifferent(int $ignoredId): void
    {
        SpmbIgnoredDuplicate::where('id', $ignoredId)->delete();
    }

    /**
     * Normalisasi nama sekolah untuk ekstraksi kata dasar (core).
     */
    protected function extractCoreName(string $name): string
    {
        $str = strtolower(trim($name));

        // Hilangkan tanda baca umum
        $str = preg_replace('/[.,\/#!$%\^&\*;:{}=\-_`~()]/', ' ', $str);

        // Hilangkan prefiks umum tingkatan dan status
        $patterns = [
            '/\bsekolah menengah pertama\b/',
            '/\bmadrasah tsanawiyah\b/',
            '/\bpondok pesantren\b/',
            '/\bponpes\b/',
            '/\bsmp negeri\b/',
            '/\bsmp swasta\b/',
            '/\bmts negeri\b/',
            '/\bmts swasta\b/',
            '/\bsmpn\b/',
            '/\bsmps\b/',
            '/\bmtsn\b/',
            '/\bnegeri\b/',
            '/\bswasta\b/',
            '/\bsmp\b/',
            '/\bmts\b/',
        ];

        $str = preg_replace($patterns, ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Eksekusi penggabungan (merge) beberapa teks sekolah asal menjadi satu nama target.
     */
    public function mergeSchoolStrings(
        array $sourceNames,
        string $targetName,
        bool $updateSiswa = true,
        bool $createAlias = true,
        ?int $targetMasterId = null,
        ?int $targetMasterSmpId = null
    ): array {
        $targetName = trim($targetName);
        if (empty($targetName)) {
            throw new \InvalidArgumentException('Nama sekolah target tidak boleh kosong.');
        }

        $sourceNames = array_values(array_unique(array_filter(array_map('trim', $sourceNames))));
        if (empty($sourceNames)) {
            throw new \InvalidArgumentException('Daftar sekolah sumber tidak boleh kosong.');
        }

        return DB::transaction(function () use ($sourceNames, $targetName, $updateSiswa, $createAlias, $targetMasterId, $targetMasterSmpId) {
            $syncService = app(SpmbSekolahAsalSyncService::class);

            // 1. Tentukan atau buat master target di spmb_sekolah_asals
            if ($targetMasterId) {
                $targetMaster = SpmbSekolahAsal::findOrFail($targetMasterId);
            } else {
                $targetMaster = SpmbSekolahAsal::where('nama_resmi', $targetName)->first();
                if (! $targetMaster) {
                    $meta = $syncService->detectMetadata($targetName);
                    $targetMaster = SpmbSekolahAsal::create([
                        'nama_resmi' => $targetName,
                        'jenis' => $meta['jenis'],
                        'status_sekolah' => $meta['status_sekolah'],
                        'kabupaten_kota' => 'Garut',
                        'tier' => 'Rintisan',
                    ]);
                }
            }

            // Jika ada targetMasterSmpId, sinkronisasikan metadata dari master SMP resmi
            if ($targetMasterSmpId) {
                $targetMaster = $this->linkMasterSmpToSekolahAsal($targetMasterSmpId, $targetMaster->id);
            }

            // Pastikan targetName sendiri terdaftar sebagai alias ke master target
            SpmbSekolahAsalAlias::updateOrCreate(
                ['alias_raw' => $targetMaster->nama_resmi],
                ['sekolah_asal_id' => $targetMaster->id]
            );

            // 2. Hitung siswa yang terdampak
            $affectedStudentsCount = Siswa::whereIn('sekolah_asal', $sourceNames)->count();

            // 3. Update data pada tabel siswas jika opsi updateSiswa diaktifkan
            if ($updateSiswa) {
                Siswa::whereIn('sekolah_asal', $sourceNames)->update([
                    'sekolah_asal' => $targetMaster->nama_resmi,
                ]);
            }

            // 4. Daftarkan / hubungkan alias jika opsi createAlias diaktifkan
            if ($createAlias) {
                foreach ($sourceNames as $src) {
                    if (strcasecmp($src, $targetMaster->nama_resmi) === 0) {
                        continue;
                    }

                    // Update atau daftarkan alias agar mengarah ke targetMaster
                    SpmbSekolahAsalAlias::updateOrCreate(
                        ['alias_raw' => $src],
                        ['sekolah_asal_id' => $targetMaster->id]
                    );

                    // Cek jika src sebelumnya adalah master sekolah lain yang namanya persis src
                    $otherMaster = SpmbSekolahAsal::where('nama_resmi', $src)
                        ->where('id', '!=', $targetMaster->id)
                        ->first();
                    if ($otherMaster) {
                        // Pindahkan seluruh alias anak master lama ke master target
                        SpmbSekolahAsalAlias::where('sekolah_asal_id', $otherMaster->id)
                            ->update(['sekolah_asal_id' => $targetMaster->id]);
                        $otherMaster->delete();
                    }
                }
            }

            // 5. Update tiering target master
            $aliases = $targetMaster->fresh()->all_names;
            $activeCount = Siswa::whereIn('sekolah_asal', $aliases)
                ->where('is_active', true)
                ->count();
            $targetMaster->update(['tier' => $syncService->calculateTier($activeCount)]);

            return [
                'affected_students' => $affectedStudentsCount,
                'target_name' => $targetMaster->nama_resmi,
                'target_master_id' => $targetMaster->id,
                'source_names' => $sourceNames,
            ];
        });
    }

    /**
     * Rename satu teks sekolah asal siswa menjadi nama baru.
     */
    public function renameSchoolString(
        string $oldName,
        string $newName,
        bool $updateSiswa = true,
        bool $createAlias = true
    ): array {
        return $this->mergeSchoolStrings([$oldName], $newName, $updateSiswa, $createAlias);
    }

    /**
     * Pencarian Master SMP resmi berdasarkan nama atau NPSN untuk dropdown / autocomplete.
     */
    public function searchMasterSmp(string $search, ?string $kabupaten = null, int $limit = 20): Collection
    {
        $term = trim($search);
        if (strlen($term) < 2) {
            return collect();
        }

        $query = SpmbMasterSmp::query();

        if ($kabupaten) {
            $query->where('kabupaten', $kabupaten);
        }

        $s = '%' . $term . '%';
        $query->where(function ($q) use ($s, $term) {
            $q->where('nama', 'like', $s)
                ->orWhere('npsn', 'like', $term . '%');
        });

        return $query->orderBy('nama')->take($limit)->get();
    }

    /**
     * Hubungkan / Sinkronisasikan sebuah data master sekolah asal dengan Master SMP resmi.
     */
    public function linkMasterSmpToSekolahAsal(int $masterSmpId, ?int $sekolahAsalId = null): SpmbSekolahAsal
    {
        $masterSmp = SpmbMasterSmp::findOrFail($masterSmpId);

        $jenis = 'SMP';
        $bentukUpper = strtoupper($masterSmp->bentuk ?? '');
        if (str_contains($bentukUpper, 'MTS')) {
            $jenis = 'MTs';
        } elseif (str_contains($bentukUpper, 'PESANTREN')) {
            $jenis = 'Pesantren';
        }

        $status = str_contains(strtoupper($masterSmp->status ?? ''), 'NEG') ? 'Negeri' : 'Swasta';

        if ($sekolahAsalId) {
            $sekolahAsal = SpmbSekolahAsal::findOrFail($sekolahAsalId);
            $sekolahAsal->update([
                'master_smp_id' => $masterSmp->id,
                'npsn' => $masterSmp->npsn,
                'jenis' => $jenis,
                'status_sekolah' => $status,
                'kecamatan' => $masterSmp->kecamatan,
                'kabupaten_kota' => $masterSmp->kabupaten,
            ]);
        } else {
            // Cari apakah sudah ada master sekolah asal dengan npsn ini atau nama ini
            $sekolahAsal = SpmbSekolahAsal::where('master_smp_id', $masterSmp->id)
                ->orWhere('npsn', $masterSmp->npsn)
                ->orWhere('nama_resmi', $masterSmp->nama)
                ->first();

            if ($sekolahAsal) {
                $sekolahAsal->update([
                    'master_smp_id' => $masterSmp->id,
                    'npsn' => $masterSmp->npsn,
                    'jenis' => $jenis,
                    'status_sekolah' => $status,
                    'kecamatan' => $masterSmp->kecamatan,
                    'kabupaten_kota' => $masterSmp->kabupaten,
                ]);
            } else {
                $sekolahAsal = SpmbSekolahAsal::create([
                    'master_smp_id' => $masterSmp->id,
                    'nama_resmi' => $masterSmp->nama,
                    'npsn' => $masterSmp->npsn,
                    'jenis' => $jenis,
                    'status_sekolah' => $status,
                    'kecamatan' => $masterSmp->kecamatan,
                    'kabupaten_kota' => $masterSmp->kabupaten,
                    'tier' => 'Rintisan',
                ]);
            }
        }

        // Daftarkan nama resmi Master SMP sebagai alias
        SpmbSekolahAsalAlias::firstOrCreate([
            'sekolah_asal_id' => $sekolahAsal->id,
            'alias_raw' => $masterSmp->nama,
        ]);

        return $sekolahAsal;
    }
}
