<?php

namespace App\Modules\AnalitikSpmb\Services;

use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsal;
use App\Modules\AnalitikSpmb\Models\SpmbSekolahAsalAlias;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpmbSekolahAsalSyncService
{
    /**
     * Sinkronkan seluruh data sekolah asal yang ada di tabel siswas ke master & alias.
     */
    public function syncFromSiswaData(): array
    {
        $rawSchools = Siswa::query()
            ->whereNotNull('sekolah_asal')
            ->where('sekolah_asal', '!=', '')
            ->select('sekolah_asal', DB::raw('count(*) as total_siswa'))
            ->groupBy('sekolah_asal')
            ->get();

        $scannedCount = $rawSchools->count();
        $newMasters = 0;
        $newAliases = 0;

        foreach ($rawSchools as $item) {
            $rawName = trim($item->sekolah_asal);
            if (empty($rawName)) {
                continue;
            }

            // Cek apakah alias ini sudah terdaftar
            $existingAlias = SpmbSekolahAsalAlias::where('alias_raw', $rawName)->first();
            if ($existingAlias) {
                continue;
            }

            // Cek apakah persis cocok dengan nama_resmi di master
            $existingMaster = SpmbSekolahAsal::where('nama_resmi', $rawName)->first();
            if ($existingMaster) {
                SpmbSekolahAsalAlias::create([
                    'sekolah_asal_id' => $existingMaster->id,
                    'alias_raw' => $rawName,
                ]);
                $newAliases++;
                continue;
            }

            // Normalisasi nama untuk pencarian cerdas
            $normalizedName = $this->normalizeSchoolName($rawName);

            // Cek apakah sudah ada master dengan nama normalisasi yang sama
            $matchedMaster = SpmbSekolahAsal::where(DB::raw('LOWER(nama_resmi)'), strtolower($normalizedName))->first();

            if (! $matchedMaster) {
                // Cek kemungkinan variasi lain (misal SMP Negeri 1 vs SMPN 1)
                $alternativeName = $this->getAlternativeName($normalizedName);
                if ($alternativeName) {
                    $matchedMaster = SpmbSekolahAsal::where(DB::raw('LOWER(nama_resmi)'), strtolower($alternativeName))->first();
                }
            }

            if ($matchedMaster) {
                SpmbSekolahAsalAlias::create([
                    'sekolah_asal_id' => $matchedMaster->id,
                    'alias_raw' => $rawName,
                ]);
                $newAliases++;
            } else {
                // Buat Master Baru
                $meta = $this->detectMetadata($rawName);
                $tier = $this->calculateTier($item->total_siswa);

                $master = SpmbSekolahAsal::create([
                    'nama_resmi' => $normalizedName,
                    'jenis' => $meta['jenis'],
                    'status_sekolah' => $meta['status_sekolah'],
                    'kabupaten_kota' => 'Garut',
                    'tier' => $tier,
                ]);
                $newMasters++;

                // Daftarkan nama mentah sebagai alias
                SpmbSekolahAsalAlias::create([
                    'sekolah_asal_id' => $master->id,
                    'alias_raw' => $rawName,
                ]);
                $newAliases++;

                // Jika nama mentah berbeda dari nama resmi normalisasi, tambahkan juga nama resmi sebagai alias
                if (strtolower($rawName) !== strtolower($normalizedName)) {
                    SpmbSekolahAsalAlias::firstOrCreate([
                        'sekolah_asal_id' => $master->id,
                        'alias_raw' => $normalizedName,
                    ]);
                }
            }
        }

        $this->updateAllTiers();

        return [
            'total_scanned' => $scannedCount,
            'new_masters' => $newMasters,
            'new_aliases' => $newAliases,
        ];
    }

    /**
     * Normalisasi string nama sekolah (Title Case, perbaikan spasi, singkatan umum)
     */
    public function normalizeSchoolName(string $name): string
    {
        $cleaned = preg_replace('/\s+/', ' ', trim($name));

        // Format umum: SMPN, MTsN, SMP, MTs, dsb.
        $patterns = [
            '/\bsmp\s*n\s*(\d+)/i' => 'SMPN $1',
            '/\bmts\s*n\s*(\d+)/i' => 'MTsN $1',
            '/\bsmp\s+negeri\s+(\d+)/i' => 'SMPN $1',
            '/\bmts\s+negeri\s+(\d+)/i' => 'MTsN $1',
            '/\bsd\s*n\s*(\d+)/i' => 'SDN $1',
        ];

        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $cleaned)) {
                $cleaned = preg_replace($pattern, $replacement, $cleaned);
                break;
            }
        }

        // Ubah ke Title Case tapi pertahankan singkatan utama
        $words = explode(' ', $cleaned);
        $result = [];
        foreach ($words as $word) {
            $upper = strtoupper($word);
            if (in_array($upper, ['SMP', 'SMPN', 'MTS', 'MTSN', 'SMA', 'SMK', 'MA', 'IT', 'SD', 'SDN', 'PLUS', 'GARUT', 'KAB', 'KAB.'])) {
                if ($upper === 'MTS') $result[] = 'MTs';
                elseif ($upper === 'MTSN') $result[] = 'MTsN';
                else $result[] = $upper;
            } else {
                $result[] = Str::ucfirst(strtolower($word));
            }
        }

        return implode(' ', $result);
    }

    /**
     * Menghasilkan bentuk alternatif (misal SMPN 1 <-> SMP Negeri 1)
     */
    protected function getAlternativeName(string $normalized): ?string
    {
        if (preg_match('/^SMPN\s+(\d+)/i', $normalized, $matches)) {
            return preg_replace('/^SMPN\s+(\d+)/i', 'SMP Negeri ' . $matches[1], $normalized);
        }
        if (preg_match('/^SMP\s+Negeri\s+(\d+)/i', $normalized, $matches)) {
            return preg_replace('/^SMP\s+Negeri\s+(\d+)/i', 'SMPN ' . $matches[1], $normalized);
        }
        if (preg_match('/^MTsN\s+(\d+)/i', $normalized, $matches)) {
            return preg_replace('/^MTsN\s+(\d+)/i', 'MTs Negeri ' . $matches[1], $normalized);
        }
        return null;
    }

    /**
     * Deteksi jenis (SMP, MTs, Pesantren, Lainnya) dan status (Negeri, Swasta)
     */
    public function detectMetadata(string $name): array
    {
        $upper = strtoupper($name);
        $jenis = 'SMP';
        $status = 'Swasta';

        if (str_contains($upper, 'MTS') || str_contains($upper, 'TSANAWIYAH')) {
            $jenis = 'MTs';
        } elseif (str_contains($upper, 'PESANTREN') || str_contains($upper, 'PONPES') || str_contains($upper, 'PONTREN')) {
            $jenis = 'Pesantren';
        } elseif (str_contains($upper, 'SMP')) {
            $jenis = 'SMP';
        } else {
            $jenis = 'Lainnya';
        }

        if (str_contains($upper, 'NEGERI') || str_contains($upper, 'SMPN') || str_contains($upper, 'MTSN')) {
            $status = 'Negeri';
        }

        return [
            'jenis' => $jenis,
            'status_sekolah' => $status,
        ];
    }

    /**
     * Hitung tiering feeder
     */
    public function calculateTier(int $totalSiswa): string
    {
        if ($totalSiswa >= 20) {
            return 'Utama';
        } elseif ($totalSiswa >= 8) {
            return 'Potensial';
        }
        return 'Rintisan';
    }

    /**
     * Perbarui tiering semua sekolah master berdasarkan total siswa aktual di sistem
     */
    public function updateAllTiers(): void
    {
        $masters = SpmbSekolahAsal::with('aliases')->get();
        foreach ($masters as $master) {
            $aliases = $master->all_names;
            $total = Siswa::whereIn('sekolah_asal', $aliases)
                ->where('is_active', true)
                ->count();

            $newTier = $this->calculateTier($total);
            if ($master->tier !== $newTier) {
                $master->update(['tier' => $newTier]);
            }
        }
    }

    /**
     * Menggabungkan beberapa sekolah master menjadi satu sekolah master
     */
    public function mergeMasters(int $targetMasterId, array $sourceMasterIds): bool
    {
        return DB::transaction(function () use ($targetMasterId, $sourceMasterIds) {
            $target = SpmbSekolahAsal::findOrFail($targetMasterId);

            foreach ($sourceMasterIds as $sourceId) {
                if ($sourceId == $targetMasterId) {
                    continue;
                }

                $source = SpmbSekolahAsal::find($sourceId);
                if (! $source) {
                    continue;
                }

                // Pindahkan nama resmi source sebagai alias ke target
                SpmbSekolahAsalAlias::firstOrCreate([
                    'sekolah_asal_id' => $target->id,
                    'alias_raw' => $source->nama_resmi,
                ]);

                // Pindahkan seluruh alias yang dimiliki source ke target
                SpmbSekolahAsalAlias::where('sekolah_asal_id', $source->id)
                    ->update(['sekolah_asal_id' => $target->id]);

                // Hapus master lama
                $source->delete();
            }

            // Update tiering target
            $aliases = $target->fresh()->all_names;
            $total = Siswa::whereIn('sekolah_asal', $aliases)
                ->where('is_active', true)
                ->count();
            $target->update(['tier' => $this->calculateTier($total)]);

            return true;
        });
    }
}
