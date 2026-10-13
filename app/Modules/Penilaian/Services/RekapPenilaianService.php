<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use Illuminate\Support\Collection;

class RekapPenilaianService
{
    /**
     * Menghasilkan data Leger Nilai lengkap untuk Rombel dan Semester tertentu.
     */
    public function getLegerRombelData(Rombel $rombel, Semester $semester): array
    {
        $siswas = Siswa::where('rombel_id', $rombel->id)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $isGenap = str_contains(strtolower($semester->semester), 'genap');

        // Ambil struktur kurikulum terurut
        $allSk = StrukturKurikulum::with(['mapel.kelompokMapel'])
            ->where('tahun_ajaran_id', $rombel->tahun_ajaran_id)
            ->where('jurusan_id', $rombel->jurusan_id)
            ->where('tingkat', $rombel->tingkat)
            ->where(function ($q) use ($isGenap) {
                if ($isGenap) {
                    $q->where('jp_genap', '>', 0)
                        ->orWhere(fn($sub) => $sub->where('jp_ganjil', 0)->where('jp_genap', 0)->where('jp_per_minggu', '>', 0));
                } else {
                    $q->where('jp_ganjil', '>', 0)
                        ->orWhere(fn($sub) => $sub->where('jp_ganjil', 0)->where('jp_genap', 0)->where('jp_per_minggu', '>', 0));
                }
            })
            ->orderBy('urutan')
            ->get();

        // Ambil penugasan mengajar rombel di semester ini
        $penugasans = PenugasanMengajar::where('rombel_id', $rombel->id)
            ->where('semester_id', $semester->id)
            ->with(['pegawai', 'tujuanPembelajarans'])
            ->get()
            ->keyBy('mapel_id');

        $pmIds = $penugasans->pluck('id')->filter()->all();
        $siswaIds = $siswas->pluck('id')->all();

        // Ambil Nilai Akhir
        $naRecords = NilaiAkhir::whereIn('penugasan_mengajar_id', $pmIds)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('penugasan_mengajar_id');

        // Ambil Asesmen Harian untuk fallback komponen jika NA belum ter-rekap
        $ahRecords = NilaiAsesmenHarian::whereIn('penugasan_mengajar_id', $pmIds)
            ->whereIn('siswa_id', $siswaIds)
            ->whereNotNull('nilai')
            ->get()
            ->groupBy('penugasan_mengajar_id');

        // Ambil Sumatif untuk fallback komponen
        $sumatifRecords = NilaiSumatif::whereIn('penugasan_mengajar_id', $pmIds)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('penugasan_mengajar_id');

        // 1. Siapkan Daftar Mata Pelajaran
        $mapelList = [];
        foreach ($allSk as $sk) {
            $mapel = $sk->mapel;
            if (!$mapel) {
                continue;
            }

            $pm = $penugasans->get($mapel->id);
            $km = $mapel->kelompokMapel;

            if ($km) {
                $kelompokLabel = $km->kode ? "Kelompok {$km->kode} ({$km->nama})" : $km->nama;
                $kelompokUrutan = $km->urutan;
            } else {
                $kelompokUrutan = 99;
                $kelompokLabel = match ($mapel->kelompok) {
                    Mapel::KELOMPOK_UMUM => 'Kelompok A (Muatan Umum)',
                    Mapel::KELOMPOK_KEJURUAN => 'Kelompok B (Muatan Kejuruan)',
                    Mapel::KELOMPOK_MULOK => 'Muatan Lokal',
                    Mapel::KELOMPOK_P5_PKL => 'Projek P5 & PKL',
                    default => $mapel->kelompok_label ?? 'Lainnya',
                };
            }

            $mapelList[$mapel->id] = [
                'id' => $mapel->id,
                'kode' => $mapel->kode,
                'nama' => $mapel->nama,
                'kkm' => (int) ($mapel->kkm_default ?? 75),
                'kelompok_label' => $kelompokLabel,
                'kelompok_urutan' => $kelompokUrutan,
                'mapel_urutan' => $mapel->urutan ?? $sk->urutan,
                'penugasan_id' => $pm?->id,
                'guru' => $pm?->pegawai?->nama_lengkap ?? 'Belum Ditugaskan',
            ];
        }

        // Ambil data keanggotaan Seni Budaya siswa di rombel ini
        $sbRecords = \App\Modules\SeniBudaya\Models\SeniBudayaAnggota::with('seniBudaya')
            ->whereIn('siswa_id', $siswaIds)
            ->where('semester_id', $semester->id)
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->keyBy('siswa_id');

        // Selalu sertakan mapel Seni Budaya di Kelompok A (Muatan Umum) jika belum ada di struktur kurikulum
        $hasSeni = collect($mapelList)->contains(function ($m) {
            return strtolower($m['kode']) === 'seni' || str_contains(strtolower($m['nama']), 'seni budaya');
        });

        if (!$hasSeni) {
            $mapelSeni = Mapel::with('kelompokMapel')->where('kode', 'SENI')->orWhere('nama', 'like', '%Seni Budaya%')->first();
            if (!$mapelSeni) {
                $mapelSeni = Mapel::firstOrCreate(
                    ['kode' => 'SENI'],
                    [
                        'nama' => 'Seni Budaya',
                        'kelompok_mapel_id' => 1,
                        'kelompok' => 'umum',
                        'kkm_default' => 75,
                        'urutan' => 6,
                        'is_active' => true,
                    ]
                );
                $mapelSeni->load('kelompokMapel');
            }

            if ($mapelSeni) {
                $km = $mapelSeni->kelompokMapel;
                $kelompokLabel = $km ? ($km->kode ? "Kelompok {$km->kode} ({$km->nama})" : $km->nama) : 'Kelompok A (Muatan Umum)';
                $kelompokUrutan = $km?->urutan ?? 1;

                $mapelList[$mapelSeni->id] = [
                    'id' => $mapelSeni->id,
                    'kode' => $mapelSeni->kode,
                    'nama' => $mapelSeni->nama,
                    'kkm' => (int) ($mapelSeni->kkm_default ?? 75),
                    'kelompok_label' => $kelompokLabel,
                    'kelompok_urutan' => $kelompokUrutan,
                    'mapel_urutan' => $mapelSeni->urutan ?? 6,
                    'penugasan_id' => null,
                    'guru' => 'Tim Guru Seni Budaya',
                ];
            }
        }

        // Urutkan mapel berdasarkan kelompok lalu urutan mapel
        uasort($mapelList, function ($a, $b) {
            if ($a['kelompok_urutan'] === $b['kelompok_urutan']) {
                return $a['mapel_urutan'] <=> $b['mapel_urutan'];
            }
            return $a['kelompok_urutan'] <=> $b['kelompok_urutan'];
        });

        // 2. Matriks Nilai Siswa
        $siswaRows = [];
        foreach ($siswas as $idx => $siswa) {
            $scores = [];
            $totalNilai = 0;
            $gradedMapelCount = 0;
            $belowKkmCount = 0;

            foreach ($mapelList as $mId => $mInfo) {
                $pmId = $mInfo['penugasan_id'];
                $kkm = $mInfo['kkm'];

                $na = $pmId && isset($naRecords[$pmId])
                    ? $naRecords[$pmId]->firstWhere('siswa_id', $siswa->id)
                    : null;

                $ahList = $pmId && isset($ahRecords[$pmId])
                    ? $ahRecords[$pmId]->where('siswa_id', $siswa->id)
                    : collect();

                $sumatif = $pmId && isset($sumatifRecords[$pmId])
                    ? $sumatifRecords[$pmId]->firstWhere('siswa_id', $siswa->id)
                    : null;

                $scoreVal = null;
                $rataFormatif = null;
                $sts = null;
                $sas = null;
                $cabangNama = null;

                $isSeniBudaya = (strtolower($mInfo['kode']) === 'seni' || str_contains(strtolower($mInfo['nama']), 'seni budaya'));
                $sb = null;

                if ($isSeniBudaya && $sbRecords->has($siswa->id)) {
                    $sb = $sbRecords->get($siswa->id);
                    if ($sb) {
                        $scoreVal = $sb->nilai_akhir !== null ? (float) $sb->nilai_akhir : null;
                        $sts = $isGenap 
                            ? ($sb->sts_2 !== null ? (float) $sb->sts_2 : null) 
                            : ($sb->sts_1 !== null ? (float) $sb->sts_1 : null);
                        $sas = $isGenap 
                            ? ($sb->sat !== null ? (float) $sb->sat : null) 
                            : ($sb->sas !== null ? (float) $sb->sas : null);
                        $cabangNama = $sb->seniBudaya?->nama;
                    }
                } elseif ($na && ($na->nilai_akhir > 0 || $na->rata_formatif > 0 || !is_null($na->sts) || !is_null($na->sas))) {
                    $scoreVal = (float) $na->nilai_akhir;
                    $rataFormatif = (float) $na->rata_formatif;
                    $sts = !is_null($na->sts) ? (float) $na->sts : null;
                    $sas = !is_null($na->sas) ? (float) $na->sas : null;
                } elseif ($ahList->isNotEmpty() || $sumatif) {
                    $rataFormatif = $ahList->isNotEmpty() ? round($ahList->avg('nilai'), 2) : 0;
                    $sts = $sumatif && !is_null($sumatif->sts) ? (float) $sumatif->sts : 0;
                    $sas = $sumatif && !is_null($sumatif->sas) ? (float) $sumatif->sas : 0;
                    $scoreVal = round(($rataFormatif * 0.5) + ($sts * 0.25) + ($sas * 0.25), 2);
                }

                $isBelowKkm = ($scoreVal !== null && $scoreVal < $kkm);

                if ($scoreVal !== null) {
                    $totalNilai += $scoreVal;
                    $gradedMapelCount++;
                    if ($isBelowKkm) {
                        $belowKkmCount++;
                    }
                }

                $descTertinggi = '';
                $descTerendah = '';
                if ($isSeniBudaya && $sb) {
                    $descTertinggi = $sb->deskripsi_tertinggi ?? '';
                    if (!empty($sb->deskripsi_terendah)) {
                        $descTerendah = $cabangNama ? "[{$cabangNama}] {$sb->deskripsi_terendah}" : $sb->deskripsi_terendah;
                    } elseif ($cabangNama) {
                        $descTerendah = "[{$cabangNama}] Perlu bimbingan remedial";
                    }
                } elseif ($na) {
                    $descTertinggi = $na->deskripsi_tertinggi ?? '';
                    $descTerendah = $na->deskripsi_terendah ?? '';
                }

                $scores[$mId] = [
                    'nilai_akhir' => $scoreVal,
                    'rata_formatif' => $rataFormatif,
                    'sts' => $sts,
                    'sas' => $sas,
                    'kkm' => $kkm,
                    'is_below_kkm' => $isBelowKkm,
                    'desc_tertinggi' => $descTertinggi,
                    'desc_terendah' => $descTerendah,
                    'cabang_nama' => $cabangNama,
                ];
            }

            $rataRata = $gradedMapelCount > 0 ? round($totalNilai / $gradedMapelCount, 2) : 0;

            $siswaRows[] = [
                'siswa_id' => $siswa->id,
                'nis' => $siswa->nipd ?? $siswa->nisn ?? '-',
                'nisn' => $siswa->nisn ?? '-',
                'nama' => $siswa->nama,
                'jenis_kelamin' => $siswa->jenis_kelamin ?? 'L',
                'scores' => $scores,
                'total_nilai' => $totalNilai,
                'graded_mapel_count' => $gradedMapelCount,
                'rata_rata' => $rataRata,
                'below_kkm_count' => $belowKkmCount,
                'is_tuntas' => ($belowKkmCount === 0 && $gradedMapelCount > 0),
                'rank' => 0,
            ];
        }

        // 3. Hitung Peringkat / Ranking Kelas
        usort($siswaRows, function ($a, $b) {
            if ($a['rata_rata'] == $b['rata_rata']) {
                return $b['total_nilai'] <=> $a['total_nilai'];
            }
            return $b['rata_rata'] <=> $a['rata_rata'];
        });

        $currentRank = 1;
        foreach ($siswaRows as $idx => &$row) {
            if ($idx > 0) {
                $prev = $siswaRows[$idx - 1];
                if ($row['rata_rata'] < $prev['rata_rata'] || $row['total_nilai'] < $prev['total_nilai']) {
                    $currentRank = $idx + 1;
                }
            }
            $row['rank'] = $row['graded_mapel_count'] > 0 ? $currentRank : '-';
        }
        unset($row);

        // Kembalikan urutan siswa berdasarkan nama untuk tabel leger utama
        $siswaRowsSortedByName = $siswaRows;
        usort($siswaRowsSortedByName, fn($a, $b) => strcasecmp($a['nama'], $b['nama']));

        // 4. Statistik Per Mata Pelajaran (Footer Leger & Tab 2)
        $mapelStats = [];
        foreach ($mapelList as $mId => $mInfo) {
            $scoresMapel = [];
            $tuntasCount = 0;
            $remedialCount = 0;
            $kkm = $mInfo['kkm'];

            $gradeDistribution = [
                'A' => 0, // >= 90
                'B' => 0, // 80 - 89
                'C' => 0, // KKM - 79
                'D' => 0, // < KKM
            ];

            $remedialStudents = [];

            foreach ($siswaRows as $sRow) {
                $sScore = $sRow['scores'][$mId]['nilai_akhir'];
                if ($sScore !== null) {
                    $scoresMapel[] = $sScore;
                    if ($sScore >= $kkm) {
                        $tuntasCount++;
                    } else {
                        $remedialCount++;
                        $remedialStudents[] = [
                            'siswa_id' => $sRow['siswa_id'],
                            'nama' => $sRow['nama'],
                            'nis' => $sRow['nis'],
                            'nilai' => $sScore,
                            'kkm' => $kkm,
                            'selisih' => round($kkm - $sScore, 2),
                            'catatan' => $sRow['scores'][$mId]['desc_terendah'] ?: 'Perlu bimbingan remedial',
                        ];
                    }

                    if ($sScore >= 90) {
                        $gradeDistribution['A']++;
                    } elseif ($sScore >= 80) {
                        $gradeDistribution['B']++;
                    } elseif ($sScore >= $kkm) {
                        $gradeDistribution['C']++;
                    } else {
                        $gradeDistribution['D']++;
                    }
                }
            }

            $countGraded = count($scoresMapel);
            $avgMapel = $countGraded > 0 ? round(array_sum($scoresMapel) / $countGraded, 2) : 0;
            $maxMapel = $countGraded > 0 ? max($scoresMapel) : 0;
            $minMapel = $countGraded > 0 ? min($scoresMapel) : 0;
            $persenTuntas = $countGraded > 0 ? round(($tuntasCount / $countGraded) * 100, 1) : 0;

            $mapelStats[$mId] = [
                'mapel_id' => $mId,
                'kode' => $mInfo['kode'],
                'nama' => $mInfo['nama'],
                'kkm' => $kkm,
                'guru' => $mInfo['guru'],
                'count_graded' => $countGraded,
                'avg' => $avgMapel,
                'max' => $maxMapel,
                'min' => $minMapel,
                'tuntas_count' => $tuntasCount,
                'remedial_count' => $remedialCount,
                'persen_tuntas' => $persenTuntas,
                'grade_distribution' => $gradeDistribution,
                'remedial_students' => $remedialStudents,
            ];
        }

        // 5. Statistik Ringkasan Rombel
        $totalSiswa = count($siswaRows);
        $siswaTuntasSemua = collect($siswaRows)->filter(fn($s) => $s['is_tuntas'])->count();
        $siswaRemedial = collect($siswaRows)->filter(fn($s) => $s['below_kkm_count'] > 0)->count();
        $siswaKritis = collect($siswaRows)->filter(fn($s) => $s['below_kkm_count'] >= 3)->count();

        $allStudentAverages = collect($siswaRows)->pluck('rata_rata')->filter(fn($v) => $v > 0);
        $rataRataRombel = $allStudentAverages->isNotEmpty() ? round($allStudentAverages->avg(), 2) : 0;

        $topStudent = collect($siswaRows)->sortByDesc('rata_rata')->first();

        // Ketuntasan Klasikal Rombel (% dari seluruh pasangan siswa x mapel)
        $totalEvaluations = collect($mapelStats)->sum('count_graded');
        $totalTuntasKlasikal = collect($mapelStats)->sum('tuntas_count');
        $persenKetuntasanKlasikal = $totalEvaluations > 0 ? round(($totalTuntasKlasikal / $totalEvaluations) * 100, 1) : 0;

        return [
            'rombel' => [
                'id' => $rombel->id,
                'nama' => $rombel->nama,
                'tingkat' => $rombel->tingkat,
                'jurusan' => $rombel->jurusan?->nama ?? '-',
                'wali_kelas' => $rombel->waliKelas?->name ?? 'Belum Ditugaskan',
                'tahun_ajaran' => $rombel->tahunAjaran?->nama ?? '-',
            ],
            'semester' => [
                'id' => $semester->id,
                'nama' => 'Semester ' . ucfirst($semester->semester),
                'semester' => strtolower($semester->semester),
            ],
            'summary' => [
                'total_siswa' => $totalSiswa,
                'rata_rata_rombel' => $rataRataRombel,
                'persen_tuntas_klasikal' => $persenKetuntasanKlasikal,
                'siswa_tuntas_count' => $siswaTuntasSemua,
                'siswa_tuntas_persen' => $totalSiswa > 0 ? round(($siswaTuntasSemua / $totalSiswa) * 100, 1) : 0,
                'siswa_remedial_count' => $siswaRemedial,
                'siswa_kritis_count' => $siswaKritis,
                'top_student_nama' => $topStudent ? $topStudent['nama'] : '-',
                'top_student_avg' => $topStudent ? $topStudent['rata_rata'] : 0,
                'total_mapel' => count($mapelList),
            ],
            'mapel_list' => $mapelList,
            'siswa_rows' => $siswaRowsSortedByName,
            'siswa_ranking' => $siswaRows, // Terurut ranking
            'mapel_stats' => $mapelStats,
        ];
    }

    /**
     * Mengambil data komparasi antar rombel pada tingkat dan semester yang sama.
     */
    public function getCrossRombelComparison(string $tingkat, Semester $semester): Collection
    {
        $rombels = Rombel::with(['jurusan', 'waliKelas', 'tahunAjaran'])
            ->where('tahun_ajaran_id', $semester->tahun_ajaran_id)
            ->when($tingkat !== 'semua', fn($q) => $q->where('tingkat', $tingkat))
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        return $rombels->map(function ($r) use ($semester) {
            $data = $this->getLegerRombelData($r, $semester);
            $sum = $data['summary'];

            return [
                'rombel_id' => $r->id,
                'nama' => $r->nama,
                'tingkat' => $r->tingkat,
                'jurusan' => $r->jurusan?->nama ?? '-',
                'wali_kelas' => $r->waliKelas?->name ?? 'Belum Ditugaskan',
                'total_siswa' => $sum['total_siswa'],
                'rata_rata_rombel' => $sum['rata_rata_rombel'],
                'persen_tuntas_klasikal' => $sum['persen_tuntas_klasikal'],
                'siswa_tuntas_count' => $sum['siswa_tuntas_count'],
                'siswa_remedial_count' => $sum['siswa_remedial_count'],
                'siswa_kritis_count' => $sum['siswa_kritis_count'],
                'top_student_nama' => $sum['top_student_nama'],
                'top_student_avg' => $sum['top_student_avg'],
            ];
        });
    }

    /**
     * Mengambil data komparasi capaian akademik antar Rayon pada semester tertentu.
     */
    public function getCrossRayonComparison(string $tingkat, Semester $semester): Collection
    {
        $rayons = Rayon::with('pembimbing')->orderBy('nama')->get();

        $pembimbingPenugasan = PenugasanPembimbingRayon::with('pegawai')
            ->where('tahun_ajaran_id', $semester->tahun_ajaran_id)
            ->get()
            ->keyBy('rayon_id');

        $siswas = Siswa::with(['rombel', 'rayon'])
            ->where('status', 'aktif')
            ->whereNotNull('rayon_id')
            ->whereHas('rombel', function ($q) use ($semester, $tingkat) {
                $q->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                if ($tingkat !== 'semua') {
                    $q->where('tingkat', $tingkat);
                }
            })
            ->get();

        $siswaIds = $siswas->pluck('id')->all();

        // Ambil Nilai Akhir semester ini untuk seluruh siswa di atas
        $naRecords = NilaiAkhir::whereIn('siswa_id', $siswaIds)
            ->whereHas('penugasanMengajar', fn($q) => $q->where('semester_id', $semester->id))
            ->with(['penugasanMengajar.mapel'])
            ->get()
            ->groupBy('siswa_id');

        // Ambil Nilai Seni Budaya aktif semester ini untuk seluruh siswa di atas
        $sbRayonRecords = \App\Modules\SeniBudaya\Models\SeniBudayaAnggota::with('seniBudaya')
            ->whereIn('siswa_id', $siswaIds)
            ->where('semester_id', $semester->id)
            ->where('status_keanggotaan', 'aktif')
            ->whereNotNull('nilai_akhir')
            ->get()
            ->keyBy('siswa_id');

        // Asosiasikan data nilai per siswa
        $studentMetrics = [];
        foreach ($siswas as $siswa) {
            $nas = $naRecords->get($siswa->id, collect());
            $totalNilai = 0;
            $countMapel = 0;
            $belowKkmCount = 0;

            foreach ($nas as $na) {
                $score = (float) $na->nilai_akhir;
                if ($score <= 0) continue;
                $kkm = (int) ($na->penugasanMengajar?->mapel?->kkm_default ?? 75);
                $totalNilai += $score;
                $countMapel++;
                if ($score < $kkm) {
                    $belowKkmCount++;
                }
            }

            // Tambahkan nilai Seni Budaya jika ada
            if ($sbRayonRecords->has($siswa->id)) {
                $sb = $sbRayonRecords->get($siswa->id);
                $sbScore = (float) $sb->nilai_akhir;
                if ($sbScore > 0) {
                    $sbKkm = (int) ($sb->seniBudaya?->kkm ?? 75);
                    $totalNilai += $sbScore;
                    $countMapel++;
                    if ($sbScore < $sbKkm) {
                        $belowKkmCount++;
                    }
                }
            }

            $avg = $countMapel > 0 ? round($totalNilai / $countMapel, 2) : 0;

            $studentMetrics[$siswa->id] = [
                'siswa_id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nipd ?? $siswa->nisn ?? '-',
                'rayon_id' => $siswa->rayon_id,
                'rombel_nama' => $siswa->rombel?->nama ?? '-',
                'tingkat' => $siswa->rombel?->tingkat ?? '-',
                'count_mapel' => $countMapel,
                'total_nilai' => $totalNilai,
                'rata_rata' => $avg,
                'below_kkm_count' => $belowKkmCount,
                'is_tuntas' => ($belowKkmCount === 0 && $countMapel > 0),
            ];
        }

        // Kelompokkan siswa berdasarkan rayon_id
        $groupedByRayon = collect($studentMetrics)->groupBy('rayon_id');

        return $rayons->map(function ($rayon) use ($groupedByRayon, $pembimbingPenugasan) {
            $rayonStudents = $groupedByRayon->get($rayon->id, collect());
            $totalSiswa = $rayonStudents->count();

            $gradedStudents = $rayonStudents->filter(fn($s) => $s['count_mapel'] > 0);
            $averages = $gradedStudents->pluck('rata_rata');
            $rataRataRayon = $averages->isNotEmpty() ? round($averages->avg(), 2) : 0;

            $totalEvaluations = $rayonStudents->sum('count_mapel');
            $totalTuntasEvaluations = $rayonStudents->sum(fn($s) => $s['count_mapel'] - $s['below_kkm_count']);
            $persenKetuntasanKlasikal = $totalEvaluations > 0 ? round(($totalTuntasEvaluations / $totalEvaluations) * 100, 1) : 0;

            $siswaTuntasCount = $rayonStudents->filter(fn($s) => $s['is_tuntas'])->count();
            $siswaRemedialCount = $rayonStudents->filter(fn($s) => $s['below_kkm_count'] > 0)->count();
            $siswaKritisCount = $rayonStudents->filter(fn($s) => $s['below_kkm_count'] >= 3)->count();

            $topStudent = $gradedStudents->sortByDesc('rata_rata')->first();

            // Pembimbing Rayon (Prioritaskan penugasan SK tahun ajaran ini, fallback ke user pembimbing pada rayon)
            $pembimbingName = 'Belum Ditugaskan';
            if (isset($pembimbingPenugasan[$rayon->id])) {
                $pembimbingName = $pembimbingPenugasan[$rayon->id]->pegawai?->nama_lengkap ?? 'Belum Ditugaskan';
            } elseif ($rayon->pembimbing) {
                $pembimbingName = $rayon->pembimbing->name;
            }

            return [
                'rayon_id' => $rayon->id,
                'nama' => $rayon->nama,
                'wilayah' => $rayon->wilayah_cakupan ?? '-',
                'pembimbing' => $pembimbingName,
                'total_siswa' => $totalSiswa,
                'rata_rata_rayon' => $rataRataRayon,
                'persen_tuntas_klasikal' => $persenKetuntasanKlasikal,
                'siswa_tuntas_count' => $siswaTuntasCount,
                'siswa_remedial_count' => $siswaRemedialCount,
                'siswa_kritis_count' => $siswaKritisCount,
                'top_student_nama' => $topStudent ? $topStudent['nama'] : '-',
                'top_student_rombel' => $topStudent ? $topStudent['rombel_nama'] : '-',
                'top_student_avg' => $topStudent ? $topStudent['rata_rata'] : 0,
                'students' => $rayonStudents->sortByDesc('rata_rata')->values()->all(),
            ];
        });
    }
}
