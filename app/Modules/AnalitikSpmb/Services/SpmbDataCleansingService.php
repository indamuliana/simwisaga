<?php

namespace App\Modules\AnalitikSpmb\Services;

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
        ?int $targetMasterId = null
    ): array {
        $targetName = trim($targetName);
        if (empty($targetName)) {
            throw new \InvalidArgumentException('Nama sekolah target tidak boleh kosong.');
        }

        $sourceNames = array_values(array_unique(array_filter(array_map('trim', $sourceNames))));
        if (empty($sourceNames)) {
            throw new \InvalidArgumentException('Daftar sekolah sumber tidak boleh kosong.');
        }

        return DB::transaction(function () use ($sourceNames, $targetName, $updateSiswa, $createAlias, $targetMasterId) {
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
}
