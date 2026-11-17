<?php

namespace App\Modules\Kesiswaan\Services;

use App\Modules\AnalitikSpmb\Models\MasterWilayah;
use App\Modules\AnalitikSpmb\Models\SpmbMasterSmp;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PemetaanAlamatService
{
    /**
     * Bersihkan teks kelurahan dari prefix awalan yang sering salah input
     */
    public function cleanKelurahan(?string $raw): string
    {
        if (empty($raw)) {
            return '';
        }

        $cleaned = trim(preg_replace('/\s+/', ' ', $raw));
        $cleaned = preg_replace('/^(desa\/kel\.|desa\/kel|desa\s+kel\.|desa|kelurahan|kel\.|kp\.|kampung)\s+/i', '', $cleaned);

        return Str::title(trim($cleaned));
    }

    /**
     * Bersihkan teks kecamatan dari prefix 'Kec.' atau 'Kota [Nama] [Arah]'
     */
    public function cleanKecamatan(?string $raw): string
    {
        if (empty($raw)) {
            return '';
        }

        $cleaned = trim(preg_replace('/\s+/', ' ', $raw));
        $cleaned = preg_replace('/^(kecamatan|kec\.)\s+/i', '', $cleaned);

        // Jika terisi "Kota Bogor Utara" atau sejenisnya, sesuaikan dengan nama resmi Kemendagri ("Bogor Utara")
        if (preg_match('/^kota\s+(bogor|bandung|cirebon|sukabumi|tasikmalaya|bekasi)\s+(utara|selatan|timur|barat|tengah)$/i', $cleaned, $m)) {
            $cleaned = Str::title($m[1]) . ' ' . Str::title($m[2]);
        }

        return Str::title(trim($cleaned));
    }

    /**
     * Standarisasikan penulisan Kabupaten/Kota
     */
    public function standardizeKabupatenKota(?string $raw): string
    {
        if (empty($raw)) {
            return '';
        }

        $cleaned = trim(preg_replace('/\s+/', ' ', $raw));
        $lower = strtolower($cleaned);

        // Kasus Garut -> Kab. Garut
        if ($lower === 'garut' || $lower === 'kab garut' || $lower === 'kabupaten garut') {
            return 'Kab. Garut';
        }

        // Awalan Kabupaten -> Kab.
        if (preg_match('/^kabupaten\s+(.*)$/i', $cleaned, $m)) {
            return 'Kab. ' . Str::title(trim($m[1]));
        }

        if (preg_match('/^kab\s+([^\.].*)$/i', $cleaned, $m)) {
            return 'Kab. ' . Str::title(trim($m[1]));
        }

        return Str::title($cleaned);
    }

    /**
     * Analisis satu siswa: deteksi masalah dan rumuskan saran perbaikan standar Kemendagri
     */
    public function analyzeSiswa(Siswa $siswa): array
    {
        $rawProvinsi = trim((string) $siswa->provinsi);
        $rawKabupaten = trim((string) $siswa->kabupaten_kota);
        $rawKecamatan = trim((string) $siswa->kecamatan);
        $rawKelurahan = trim((string) $siswa->kelurahan);
        $rawAlamat = trim((string) $siswa->alamat);
        $rawSekolah = trim((string) $siswa->sekolah_asal);

        $combined = strtolower("{$rawAlamat} {$rawKelurahan} {$rawKecamatan} {$rawSekolah}");

        // 1. Deteksi Luar Negeri (Malaysia / Sabah / CLC / Tawau)
        if (preg_match('/\b(tawau|sandakan|kota kinabalu|lahad datu|keningau|beaufort|clc|lc|sabah|malaysia)\b/i', $combined)) {
            $suggestedKab = 'Tawau';
            if (str_contains($combined, 'sandakan')) {
                $suggestedKab = 'Sandakan';
            } elseif (str_contains($combined, 'kota kinabalu')) {
                $suggestedKab = 'Kota Kinabalu';
            } elseif (str_contains($combined, 'beaufort')) {
                $suggestedKab = 'Beaufort';
            } elseif (str_contains($combined, 'lahad datu')) {
                $suggestedKab = 'Lahad Datu';
            } elseif (str_contains($combined, 'keningau')) {
                $suggestedKab = 'Keningau';
            }

            $isAlreadyCorrect = ($siswa->negara === 'Malaysia' && $siswa->provinsi === 'Sabah' && $siswa->kabupaten_kota === $suggestedKab);

            return [
                'siswa_id' => $siswa->id,
                'status' => $isAlreadyCorrect ? 'sudah_valid' : 'luar_negeri',
                'status_label' => $isAlreadyCorrect ? 'Valid (Luar Negeri)' : 'Siswa Luar Negeri (Sabah/Malaysia)',
                'is_anomali' => ! $isAlreadyCorrect,
                'confidence' => 'pasti',
                'issues' => $isAlreadyCorrect ? [] : ['Terdeteksi alamat dan/atau sekolah asal di Sabah, Malaysia'],
                'current' => [
                    'kelurahan' => $rawKelurahan,
                    'kecamatan' => $rawKecamatan,
                    'kabupaten_kota' => $rawKabupaten,
                    'provinsi' => $rawProvinsi,
                    'negara' => $siswa->negara ?: 'Indonesia',
                ],
                'suggested' => [
                    'kelurahan' => $this->cleanKelurahan($rawKelurahan) ?: $rawKelurahan,
                    'kecamatan' => $this->cleanKecamatan($rawKecamatan) ?: $suggestedKab,
                    'kabupaten_kota' => $suggestedKab,
                    'provinsi' => 'Sabah',
                    'negara' => 'Malaysia',
                ],
            ];
        }

        // 2. Analisis Data Dalam Negeri
        $cleanKel = $this->cleanKelurahan($rawKelurahan);
        $cleanKec = $this->cleanKecamatan($rawKecamatan);
        $stdKab = $this->standardizeKabupatenKota($rawKabupaten);

        $issues = [];
        $hasDirtyPrefix = false;

        if (! empty($rawKelurahan) && $rawKelurahan !== $cleanKel) {
            $hasDirtyPrefix = true;
            $issues[] = "Awalan pada kelurahan ('{$rawKelurahan}') perlu dibersihkan.";
        }

        if (! empty($rawKecamatan) && $rawKecamatan !== $cleanKec) {
            $hasDirtyPrefix = true;
            $issues[] = "Awalan pada kecamatan ('{$rawKecamatan}') perlu dibersihkan.";
        }

        $hasEmptyField = empty($rawProvinsi) || empty($rawKabupaten) || empty($rawKecamatan) || empty($rawKelurahan);
        if (empty($rawProvinsi)) {
            $issues[] = 'Provinsi belum terisi.';
        }
        if (empty($rawKabupaten)) {
            $issues[] = 'Kabupaten/Kota belum terisi.';
        }
        if (empty($rawKecamatan)) {
            $issues[] = 'Kecamatan belum terisi.';
        }

        // 3. Pencocokan ke Master Wilayah Kemendagri
        $matchedMaster = null;
        $confidence = 'perlu_cek';
        $suggestedProv = $rawProvinsi ?: 'Jawa Barat';
        $suggestedKab = $stdKab ?: 'Kab. Garut';
        $suggestedKec = $cleanKec;
        $suggestedKel = $cleanKel;

        // Strategi A: Cari berdasarkan Desa/Kelurahan + Kecamatan
        if (! empty($cleanKel) && ! empty($cleanKec)) {
            $kelMatches = MasterWilayah::where('level', 4)
                ->where('nama', 'like', $cleanKel)
                ->get();

            foreach ($kelMatches as $km) {
                $kecParent = MasterWilayah::where('kode', $km->parent_kode)->first();
                if ($kecParent && strcasecmp($kecParent->nama, $cleanKec) === 0) {
                    $kabParent = MasterWilayah::where('kode', $kecParent->parent_kode)->first();
                    $provParent = $kabParent ? MasterWilayah::where('kode', $kabParent->parent_kode)->first() : null;

                    if ($kabParent && $provParent) {
                        $suggestedKel = $km->nama;
                        $suggestedKec = $kecParent->nama;
                        $suggestedKab = $kabParent->nama;
                        $suggestedProv = $provParent->nama;
                        $confidence = 'pasti';
                        $matchedMaster = true;
                        break;
                    }
                }
            }
        }

        // Strategi B: Cari berdasarkan Kecamatan saja jika Strategi A belum menemukan
        if (! $matchedMaster && ! empty($cleanKec)) {
            $kecMatches = MasterWilayah::where('level', 3)
                ->where('nama', 'like', $cleanKec)
                ->get();

            // Jika ada kecocokan di Jawa Barat, prioritaskan Jawa Barat
            $prioritizedKec = null;
            if ($kecMatches->count() > 1) {
                foreach ($kecMatches as $km) {
                    $kabParent = MasterWilayah::where('kode', $km->parent_kode)->first();
                    $provParent = $kabParent ? MasterWilayah::where('kode', $kabParent->parent_kode)->first() : null;
                    if ($provParent && $provParent->nama === 'Jawa Barat') {
                        $prioritizedKec = $km;
                        break;
                    }
                }
            } else {
                $prioritizedKec = $kecMatches->first();
            }

            if ($prioritizedKec) {
                $kabParent = MasterWilayah::where('kode', $prioritizedKec->parent_kode)->first();
                $provParent = $kabParent ? MasterWilayah::where('kode', $kabParent->parent_kode)->first() : null;

                if ($kabParent && $provParent) {
                    $suggestedKec = $prioritizedKec->nama;
                    $suggestedKab = $kabParent->nama;
                    $suggestedProv = $provParent->nama;
                    $confidence = ($confidence === 'pasti') ? 'pasti' : 'tinggi';
                    $matchedMaster = true;
                }
            }
        }

        // Cek Ketidaksinkronan Kabupaten
        $isKabMismatch = false;
        if (! empty($stdKab) && ! empty($suggestedKab) && strcasecmp($stdKab, $suggestedKab) !== 0) {
            $isKabMismatch = true;
            $issues[] = "Kabupaten tidak sinkron: saat ini '{$rawKabupaten}', berdasarkan Master Kemendagri adalah '{$suggestedKab}'.";
        }

        // Tentukan Kategori Status Akhir
        if ($isKabMismatch) {
            $status = 'kabupaten_tidak_sinkron';
            $statusLabel = 'Kabupaten Tidak Sinkron';
        } elseif ($hasEmptyField) {
            $status = 'data_kosong';
            $statusLabel = 'Data Belum Lengkap';
        } elseif ($hasDirtyPrefix) {
            $status = 'teks_kotor';
            $statusLabel = 'Format Teks Kotor / Prefix';
        } elseif (empty($issues)) {
            $status = 'sudah_valid';
            $statusLabel = 'Sesuai Master Kemendagri';
        } else {
            $status = 'perlu_cek';
            $statusLabel = 'Perlu Dicek';
        }

        $isAnomali = ($status !== 'sudah_valid');

        return [
            'siswa_id' => $siswa->id,
            'status' => $status,
            'status_label' => $statusLabel,
            'is_anomali' => $isAnomali,
            'confidence' => $confidence,
            'issues' => $issues,
            'current' => [
                'kelurahan' => $rawKelurahan,
                'kecamatan' => $rawKecamatan,
                'kabupaten_kota' => $rawKabupaten,
                'provinsi' => $rawProvinsi,
                'negara' => $siswa->negara ?: 'Indonesia',
            ],
            'suggested' => [
                'kelurahan' => $suggestedKel,
                'kecamatan' => $suggestedKec,
                'kabupaten_kota' => $suggestedKab,
                'provinsi' => $suggestedProv,
                'negara' => 'Indonesia',
            ],
        ];
    }

    /**
     * Terapkan saran perbaikan ke satu siswa
     */
    public function applySuggestion(Siswa $siswa, ?array $overrideData = null): bool
    {
        if ($overrideData) {
            $data = $overrideData;
        } else {
            $analysis = $this->analyzeSiswa($siswa);
            $data = $analysis['suggested'];
        }

        return (bool) $siswa->update([
            'kelurahan' => $data['kelurahan'] ?? $siswa->kelurahan,
            'kecamatan' => $data['kecamatan'] ?? $siswa->kecamatan,
            'kabupaten_kota' => $data['kabupaten_kota'] ?? $siswa->kabupaten_kota,
            'provinsi' => $data['provinsi'] ?? $siswa->provinsi,
            'negara' => $data['negara'] ?? ($siswa->negara ?: 'Indonesia'),
        ]);
    }

    /**
     * Terapkan saran perbaikan secara massal berdasarkan daftar Siswa ID
     */
    public function applyBatchSuggestions(array $siswaIds): int
    {
        if (empty($siswaIds)) {
            return 0;
        }

        $siswas = Siswa::whereIn('id', $siswaIds)->get();
        $updatedCount = 0;

        DB::transaction(function () use ($siswas, &$updatedCount) {
            foreach ($siswas as $siswa) {
                $analysis = $this->analyzeSiswa($siswa);
                if ($analysis['is_anomali']) {
                    $this->applySuggestion($siswa, $analysis['suggested']);
                    $updatedCount++;
                }
            }
        });

        return $updatedCount;
    }

    /**
     * Ambil daftar 38 Provinsi resmi dari Master Wilayah
     */
    public function getProvincesList(): array
    {
        return MasterWilayah::where('level', 1)
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();
    }

    /**
     * Ambil daftar Kabupaten/Kota berdasarkan nama Provinsi
     */
    public function getKabupatenList(?string $provinsiNama): array
    {
        if (empty($provinsiNama)) {
            return MasterWilayah::where('level', 2)
                ->orderBy('nama')
                ->pluck('nama')
                ->toArray();
        }

        $prov = MasterWilayah::where('level', 1)->where('nama', $provinsiNama)->first();
        if (! $prov) {
            return [];
        }

        return MasterWilayah::where('level', 2)
            ->where('parent_kode', $prov->kode)
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();
    }

    /**
     * Ambil daftar Kecamatan berdasarkan nama Kabupaten
     */
    public function getKecamatanList(?string $kabupatenNama, ?string $provinsiNama = null): array
    {
        if (empty($kabupatenNama)) {
            return [];
        }

        $query = MasterWilayah::where('level', 2)->where('nama', $kabupatenNama);
        if ($provinsiNama) {
            $prov = MasterWilayah::where('level', 1)->where('nama', $provinsiNama)->first();
            if ($prov) {
                $query->where('parent_kode', $prov->kode);
            }
        }
        $kab = $query->first();

        if (! $kab) {
            return [];
        }

        return MasterWilayah::where('level', 3)
            ->where('parent_kode', $kab->kode)
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();
    }

    /**
     * Ambil daftar Desa/Kelurahan berdasarkan nama Kecamatan
     */
    public function getDesaList(?string $kecamatanNama, ?string $kabupatenNama = null): array
    {
        if (empty($kecamatanNama)) {
            return [];
        }

        $query = MasterWilayah::where('level', 3)->where('nama', $kecamatanNama);
        if ($kabupatenNama) {
            $kab = MasterWilayah::where('level', 2)->where('nama', $kabupatenNama)->first();
            if ($kab) {
                $query->where('parent_kode', $kab->kode);
            }
        }
        $kec = $query->first();

        if (! $kec) {
            return [];
        }

        return MasterWilayah::where('level', 4)
            ->where('parent_kode', $kec->kode)
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();
    }
}
