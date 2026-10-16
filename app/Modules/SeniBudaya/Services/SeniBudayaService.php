<?php

namespace App\Modules\SeniBudaya\Services;

use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Models\SeniBudayaPresensi;
use App\Modules\SeniBudaya\Models\SeniBudayaPresensiDetail;
use App\Modules\SeniBudaya\Models\SeniBudayaTp;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SeniBudayaService
{
    /**
     * Menghitung ulang akumulasi kehadiran dan persentase kehadiran siswa
     */
    public function recalculateAttendance(int $seniBudayaId, int $semesterId): void
    {
        $totalSessions = SeniBudayaPresensi::where('seni_budaya_id', $seniBudayaId)
            ->where('semester_id', $semesterId)
            ->count();

        $anggotas = SeniBudayaAnggota::where('seni_budaya_id', $seniBudayaId)
            ->where('semester_id', $semesterId)
            ->get();

        foreach ($anggotas as $anggota) {
            if ($totalSessions > 0) {
                $hadirCount = SeniBudayaPresensiDetail::whereHas('presensi', function ($q) use ($seniBudayaId, $semesterId) {
                    $q->where('seni_budaya_id', $seniBudayaId)
                        ->where('semester_id', $semesterId);
                })
                    ->where('siswa_id', $anggota->siswa_id)
                    ->where('status', 'Hadir')
                    ->count();

                $persentase = round(($hadirCount / $totalSessions) * 100, 2);
                $nilaiPresensi = $persentase;
            } else {
                $hadirCount = 0;
                $persentase = 100.00; // Default jika belum ada pertemuan dimulai
                $nilaiPresensi = 100.00;
            }

            $anggota->update([
                'total_hadir' => $hadirCount,
                'total_pertemuan' => $totalSessions,
                'persentase_kehadiran' => $persentase,
                'nilai_presensi' => $nilaiPresensi,
            ]);
        }
    }

    /**
     * Hitung nilai akhir berdasarkan bobot kehadiran dan bobot asesmen
     */
    public function calculateGrade(
        float $nilaiPresensi,
        float $nilaiAsesmen,
        int $bobotPresensi = 30,
        int $bobotPenilaian = 70,
        int $kkm = 75
    ): array {
        if ($bobotPresensi <= 0) {
            $finalScore = round($nilaiAsesmen, 2);
        } else {
            $totalBobot = $bobotPresensi + $bobotPenilaian;
            if ($totalBobot <= 0) {
                $totalBobot = 100;
            }
            $finalScore = round(
                (($nilaiPresensi * $bobotPresensi) + ($nilaiAsesmen * $bobotPenilaian)) / $totalBobot,
                2
            );
        }

        // Tentukan Predikat Huruf & Capaian KM
        if ($finalScore >= 90) {
            $predikat = 'A';
        } elseif ($finalScore >= 80) {
            $predikat = 'B';
        } elseif ($finalScore >= $kkm) {
            $predikat = 'C';
        } else {
            $predikat = 'D';
        }

        $calcKm = app(\App\Modules\Penilaian\Services\PenilaianScoreCalculator::class);
        $predikatKm = $calcKm->resolvePredikat($finalScore, $kkm);

        return [
            'nilai_akhir' => $finalScore,
            'nilai_huruf' => $predikat,
            'predikat' => $predikatKm,
        ];
    }

    /**
     * Menghasilkan teks deskripsi capaian rapor Kurikulum Merdeka dari TP yang dipilih
     */
    public function generateDeskripsi(array $tpTercapaiIds, array $tpPerluBimbinganIds, int $seniBudayaId): array
    {
        $descTinggi = null;
        $descRendah = null;

        if (!empty($tpTercapaiIds)) {
            $tps = SeniBudayaTp::where('seni_budaya_id', $seniBudayaId)
                ->whereIn('id', $tpTercapaiIds)
                ->orderBy('urutan')
                ->get();

            if ($tps->isNotEmpty()) {
                $list = $tps->pluck('deskripsi')
                    ->map(fn($d) => lcfirst(rtrim(trim($d), '.')))
                    ->all();

                $joined = count($list) > 1 ? implode(' dan ', $list) : ($list[0] ?? '');
                if ($joined) {
                    $descTinggi = 'Menunjukkan penguasaan yang sangat baik dalam ' . $joined . '.';
                }
            }
        }

        if (!empty($tpPerluBimbinganIds)) {
            $tps = SeniBudayaTp::where('seni_budaya_id', $seniBudayaId)
                ->whereIn('id', $tpPerluBimbinganIds)
                ->orderBy('urutan')
                ->get();

            if ($tps->isNotEmpty()) {
                $list = $tps->pluck('deskripsi')
                    ->map(fn($d) => lcfirst(rtrim(trim($d), '.')))
                    ->all();

                $joined = count($list) > 1 ? implode(' dan ', $list) : ($list[0] ?? '');
                if ($joined) {
                    $descRendah = 'Perlu bimbingan lebih lanjut dalam ' . $joined . '.';
                }
            }
        }

        return [
            'deskripsi_tertinggi' => $descTinggi,
            'deskripsi_terendah' => $descRendah,
        ];
    }

    /**
     * Mendaftarkan siswa ke cabang seni budaya tertentu
     */
    public function enrollStudent(int $siswaId, int $seniBudayaId, int $semesterId): array
    {
        // Cek apakah siswa sudah terdaftar di cabang lain pada semester yang sama
        $existing = SeniBudayaAnggota::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->first();

        if ($existing) {
            if ($existing->seni_budaya_id === $seniBudayaId) {
                return [
                    'success' => true,
                    'is_new' => false,
                    'message' => 'Siswa sudah terdaftar di cabang Seni Budaya ini.',
                    'anggota' => $existing,
                ];
            }

            // Pindahkan cabang & bersihkan data TP cabang sebelumnya agar tidak dangling
            $existing->update([
                'seni_budaya_id' => $seniBudayaId,
                'status_keanggotaan' => 'aktif',
                'tp_tercapai_ids' => null,
                'tp_perlu_bimbingan_ids' => null,
                'tp_evaluations' => null,
                'deskripsi_evaluations' => null,
                'deskripsi_tertinggi' => null,
                'deskripsi_terendah' => null,
            ]);

            return [
                'success' => true,
                'is_new' => false,
                'message' => 'Cabang Seni Budaya siswa berhasil diperbarui.',
                'anggota' => $existing,
            ];
        }

        $new = SeniBudayaAnggota::create([
            'seni_budaya_id' => $seniBudayaId,
            'siswa_id' => $siswaId,
            'semester_id' => $semesterId,
            'nilai_presensi' => 100.00,
            'persentase_kehadiran' => 100.00,
            'status_keanggotaan' => 'aktif',
        ]);

        return [
            'success' => true,
            'is_new' => true,
            'message' => 'Siswa berhasil didaftarkan.',
            'anggota' => $new,
        ];
    }
}
