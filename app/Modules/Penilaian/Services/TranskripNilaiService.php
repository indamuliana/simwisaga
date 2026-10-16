<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\KelompokMapel;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use Illuminate\Support\Collection;

class TranskripNilaiService
{
    /**
     * Menentukan nomor semester (1 s/d 6) berdasarkan tingkat (X, XI, XII) dan nama semester.
     */
    public static function getSemesterIndex(string $tingkat, string $semesterName): int
    {
        $isGenap = str_contains(strtolower($semesterName), 'genap');
        $t = strtoupper(trim($tingkat));

        if ($t === 'X') {
            return $isGenap ? 2 : 1;
        }
        if ($t === 'XI') {
            return $isGenap ? 4 : 3;
        }
        if ($t === 'XII') {
            return $isGenap ? 6 : 5;
        }

        return $isGenap ? 2 : 1;
    }

    /**
     * Menentukan predikat huruf berdasarkan nilai skala 0-100.
     */
    public static function getPredikat(float $score): string
    {
        if ($score >= 85) {
            return 'A';
        }
        if ($score >= 75) {
            return 'B';
        }
        if ($score >= 60) {
            return 'C';
        }

        return 'D';
    }

    /**
     * Keterangan predikat.
     */
    public static function getKeteranganPredikat(string $predikat): string
    {
        return match ($predikat) {
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            'D' => 'Kurang',
            default => '-',
        };
    }

    /**
     * Mengambil data transkrip individual lengkap 3 tahun (6 semester) untuk satu siswa.
     */
    public function getSiswaTranskripData(int $siswaId): array
    {
        $siswa = Siswa::with(['rombel.jurusan', 'rombel.waliKelas', 'rayon'])
            ->findOrFail($siswaId);

        $jurusan = $siswa->rombel?->jurusan;
        $jurusanId = $jurusan?->id;

        // Ambil seluruh NilaiAkhir siswa
        $nilaiAkhirs = NilaiAkhir::with([
            'penugasanMengajar.mapel.kelompokMapel',
            'penugasanMengajar.semester.tahunAjaran',
            'penugasanMengajar.rombel',
        ])
            ->where('siswa_id', $siswaId)
            ->get();

        // Ambil daftar Mapel kurikulum untuk jurusan ini (X, XI, XII)
        $kurikulumMapels = collect();
        if ($jurusanId) {
            $sks = StrukturKurikulum::with(['mapel.kelompokMapel'])
                ->where('jurusan_id', $jurusanId)
                ->orderBy('urutan')
                ->get();

            foreach ($sks as $sk) {
                if ($sk->mapel) {
                    $kurikulumMapels->put($sk->mapel_id, $sk->mapel);
                }
            }
        }

        // Kumpulkan juga mapel yang ada nilainya jika tidak terdaftar di StrukturKurikulum
        foreach ($nilaiAkhirs as $na) {
            $m = $na->penugasanMengajar?->mapel;
            if ($m && !$kurikulumMapels->has($m->id)) {
                $kurikulumMapels->put($m->id, $m);
            }
        }

        // Integrasi Seni Budaya jika siswa terdaftar
        $mapelSeni = Mapel::with('kelompokMapel')
            ->where('kode', 'SENI')
            ->orWhere('nama', 'like', '%Seni Budaya%')
            ->first();

        if ($mapelSeni && !$kurikulumMapels->has($mapelSeni->id)) {
            $kurikulumMapels->put($mapelSeni->id, $mapelSeni);
        }

        $sbAnggotas = collect();
        if (class_exists(\App\Modules\SeniBudaya\Models\SeniBudayaAnggota::class)) {
            $sbAnggotas = \App\Modules\SeniBudaya\Models\SeniBudayaAnggota::with(['seniBudaya', 'semester.tahunAjaran'])
                ->where('siswa_id', $siswaId)
                ->get();
        }
        $latestSb = $sbAnggotas->where('status_keanggotaan', 'aktif')->last() ?? $sbAnggotas->last();

        // Integrasi Mapel Pilihan jika siswa terdaftar / memiliki nilai
        $mapilAnggotas = collect();
        $mapilAkhirs = collect();
        if (class_exists(\App\Modules\Akademik\Models\MapelPilihanAnggota::class)) {
            $mapilAnggotas = \App\Modules\Akademik\Models\MapelPilihanAnggota::with('mapelPilihan')
                ->where('siswa_id', $siswaId)
                ->where('status', 'aktif')
                ->get();
        }
        if (class_exists(\App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir::class)) {
            $mapilAkhirs = \App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir::with(['mapelPilihan', 'semester'])
                ->where('siswa_id', $siswaId)
                ->whereNotNull('nilai_akhir')
                ->get();
        }

        foreach ($mapilAnggotas as $mAnggota) {
            $mapil = $mAnggota->mapelPilihan;
            if (!$mapil) continue;
            $mKey = 90000 + $mapil->id;
            if (!$kurikulumMapels->has($mKey)) {
                $mObj = new Mapel([
                    'kode' => $mapil->kode,
                    'nama' => $mapil->nama,
                    'kkm_default' => $mapil->kkm_default ?? 75,
                    'urutan' => 100 + $mapil->id,
                ]);
                $mObj->id = $mKey;
                $mObj->is_mapil = true;
                $kurikulumMapels->put($mKey, $mObj);
            }
        }

        foreach ($mapilAkhirs as $naMapil) {
            $mapil = $naMapil->mapelPilihan;
            if (!$mapil) continue;
            $mKey = 90000 + $mapil->id;
            if (!$kurikulumMapels->has($mKey)) {
                $mObj = new Mapel([
                    'kode' => $mapil->kode,
                    'nama' => $mapil->nama,
                    'kkm_default' => $mapil->kkm_default ?? 75,
                    'urutan' => 100 + $mapil->id,
                ]);
                $mObj->id = $mKey;
                $mObj->is_mapil = true;
                $kurikulumMapels->put($mKey, $mObj);
            }
        }

        // Kelompokkan mapel berdasarkan KelompokMapel (A, B, C, D)
        $kelompokMaster = KelompokMapel::orderBy('urutan')->get();
        if ($kelompokMaster->isEmpty()) {
            $kelompokMaster = collect([
                (object) ['id' => 1, 'kode' => 'A', 'nama' => 'Muatan Umum', 'urutan' => 1],
                (object) ['id' => 2, 'kode' => 'B', 'nama' => 'Muatan Kejuruan', 'urutan' => 2],
                (object) ['id' => 3, 'kode' => 'C', 'nama' => 'Muatan Lokal', 'urutan' => 3],
                (object) ['id' => 4, 'kode' => 'D', 'nama' => 'Projek Penguatan Profil Pelajar Pancasila (P5) & PKL', 'urutan' => 4],
            ]);
        }

        // Pastikan kelompok Mata Pelajaran Pilihan ada jika ada mapil
        $hasMapil = $kurikulumMapels->contains(fn($m) => !empty($m->is_mapil));
        if ($hasMapil) {
            $hasGroupPilihan = $kelompokMaster->contains(fn($km) => str_contains(strtolower($km->nama), 'pilihan'));
            if (!$hasGroupPilihan) {
                $kelompokMaster->push((object) [
                    'id' => 'pilihan',
                    'kode' => 'C',
                    'nama' => 'Mata Pelajaran Pilihan',
                    'urutan' => 2.5,
                ]);
            }
        }

        // Matriks nilai siswa: [mapel_id][semester_index] = score
        $studentScores = [];
        $semesterIdToIdx = [];

        foreach ($nilaiAkhirs as $na) {
            $pm = $na->penugasanMengajar;
            if (!$pm || !$pm->mapel_id) {
                continue;
            }

            $tingkat = $pm->rombel?->tingkat ?? 'X';
            $semName = $pm->semester?->semester ?? 'ganjil';
            $semIdx = self::getSemesterIndex($tingkat, $semName);

            if ($pm->semester_id) {
                $semesterIdToIdx[$pm->semester_id] = $semIdx;
            }

            $score = $na->nilai_akhir !== null ? (float) $na->nilai_akhir : null;
            if ($score !== null) {
                $studentScores[$pm->mapel_id][$semIdx] = $score;
            }
        }

        // Petakan nilai Seni Budaya ke semester index
        $currentTingkat = $siswa->rombel?->tingkat ?? 'X';
        $activeSem = \App\Modules\Akademik\Models\Semester::active()->first();
        if ($activeSem) {
            $semesterIdToIdx[$activeSem->id] ??= self::getSemesterIndex($currentTingkat, $activeSem->semester);
        }

        if ($mapelSeni) {
            foreach ($sbAnggotas as $sb) {
                $semIdx = $semesterIdToIdx[$sb->semester_id] ?? null;
                if (!$semIdx && $sb->semester) {
                    $semIdx = self::getSemesterIndex($currentTingkat, $sb->semester->semester);
                }
                if ($semIdx && $sb->nilai_akhir !== null) {
                    $studentScores[$mapelSeni->id][$semIdx] = (float) $sb->nilai_akhir;
                }
            }
        }

        // Petakan nilai Mapel Pilihan ke semester index
        foreach ($mapilAkhirs as $naMapil) {
            $mKey = 90000 + $naMapil->mapel_pilihan_id;
            $semIdx = $semesterIdToIdx[$naMapil->semester_id] ?? null;
            if (!$semIdx && $naMapil->semester) {
                $semIdx = self::getSemesterIndex($currentTingkat, $naMapil->semester->semester);
            }
            if ($semIdx && $naMapil->nilai_akhir !== null) {
                $studentScores[$mKey][$semIdx] = (float) $naMapil->nilai_akhir;
            }
        }

        // Susun baris mapel per kelompok
        $groupedMapels = [];
        $allMapelAverages = [];
        $totalSemesterScores = array_fill(1, 6, []);

        foreach ($kelompokMaster->sortBy('urutan') as $km) {
            $kmKode = $km->kode;
            $kmNama = $km->nama;

            $mapelsInGroup = $kurikulumMapels->filter(function ($m) use ($km) {
                if ($km->id === 'pilihan' || str_contains(strtolower($km->nama), 'pilihan')) {
                    return !empty($m->is_mapil);
                }
                if (!empty($m->is_mapil)) {
                    return false;
                }
                return $m->kelompok_mapel_id == $km->id ||
                    (empty($m->kelompok_mapel_id) && strtolower($m->kelompok ?? '') === strtolower($km->kode));
            })->sortBy('urutan');

            $rows = [];
            foreach ($mapelsInGroup as $mapel) {
                $mScores = $studentScores[$mapel->id] ?? [];
                $semScores = [];
                $filledValues = [];

                for ($s = 1; $s <= 6; $s++) {
                    $val = $mScores[$s] ?? null;
                    $semScores[$s] = $val;
                    if ($val !== null) {
                        $filledValues[] = $val;
                        $totalSemesterScores[$s][] = $val;
                    }
                }

                $rataMapel = !empty($filledValues) ? round(array_sum($filledValues) / count($filledValues), 2) : null;
                if ($rataMapel !== null) {
                    $allMapelAverages[] = $rataMapel;
                }

                $predikat = $rataMapel !== null ? self::getPredikat($rataMapel) : '-';
                $kkm = (float) ($mapel->kkm_default ?? 75);

                // Ganti nama jika Seni Budaya
                $namaMapel = $mapel->nama;
                if ((str_contains(strtolower($mapel->kode), 'seni') || str_contains(strtolower($mapel->nama), 'seni')) && $latestSb?->seniBudaya) {
                    $namaMapel = 'Seni Budaya: ' . $latestSb->seniBudaya->nama;
                }

                $rows[] = [
                    'mapel_id' => $mapel->id,
                    'kode' => $mapel->kode,
                    'nama' => $namaMapel,
                    'kkm' => $kkm,
                    'urutan' => $mapel->urutan ?? 99,
                    'semesters' => $semScores, // keys 1 to 6
                    'rata_mapel' => $rataMapel,
                    'predikat' => $predikat,
                    'keterangan' => self::getKeteranganPredikat($predikat),
                    'is_tuntas' => $rataMapel !== null ? ($rataMapel >= $kkm) : true,
                ];
            }

            if (!empty($rows)) {
                $groupedMapels[] = [
                    'kelompok_kode' => $kmKode,
                    'kelompok_nama' => $kmNama,
                    'items' => $rows,
                ];
            }
        }

        // Rata-rata per semester di bagian footer tabel
        $semesterAverages = [];
        for ($s = 1; $s <= 6; $s++) {
            $vals = $totalSemesterScores[$s];
            $semesterAverages[$s] = !empty($vals) ? round(array_sum($vals) / count($vals), 2) : null;
        }

        // Rata-rata Kumulatif Keseluruhan (IPK / Nilai Transkrip)
        $nilaiKumulatif = !empty($allMapelAverages) ? round(array_sum($allMapelAverages) / count($allMapelAverages), 2) : 0;
        $predikatKumulatif = self::getPredikat($nilaiKumulatif);

        // Periksa apakah semua mata pelajaran tuntas (rata >= KKM)
        $totalMapelCount = count($allMapelAverages);
        $totalRemedialMapels = 0;
        foreach ($groupedMapels as $g) {
            foreach ($g['items'] as $item) {
                if ($item['rata_mapel'] !== null && $item['rata_mapel'] < $item['kkm']) {
                    $totalRemedialMapels++;
                }
            }
        }

        $isLulus = $totalMapelCount > 0 && $totalRemedialMapels === 0 && $nilaiKumulatif >= 75;

        return [
            'siswa' => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nipd' => $siswa->nipd,
                'nisn' => $siswa->nisn,
                'jk' => $siswa->jk ?? $siswa->jenis_kelamin ?? 'L',
                'tempat_lahir' => $siswa->tempat_lahir ?? 'Garut',
                'tanggal_lahir' => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-',
                'rombel' => $siswa->rombel?->nama ?? '-',
                'tingkat' => $siswa->rombel?->tingkat ?? 'XII',
                'jurusan_kode' => $jurusan?->kode ?? '-',
                'jurusan_nama' => $jurusan?->nama ?? '-',
                'wali_kelas' => $siswa->rombel?->waliKelas?->name ?? '-',
                'tahun_ajaran' => $siswa->rombel?->tahunAjaran?->nama ?? '-',
            ],
            'grouped_mapels' => $groupedMapels,
            'semester_averages' => $semesterAverages,
            'summary' => [
                'total_mapel' => $totalMapelCount,
                'nilai_kumulatif' => $nilaiKumulatif,
                'predikat' => $predikatKumulatif,
                'keterangan_predikat' => self::getKeteranganPredikat($predikatKumulatif),
                'total_remedial' => $totalRemedialMapels,
                'status_kelulusan' => $isLulus ? 'MEMENUHI SYARAT KELULUSAN' : 'PERLU PERBAIKAN / REMEDIAL',
                'is_lulus' => $isLulus,
            ],
        ];
    }

    /**
     * Mengambil matriks rekapitulasi nilai 3 tahun untuk seluruh siswa dalam satu jurusan / angkatan / rombel.
     */
    public function getCohortTranskripData(
        int $jurusanId,
        string $tingkat = 'XII',
        ?int $rombelId = null,
        ?string $search = null,
        ?int $tahunAjaranId = null
    ): array {
        $jurusan = Jurusan::findOrFail($jurusanId);

        // Ambil rombel yang sesuai
        $rombelQuery = Rombel::with('tahunAjaran')->where('jurusan_id', $jurusanId);
        if ($tingkat !== 'semua') {
            $rombelQuery->where('tingkat', $tingkat);
        }
        if ($rombelId) {
            $rombelQuery->where('id', $rombelId);
        } elseif ($tahunAjaranId) {
            $rombelQuery->where('tahun_ajaran_id', $tahunAjaranId);
        }
        $rombelIds = $rombelQuery->pluck('id')->toArray();

        // Ambil seluruh siswa di rombel terpilih
        $siswaQuery = Siswa::with(['rombel.jurusan', 'rombel.tahunAjaran'])
            ->whereIn('rombel_id', $rombelIds)
            ->where('is_active', true);

        if ($search && trim($search) !== '') {
            $s = trim($search);
            $siswaQuery->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('nipd', 'like', "%{$s}%")
                    ->orWhere('nisn', 'like', "%{$s}%");
            });
        }

        $siswas = $siswaQuery->orderBy('nama')->get();

        if ($siswas->isEmpty()) {
            return [
                'jurusan' => $jurusan,
                'tingkat' => $tingkat,
                'total_siswa' => 0,
                'students' => [],
                'stats' => [
                    'rata_angkatan' => 0,
                    'nilai_tertinggi' => 0,
                    'siswa_tertinggi' => '-',
                    'nilai_terendah' => 0,
                    'persen_tuntas' => 0,
                    'jumlah_tuntas' => 0,
                ],
            ];
        }

        // Ambil semua NilaiAkhir untuk para siswa ini
        $allNilaiAkhirs = NilaiAkhir::with([
            'penugasanMengajar.semester',
            'penugasanMengajar.rombel',
        ])
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        // Ambil data nilai Seni Budaya untuk siswa rombel ini
        $allSbAnggotas = collect();
        if (class_exists(\App\Modules\SeniBudaya\Models\SeniBudayaAnggota::class)) {
            $allSbAnggotas = \App\Modules\SeniBudaya\Models\SeniBudayaAnggota::with('semester')
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->get()
                ->groupBy('siswa_id');
        }

        // Ambil data nilai Mapel Pilihan untuk siswa rombel ini
        $allMapilAkhirs = collect();
        if (class_exists(\App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir::class)) {
            $allMapilAkhirs = \App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir::with('semester')
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->whereNotNull('nilai_akhir')
                ->get()
                ->groupBy('siswa_id');
        }

        $studentRows = [];
        $cohortKumulatifs = [];

        foreach ($siswas as $siswa) {
            $nas = $allNilaiAkhirs->get($siswa->id) ?? collect();

            // Kumpulkan nilai per semester (1 s/d 6)
            $semScores = [
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => [],
            ];

            foreach ($nas as $na) {
                $pm = $na->penugasanMengajar;
                if (!$pm || $na->nilai_akhir === null) {
                    continue;
                }

                $t = $pm->rombel?->tingkat ?? 'X';
                $sName = $pm->semester?->semester ?? 'ganjil';
                $semIdx = self::getSemesterIndex($t, $sName);

                $semScores[$semIdx][] = (float) $na->nilai_akhir;
            }

            // Tambahkan nilai Seni Budaya siswa ke agregasi semester
            $sbList = $allSbAnggotas->get($siswa->id) ?? collect();
            foreach ($sbList as $sb) {
                if ($sb->nilai_akhir === null) {
                    continue;
                }
                $sName = $sb->semester?->semester ?? 'ganjil';
                $semIdx = self::getSemesterIndex($tingkat, $sName);
                $semScores[$semIdx][] = (float) $sb->nilai_akhir;
            }

            // Tambahkan nilai Mapel Pilihan siswa ke agregasi semester
            $mapilList = $allMapilAkhirs->get($siswa->id) ?? collect();
            foreach ($mapilList as $naMapil) {
                if ($naMapil->nilai_akhir === null) {
                    continue;
                }
                $sName = $naMapil->semester?->semester ?? 'ganjil';
                $semIdx = self::getSemesterIndex($tingkat, $sName);
                $semScores[$semIdx][] = (float) $naMapil->nilai_akhir;
            }

            // Hitung rata-rata per semester
            $rataS = [];
            for ($i = 1; $i <= 6; $i++) {
                $scores = $semScores[$i];
                $rataS[$i] = !empty($scores) ? round(array_sum($scores) / count($scores), 2) : null;
            }

            // Rata-rata per tingkat
            // Kelas X: (S1 + S2) / 2
            $valsX = array_filter([$rataS[1], $rataS[2]], fn($v) => $v !== null);
            $rataThnX = !empty($valsX) ? round(array_sum($valsX) / count($valsX), 2) : null;

            // Kelas XI: (S3 + S4) / 2
            $valsXI = array_filter([$rataS[3], $rataS[4]], fn($v) => $v !== null);
            $rataThnXI = !empty($valsXI) ? round(array_sum($valsXI) / count($valsXI), 2) : null;

            // Kelas XII: (S5 + S6) / 2
            $valsXII = array_filter([$rataS[5], $rataS[6]], fn($v) => $v !== null);
            $rataThnXII = !empty($valsXII) ? round(array_sum($valsXII) / count($valsXII), 2) : null;

            // Rata-rata Kumulatif 3 Tahun (dihitung dari seluruh semester yang memiliki nilai)
            $allActiveSemesters = array_filter($rataS, fn($v) => $v !== null);
            $nilaiKumulatif = !empty($allActiveSemesters) ? round(array_sum($allActiveSemesters) / count($allActiveSemesters), 2) : 0;

            if ($nilaiKumulatif > 0) {
                $cohortKumulatifs[] = $nilaiKumulatif;
            }

            $predikat = self::getPredikat($nilaiKumulatif);
            $isTuntas = $nilaiKumulatif >= 75;

            $studentRows[] = [
                'siswa_id' => $siswa->id,
                'nama' => $siswa->nama,
                'nipd' => $siswa->nipd ?? '-',
                'nisn' => $siswa->nisn ?? '-',
                'jk' => $siswa->jk ?? $siswa->jenis_kelamin ?? 'L',
                'rombel_nama' => $siswa->rombel?->nama ?? '-',
                'tahun_ajaran' => $siswa->rombel?->tahunAjaran?->nama ?? '-',
                'ta_is_active' => (bool) ($siswa->rombel?->tahunAjaran?->is_active ?? false),
                's1' => $rataS[1],
                's2' => $rataS[2],
                'rata_x' => $rataThnX,
                's3' => $rataS[3],
                's4' => $rataS[4],
                'rata_xi' => $rataThnXI,
                's5' => $rataS[5],
                's6' => $rataS[6],
                'rata_xii' => $rataThnXII,
                'nilai_kumulatif' => $nilaiKumulatif,
                'predikat' => $predikat,
                'is_tuntas' => $isTuntas,
                'ranking' => 0, // diisi setelah sorting
            ];
        }

        // Urutkan siswa berdasarkan nilai kumulatif tertinggi untuk menentukan Ranking
        usort($studentRows, function ($a, $b) {
            return $b['nilai_kumulatif'] <=> $a['nilai_kumulatif'];
        });

        $rank = 1;
        $prevScore = null;
        $assignedRank = 1;

        foreach ($studentRows as $idx => &$row) {
            if ($prevScore !== null && $row['nilai_kumulatif'] < $prevScore) {
                $assignedRank = $idx + 1;
            }
            $row['ranking'] = $assignedRank;
            $prevScore = $row['nilai_kumulatif'];
        }
        unset($row);

        // Hitung statistik angkatan
        $totalSiswa = count($studentRows);
        $rataAngkatan = !empty($cohortKumulatifs) ? round(array_sum($cohortKumulatifs) / count($cohortKumulatifs), 2) : 0;
        $nilaiTertinggi = !empty($cohortKumulatifs) ? max($cohortKumulatifs) : 0;
        $nilaiTerendah = !empty($cohortKumulatifs) ? min($cohortKumulatifs) : 0;

        $siswaTertinggi = !empty($studentRows) && $studentRows[0]['nilai_kumulatif'] > 0
            ? $studentRows[0]['nama']
            : '-';

        $jumlahTuntas = count(array_filter($studentRows, fn($r) => $r['is_tuntas']));
        $persenTuntas = $totalSiswa > 0 ? round(($jumlahTuntas / $totalSiswa) * 100, 1) : 0;

        return [
            'jurusan' => $jurusan,
            'tingkat' => $tingkat,
            'total_siswa' => $totalSiswa,
            'students' => $studentRows,
            'stats' => [
                'rata_angkatan' => $rataAngkatan,
                'nilai_tertinggi' => $nilaiTertinggi,
                'siswa_tertinggi' => $siswaTertinggi,
                'nilai_terendah' => $nilaiTerendah,
                'jumlah_tuntas' => $jumlahTuntas,
                'persen_tuntas' => $persenTuntas,
            ],
        ];
    }

    /**
     * Mengambil data komparasi capaian nilai mata pelajaran selama 3 tahun untuk suatu jurusan.
     */
    public function getMapelComparisonData(int $jurusanId, string $tingkat = 'XII'): array
    {
        $jurusan = Jurusan::findOrFail($jurusanId);

        // Ambil seluruh mapel kurikulum jurusan ini
        $sks = StrukturKurikulum::with(['mapel.kelompokMapel'])
            ->where('jurusan_id', $jurusanId)
            ->orderBy('urutan')
            ->get();

        $mapels = $sks->pluck('mapel')->filter()->unique('id')->values();
        $mapelSeni = Mapel::with('kelompokMapel')
            ->where('kode', 'SENI')
            ->orWhere('nama', 'like', '%Seni Budaya%')
            ->first();

        if ($mapelSeni && !$mapels->contains('id', $mapelSeni->id)) {
            $mapels->push($mapelSeni);
        }

        // Rombel yang relevan
        $rombelQuery = Rombel::where('jurusan_id', $jurusanId);
        if ($tingkat !== 'semua') {
            $rombelQuery->where('tingkat', $tingkat);
        }
        $rombelIds = $rombelQuery->pluck('id')->toArray();
        $siswaIds = Siswa::whereIn('rombel_id', $rombelIds)->pluck('id')->toArray();

        // Query NilaiAkhir untuk siswa-siswa ini
        $allNas = NilaiAkhir::with(['penugasanMengajar.semester', 'penugasanMengajar.rombel'])
            ->whereIn('siswa_id', $siswaIds)
            ->get();

        $mapelScores = [];
        foreach ($allNas as $na) {
            $pm = $na->penugasanMengajar;
            if (!$pm || $na->nilai_akhir === null) {
                continue;
            }

            $mId = $pm->mapel_id;
            $t = $pm->rombel?->tingkat ?? 'X';
            $sName = $pm->semester?->semester ?? 'ganjil';
            $semIdx = self::getSemesterIndex($t, $sName);

            $mapelScores[$mId][$semIdx][] = (float) $na->nilai_akhir;
        }

        // Tambahkan nilai Seni Budaya ke komparasi mapel
        if ($mapelSeni && class_exists(\App\Modules\SeniBudaya\Models\SeniBudayaAnggota::class)) {
            $allSbAnggotas = \App\Modules\SeniBudaya\Models\SeniBudayaAnggota::with('semester')
                ->whereIn('siswa_id', $siswaIds)
                ->get();

            foreach ($allSbAnggotas as $sb) {
                if ($sb->nilai_akhir === null) {
                    continue;
                }
                $sName = $sb->semester?->semester ?? 'ganjil';
                $semIdx = self::getSemesterIndex($tingkat, $sName);
                $mapelScores[$mapelSeni->id][$semIdx][] = (float) $sb->nilai_akhir;
            }
        }

        // Tambahkan Mapel Pilihan ke komparasi mapel jika ada
        if (class_exists(\App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir::class)) {
            $allMapilAkhirsComp = \App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir::with(['semester', 'mapelPilihan'])
                ->whereIn('siswa_id', $siswaIds)
                ->whereNotNull('nilai_akhir')
                ->get();

            foreach ($allMapilAkhirsComp as $naMapil) {
                $mapil = $naMapil->mapelPilihan;
                if (!$mapil) continue;
                $mKey = 90000 + $mapil->id;
                if (!$mapels->contains('id', $mKey)) {
                    $mObj = new Mapel([
                        'kode' => $mapil->kode,
                        'nama' => $mapil->nama,
                        'kkm_default' => $mapil->kkm_default ?? 75,
                        'urutan' => 100 + $mapil->id,
                    ]);
                    $mObj->id = $mKey;
                    $mObj->is_mapil = true;
                    $mapels->push($mObj);
                }

                $sName = $naMapil->semester?->semester ?? 'ganjil';
                $semIdx = self::getSemesterIndex($tingkat, $sName);
                $mapelScores[$mKey][$semIdx][] = (float) $naMapil->nilai_akhir;
            }
        }

        $comparisonRows = [];
        foreach ($mapels as $m) {
            $semAvgs = [];
            $allVals = [];

            for ($s = 1; $s <= 6; $s++) {
                $scores = $mapelScores[$m->id][$s] ?? [];
                $avg = !empty($scores) ? round(array_sum($scores) / count($scores), 2) : null;
                $semAvgs[$s] = $avg;
                if ($avg !== null) {
                    $allVals[] = $avg;
                }
            }

            $rataOverall = !empty($allVals) ? round(array_sum($allVals) / count($allVals), 2) : null;
            $kkm = (float) ($m->kkm_default ?? 75);

            $comparisonRows[] = [
                'mapel_id' => $m->id,
                'kode' => $m->kode,
                'nama' => $m->nama,
                'kelompok' => !empty($m->is_mapil) ? 'Mata Pelajaran Pilihan' : ($m->kelompokMapel?->nama ?? 'Muatan Umum'),
                'kkm' => $kkm,
                'semesters' => $semAvgs,
                'rata_overall' => $rataOverall,
                'predikat' => $rataOverall !== null ? self::getPredikat($rataOverall) : '-',
                'is_tuntas' => $rataOverall !== null ? ($rataOverall >= $kkm) : true,
            ];
        }

        return [
            'jurusan' => $jurusan,
            'items' => $comparisonRows,
        ];
    }
}
