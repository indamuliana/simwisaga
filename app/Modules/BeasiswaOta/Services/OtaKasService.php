<?php

namespace App\Modules\BeasiswaOta\Services;

use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\AlokasiDetail;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\KasMutasi;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;

class OtaKasService
{
    /**
     * Hitung sisa saldo kas insidental saat ini.
     */
    public static function getSaldoInsidental(): float
    {
        $masuk = (float) KasMutasi::where('pos_kas', 'insidental')->where('tipe', 'masuk')->sum('nominal');
        $keluar = (float) KasMutasi::where('pos_kas', 'insidental')->where('tipe', 'keluar')->sum('nominal');
        return $masuk - $keluar;
    }

    /**
     * Hitung sisa saldo kas kelompok tertentu saat ini.
     */
    public static function getSaldoKelompok(int $kelompokId): float
    {
        $masuk = (float) KasMutasi::where('pos_kas', 'kelompok')->where('kelompok_id', $kelompokId)->where('tipe', 'masuk')->sum('nominal');
        $keluar = (float) KasMutasi::where('pos_kas', 'kelompok')->where('kelompok_id', $kelompokId)->where('tipe', 'keluar')->sum('nominal');
        return $masuk - $keluar;
    }

    /**
     * Hitung total saldo kas OTA secara keseluruhan.
     */
    public static function getRingkasanKas(): array
    {
        $saldoInsidental = static::getSaldoInsidental();

        $kelompokMasuk = (float) KasMutasi::where('pos_kas', 'kelompok')->where('tipe', 'masuk')->sum('nominal');
        $kelompokKeluar = (float) KasMutasi::where('pos_kas', 'kelompok')->where('tipe', 'keluar')->sum('nominal');
        $saldoKelompok = $kelompokMasuk - $kelompokKeluar;

        return [
            'saldo_kelompok' => $saldoKelompok,
            'saldo_insidental' => $saldoInsidental,
            'total_kas' => $saldoKelompok + $saldoInsidental,
        ];
    }

    /**
     * Catat donasi uang masuk.
     */
    public static function catatDonasiMasuk(
        Donatur $donatur,
        float $nominal,
        string $tanggal,
        ?int $kelompokId = null,
        ?string $keterangan = null,
        ?int $userId = null,
        ?int $tahunAjaranId = null,
        ?array $periodeBulan = null,
        int $jumlahBulan = 1,
        ?string $keteranganPeriode = null
    ): DonasiMasuk {
        return DB::transaction(function () use ($donatur, $nominal, $tanggal, $kelompokId, $keterangan, $userId, $tahunAjaranId, $periodeBulan, $jumlahBulan, $keteranganPeriode) {
            $isRutin = ($donatur->tipe === 'rutin' && $kelompokId);
            $posKas = $isRutin ? 'kelompok' : 'insidental';
            $targetKelompokId = $isRutin ? $kelompokId : null;

            $donasi = DonasiMasuk::create([
                'donatur_id' => $donatur->id,
                'kelompok_id' => $targetKelompokId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'tipe_donatur' => $donatur->tipe,
                'tanggal_donasi' => $tanggal,
                'nominal' => $nominal,
                'periode_bulan' => $periodeBulan,
                'jumlah_bulan' => $jumlahBulan,
                'keterangan_periode' => $keteranganPeriode,
                'keterangan' => $keterangan,
                'created_by' => $userId,
            ]);

            // Hitung saldo pos terkini
            $saldoSebelumnya = $posKas === 'kelompok' 
                ? static::getSaldoKelompok($targetKelompokId)
                : static::getSaldoInsidental();

            $periodeDesc = $keteranganPeriode ? " ({$keteranganPeriode})" : "";

            KasMutasi::create([
                'tanggal' => $tanggal,
                'tipe' => 'masuk',
                'pos_kas' => $posKas,
                'kelompok_id' => $targetKelompokId,
                'referensi_tipe' => 'donasi',
                'referensi_id' => $donasi->id,
                'nominal' => $nominal,
                'saldo_pos' => $saldoSebelumnya + $nominal,
                'deskripsi' => "Penerimaan donasi dari {$donatur->nama}{$periodeDesc} (" . ($targetKelompokId ? "Kelompok #{$targetKelompokId}" : "Kas Bebas Insidental") . ")",
            ]);

            return $donasi;
        });
    }

    /**
     * Catat donasi masuk secara kolektif per kelompok via Koordinator.
     */
    public static function catatDonasiKolektifKelompok(
        Kelompok $kelompok,
        string $koordinatorNama,
        array $items,
        string $tanggal,
        ?string $keteranganGlobal = null,
        ?int $userId = null,
        ?int $tahunAjaranId = null
    ): array {
        return DB::transaction(function () use ($kelompok, $koordinatorNama, $items, $tanggal, $keteranganGlobal, $userId, $tahunAjaranId) {
            $datePrefix = date('Ymd', strtotime($tanggal));
            $randomSuffix = strtoupper(bin2hex(random_bytes(2)));
            $batchKode = sprintf('BATCH-%s-%s-%s', $kelompok->kode_kelompok, $datePrefix, $randomSuffix);

            $totalBatch = 0;
            $createdDonasis = [];

            foreach ($items as $item) {
                $donaturId = (int) ($item['donatur_id'] ?? 0);
                $nom = (float) ($item['nominal'] ?? 0);
                if ($donaturId <= 0 || $nom <= 0) {
                    continue;
                }

                $donatur = Donatur::find($donaturId);
                if (!$donatur) {
                    continue;
                }

                $periodeBulan = $item['periode_bulan'] ?? null;
                $jumlahBulan = (int) ($item['jumlah_bulan'] ?? (is_array($periodeBulan) ? count($periodeBulan) : 1));
                $keteranganPeriode = $item['keterangan_periode'] ?? null;

                $ket = !empty($item['keterangan']) 
                    ? $item['keterangan'] 
                    : ($keteranganGlobal ?: "Setoran kolektif {$kelompok->nama_kelompok} via Koordinator {$koordinatorNama}");

                $donasi = DonasiMasuk::create([
                    'batch_kode' => $batchKode,
                    'penyetor_nama' => $koordinatorNama,
                    'is_kolektif' => true,
                    'donatur_id' => $donatur->id,
                    'kelompok_id' => $kelompok->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'tipe_donatur' => 'rutin',
                    'tanggal_donasi' => $tanggal,
                    'nominal' => $nom,
                    'periode_bulan' => $periodeBulan,
                    'jumlah_bulan' => $jumlahBulan,
                    'keterangan_periode' => $keteranganPeriode,
                    'keterangan' => $ket,
                    'created_by' => $userId,
                ]);

                $totalBatch += $nom;
                $createdDonasis[] = $donasi;
            }

            if ($totalBatch > 0) {
                $saldoSebelumnya = static::getSaldoKelompok($kelompok->id);
                KasMutasi::create([
                    'tanggal' => $tanggal,
                    'tipe' => 'masuk',
                    'pos_kas' => 'kelompok',
                    'kelompok_id' => $kelompok->id,
                    'referensi_tipe' => 'donasi_kolektif',
                    'referensi_id' => $createdDonasis[0]->id,
                    'nominal' => $totalBatch,
                    'saldo_pos' => $saldoSebelumnya + $totalBatch,
                    'deskripsi' => "Setoran kolektif {$kelompok->nama_kelompok} (" . count($createdDonasis) . " donatur) via Koordinator {$koordinatorNama}",
                ]);
            }

            return [
                'batch_kode' => $batchKode,
                'total_nominal' => $totalBatch,
                'count' => count($createdDonasis),
                'donasis' => $createdDonasis,
            ];
        });
    }

    /**
     * Catat penyaluran beasiswa 6 bulanan rutin dari kas kelompok.
     */
    public static function catatAlokasiRutin(
        Kelompok $kelompok,
        Siswa $siswa,
        array $details,
        float $totalNominal,
        float $surplusDialihkan,
        string $tanggal,
        ?int $semesterId = null,
        ?int $tahunAjaranId = null,
        ?string $catatan = null,
        ?int $userId = null
    ): Alokasi {
        return DB::transaction(function () use (
            $kelompok, $siswa, $details, $totalNominal, $surplusDialihkan,
            $tanggal, $semesterId, $tahunAjaranId, $catatan, $userId
        ) {
            // Lock baris kelompok untuk mencegah concurrent check-then-write race conditions
            Kelompok::where('id', $kelompok->id)->lockForUpdate()->first();

            $saldoKelompok = static::getSaldoKelompok($kelompok->id);
            $totalKeluar = $totalNominal + $surplusDialihkan;
            if ($saldoKelompok < $totalKeluar) {
                throw new \InvalidArgumentException("Saldo kas kelompok #{$kelompok->id} ({$kelompok->nama_kelompok}) tidak mencukupi (Saldo: Rp " . number_format($saldoKelompok, 0, ',', '.') . ", Dibutuhkan: Rp " . number_format($totalKeluar, 0, ',', '.') . ").");
            }

            $alokasi = Alokasi::create([
                'tipe_alokasi' => 'rutin_semester',
                'kelompok_id' => $kelompok->id,
                'siswa_id' => $siswa->id,
                'semester_id' => $semesterId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'tanggal_alokasi' => $tanggal,
                'total_nominal' => $totalNominal,
                'surplus_dialihkan' => $surplusDialihkan,
                'catatan' => $catatan,
                'created_by' => $userId,
            ]);

            foreach ($details as $item) {
                AlokasiDetail::create([
                    'alokasi_id' => $alokasi->id,
                    'komponen_biaya_id' => $item['komponen_biaya_id'] ?? null,
                    'jenis_komponen' => $item['jenis_komponen'] ?? 'lainnya',
                    'nama_komponen' => $item['nama_komponen'] ?? 'Komponen Biaya',
                    'bulan' => $item['bulan'] ?? null,
                    'tahun' => $item['tahun'] ?? null,
                    'nominal' => $item['nominal'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            // 1. Mutasi Kas Keluar dari Kelompok untuk Alokasi Siswa
            KasMutasi::create([
                'tanggal' => $tanggal,
                'tipe' => 'keluar',
                'pos_kas' => 'kelompok',
                'kelompok_id' => $kelompok->id,
                'referensi_tipe' => 'alokasi',
                'referensi_id' => $alokasi->id,
                'nominal' => $totalNominal,
                'saldo_pos' => $saldoKelompok - $totalNominal,
                'deskripsi' => "Penyaluran beasiswa 6 bulanan untuk siswa {$siswa->nama} ({$kelompok->nama_kelompok})",
            ]);

            // 2. Jika ada surplus yang dialihkan ke kas insidental
            if ($surplusDialihkan > 0) {
                // Keluar dari kas kelompok
                $saldoKelompokAfter = static::getSaldoKelompok($kelompok->id);
                KasMutasi::create([
                    'tanggal' => $tanggal,
                    'tipe' => 'keluar',
                    'pos_kas' => 'kelompok',
                    'kelompok_id' => $kelompok->id,
                    'referensi_tipe' => 'surplus_transfer',
                    'referensi_id' => $alokasi->id,
                    'nominal' => $surplusDialihkan,
                    'saldo_pos' => $saldoKelompokAfter - $surplusDialihkan,
                    'deskripsi' => "Pengalihan surplus dana {$kelompok->nama_kelompok} ke Kas Insidental",
                ]);

                // Masuk ke kas insidental
                $saldoInsidental = static::getSaldoInsidental();
                KasMutasi::create([
                    'tanggal' => $tanggal,
                    'tipe' => 'masuk',
                    'pos_kas' => 'insidental',
                    'kelompok_id' => null,
                    'referensi_tipe' => 'surplus_transfer',
                    'referensi_id' => $alokasi->id,
                    'nominal' => $surplusDialihkan,
                    'saldo_pos' => $saldoInsidental + $surplusDialihkan,
                    'deskripsi' => "Penerimaan surplus beasiswa dari {$kelompok->nama_kelompok}",
                ]);
            }

            return $alokasi;
        });
    }

    /**
     * Catat penyaluran beasiswa insidental fleksibel ke siswa manapun.
     */
    public static function catatAlokasiInsidental(
        Siswa $siswa,
        array $details,
        float $totalNominal,
        string $tanggal,
        ?int $semesterId = null,
        ?int $tahunAjaranId = null,
        ?string $catatan = null,
        ?int $userId = null
    ): Alokasi {
        return DB::transaction(function () use (
            $siswa, $details, $totalNominal,
            $tanggal, $semesterId, $tahunAjaranId, $catatan, $userId
        ) {
            $saldoInsidental = static::getSaldoInsidental();
            if ($saldoInsidental < $totalNominal) {
                throw new \InvalidArgumentException("Saldo kas insidental tidak mencukupi (Saldo: Rp " . number_format($saldoInsidental, 0, ',', '.') . ", Dibutuhkan: Rp " . number_format($totalNominal, 0, ',', '.') . ").");
            }

            $alokasi = Alokasi::create([
                'tipe_alokasi' => 'insidental',
                'kelompok_id' => null,
                'siswa_id' => $siswa->id,
                'semester_id' => $semesterId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'tanggal_alokasi' => $tanggal,
                'total_nominal' => $totalNominal,
                'surplus_dialihkan' => 0.00,
                'catatan' => $catatan,
                'created_by' => $userId,
            ]);

            foreach ($details as $item) {
                AlokasiDetail::create([
                    'alokasi_id' => $alokasi->id,
                    'komponen_biaya_id' => $item['komponen_biaya_id'] ?? null,
                    'jenis_komponen' => $item['jenis_komponen'] ?? 'lainnya',
                    'nama_komponen' => $item['nama_komponen'] ?? 'Bantuan Dana Insidental',
                    'bulan' => $item['bulan'] ?? null,
                    'tahun' => $item['tahun'] ?? null,
                    'nominal' => $item['nominal'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            // Potong Kas Insidental
            $saldoInsidental = static::getSaldoInsidental();
            KasMutasi::create([
                'tanggal' => $tanggal,
                'tipe' => 'keluar',
                'pos_kas' => 'insidental',
                'kelompok_id' => null,
                'referensi_tipe' => 'alokasi',
                'referensi_id' => $alokasi->id,
                'nominal' => $totalNominal,
                'saldo_pos' => $saldoInsidental - $totalNominal,
                'deskripsi' => "Penyaluran bantuan dana insidental untuk siswa {$siswa->nama}",
            ]);

            return $alokasi;
        });
    }
}
