<?php

namespace App\Modules\Ekstrakurikuler\Services;

use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensi;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensiDetail;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerService
{
    /**
     * Mendaftarkan seluruh siswa aktif kelas X ke ekskul Pramuka (Wajib)
     */
    public function autoEnrollPramukaKelasX(int $semesterId): array
    {
        $pramuka = Ekstrakurikuler::where('is_wajib', true)
            ->orWhere('nama', 'like', '%Pramuka%')
            ->first();

        if (!$pramuka) {
            return [
                'success' => false,
                'message' => 'Ekskul Pramuka (Wajib) tidak ditemukan di master data.',
                'count' => 0
            ];
        }

        // Ambil seluruh siswa aktif di rombel tingkat X
        $siswasX = Siswa::where('is_active', true)
            ->whereHas('rombel', function ($q) {
                $q->where('tingkat', 'X');
            })
            ->get();

        $enrolledCount = 0;
        foreach ($siswasX as $siswa) {
            $created = EkstrakurikulerAnggota::firstOrCreate(
                [
                    'ekstrakurikuler_id' => $pramuka->id,
                    'siswa_id' => $siswa->id,
                    'semester_id' => $semesterId,
                ],
                [
                    'status_keanggotaan' => 'aktif',
                    'nilai_presensi' => 100,
                    'persentase_kehadiran' => 100.00,
                ]
            );

            if ($created->wasRecentlyCreated) {
                $enrolledCount++;
            }
        }

        return [
            'success' => true,
            'message' => "Berhasil menyinkronkan {$enrolledCount} siswa Kelas X ke ekskul {$pramuka->nama}.",
            'count' => $enrolledCount,
            'total_x' => $siswasX->count(),
        ];
    }

    /**
     * Mengecek kepatuhan siswa: wajib memilih minimal 1 ekskul pilihan bakat & minat
     */
    public function checkStudentCompliance(int $semesterId, ?int $rombelId = null, ?string $tingkat = null): array
    {
        $query = Siswa::with(['rombel', 'rayon'])
            ->where('is_active', true);

        if ($rombelId) {
            $query->where('rombel_id', $rombelId);
        }

        if ($tingkat) {
            $query->whereHas('rombel', fn($q) => $q->where('tingkat', $tingkat));
        }

        $allStudents = $query->orderBy('nama')->get();

        // Ambil semua keanggotaan aktif di semester ini
        $anggotas = EkstrakurikulerAnggota::with('ekstrakurikuler')
            ->where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->groupBy('siswa_id');

        $compliant = [];
        $nonCompliant = [];

        foreach ($allStudents as $siswa) {
            $studentEkskuls = $anggotas->get($siswa->id, collect());
            $rombelTingkat = $siswa->rombel ? $siswa->rombel->tingkat : null;
            $isTingkatX = ($rombelTingkat === 'X');
            $isTingkatXI = ($rombelTingkat === 'XI');
            $isTingkatXII = ($rombelTingkat === 'XII');

            // Hitung ekskul wajib vs ekskul pilihan
            $hasWajib = $studentEkskuls->contains(fn($a) => $a->ekstrakurikuler && $a->ekstrakurikuler->is_wajib);
            $pilihanEkskuls = $studentEkskuls->filter(fn($a) => $a->ekstrakurikuler && !$a->ekstrakurikuler->is_wajib);
            $pilihanCount = $pilihanEkskuls->count();
            $totalCount = $studentEkskuls->count();

            // Aturan kepatuhan:
            // 1. Siswa Tingkat X: Wajib Pramuka (UPD Wajib) + Wajib minimal 1 UPD Pilihan
            // 2. Siswa Tingkat XI: Tidak wajib Pramuka (UPD wajib kosong BUKAN masalah). Wajib minimal 1 UPD (apapun jenisnya).
            // 3. Siswa Tingkat XII: Bebas kewajiban UPD (fokus PKL / Ujikom / persiapan kelulusan). Selalu berstatus patuh.
            $isCompliant = false;
            $statusText = '';

            if ($isTingkatXII) {
                $isCompliant = true;
                if ($totalCount > 0) {
                    $statusText = 'Bebas Kewajiban (' . $totalCount . ' UPD Sukarela)';
                } else {
                    $statusText = 'Bebas Kewajiban UPD (Tingkat XII)';
                }
            } elseif ($isTingkatX) {
                if ($hasWajib && $pilihanCount >= 1) {
                    $isCompliant = true;
                    $statusText = 'Lengkap (Pramuka + ' . $pilihanCount . ' Pilihan)';
                } elseif (!$hasWajib && $pilihanCount >= 1) {
                    $isCompliant = false;
                    $statusText = 'Belum Masuk Pramuka Wajib';
                } elseif ($hasWajib && $pilihanCount === 0) {
                    $isCompliant = false;
                    $statusText = 'Belum Memilih UPD Pilihan';
                } else {
                    $isCompliant = false;
                    $statusText = 'Belum Ada UPD (Wajib & Pilihan)';
                }
            } else {
                // Tingkat XI atau lainnya:
                // Tidak wajib Pramuka. Asalkan memiliki minimal 1 UPD (pilihan atau sukarela ikut Pramuka) = Patuh.
                if ($totalCount >= 1) {
                    $isCompliant = true;
                    $statusText = 'Lengkap (' . $totalCount . ' UPD)';
                } else {
                    $isCompliant = false;
                    $statusText = 'Belum Memilih UPD';
                }
            }

            $item = [
                'siswa' => $siswa,
                'tingkat' => $rombelTingkat,
                'is_tingkat_x' => $isTingkatX,
                'is_tingkat_xi' => $isTingkatXI,
                'is_tingkat_xii' => $isTingkatXII,
                'has_wajib' => $hasWajib,
                'pilihan_count' => $pilihanCount,
                'total_count' => $totalCount,
                'is_compliant' => $isCompliant,
                'status_text' => $statusText,
                'missing_pramuka' => ($isTingkatX && !$hasWajib),
                'ekskuls' => $studentEkskuls->map(fn($a) => $a->ekstrakurikuler?->nama)->filter()->values()->all(),
            ];

            if ($isCompliant) {
                $compliant[] = $item;
            } else {
                $nonCompliant[] = $item;
            }
        }

        return [
            'total_students' => $allStudents->count(),
            'compliant_count' => count($compliant),
            'non_compliant_count' => count($nonCompliant),
            'compliance_rate' => $allStudents->count() > 0 ? round((count($compliant) / $allStudents->count()) * 100, 1) : 0,
            'compliant_list' => $compliant,
            'non_compliant_list' => $nonCompliant,
        ];
    }

    /**
     * Hitung akumulasi kehadiran sesi mingguan per siswa dan update ke data anggota
     */
    public function recalculateAttendance(int $ekskulId, int $semesterId): void
    {
        $totalSessions = EkskulPresensi::where('ekstrakurikuler_id', $ekskulId)
            ->where('semester_id', $semesterId)
            ->count();

        $anggotas = EkstrakurikulerAnggota::where('ekstrakurikuler_id', $ekskulId)
            ->where('semester_id', $semesterId)
            ->get();

        foreach ($anggotas as $anggota) {
            if ($totalSessions > 0) {
                $hadirCount = EkskulPresensiDetail::whereHas('presensi', function ($q) use ($ekskulId, $semesterId) {
                    $q->where('ekstrakurikuler_id', $ekskulId)
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
                'total_pertemuan' => $totalSessions,
                'total_hadir' => $hadirCount,
                'persentase_kehadiran' => $persentase,
                'nilai_presensi' => $nilaiPresensi,
            ]);
        }
    }

    /**
     * Hitung nilai akhir berdasarkan pembobotan (presensi % + asesmen %)
     * dan konversi ke predikat rapor serta deskripsi
     */
    public function calculateFinalGrade(
        EkstrakurikulerAnggota $anggota,
        ?string $periode = null,
        ?float $overrideAssessmentScore = null
    ): array {
        $ekskul = $anggota->ekstrakurikuler;
        if (!$ekskul) {
            $ekskul = Ekstrakurikuler::find($anggota->ekstrakurikuler_id);
        }

        $bobotPresensi = $ekskul->bobot_presensi ?? 30;
        $bobotPenilaian = $ekskul->bobot_penilaian ?? 70;

        // Ambil nilai asesmen sesuai periode rapor
        $assessmentScore = $overrideAssessmentScore;
        if ($assessmentScore === null) {
            if ($periode === 'STS 1' || $periode === 'sts_1') {
                $assessmentScore = $anggota->sts_1;
            } elseif ($periode === 'SAS' || $periode === 'sas') {
                $assessmentScore = $anggota->sas;
            } elseif ($periode === 'STS 2' || $periode === 'sts_2') {
                $assessmentScore = $anggota->sts_2;
            } elseif ($periode === 'SAT' || $periode === 'sat') {
                $assessmentScore = $anggota->sat;
            } else {
                // Fallback: ambil nilai asesmen yang terisi terbaru
                $assessmentScore = $anggota->sat ?? $anggota->sts_2 ?? $anggota->sas ?? $anggota->sts_1;
            }
        }

        $nilaiPresensi = $anggota->nilai_presensi ?? 100;

        if ($assessmentScore !== null) {
            $finalScore = round(
                (($nilaiPresensi * $bobotPresensi) / 100) + (($assessmentScore * $bobotPenilaian) / 100),
                2
            );
        } else {
            $finalScore = $nilaiPresensi; // jika belum ada asesmen, nilai sementara dari presensi
        }

        // Tentukan predikat berdasarkan threshold yang dapat diedit
        $thA = $ekskul->threshold_a ?? 90;
        $thB = $ekskul->threshold_b ?? 80;
        $thC = $ekskul->threshold_c ?? 70;

        if ($finalScore >= $thA) {
            $predikat = 'Sangat Baik';
            $hurufSingkat = 'A';
            $deskripsiDefault = "Menunjukkan keaktifan dan penguasaan teknik yang sangat baik dalam kegiatan {$ekskul->nama}.";
        } elseif ($finalScore >= $thB) {
            $predikat = 'Baik';
            $hurufSingkat = 'B';
            $deskripsiDefault = "Menunjukkan penguasaan teknik dan partisipasi yang baik dalam kegiatan {$ekskul->nama}.";
        } elseif ($finalScore >= $thC) {
            $predikat = 'Cukup';
            $hurufSingkat = 'C';
            $deskripsiDefault = "Cukup aktif dalam mengikuti pembinaan kegiatan {$ekskul->nama}. Perlu ditingkatkan konsistensi latihan.";
        } else {
            $predikat = 'Kurang';
            $hurufSingkat = 'D';
            $deskripsiDefault = "Perlu bimbingan dan peningkatan kehadiran serta partisipasi dalam kegiatan {$ekskul->nama}.";
        }

        return [
            'nilai_presensi' => $nilaiPresensi,
            'nilai_asesmen' => $assessmentScore,
            'nilai_angka' => $finalScore,
            'nilai_huruf' => $predikat,
            'huruf_singkat' => $hurufSingkat,
            'deskripsi' => $deskripsiDefault,
            'bobot_presensi' => $bobotPresensi,
            'bobot_penilaian' => $bobotPenilaian,
        ];
    }
}
