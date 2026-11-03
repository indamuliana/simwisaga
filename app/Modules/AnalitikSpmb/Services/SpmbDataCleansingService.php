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
        $subquery = Siswa::query()
            ->selectRaw("COALESCE(NULLIF(sekolah_asal_raw, ''), sekolah_asal) as raw_name")
            ->selectRaw('sekolah_asal as current_siswa_name')
            ->selectRaw('CASE WHEN is_active = 1 THEN 1 ELSE 0 END as is_active')
            ->where(function ($q) {
                $q->where(function ($q1) {
                    $q1->whereNotNull('sekolah_asal')->where('sekolah_asal', '!=', '');
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('sekolah_asal_raw')->where('sekolah_asal_raw', '!=', '');
                });
            });

        $rawSchools = DB::query()
            ->fromSub($subquery, 't')
            ->select('raw_name')
            ->selectRaw('MAX(current_siswa_name) as current_siswa_name')
            ->selectRaw('COUNT(*) as total_siswa')
            ->selectRaw('SUM(is_active) as total_aktif')
            ->groupBy('raw_name')
            ->orderByDesc('total_siswa')
            ->get();

        // Ambil pemetaan alias yang ada
        $aliases = SpmbSekolahAsalAlias::with('sekolahAsal')->get()->keyBy(function ($item) {
            return strtolower(trim($item->alias_raw));
        });

        $result = $rawSchools->map(function ($item) use ($aliases) {
            $key = strtolower(trim($item->raw_name));
            $mappedAlias = $aliases->get($key);
            $master = $mappedAlias?->sekolahAsal;
            $isMasterItself = ($master && strcasecmp(trim($item->raw_name), trim($master->nama_resmi)) === 0);

            return (object) [
                'raw_name' => $item->raw_name,
                'current_siswa_name' => $item->current_siswa_name ?? $item->raw_name,
                'total_siswa' => (int) $item->total_siswa,
                'total_aktif' => (int) $item->total_aktif,
                'is_mapped' => (bool) $master,
                'is_master_itself' => $isMasterItself,
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
                    || str_contains(strtolower($item->current_siswa_name), $s)
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
            ->where(function ($q) use ($schoolNames) {
                $q->whereIn('sekolah_asal', $schoolNames)
                  ->orWhereIn('sekolah_asal_raw', $schoolNames);
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    /**
     * Deteksi kelompok nama sekolah asal yang berpotensi merupakan entitas yang sama (duplikat).
     */
    public function detectPotentialDuplicates(): array
    {
        $subquery = Siswa::query()
            ->selectRaw("COALESCE(NULLIF(sekolah_asal_raw, ''), sekolah_asal) as raw_name")
            ->where(function ($q) {
                $q->where(function ($q1) {
                    $q1->whereNotNull('sekolah_asal')->where('sekolah_asal', '!=', '');
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('sekolah_asal_raw')->where('sekolah_asal_raw', '!=', '');
                });
            });

        $allUnique = DB::query()
            ->fromSub($subquery, 't')
            ->select('raw_name as sekolah_asal')
            ->selectRaw('COUNT(*) as total_siswa')
            ->groupBy('raw_name')
            ->get();

        if ($allUnique->count() < 2) {
            return [];
        }

        // Ambil mapping alias saat ini untuk mengecek apakah sekolah sudah disatukan ke master yang sama
        $aliases = SpmbSekolahAsalAlias::with('sekolahAsal')->get();
        $aliasMap = [];
        $masterNameMap = [];
        foreach ($aliases as $alias) {
            $key = strtolower(trim($alias->alias_raw));
            $aliasMap[$key] = $alias->sekolah_asal_id;
            $masterNameMap[$key] = $alias->sekolahAsal?->nama_resmi;
        }

        // Siapkan struktur core name untuk pencocokan kemiripan
        $items = $allUnique->map(function ($item) use ($aliasMap, $masterNameMap) {
            $cleanCore = $this->extractCoreName($item->sekolah_asal);
            $rawLower = strtolower(trim($item->sekolah_asal));

            return [
                'raw_name' => $item->sekolah_asal,
                'core' => $cleanCore,
                'total_siswa' => (int) $item->total_siswa,
                'master_id' => $aliasMap[$rawLower] ?? null,
                'master_name' => $masterNameMap[$rawLower] ?? null,
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
            $rawA = $items[$i]['raw_name'];
            $masterA = $items[$i]['master_id'];

            if (strlen($coreA) < 3) {
                continue;
            }

            for ($j = $i + 1; $j < $count; $j++) {
                if (isset($visited[$j])) {
                    continue;
                }

                $rawB = $items[$j]['raw_name'];
                $masterB = $items[$j]['master_id'];

                // Jika kedua nama sekolah SUDAH terpetakan ke master yang sama, mereka sudah disatukan!
                if ($masterA !== null && $masterB !== null && $masterA === $masterB) {
                    continue;
                }

                $pairHash = SpmbIgnoredDuplicate::hashPair($rawA, $rawB);
                if (isset($ignoredHashes[$pairHash])) {
                    continue;
                }

                $coreB = $items[$j]['core'];
                if (strlen($coreB) < 3) {
                    continue;
                }

                if ($this->isSimilarSchool($coreA, $coreB, $rawA, $rawB)) {
                    $group[] = $items[$j];
                    $visited[$j] = true;
                }
            }

            // Pastikan di dalam kelompok ada minimal 2 item dan minimal ada 2 master berbeda (atau belum terpetakan)
            $distinctMasterKeys = [];
            foreach ($group as $gItem) {
                $mKey = $gItem['master_id'] !== null ? 'master_' . $gItem['master_id'] : 'raw_' . $gItem['raw_name'];
                $distinctMasterKeys[$mKey] = true;
            }

            if (count($group) > 1 && count($distinctMasterKeys) > 1) {
                $visited[$i] = true;

                // Urutkan anggota grup berdasarkan jumlah siswa terbanyak (sebagai kandidat target utama)
                usort($group, fn ($a, $b) => $b['total_siswa'] <=> $a['total_siswa']);

                $suggestedTarget = $group[0]['master_name'] ?? $group[0]['raw_name'];
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
                        'kabupaten_kota' => 'Kab. Garut',
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
            $affectedStudentsCount = Siswa::where(function ($q) use ($sourceNames) {
                $q->whereIn('sekolah_asal', $sourceNames)
                  ->orWhereIn('sekolah_asal_raw', $sourceNames);
            })->count();

            // 3. Update data pada tabel siswas jika opsi updateSiswa diaktifkan
            if ($updateSiswa) {
                // Pastikan sekolah_asal_raw tersimpan sebelum diubah
                Siswa::whereIn('sekolah_asal', $sourceNames)
                    ->where(function ($q) {
                        $q->whereNull('sekolah_asal_raw')->orWhere('sekolah_asal_raw', '');
                    })
                    ->update([
                        'sekolah_asal_raw' => DB::raw('sekolah_asal'),
                    ]);

                // Update teks sekolah_asal ke nama master resmi
                Siswa::where(function ($q) use ($sourceNames) {
                    $q->whereIn('sekolah_asal', $sourceNames)
                      ->orWhereIn('sekolah_asal_raw', $sourceNames);
                })->update([
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
     * Melepaskan pemetaan suatu teks asal SMP dari master.
     * Mengembalikan data sekolah_asal pada tabel siswa ke teks aslinya (sekolah_asal_raw)
     * dan menghapus relasi alias dari master.
     */
    public function unmapSchoolString(string $rawName): array
    {
        $rawName = trim($rawName);
        if (empty($rawName)) {
            throw new \InvalidArgumentException('Nama sekolah yang ingin dilepaskan tidak boleh kosong.');
        }

        return DB::transaction(function () use ($rawName) {
            $syncService = app(SpmbSekolahAsalSyncService::class);

            // 1. Cari alias di database (case-insensitive)
            $alias = SpmbSekolahAsalAlias::with('sekolahAsal')
                ->whereRaw('LOWER(alias_raw) = ?', [strtolower($rawName)])
                ->first();

            $oldMasterId = $alias?->sekolah_asal_id;
            $oldMaster = $alias?->sekolahAsal;
            $oldMasterName = $oldMaster?->nama_resmi ?? '';

            // 2. Kembalikan data siswa di tabel siswas ke teks aslinya
            $affectedStudents = Siswa::where(function ($q) use ($rawName) {
                $q->where('sekolah_asal_raw', $rawName)
                  ->orWhere(function ($q2) use ($rawName) {
                      $q2->whereNull('sekolah_asal_raw')
                         ->where('sekolah_asal', $rawName);
                  });
            })->update([
                'sekolah_asal' => $rawName,
                'sekolah_asal_raw' => $rawName,
            ]);

            // 3. Hapus alias atau kelola master jika rawName adalah master itu sendiri
            if ($alias) {
                $isMasterItself = ($oldMaster && strcasecmp(trim($oldMaster->nama_resmi), trim($alias->alias_raw)) === 0);

                if ($isMasterItself) {
                    $otherAliasesCount = SpmbSekolahAsalAlias::where('sekolah_asal_id', $oldMaster->id)
                        ->where('id', '!=', $alias->id)
                        ->count();

                    $alias->delete();

                    // Jika tidak ada alias lain yang terhubung, hapus master tersebut
                    if ($otherAliasesCount === 0) {
                        $oldMaster->delete();
                        $oldMasterId = null;
                    }
                } else {
                    $alias->delete();
                }
            }

            // 4. Update tier master lama jika masih ada
            if ($oldMasterId) {
                $remMaster = SpmbSekolahAsal::find($oldMasterId);
                if ($remMaster) {
                    $aliases = $remMaster->fresh()->all_names;
                    $activeCount = Siswa::whereIn('sekolah_asal', $aliases)->where('is_active', true)->count();
                    $remMaster->update(['tier' => $syncService->calculateTier($activeCount)]);
                }
            }

            return [
                'raw_name' => $rawName,
                'old_master_name' => $oldMasterName,
                'affected_students' => $affectedStudents,
            ];
        });
    }

    /**
     * Mengubah pemetaan suatu teks sekolah asal ke master sekolah yang baru.
     * Mengalihkan relasi alias dan memperbarui data sekolah_asal siswa di tabel siswas.
     */
    public function editMapping(string $rawName, int $newMasterId): array
    {
        $rawName = trim($rawName);
        if (empty($rawName)) {
            throw new \InvalidArgumentException('Nama sekolah tidak boleh kosong.');
        }

        return DB::transaction(function () use ($rawName, $newMasterId) {
            $syncService = app(SpmbSekolahAsalSyncService::class);
            $newMaster = SpmbSekolahAsal::findOrFail($newMasterId);

            // 1. Cari alias yang ada
            $alias = SpmbSekolahAsalAlias::whereRaw('LOWER(alias_raw) = ?', [strtolower($rawName)])->first();
            $oldMasterId = $alias?->sekolah_asal_id;

            if ($alias) {
                $alias->update(['sekolah_asal_id' => $newMaster->id]);
            } else {
                SpmbSekolahAsalAlias::create([
                    'alias_raw' => $rawName,
                    'sekolah_asal_id' => $newMaster->id,
                ]);
            }

            // 2. Pastikan sekolah_asal_raw tersimpan di siswa
            Siswa::where('sekolah_asal', $rawName)
                ->where(function ($q) {
                    $q->whereNull('sekolah_asal_raw')->orWhere('sekolah_asal_raw', '');
                })
                ->update(['sekolah_asal_raw' => $rawName]);

            // 3. Update sekolah_asal siswa ke master baru
            $affectedStudents = Siswa::where(function ($q) use ($rawName) {
                $q->where('sekolah_asal_raw', $rawName)
                  ->orWhere('sekolah_asal', $rawName);
            })->update([
                'sekolah_asal' => $newMaster->nama_resmi,
            ]);

            // 4. Update tiering master lama dan baru
            if ($oldMasterId && $oldMasterId !== $newMaster->id) {
                $oldMaster = SpmbSekolahAsal::find($oldMasterId);
                if ($oldMaster) {
                    $oldAliases = $oldMaster->fresh()->all_names;
                    $oldActiveCount = Siswa::whereIn('sekolah_asal', $oldAliases)->where('is_active', true)->count();
                    $oldMaster->update(['tier' => $syncService->calculateTier($oldActiveCount)]);
                }
            }

            $newAliases = $newMaster->fresh()->all_names;
            $newActiveCount = Siswa::whereIn('sekolah_asal', $newAliases)->where('is_active', true)->count();
            $newMaster->update(['tier' => $syncService->calculateTier($newActiveCount)]);

            return [
                'raw_name' => $rawName,
                'target_master_id' => $newMaster->id,
                'target_name' => $newMaster->nama_resmi,
                'affected_students' => $affectedStudents,
            ];
        });
    }

    /**
     * Sinkronisasikan seluruh siswa yang memiliki alias terpetakan agar teks sekolah_asal
     * di tabel siswas mengikuti nama_resmi master sekolah asal.
     */
    public function syncAllMappedToSiswa(): int
    {
        $aliases = SpmbSekolahAsalAlias::with('sekolahAsal')->get();
        $totalUpdated = 0;

        foreach ($aliases as $alias) {
            $master = $alias->sekolahAsal;
            if (! $master || strcasecmp(trim($alias->alias_raw), trim($master->nama_resmi)) === 0) {
                continue;
            }

            // Simpan sekolah_asal_raw jika belum ada
            Siswa::where('sekolah_asal', $alias->alias_raw)
                ->where(function ($q) {
                    $q->whereNull('sekolah_asal_raw')->orWhere('sekolah_asal_raw', '');
                })
                ->update(['sekolah_asal_raw' => $alias->alias_raw]);

            // Update ke nama resmi
            $updated = Siswa::where(function ($q) use ($alias) {
                $q->where('sekolah_asal_raw', $alias->alias_raw)
                  ->orWhere('sekolah_asal', $alias->alias_raw);
            })
            ->where('sekolah_asal', '!=', $master->nama_resmi)
            ->update(['sekolah_asal' => $master->nama_resmi]);

            $totalUpdated += $updated;
        }

        return $totalUpdated;
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

    /**
     * Sinkronisasikan seluruh data sekolah asal (master & unmapped siswa) dengan Master SMP resmi.
     * Menggunakan strategi multi-level matching (NPSN -> Exact Name -> Alias -> Prefix Alternatif).
     *
     * @return array{matched_masters: int, matched_unmapped_siswa: int, total_affected_students: int}
     */
    public function syncAllWithMasterSmp(): array
    {
        $syncService = app(SpmbSekolahAsalSyncService::class);
        $matchedMasters = 0;
        $matchedUnmappedSiswa = 0;
        $totalAffectedStudents = 0;

        // 1. Sinkronkan SpmbSekolahAsal yang sudah ada ke SpmbMasterSmp
        $allLocalMasters = SpmbSekolahAsal::with('aliases')->get();
        foreach ($allLocalMasters as $local) {
            $matchedMasterSmp = null;

            // Level 1: Match by NPSN jika sudah ada
            if ($local->npsn) {
                $matchedMasterSmp = SpmbMasterSmp::where('npsn', $local->npsn)->first();
            }

            // Level 2: Match by exact name (case-insensitive)
            if (! $matchedMasterSmp) {
                $matchedMasterSmp = SpmbMasterSmp::whereRaw('LOWER(nama) = ?', [strtolower(trim($local->nama_resmi))])->first();
            }

            // Level 3: Match by alias
            if (! $matchedMasterSmp) {
                foreach ($local->aliases as $al) {
                    $m = SpmbMasterSmp::whereRaw('LOWER(nama) = ?', [strtolower(trim($al->alias_raw))])->first();
                    if ($m) {
                        $matchedMasterSmp = $m;
                        break;
                    }
                }
            }

            // Level 4: Match by alternative abbreviation (SMPN <-> SMP Negeri, MTsN <-> MTs Negeri)
            if (! $matchedMasterSmp) {
                $alt = $syncService->getAlternativeName($local->nama_resmi);
                if ($alt) {
                    $matchedMasterSmp = SpmbMasterSmp::whereRaw('LOWER(nama) = ?', [strtolower(trim($alt))])->first();
                }
            }

            if ($matchedMasterSmp) {
                $this->linkMasterSmpToSekolahAsal($matchedMasterSmp->id, $local->id);
                $matchedMasters++;
            }
        }

        // 2. Cocokkan siswa yang berstatus unmapped
        $unmapped = $this->getUniqueSchoolStrings('', 'unmapped');
        foreach ($unmapped as $item) {
            $raw = trim($item->raw_name);
            $matchedMasterSmp = null;

            // Level 2: Exact name match
            $matchedMasterSmp = SpmbMasterSmp::whereRaw('LOWER(nama) = ?', [strtolower($raw)])->first();

            // Level 4: Prefix abbreviation variation
            if (! $matchedMasterSmp) {
                $alt = $syncService->getAlternativeName($raw);
                if ($alt) {
                    $matchedMasterSmp = SpmbMasterSmp::whereRaw('LOWER(nama) = ?', [strtolower(trim($alt))])->first();
                }
            }

            if ($matchedMasterSmp) {
                $mergeRes = $this->mergeSchoolStrings(
                    [$raw],
                    $matchedMasterSmp->nama,
                    true,
                    true,
                    null,
                    $matchedMasterSmp->id
                );
                $matchedUnmappedSiswa++;
                $totalAffectedStudents += (int) ($mergeRes['affected_students'] ?? 0);
            }
        }

        // 3. Update tiering seluruh master sekolah asal
        $syncService->updateAllTiers();

        return [
            'matched_masters' => $matchedMasters,
            'matched_unmapped_siswa' => $matchedUnmappedSiswa,
            'total_affected_students' => $totalAffectedStudents,
        ];
    }
}
