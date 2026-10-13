<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PopulateTranskripHTLSeeder extends Seeder
{
    /**
     * Seeder untuk:
     * 1. Pengisian nilai beragam seluruh mapel untuk rombel X HTL 1 (TA 2025/2026).
     * 2. Pengisian nilai beragam seluruh mapel untuk rombel XII HTL 2 (TA 2025/2026).
     * 3. Pengisian nilai 3 tahun penuh (Semester 1 s.d 6) untuk rombel XII HTL 1
     *    agar alur logika transkrip 3 tahun tampil sempurna.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Nilai Transkrip HTL (X HTL 1, XII HTL 2, dan Rekam Jejak 3 Tahun XII HTL 1)...');

        $jurusanHtl = Jurusan::where('kode', 'HTL')->firstOrFail();

        // 1. Tahun Ajaran
        $ta2023 = TahunAjaran::where('nama', '2023/2024')->firstOrFail();
        $ta2024 = TahunAjaran::where('nama', '2024/2025')->firstOrFail();
        $ta2025 = TahunAjaran::where('nama', '2025/2026')->firstOrFail();

        // 2. Semesters
        $sem2023Ganjil = Semester::where('tahun_ajaran_id', $ta2023->id)->where('semester', 'ganjil')->firstOrFail();
        $sem2023Genap  = Semester::where('tahun_ajaran_id', $ta2023->id)->where('semester', 'genap')->firstOrFail();

        $sem2024Ganjil = Semester::where('tahun_ajaran_id', $ta2024->id)->where('semester', 'ganjil')->firstOrFail();
        $sem2024Genap  = Semester::where('tahun_ajaran_id', $ta2024->id)->where('semester', 'genap')->firstOrFail();

        $sem2025Ganjil = Semester::where('tahun_ajaran_id', $ta2025->id)->where('semester', 'ganjil')->firstOrFail();
        $sem2025Genap  = Semester::where('tahun_ajaran_id', $ta2025->id)->where('semester', 'genap')->firstOrFail();

        // 3. Mapping Guru Pengampu Mapel Perhotelan
        $guruMapping = $this->getGuruMapping();

        DB::transaction(function () use (
            $jurusanHtl, $ta2023, $ta2024, $ta2025,
            $sem2023Ganjil, $sem2023Genap,
            $sem2024Ganjil, $sem2024Genap,
            $sem2025Ganjil, $sem2025Genap,
            $guruMapping
        ) {
            // ==============================================================
            // A. ROMBEL X HTL 1 (TA 2025/2026) - Seluruh Mapel Ganjil & Genap
            // ==============================================================
            $this->command->info(PHP_EOL . '=== 1. Memproses Rombel X HTL 1 (TA 2025/2026) ===');
            $rombelXHTL1 = Rombel::where('tahun_ajaran_id', $ta2025->id)->where('nama', 'X HTL 1')->firstOrFail();
            $siswasXHTL1 = Siswa::inRombel($rombelXHTL1->id)->get();
            $this->command->info("Ditemukan {$siswasXHTL1->count()} siswa di X HTL 1.");

            $mapelsTingkatX = ['PAIBP', 'PPKN', 'BINDO', 'PJOK', 'SEJA', 'SENI', 'MATE', 'BING', 'INFO', 'IPAS', 'DDK-HTL', 'BSUNDA'];

            // Isi nilai Ganjil & Genap untuk X HTL 1
            $this->populateNilaiSemester($rombelXHTL1, $sem2025Ganjil, $mapelsTingkatX, $siswasXHTL1, 'ganjil', $guruMapping, $ta2025->id);
            $this->populateNilaiSemester($rombelXHTL1, $sem2025Genap,  $mapelsTingkatX, $siswasXHTL1, 'genap',  $guruMapping, $ta2025->id);

            // ==============================================================
            // B. SETUP & POPULATE ROMBEL XII HTL 2 (TA 2025/2026)
            // ==============================================================
            $this->command->info(PHP_EOL . '=== 2. Memproses Rombel XII HTL 2 (TA 2025/2026) ===');
            $rombelXIIHTL2 = Rombel::firstOrCreate(
                [
                    'tahun_ajaran_id' => $ta2025->id,
                    'nama' => 'XII HTL 2',
                ],
                [
                    'tingkat' => 'XII',
                    'jurusan_id' => $jurusanHtl->id,
                    'nomor_urut' => 2,
                ]
            );

            // Pastikan ada siswa di XII HTL 2
            $siswasXIIHTL2 = Siswa::inRombel($rombelXIIHTL2->id)->get();
            if ($siswasXIIHTL2->isEmpty()) {
                $siswasXIIHTL2 = $this->createSampleStudentsForRombel($rombelXIIHTL2, 16);
                $this->command->info("Berhasil membuat {$siswasXIIHTL2->count()} siswa baru untuk rombel XII HTL 2.");
            } else {
                $this->command->info("Ditemukan {$siswasXIIHTL2->count()} siswa di XII HTL 2.");
            }

            // Mapel Kelas XII: Ganjil = PKL (44 JP), Genap = Tatap Muka (48 JP)
            $this->populateNilaiSemester($rombelXIIHTL2, $sem2025Ganjil, ['PKL'], $siswasXIIHTL2, 'ganjil', $guruMapping, $ta2025->id, 5);
            $mapelsTatapMukaXII = ['PAIBP', 'PPKN', 'BINDO', 'MATE', 'BING', 'KK-HTL', 'PKK', 'MAPEL-PIL', 'BSUNDA'];
            $this->populateNilaiSemester($rombelXIIHTL2, $sem2025Genap,  $mapelsTatapMukaXII, $siswasXIIHTL2, 'genap',  $guruMapping, $ta2025->id, 6);

            // ==============================================================
            // C. POPULATE HISTORI NILAI 3 TAHUN UNTUK SISWA XII HTL 1
            // ==============================================================
            $this->command->info(PHP_EOL . '=== 3. Memproses Histori Nilai 3 Tahun untuk Rombel XII HTL 1 ===');
            $rombelXIIHTL1 = Rombel::where('tahun_ajaran_id', $ta2025->id)->where('nama', 'XII HTL 1')->firstOrFail();
            $siswasXIIHTL1 = Siswa::inRombel($rombelXIIHTL1->id)->get();
            $this->command->info("Ditemukan {$siswasXIIHTL1->count()} siswa di XII HTL 1.");

            // 1. Tahun 1: Kelas X di TA 2023/2024
            $rombelX2023 = Rombel::firstOrCreate(
                ['tahun_ajaran_id' => $ta2023->id, 'nama' => 'X HTL 1'],
                ['tingkat' => 'X', 'jurusan_id' => $jurusanHtl->id, 'nomor_urut' => 1]
            );
            $this->populateNilaiSemester($rombelX2023, $sem2023Ganjil, $mapelsTingkatX, $siswasXIIHTL1, 'ganjil', $guruMapping, $ta2023->id, 1);
            $this->populateNilaiSemester($rombelX2023, $sem2023Genap,  $mapelsTingkatX, $siswasXIIHTL1, 'genap',  $guruMapping, $ta2023->id, 2);

            // 2. Tahun 2: Kelas XI di TA 2024/2025
            $rombelXI2024 = Rombel::firstOrCreate(
                ['tahun_ajaran_id' => $ta2024->id, 'nama' => 'XI HTL 1'],
                ['tingkat' => 'XI', 'jurusan_id' => $jurusanHtl->id, 'nomor_urut' => 1]
            );
            $mapelsTingkatXI = ['PAIBP', 'PPKN', 'BINDO', 'PJOK', 'SEJA', 'MATE', 'BING', 'KK-HTL', 'PKK', 'MAPEL-PIL', 'BSUNDA'];
            $this->populateNilaiSemester($rombelXI2024, $sem2024Ganjil, $mapelsTingkatXI, $siswasXIIHTL1, 'ganjil', $guruMapping, $ta2024->id, 3);
            $this->populateNilaiSemester($rombelXI2024, $sem2024Genap,  $mapelsTingkatXI, $siswasXIIHTL1, 'genap',  $guruMapping, $ta2024->id, 4);

            // 3. Tahun 3: Kelas XII di TA 2025/2026 (Tahun Berjalan)
            // Semester 5 (Ganjil): PKL (44 JP)
            $this->populateNilaiSemester($rombelXIIHTL1, $sem2025Ganjil, ['PKL'], $siswasXIIHTL1, 'ganjil', $guruMapping, $ta2025->id, 5);
            // Semester 6 (Genap): Tatap Muka Sekolah (48 JP)
            $this->populateNilaiSemester($rombelXIIHTL1, $sem2025Genap,  $mapelsTatapMukaXII, $siswasXIIHTL1, 'genap',  $guruMapping, $ta2025->id, 6);

            // Riwayat Rombel Siswa XII HTL 1 untuk kelengkapan audit
            foreach ($siswasXIIHTL1 as $s) {
                \App\Modules\Kesiswaan\Models\RombelSiswa::firstOrCreate([
                    'tahun_ajaran_id' => $ta2023->id,
                    'siswa_id' => $s->id,
                    'rombel_id' => $rombelX2023->id,
                ]);
                \App\Modules\Kesiswaan\Models\RombelSiswa::firstOrCreate([
                    'tahun_ajaran_id' => $ta2024->id,
                    'siswa_id' => $s->id,
                    'rombel_id' => $rombelXI2024->id,
                ]);
                \App\Modules\Kesiswaan\Models\RombelSiswa::firstOrCreate([
                    'tahun_ajaran_id' => $ta2025->id,
                    'siswa_id' => $s->id,
                    'rombel_id' => $rombelXIIHTL1->id,
                ]);
            }

            $this->command->info(PHP_EOL . '>>> SEEDING NILAI TRANSKRIP HTL SELESAI DENGAN SUKSES! <<<');
        });
    }

    /**
     * Mengisi nilai formatif, AH, sumatif, dan nilai akhir untuk satu rombel & semester
     */
    private function populateNilaiSemester(
        Rombel $rombel,
        Semester $semester,
        array $mapelKodes,
        $siswas,
        string $semKey,
        array $guruMapping,
        int $tahunAjaranId,
        ?int $targetSemesterIdx = null
    ): void {
        $isGenap = $semKey === 'genap';
        $ahCodes = $isGenap ? ['AH 5', 'AH 6', 'AH 7', 'AH 8'] : ['AH 1', 'AH 2', 'AH 3', 'AH 4'];

        foreach ($mapelKodes as $mKode) {
            $mapel = Mapel::where('kode', $mKode)->first();
            if (!$mapel) {
                continue;
            }

            // Guru Pengampu
            $guruId = $guruMapping[$mKode] ?? Pegawai::first()->id;

            // Alokasi JP
            $struktur = StrukturKurikulum::where('tahun_ajaran_id', $tahunAjaranId)
                ->where('jurusan_id', $rombel->jurusan_id)
                ->where('tingkat', $rombel->tingkat)
                ->where('mapel_id', $mapel->id)
                ->first();

            $jp = $struktur ? ($isGenap ? $struktur->jp_genap : $struktur->jp_ganjil) : 4;
            if ($jp <= 0 && $mKode !== 'PKL') {
                $jp = 4;
            }

            // Buat Penugasan Mengajar
            $pm = PenugasanMengajar::firstOrCreate(
                [
                    'semester_id' => $semester->id,
                    'rombel_id' => $rombel->id,
                    'mapel_id' => $mapel->id,
                ],
                [
                    'pegawai_id' => $guruId,
                    'jp' => $jp,
                    'keterangan' => 'Penugasan Kurikulum Pembelajaran Mendalam',
                ]
            );

            // Ambil / Buat TP untuk Penugasan ini
            $tps = TujuanPembelajaran::where('penugasan_mengajar_id', $pm->id)->orderBy('alokasi_ah')->get();
            if ($tps->isEmpty()) {
                // Buat 4 TP sesuai AH
                foreach ($ahCodes as $idx => $ah) {
                    $tpKode = $isGenap ? 'TP ' . ($idx + 5) : 'TP ' . ($idx + 1);
                    $desc = $this->getDefaultTpDesc($mKode, $rombel->tingkat, $semKey, $idx);
                    $tps->push(TujuanPembelajaran::create([
                        'penugasan_mengajar_id' => $pm->id,
                        'kode' => $tpKode,
                        'alokasi_ah' => $ah,
                        'deskripsi' => $desc,
                        'is_active' => true,
                    ]));
                }
            }

            // Generate Nilai untuk Setiap Siswa
            foreach ($siswas as $idx => $siswa) {
                // Basis nilai berdasarkan profil siswa (antara 76 s.d 95)
                // Variasi dinamis per siswa, mapel, dan semester
                $baseScore = 78 + (($siswa->id * 7 + $mapel->id * 5 + ($targetSemesterIdx ?? 1) * 3) % 18); // range 78 - 95
                
                // Tambah sedikit deviasi agar beberapa siswa memiliki skor A (90+), B (80-89), atau C (70-79)
                $studentSkill = max(70, min(97, $baseScore));

                // 1. Simpan Nilai AH dan Formatif
                $ahScores = [];
                foreach ($ahCodes as $ahIdx => $ah) {
                    $offset = (($siswa->id + $ahIdx * 4) % 7) - 3; // -3 to +3
                    $scoreAh = max(68, min(98, $studentSkill + $offset));
                    $ahScores[$ah] = $scoreAh;

                    NilaiAsesmenHarian::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'penugasan_mengajar_id' => $pm->id,
                            'kode_ah' => $ah,
                        ],
                        ['nilai' => $scoreAh]
                    );

                    // Sinkron ke TP binaan AH ini
                    $targetTp = $tps->where('alokasi_ah', $ah)->first() ?? $tps->get($ahIdx);
                    if ($targetTp) {
                        NilaiFormatif::updateOrCreate(
                            [
                                'siswa_id' => $siswa->id,
                                'tujuan_pembelajaran_id' => $targetTp->id,
                            ],
                            ['nilai' => $scoreAh]
                        );
                    }
                }

                // 2. Simpan Nilai Sumatif (STS & SAS/SAT)
                $stsScore = max(70, min(98, $studentSkill + (($siswa->id % 5) - 2)));
                $sasScore = max(72, min(98, $studentSkill + ((($siswa->id + 3) % 5) - 2)));

                NilaiSumatif::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'penugasan_mengajar_id' => $pm->id,
                    ],
                    [
                        'sts' => $stsScore,
                        'sas' => $sasScore,
                    ]
                );

                // 3. Hitung dan Simpan Nilai Akhir
                $avgFormatif = round(array_sum($ahScores) / count($ahScores), 2);
                $finalScore = round(($avgFormatif * 0.5) + ($stsScore * 0.25) + ($sasScore * 0.25), 2);

                $firstTp = $tps->first();
                $lastTp  = $tps->last();

                $descTinggi = $firstTp
                    ? "Menunjukkan penguasaan yang sangat baik dalam " . lcfirst($firstTp->deskripsi)
                    : "Mencapai kompetensi pembelajaran secara optimal.";

                $descRendah = ($lastTp && $finalScore < 85)
                    ? "Perlu penguatan dan bimbingan dalam " . lcfirst($lastTp->deskripsi)
                    : "";

                $block1Key = $isGenap ? '9M3' : '9M1';
                $block2Key = $isGenap ? '9M4' : '9M2';

                $data9m = [
                    $block1Key => [
                        'nilai_formatif' => round(($ahScores[$ahCodes[0]] + $ahScores[$ahCodes[1]]) / 2, 2),
                        'nilai_sumatif' => $stsScore,
                        'nilai_akhir' => round(((($ahScores[$ahCodes[0]] + $ahScores[$ahCodes[1]]) / 2) * 0.6) + ($stsScore * 0.4), 2),
                        'deskripsi_tertinggi' => $descTinggi,
                        'deskripsi_terendah' => $descRendah,
                    ],
                    $block2Key => [
                        'nilai_formatif' => round(($ahScores[$ahCodes[2]] + $ahScores[$ahCodes[3]]) / 2, 2),
                        'nilai_sumatif' => $sasScore,
                        'nilai_akhir' => $finalScore,
                        'deskripsi_tertinggi' => $descTinggi,
                        'deskripsi_terendah' => $descRendah,
                    ],
                ];

                NilaiAkhir::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'penugasan_mengajar_id' => $pm->id,
                    ],
                    [
                        'rata_formatif' => $avgFormatif,
                        'sts' => $stsScore,
                        'sas' => $sasScore,
                        'nilai_akhir' => $finalScore,
                        'deskripsi_tertinggi' => $descTinggi,
                        'deskripsi_terendah' => $descRendah,
                        'data_9m' => $data9m,
                    ]
                );
            }
        }
    }

    /**
     * Membuat 16 siswa sample untuk rombel XII HTL 2
     */
    private function createSampleStudentsForRombel(Rombel $rombel, int $count = 16)
    {
        $names = [
            ['Aditya Pratama', 'L'],
            ['Annisa Putri Ramadhani', 'P'],
            ['Bayu Segara', 'L'],
            ['Cantika Dewi Lestari', 'P'],
            ['Daffa Alfarizi', 'L'],
            ['Fathir Ramadhan', 'L'],
            ['Gita Gutawa Putri', 'P'],
            ['Hani Syakira', 'P'],
            ['Kevin Sanjaya', 'L'],
            ['Larasati Kusuma Putri', 'P'],
            ['Muhammad Farhan', 'L'],
            ['Nabila Syahrani', 'P'],
            ['Rafi Ahmad Fauzi', 'L'],
            ['Salsa Rahmadani', 'P'],
            ['Tegar Prakoso', 'L'],
            ['Zahra Amalia Hidayat', 'P'],
        ];

        $created = collect();
        $rayonId = DB::table('rayons')->value('id') ?? 1;

        foreach ($names as $idx => $item) {
            $nama = $item[0];
            $jk = $item[1];
            $nisn = '008' . str_pad((string)(2000000 + $idx), 7, '0', STR_PAD_LEFT);
            $nipd = '123' . str_pad((string)(1050 + $idx), 5, '0', STR_PAD_LEFT);

            // User login siswa
            $user = User::firstOrCreate(
                ['email' => "siswa.htl2.{$idx}@wikrama.sch.id"],
                [
                    'username' => 'siswa_htl2_' . ($idx + 1),
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );

            $siswa = Siswa::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'user_id' => $user->id,
                    'rombel_id' => $rombel->id,
                    'rayon_id' => $rayonId,
                    'nama' => $nama,
                    'nipd' => $nipd,
                    'jk' => $jk,
                    'agama' => 'Islam',
                    'tempat_lahir' => 'Garut',
                    'tanggal_lahir' => '2007-05-15',
                    'alamat' => 'Jl. Raya Garut No. ' . ($idx + 10),
                    'status' => 'aktif',
                    'is_active' => true,
                ]
            );

            \App\Modules\Kesiswaan\Models\RombelSiswa::firstOrCreate([
                'tahun_ajaran_id' => $rombel->tahun_ajaran_id,
                'siswa_id' => $siswa->id,
                'rombel_id' => $rombel->id,
            ]);

            $created->push($siswa);
        }

        return $created;
    }

    /**
     * Mapping Guru Pengampu Mapel
     */
    private function getGuruMapping(): array
    {
        $findId = function ($keyword) {
            return Pegawai::where('nama_lengkap', 'like', "%{$keyword}%")->value('id') ?? Pegawai::first()->id;
        };

        return [
            'PAIBP'     => $findId('M. Ridwan'),
            'PPKN'      => $findId('Yanti Sumiati'),
            'BINDO'     => $findId('Asep Gunawan'),
            'PJOK'      => $findId('Budi Santoso'),
            'SEJA'      => $findId('Fajar Nugraha'),
            'SENI'      => $findId('Anita Rahayu'),
            'MATE'      => $findId('Rina Marlina'),
            'BING'      => $findId('Sarah Lestari'),
            'INFO'      => $findId('Beni Iskandar'),
            'IPAS'      => $findId('Wahyu Hidayat'),
            'DDK-HTL'   => $findId('Dewi Anggraeni'),
            'KK-HTL'    => $findId('Dewi Anggraeni'),
            'PKK'       => $findId('Gina Gandina'),
            'PKL'       => $findId('Dewi Anggraeni'),
            'MAPEL-PIL' => $findId('Maya Sartika'),
            'BSUNDA'    => $findId('Cecep Supriatna'),
            'KKA'       => $findId('Ilham Maulana'),
        ];
    }

    /**
     * Fallback deskripsi TP resmi jika belum ter-generate
     */
    private function getDefaultTpDesc(string $mapelKode, string $tingkat, string $semKey, int $idx): string
    {
        $mapel = Mapel::where('kode', $mapelKode)->first();
        $nama = $mapel ? $mapel->nama : $mapelKode;
        return "Menguasai kompetensi esensial {$nama} lingkup materi " . ($idx + 1) . " pada semester {$semKey}.";
    }
}
