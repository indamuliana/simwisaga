<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TujuanPembelajaranKurikulumMendalamSeeder extends Seeder
{
    /**
     * Seeder Tujuan Pembelajaran (TP) Resmi Kurikulum Merdeka SMK
     * Sumber: Keputusan Kepala BSKAP Kemendikbudristek No. 032/H/KR/2024
     * & Panduan Pembelajaran dan Asesmen (PPA) Kemendikdasmen RI
     *
     * Alokasi Sepanjang Tahun:
     * - Semester Ganjil: AH 1, AH 2, AH 3, AH 4 (Kode: TP 1 s.d TP 4)
     * - Semester Genap : AH 5, AH 6, AH 7, AH 8 (Kode: TP 5 s.d TP 8)
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Tujuan Pembelajaran (TP) Resmi Kurikulum Merdeka SMK...');

        $ta = TahunAjaran::where('is_active', true)->first();
        if (!$ta) {
            $ta = TahunAjaran::where('nama', '2025/2026')->firstOrFail();
        }

        $semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->firstOrFail();
        $semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->firstOrFail();

        // 1. DATA BANK TUJUAN PEMBELAJARAN RESMI BSKAP 032/H/KR/2024
        $tpBank = $this->getBankTujuanPembelajaran();

        DB::transaction(function () use ($ta, $semGanjil, $semGenap, $tpBank) {
            // A. SELARASKAN PENUGASAN MENGAJAR SEMESTER GENAP UNTUK MAPEL SEPANJANG TAHUN
            $this->sinkronisasiPenugasanGenap($ta, $semGanjil, $semGenap);

            // B. PERBAIKI KHUSUS PENUGASAN 228 & 258 (PAIBP KELAS X HTL 1)
            $this->rapikanPenugasan228Dan258($semGanjil, $semGenap);

            // C. POPULATE TP RESMI UNTUK SELURUH PENUGASAN MENGAJAR DI TAHUN AJARAN AKTIF
            $allPenugasans = PenugasanMengajar::with(['mapel', 'rombel', 'semester'])
                ->whereHas('semester', function ($q) use ($ta) {
                    $q->where('tahun_ajaran_id', $ta->id);
                })
                ->get();

            $totalTpCreated = 0;
            $totalTpUpdated = 0;

            foreach ($allPenugasans as $pm) {
                if (!$pm->mapel || !$pm->rombel || !$pm->semester) {
                    continue;
                }

                $mapelKode = $pm->mapel->kode;
                $tingkat = $pm->rombel->tingkat;
                $isGenap = str_contains(strtolower($pm->semester->semester), 'genap');
                $semKey = $isGenap ? 'genap' : 'ganjil';

                // Cari TP di Bank Data
                $tpsForMapel = $this->resolveTpsFromBank($tpBank, $mapelKode, $tingkat, $semKey);
                if (empty($tpsForMapel)) {
                    continue;
                }

                foreach ($tpsForMapel as $tpData) {
                    $existing = TujuanPembelajaran::where('penugasan_mengajar_id', $pm->id)
                        ->where('alokasi_ah', $tpData['alokasi_ah'])
                        ->first();

                    if (!$existing) {
                        $existingByKode = TujuanPembelajaran::where('penugasan_mengajar_id', $pm->id)
                            ->where('kode', $tpData['kode'])
                            ->first();

                        if ($existingByKode) {
                            $existingByKode->update([
                                'alokasi_ah' => $tpData['alokasi_ah'],
                                'deskripsi' => $tpData['deskripsi'],
                                'is_active' => true,
                            ]);
                            $totalTpUpdated++;
                            continue;
                        }

                        TujuanPembelajaran::create([
                            'penugasan_mengajar_id' => $pm->id,
                            'kode' => $tpData['kode'],
                            'alokasi_ah' => $tpData['alokasi_ah'],
                            'deskripsi' => $tpData['deskripsi'],
                            'is_active' => true,
                        ]);
                        $totalTpCreated++;
                    } else {
                        $existing->update([
                            'kode' => $tpData['kode'],
                            'deskripsi' => $tpData['deskripsi'],
                            'is_active' => true,
                        ]);
                        $totalTpUpdated++;
                    }
                }
            }

            $this->command->info("Seeding TP Selesai! Berhasil membuat {$totalTpCreated} TP baru dan memperbarui {$totalTpUpdated} TP.");
        });
    }

    /**
     * Memastikan penugasan semester genap ada untuk mata pelajaran tahunan
     */
    private function sinkronisasiPenugasanGenap($ta, $semGanjil, $semGenap): void
    {
        $ganjilPms = PenugasanMengajar::where('semester_id', $semGanjil->id)->get();

        foreach ($ganjilPms as $gPm) {
            $mapel = Mapel::find($gPm->mapel_id);
            $rombel = Rombel::find($gPm->rombel_id);

            if (!$mapel || !$rombel) {
                continue;
            }

            // PKL Kelas XII khusus di Semester 1 (Ganjil) saja, jangan disalin ke Genap
            if ($mapel->kode === 'PKL' && $rombel->tingkat === 'XII') {
                continue;
            }

            // Cek apakah di struktur kurikulum semester genap ada jamnya (> 0)
            $strukturGenap = StrukturKurikulum::where('tahun_ajaran_id', $ta->id)
                ->where('jurusan_id', $rombel->jurusan_id)
                ->where('tingkat', $rombel->tingkat)
                ->where('mapel_id', $mapel->id)
                ->first();

            $jpGenap = $strukturGenap ? (int) $strukturGenap->jp_genap : (int) $gPm->jp;
            if ($jpGenap <= 0 && $strukturGenap !== null) {
                continue;
            }

            PenugasanMengajar::firstOrCreate(
                [
                    'semester_id' => $semGenap->id,
                    'mapel_id' => $gPm->mapel_id,
                    'rombel_id' => $gPm->rombel_id,
                ],
                [
                    'pegawai_id' => $gPm->pegawai_id,
                    'jp' => $jpGenap > 0 ? $jpGenap : $gPm->jp,
                    'keterangan' => $gPm->keterangan,
                ]
            );
        }

        // Khusus Kelas XII: Mapel tatap muka di Semester Genap (48 JP) jika belum ada di ganjil
        $rombelsXII = Rombel::where('tahun_ajaran_id', $ta->id)->where('tingkat', 'XII')->get();
        foreach ($rombelsXII as $r12) {
            $struktursGenapXII = StrukturKurikulum::where('tahun_ajaran_id', $ta->id)
                ->where('jurusan_id', $r12->jurusan_id)
                ->where('tingkat', 'XII')
                ->where('jp_genap', '>', 0)
                ->get();

            foreach ($struktursGenapXII as $sk) {
                if ($sk->mapel && $sk->mapel->kode === 'PKL') {
                    continue;
                }

                // Ambil guru dari tingkat XI atau guru mapel terkait
                $existingGenapPm = PenugasanMengajar::where('semester_id', $semGenap->id)
                    ->where('rombel_id', $r12->id)
                    ->where('mapel_id', $sk->mapel_id)
                    ->first();

                if (!$existingGenapPm) {
                    $guruId = PenugasanMengajar::where('mapel_id', $sk->mapel_id)
                        ->whereNotNull('pegawai_id')
                        ->value('pegawai_id');

                    PenugasanMengajar::create([
                        'semester_id' => $semGenap->id,
                        'rombel_id' => $r12->id,
                        'mapel_id' => $sk->mapel_id,
                        'pegawai_id' => $guruId,
                        'jp' => $sk->jp_genap,
                        'keterangan' => 'Tatap Muka Semester 2 Kelas XII',
                    ]);
                }
            }
        }
    }

    /**
     * Perbaikan Khusus Penugasan 228 & 258 (PAIBP X HTL 1)
     */
    private function rapikanPenugasan228Dan258($semGanjil, $semGenap): void
    {
        $pm228 = PenugasanMengajar::find(228);
        $pm258 = PenugasanMengajar::find(258);

        if ($pm228) {
            // Pastikan TP 1 s.d TP 4 lengkap di Ganjil
            $ganjilTps = [
                ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis Q.S. al-Ma'idah/5: 48 dan Q.S. at-Taubah/9: 105 serta hadis terkait kompetisi dalam kebaikan dan etos kerja."],
                ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis makna syu'abul iman (cabang-cabang iman), tanda-tanda, serta mempraktikkannya dalam kehidupan sehari-hari."],
                ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi pencegahan perilaku tercela (ghadhab, keras hati, dan munafik) dalam interaksi pergaulan remaja."],
                ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan prinsip dan ketentuan fikih muamalah (asuransi syariah, bank syariah, dan koperasi syariah) dalam bertransaksi."],
            ];

            foreach ($ganjilTps as $gtp) {
                TujuanPembelajaran::updateOrCreate(
                    ['penugasan_mengajar_id' => $pm228->id, 'alokasi_ah' => $gtp['alokasi_ah']],
                    [
                        'kode' => $gtp['kode'],
                        'deskripsi' => $gtp['deskripsi'],
                        'is_active' => true,
                    ]
                );
            }
        }

        if ($pm258) {
            // Perbaiki TP Genap: Update TP dummy ID 115 (Ini adalah TP 7) menjadi TP 8 AH 8 resmi
            $tpDummy = TujuanPembelajaran::where('penugasan_mengajar_id', $pm258->id)
                ->where('deskripsi', 'like', '%Ini adalah TP 7%')
                ->first();

            if ($tpDummy) {
                $tpDummy->update([
                    'kode' => 'TP 8',
                    'alokasi_ah' => 'AH 8',
                    'deskripsi' => "Mempraktikkan tata cara perawatan jenazah (memandikan, mengkafani, menyalatkan, dan menguburkan) sesuai syariat Islam.",
                    'is_active' => true,
                ]);
            }

            $genapTps = [
                ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis Q.S. al-Hujurat/49: 10 dan 13 serta hadis tentang toleransi (tasamuh) dan persaudaraan (ukhuwah)."],
                ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Meneladani nilai-nilai keteladanan ulama penyebar Islam di Nusantara (Wali Songo) dan kearifan lokal dakwah Islam."],
                ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis hakikat mencintai Allah Swt., khauf, raja', dan tawakal dalam memperkokoh tauhid keseharian."],
                ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mempraktikkan tata cara perawatan jenazah (memandikan, mengkafani, menyalatkan, dan menguburkan) sesuai syariat Islam."],
            ];

            foreach ($genapTps as $gtp) {
                TujuanPembelajaran::updateOrCreate(
                    ['penugasan_mengajar_id' => $pm258->id, 'alokasi_ah' => $gtp['alokasi_ah']],
                    [
                        'kode' => $gtp['kode'],
                        'deskripsi' => $gtp['deskripsi'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Resolusi TP dari bank berdasarkan kode mapel dan aliasnya
     */
    private function resolveTpsFromBank(array $tpBank, string $mapelKode, string $tingkat, string $semKey): array
    {
        // Normalisasi alias kode mapel
        $normalizedKode = match ($mapelKode) {
            'MTK' => 'MATE',
            'BIND' => 'BINDO',
            'DPPLG' => 'DDK-PPLG',
            'BD' => 'KK-PPLG',
            'PWPB' => 'KK-PPLG',
            default => $mapelKode,
        };

        if (isset($tpBank[$normalizedKode][$tingkat][$semKey])) {
            return $tpBank[$normalizedKode][$tingkat][$semKey];
        }

        // Fallback jika hanya ada per tingkat tanpa semester terpisah atau umum
        if (isset($tpBank[$normalizedKode]['ALL'][$semKey])) {
            return $tpBank[$normalizedKode]['ALL'][$semKey];
        }

        return [];
    }

    /**
     * Bank Tujuan Pembelajaran (TP) Lengkap & Kredibel BSKAP No. 032/H/KR/2024
     */
    private function getBankTujuanPembelajaran(): array
    {
        return [
            // 1. PENDIDIKAN AGAMA ISLAM DAN BUDI PEKERTI
            'PAIBP' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis Q.S. al-Ma'idah/5: 48 dan Q.S. at-Taubah/9: 105 serta hadis terkait kompetisi dalam kebaikan dan etos kerja."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis makna syu'abul iman (cabang-cabang iman), tanda-tanda, serta mempraktikkannya dalam kehidupan sehari-hari."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi pencegahan perilaku tercela (ghadhab, keras hati, dan munafik) dalam interaksi pergaulan remaja."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan prinsip dan ketentuan fikih muamalah (asuransi syariah, bank syariah, dan koperasi syariah) dalam bertransaksi."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis Q.S. al-Hujurat/49: 10 dan 13 serta hadis tentang toleransi (tasamuh) dan persaudaraan (ukhuwah)."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Meneladani nilai-nilai keteladanan ulama penyebar Islam di Nusantara (Wali Songo) dan kearifan lokal dakwah Islam."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis hakikat mencintai Allah Swt., khauf, raja', dan tawakal dalam memperkokoh tauhid keseharian."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mempraktikkan tata cara perawatan jenazah (memandikan, mengkafani, menyalatkan, dan menguburkan) sesuai syariat Islam."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis Q.S. Ali 'Imran/3: 190-191 dan 159 serta hadis terkait berpikir kritis dan bersikap demokratis."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menjelaskan cabang iman tentang memelihara lisan, menutup aib orang lain, dan menepati janji dalam pergaulan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menghindari bahaya minuman keras, judi, dan pertengkaran antarpelajar melalui penguatan akhlak mulia."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan rukun, syarat, dan adab pelaksanaan khutbah, tablig, dan dakwah di lingkungan masyarakat."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis Q.S. Yunus/10: 40-41 dan Q.S. al-Ma'idah/5: 32 serta hadis toleransi dan larangan tindak kekerasan."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menganalisis perkembangan peradaban Islam pada masa kejayaan (Umayyah dan Abbasiyah) serta keteladanan tokohnya."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menerapkan hukum Islam tentang pernikahan, talak, dan iddah untuk mewujudkan keluarga sakinah mawaddah warahmah."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengidentifikasi ketentuan waris (faraidh) dan pembagian harta pusaka sesuai kaidah fikih mawaris."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis Q.S. Luqman/31: 13-14 dan hadis tentang kewajiban beribadah serta berbakti kepada kedua orang tua."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Meyakini dan menganalisis makna iman kepada hari akhir (kiamat) serta dampaknya terhadap perilaku mawas diri."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengembangkan sikap kerja keras, tanggung jawab, dan integritas profesional berbasis etika Islam."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan ketentuan ijtihad dan sumber hukum Islam sekunder dalam menyelesaikan persoalan kontemporer."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis Q.S. al-Baqarah/2: 155-156 serta hadis tentang sabar dan tabah menghadapi ujian hidup."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Meneladani keteladanan pemikir dan pembaharu Islam modern (Jamaluddin al-Afghani, Muhammad Abduh, dll)."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menumbuhkan sikap moderasi beragama dan kerukunan umat beragama dalam bingkai kebinekaan Indonesia."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menganalisis perkembangan Islam di dunia global kontemporer serta kontribusinya bagi peradaban dunia."],
                    ],
                ],
            ],

            // 2. PENDIDIKAN PANCASILA
            'PPKN' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis kedudukan Pancasila sebagai ideologi negara, pandangan hidup, dan dasar pembentukan norma hukum."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menginternalisasi penerapan nilai-nilai sila Pancasila dalam pemecahan masalah kehidupan bermasyarakat dan berbangsa."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis produk peraturan perundang-undangan dan hierarki tata urutan regulasi hukum di Indonesia."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menunjukkan komitmen penegakan hak dan kewajiban warga negara yang dijamin dalam UUD NRI Tahun 1945."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengidentifikasi potensi benturan dan strategi resolusi konflik sosial berbasis keragaman budaya Nusantara."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mempromosikan budaya gotong royong dan kolaborasi antarkelompok dalam masyarakat majemuk Indonesia."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis ancaman militer dan non-militer terhadap keutuhan NKRI di era perkembangan teknologi informasi."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Merancang aksi nyata peran warga negara dalam mempertahankan kedaulatan negara kesatuan Republik Indonesia."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Membedakan keterkaitan antarsila Pancasila sebagai satu kesatuan bulat dan utuh dalam gagasan kenegaraan."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Mengevaluasi peluang dan tantangan penerapan nilai Pancasila di era disrupsi digital dan globalisasi."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis sistem hukum dan peradilan nasional dalam penegakan kepastian hukum dan keadilan."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengkaji pelanggaran hak asasi manusia (HAM) berat serta instrumen perlindungan HAM nasional dan internasional."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Merumuskan strategi pelestarian kearifan lokal dan pertahanan identitas budaya nasional dari pengaruh global."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menunjukkan peran aktif pemuda dalam memupuk harmoni kebinekaan dan toleransi antargolongan."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis batas wilayah kedaulatan Indonesia, zona maritim (ZEE), dan pengelolaan perbatasan terluar."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengevaluasi sistem pertahanan dan keamanan rakyat semesta (Sishankamrata) dalam menjaga stabilitas nasional."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis praktik baik pengamalan ideologi Pancasila dalam tata kelola dunia usaha dan dunia industri (DUDI)."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Merumuskan solusi konstitusional terhadap fenomena intoleransi dan polarisasi dalam iklim demokrasi modern."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menelaah dinamika keterbukaan informasi publik dan etika konstitusional dalam partisipasi politik digital."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengevaluasi peran lembaga penegak hukum (Kepolisian, Kejaksaan, KPK, Kehakiman) dalam penegakan hukum."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis peran Indonesia dalam menjaga perdamaian dunia melalui diplomasi dan organisasi internasional."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Memproyeksikan kontribusi lulusan SMK dalam pembangunan ketahanan ekonomi dan kemandirian bangsa."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis pengaruh kemajuan IPTEK terhadap persatuan dan integritas bangsa dalam kerangka NKRI."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Merancang proyek kewarganegaraan digital yang berlandaskan etika moral Pancasila di ruang siber."],
                    ],
                ],
            ],

            // 3. BAHASA INDONESIA
            'BINDO' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Mengevaluasi informasi akurat dan gagasan utama dalam teks laporan hasil observasi (LHO) secara kritis."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menulis teks laporan hasil observasi sistematis berbasis fakta lapangan dengan kaidah kebahasaan baku."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis pesan tersurat dan tersirat serta struktur humor kritik dalam teks anekdot lisan maupun tulis."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menulis teks eksposisi hasil penelitian sederhana disertai argumen logis dan rujukan data yang valid."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengidentifikasi unsur intrinsik, nilai moral, dan karakteristik bahasa arkais dalam hikayat serta cerpen."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menulis cerita pendek inspiratif kontekstual kehidupan SMK dengan sudut pandang penceritaan variatif."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis struktur taktik persuasif dan kesepakatan rasional dalam negosiasi lisan maupun tulis."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengembangkan teks biografi tokoh inspiratif menggunakan gaya naratif faktual yang runtut dan menarik."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis ide pokok dan hubungan kausalitas dalam teks prosedur kompleks bidang vokasi/kejuruan."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menyusun rancangan teks prosedur operasional standar (SOP) kerja yang akurat dan mudah dipahami."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis struktur sebab-akibat dan fenomena ilmiah dalam teks eksplanasi lingkungan dan industri."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengonstruksi naskah ceramah atau orasi persuasif dengan memperhatikan intonasi, diksi, dan pesan etis."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengkaji unsur pembangun, pesan simbolik, dan musikalitas puisi modern serta membaca indah puisi."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun proposal kegiatan sekolah atau proposal wirausaha dengan struktur metodologis yang sistematis."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis sistematika dan kaidah kebahasaan karya ilmiah terapan berbasis pemecahan masalah kejuruan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menulis resensi atau ulasan kritis terhadap karya sastra dan buku nonfiksi dengan argumen objektif."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Mengidentifikasi unsur surat lamaran pekerjaan dan menyusun surat lamaran profesional berbasis lowongan nyata."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis riwayat informasi sejarah dan nilai perjuangan dalam teks cerita/novel sejarah Indonesia."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menulis esai atau teks editorial berbobot tentang isu aktual dunia vokasi dengan argumentasi kritis solutif."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis opini publik, fakta, dan gaya bahasa retoris dalam artikel ilmiah populer di media massa."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengevaluasi pesan moral dan kritik sosial dalam naskah drama panggung serta memvisualisasikannya."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun naskah drama pendek bertema dinamika dunia kerja dengan konflik dramatik yang proporsional."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengembangkan portofolio karya tulis dan naskah presentasi profesional untuk persiapan UKK keahlian."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menampilkan presentasi persuasif dan wawancara kerja profesional dengan artikulasi jelas dan santun."],
                    ],
                ],
            ],

            // 4. MATEMATIKA
            'MATE' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menerapkan konsep bilangan berpangkat (eksponen), bentuk akar, dan logaritma dalam perhitungan aplikatif."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menentukan suku ke-n dan jumlah deret aritmetika serta geometri dalam konteks pemodelan pertumbuhan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menyelesaikan sistem persamaan linear tiga variabel (SPLTV) untuk memecahkan masalah kontekstual kejuruan."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Memodelkan dan menyelesaikan sistem pertidaksamaan linear dua variabel dalam optimasi masalah ekonomi."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menggunakan perbandingan trigonometri sudut lancip pada segitiga siku-siku untuk mengukur ketinggian/jarak."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menerapkan aturan sinus dan kosinus untuk menghitung panjang sisi dan besar sudut pada segitiga sembarang."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis pemusatan dan penyebaran data kelompok (mean, median, modus, varians) dengan tabel/diagram."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menghitung peluang kejadian majemuk (saling lepas, saling bebas, dan bersyarat) dalam eksperimen statistik."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menentukan domain, range, serta operasi aljabar fungsi komposisi dalam pemodelan proses berantai."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menentukan fungsi invers dan memverifikasi sifat-sifat invers fungsi pada masalah kehidupan nyata."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengonstruksi persamaan lingkaran berpusat di (0,0) dan (a,b) serta persamaan garis singgung lingkaran."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan konsep sudut pusat, sudut keliling, panjang busur, dan luas juring lingkaran dalam rekayasa."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menyelesaikan operasi penjumlahan, perkalian matriks, dan transpos matriks berordo 2x2/3x3."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menentukan determinan dan invers matriks serta menggunakannya untuk menyelesaikan SPL."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menerapkan transformasi geometri (translasi, refleksi, rotasi, dilatasi) pada koordinat bidang kartesius."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menganalisis regresi linear sederhana dan koefisien korelasi Pearson untuk menginterpretasikan tren data."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menghitung nilai limit fungsi aljabar pada titik berhingga dan mendekati tak hingga dengan pemfaktoran."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menentukan turunan pertama fungsi aljabar menggunakan sifat-sifat dasar dan aturan rantai turunan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menerapkan konsep turunan fungsi untuk menentukan interval fungsi naik/turun dan titik stasioner."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menyelesaikan masalah kontekstual optimasi (keuntungan maksimum dan biaya minimum) berbasis turunan."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menentukan antiturunan (integral tak tentu) fungsi aljabar berdasarkan teorema dasar kalkulus."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menghitung nilai integral tentu fungsi aljabar dan menggunakannya untuk menentukan luas daerah bidang datar."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis konsep bunga tunggal, bunga majemuk, anuitas, dan amortisasi dalam transaksi keuangan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Memodelkan dan mengevaluasi kelayakan investasi modal usaha menggunakan kalkulasi matematika finansial."],
                    ],
                ],
            ],

            // 5. BAHASA INGGRIS
            'BING' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis ungkapan memperkenalkan diri dan orang lain dalam interaksi formal vokasi."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Memproduksi teks deskriptif lisan dan tulis tentang profil tempat wisata dan tempat kerja kejuruan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menggunakan ungkapan memberi dan menanggapi ucapan selamat serta pujian bersayap."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menyusun teks pemberitahuan (announcement) resmi yang komunikatif sesuai konteks kegiatan sekolah."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis teks recount lisan dan tulis tentang pengalaman kerja lampau atau observasi industri."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun teks recount personal dan faktual menggunakan pola kalimat past tense secara runtut."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengidentifikasi pesan moral, alur, dan karakteristik bahasa kiasan dalam teks naratif legenda Nusantara."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menulis teks prosedur manual pengoperasian alat atau panduan teknis keselamatan kerja."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menerapkan ungkapan memberi dan meminta pendapat serta pikiran logis dalam percakapan."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menyusun teks undangan resmi (formal invitation) untuk pameran industri atau rapat kedinasan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis struktur dan argumen dalam teks eksposisi analitis bertema teknologi dan lingkungan."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menulis teks eksposisi analitis persuasif tentang isu industri dengan argumen pendukung faktual."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menggunakan kalimat pasif (passive voice) dalam memaparkan alur kerja dan manual panduan teknis."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menganalisis klausa sebab-akibat (cause and effect clauses) dalam teks eksplanasi ilmiah."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menafsirkan makna lirik lagu berbahasa Inggris terkait empati sosial dan motivasi perjuangan hidup."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Melakukan simulasi percakapan telepon bisnis dan penanganan reservasi/keluhan pelanggan."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis application letter dan merancang surat lamaran kerja profesional berbahasa Inggris beserta CV."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Melakukan simulasi wawancara kerja (job interview) berbahasa Inggris dengan intonasi percaya diri."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menginterpretasikan data grafik, tabel, infografis tren industri kejuruan secara verbal."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan kalimat pengandaian bersyarat (conditional sentences) dalam negosiasi solusi kerja."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis teks laporan teknis (technical report) dan manual instalasi piranti berbasis standar internasional."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun laporan ringkas hasil magang/PKL berbahasa Inggris dengan format baku laporan industri."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menyampaikan presentasi bisnis produk atau jasa unggulan di hadapan audiens global."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengevaluasi dokumen korespondensi niaga (inquiry letter, quotation, invoice) dalam perdagangan."],
                    ],
                ],
            ],

            // 6. INFORMATIKA
            'INFO' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menerapkan 4 pilar berpikir komputasional (dekomposisi, pola, abstraksi, algoritma) dalam pemecahan masalah."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Memanfaatkan fitur integrasi aplikasi perkantoran (mail merge, embed object, chart) dalam dokumen resmi."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi komponen perangkat keras, perangkat lunak, siklus pemrosesan instruksi, dan OS."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis topologi jaringan lokal, mekanisme pengalamatan IP, protokol internet, dan keamanan siber."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengolah, membersihkan, dan memodelkan data bervolume besar menggunakan teknik data wrangling."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Merancang algoritma dengan flowchart dan pseudocode serta mengimplementasikannya dalam bahasa Python."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengembangkan kode program modular menggunakan fungsi, percabangan, perulangan, dan struktur array."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menganalisis dampak sosial digital, etika lisensi software, serta merancang proyek komputasi terapan."],
                    ],
                ],
            ],

            // 7. PROJEK IPAS
            'IPAS' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis keterkaitan interaksi biotik-abiotik, rantai makanan, dan mitigasi pencemaran lingkungan."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Mengklasifikasi zat, sifat fisika-kimia materi, serta mempraktikkan pemisahan campuran dan pencegahan korosi."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menerapkan konsep usaha, energi kinetik, potensial, serta konversi energi terbarukan ramah lingkungan."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengidentifikasi struktur lapisan bumi, mitigasi bencana gempa/tsunami, dan orbit antariksa."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis konektivitas spasial, letak geografis, dan pengaruh aksesibilitas wilayah terhadap potensi daerah."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menelaah dinamika interaksi sosial, fungsi lembaga sosial, dan resolusi konflik budaya masyarakat."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis perilaku pelaku ekonomi, mekanisme pasar permintaan-penawaran, dan literasi keuangan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Merancang dan menyajikan laporan projek ilmiah terpadu IPAS untuk mengatasi permasalahan limbah SMK."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis dinamika ekosistem industri dan penerapan prinsip zero waste dalam produksi."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Mengkaji reaksi kimia terapan dan dampaknya terhadap keselamatan lingkungan kerja."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengevaluasi efisiensi konsumsi energi listrik dan termal pada peralatan kejuruan."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menyusun strategi mitigasi risiko bencana alam berbasis kearifan lokal di lingkungan sekolah."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis potensi geospasial kawasan industri dan keterhubungannya dengan jalur logistik."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengkaji interaksi sosial ketenagakerjaan dan budaya kerja multikultural di dunia usaha."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis dampak inflasi dan fluktuasi ekonomi makro terhadap daya beli masyarakat."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Melaksanakan projek aksi terapan IPAS berbasis solusi masalah riil di industri kejuruan."],
                    ],
                ],
            ],

            // 8. SEJARAH
            'SEJA' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menerapkan konsep dasar ilmu sejarah (diakronik, sinkronik, kausalitas) dalam meneliti masa lalu."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis corak kehidupan manusia purba praaksara Indonesia dan teknologi adaptasi lingkungannya."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menelaah proses masuknya pengaruh kebudayaan Hindu-Buddha serta jalur perdagangan maritim Nusantara."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis sistem pemerintahan, peninggalan budaya, dan dinamika kerajaan Hindu-Buddha di Indonesia."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis teori masuknya Islam serta terbentuknya kesultanan maritim di Nusantara."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengidentifikasi integrasi sosial ekonomi kesultanan Islam di Nusantara dan akulturasi budayanya."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengevaluasi motivasi penjelajahan samudra bangsa Eropa dan awal kolonialisme imperialisme VOC."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menyusun narasi sejarah lokal tentang perlawanan rakyat terhadap hegemoni kolonial di Nusantara."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis dampak tanam paksa dan kebijakan pintu terbuka Belanda terhadap struktur sosial pribumi."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menelaah bangkitnya pergerakan nasional, Budi Utomo, Sarekat Islam, dan Sumpah Pemuda 1928."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengevaluasi masa pendudukan militer Jepang, eksploitasi romusha, serta pembentukan PETA."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis peristiwa Rengasdengklok, perumusan naskah proklamasi, dan kemerdekaan 17 Agustus 1945."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis perjuangan diplomasi (Linggarjati, Renville, KMB) dan fisik mempertahankan kemerdekaan RI."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menelaah ancaman disintegrasi bangsa (pemberontakan PKI Madiun, DI/TII, PRRI/Permesta) 1948-1965."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengevaluasi dinamika demokrasi liberal parlementer dan demokrasi terpimpin di Indonesia."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengkaji lahirnya Orde Baru, kebijakan pembangunan ekonomi, dan swasembada pangan nasional."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis latar belakang krisis moneter 1997, gerakan mahasiswa, dan jatuhnya Orde Baru 1998."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menelaah dinamika pemerintahan era Reformasi dalam penataan sistem demokrasi multipartai."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengevaluasi pelaksanaan pemilu langsung, otonomi daerah, dan penegakan supremasi hukum."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis peran aktif Indonesia dalam diplomasi perdamaian ASEAN dan KTT Asia Afrika."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis perkembangan sains dan teknologi di Indonesia (industri pesawat, telekomunikasi) modern."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengidentifikasi dinamika pembangunan ekonomi nasional dan kontribusi lulusan vokasi dalam industri."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengkaji tantangan globalisasi, revolusi industri 4.0, dan pelestarian nilai sejarah kebangsaan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Merancang mini-riset sejarah lisan tentang perkembangan sentra industri kejuruan lokal."],
                    ],
                ],
            ],

            // 9. PJOK
            'PJOK' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Mempraktikkan gerak spesifik passing, dribbling, dan shooting permainan sepak bola/futsal."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menguasai keterampilan teknik dasar passing atas, bawah, servis, dan spike pada bola voli."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mempraktikkan variasi pukulan forehand, backhand, smash, dan dropshot dalam bulu tangkis."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis dan mempraktikkan keterampilan lari jarak pendek dan nomor lompat jauh atletik."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mempraktikkan teknik dasar kuda-kuda, pukulan, tangkisan, dan tendangan pencak silat."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun dan melaksanakan latihan peningkatan komponen kebugaran jasmani terukur."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mempraktikkan rangkaian gerak senam lantai (roll depan/belakang, handstand, kayang) terkontrol."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menganalisis bahaya pergaulan bebas, narkoba, zat adiktif, dan prinsip pola makan bergizi."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis dan menyimulasikan pola penyerangan dan pertahanan beregu pada bola basket."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Merancang strategi rotasi posisi dan taktik servis menyerang dalam pertandingan bola voli."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menerapkan taktik rally tunggal dan ganda serta penguasaan lapangan dalam bulu tangkis."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mempraktikkan teknik pergantian tongkat estafet dan lari jarak menengah dengan ritme efisien."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menguasai taktik elakan, kuncian, dan bantingan dalam simulasi pertandingan pencak silat."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Melakukan tes kebugaran jasmani (TKJI) dan mengevaluasi indeks massa tubuh (BMI) akurat."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Merancang dan memperagakan koreografi senam irama (aerobik) beregu dengan musik energik."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menerapkan prinsip pertolongan pertama pada kecelakaan (P3K) dan mitigasi cedera otot."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Mengevaluasi taktik permainan sepak bola dan memimpin pertandingan sebagai perangkat wasit."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis strategi pengembalian bola dan penempatan ruang pada permainan tenis meja."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Merancang latihan sirkuit (circuit training) untuk menjaga stamina kerja di lingkungan industri."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mempraktikkan teknik renang serta prosedur keselamatan di perairan terbuka/kolam."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menunjukkan kepemimpinan, kerja sama tim, dan sportivitas dalam turnamen olahraga sekolah."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mempraktikkan gerakan senam ergonomis di tempat kerja untuk mencegah gangguan muskuloskeletal."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengevaluasi manajemen stres, pola istirahat, dan gaya hidup aktif bagi tenaga kerja profesional."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menyusun panduan kebugaran pribadi berkelanjutan berbasis aktivitas fisik rutin sepanjang hayat."],
                    ],
                ],
            ],

            // 10. BAHASA SUNDA
            'BSUNDA' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis struktur teks, eusi, jeung ajén-inajén dina carita babad/sajarah lokal Sunda."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Nulis jeung mintonkeun biantara (pidato) basa Sunda ngagunakeun tatakrama basa nu merenah."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis unsur intrinsik, latar carita, jeung amanat dina carpon Sunda kontémporér."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Nulis résénsi buku kumpulan carpon atawa novél basa Sunda kalawan ngalarapkeun éjahan baku."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis struktur wangun, rima, jeung purwakanti dina rumpaka kawih/kakawihan Sunda."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Ngarumuskeun téks warta basa Sunda sarta mawakeun warta kalawan artikulasi écés."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Maham tata tulis jeung ngalarapkeun aksara Sunda dina nulis kalimah basajan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Maham struktur jeung ajén filosofis dina carita wayang Sunda Mahabarata/Ramayana."],
                    ],
                ],
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis tatakrama basa Sunda (hormat/loma) dina paguneman di lingkungan gawé."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Nyusun téks panata calagara (MC) formal sakola maké basa Sunda anu lentongna merenah."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis téks artikel opini atawa pedaran kabudayaan Sunda sarta ngaidéntifikasi gagasan pokona."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Nulis artikel bahasan ngeunaan tradisi, upacara adat, atawa kulinér Sunda sacara référénsial."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis struktur carita novél basa Sunda sarta ngabandingkeun karakter palaku."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Nulis jeung mendeklamasikeun sajak Sunda modern kalawan éksprési anu pas."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Ngalarapkeun aksara Sunda kana alih aksara wacana panjang tina aksara Latén."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mintonkeun pagelaran drama pondok basa Sunda sacara rukun gawe babarengan."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis struktur jeung ragam basa dina téks wawancara basa Sunda pikeun narasumber."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Ngalakukeun simulasi wawancara gawé dina basa Sunda kalawan rengkuh luyu etika."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis téks carita pantun Sunda klasik sarta ngagali kearifan lokal Sunda."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Nulis éséy kritis ngeunaan kamajuan téknologi jeung pangaruhna kana basa Sunda."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengapresiasi diksi jeung ungkara paribasa dina guguritan Sunda pupuh Asmarandana."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Nulis guguritan dumasar aturan guru lagu jeung wilangan téma etos gawé profésional."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Nyusun draf naskah profil usaha atawa pariwisata Jawa Barat dina brosur basa Sunda."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mintonkeun sandiwara/monolog basa Sunda téma kahirupan masarakat modérn."],
                    ],
                ],
            ],

            // 11. DASAR-DASAR PROGRAM KEAHLIAN
            'DDK-PPLG' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menjelaskan proses bisnis menyeluruh pengembangan perangkat lunak, SDLC, dan manajemen proyek TI."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis perkembangan teknologi terkini (AI, cloud, IoT, big data) dan industri software."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi profil profesi (programmer, tester, UI/UX) dan peluang technopreneurship."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan prosedur K3LH, ergonomi posisi kerja komputer, dan budaya 5R di lab rekayasa."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menerapkan konsep dasar algoritma pemrograman, pseudocode, flowchart, dan tipe data."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengimplementasikan struktur kontrol percabangan, perulangan, array, dan fungsi modular."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menerapkan prinsip dasar OOP: class, object, attribute, method, dan encapsulation."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengembangkan aplikasi antarmuka sederhana berbasis web dengan konsep clean code industri."],
                    ],
                ],
            ],

            'DDK-TJKT' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menjelaskan proses bisnis perencanaan, instalasi, dan pemeliharaan jaringan telekomunikasi."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis tren teknologi 5G, FTTH, Internet of Things (IoT), dan cloud dalam telekomunikasi."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi peluang karir teknisi jaringan, network engineer, dan wirausaha TJKT."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan standar K3LH pekerjaan instalasi kelistrikan jaringan dan bekerja di ketinggian."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengidentifikasi spesifikasi dan fungsi media transmisi kabel UTP/STP, fiber optic, dan wireless."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengoperasikan alat ukur jaringan: crimping tool, LAN tester, OTDR, fusion splicer, dan OPM."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Merakit personal computer (PC) dan menginstalasi sistem operasi jaringan sesuai SOP."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Membangun konfigurasi Local Area Network (LAN) dan menguji konektivitas jaringan komputer."],
                    ],
                ],
            ],

            'DDK-PMN' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menjelaskan proses bisnis ritel, rantai pasok distribusi, dan bauran pemasaran (marketing mix)."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis transformasi pemasaran konvensional menuju digital marketing dan marketplace omnichannel."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi peran profesi digital marketer, pramuniaga, dan wirausaha ritel modern."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan prinsip K3LH di gerai ritel, keselamatan kasir, dan penataan 5R toko."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menganalisis motif belanja dan pemetaan profil segmentasi pasar konsumen ritel."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mempraktikkan etika komunikasi bisnis verbal dan non-verbal dalam negosiasi penawaran."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengimplementasikan standar pelayanan prima (service excellence) 3A kepada pelanggan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menangani keluhan pelanggan (complaint handling) secara profesional untuk loyalitas pembeli."],
                    ],
                ],
            ],

            'DDK-HTL' => [
                'X' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menjelaskan karakteristik industri hospitality, klasifikasi hotel, dan struktur departemen hotel."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menganalisis tren modern perhotelan: green hotel, self check-in, dan aplikasi PMS perhotelan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengidentifikasi peluang karir hotelier profesional, guest relations, concierge, dan wirausaha."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan protokol CHSE, sanitasi higienis perhotelan, K3LH, dan pencegahan kebakaran."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menguasai etika grooming, table manners, dan adab berkomunikasi dalam bahasa perhotelan."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Melaksanakan prosedur dasar penerimaan tamu (check-in/out) dan reservasi pada Front Office."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mempraktikkan standar penataan tempat tidur (making bed) dan pembersihan kamar di Housekeeping."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengoperasikan peralatan tata hidang (table set-up) dan menyajikan pesanan di F&B Service."],
                    ],
                ],
            ],

            // 12. KONSENTRASI KEAHLIAN
            'KK-PPLG' => [
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menerapkan inheritance, polymorphism, abstract class, dan interface dalam OOP modern."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Merancang diagram Entity Relationship (ERD) dan normalisasi basis data relasional hingga 3NF."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengimplementasikan query DDL dan DML pada basis data SQL relasional secara terstruktur."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menggunakan query relasi multi-tabel (JOIN, subquery, view) untuk pengolahan data kompleks."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Membangun antarmuka web responsif menggunakan HTML5, Tailwind CSS, dan komponen modern."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengembangkan manipulasi DOM dan validasi formulir interaktif menggunakan JavaScript."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Membangun aplikasi web dinamis berbasis Laravel dengan arsitektur MVC dan otentikasi aman."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengintegrasikan operasi CRUD antarmuka web dengan sistem basis data relasional MySQL."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Merancang arsitektur RESTful API standar JSON dan mengamankan endpoint web service."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Merancang antarmuka aplikasi mobile multi-platform menggunakan framework modern."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengimplementasikan state management dan routing navigasi pada aplikasi mobile."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengintegrasikan aplikasi mobile dengan REST API server untuk sinkronisasi data online."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menggunakan sensor perangkat mobile (kamera, GPS) dan push notification pada aplikasi."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Melaksanakan pengujian perangkat lunak menggunakan metode Black Box dan automated testing."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Melakukan build release aplikasi Android (APK) dan menyusun dokumentasi user guide."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mempublikasikan dan mengelola deployment aplikasi web pada cloud VPS produksi."],
                    ],
                ],
            ],

            'KK-TJKT' => [
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Merancang skema subnetting IPv4 (VLSM/CIDR) dan pemetaan prefix IPv6 pada topologi jaringan."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Melakukan instalasi kabel twisted pair, penataan patch panel, dan pengukuran redaman fiber."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengkonfigurasi VLAN, inter-VLAN routing, trunking 802.1Q, dan STP pada switch manage."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menerapkan routing statis dan routing dinamis (OSPF, BGP) antar router untuk distribusi data."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengkonfigurasi Access Point nirkabel, captive portal login, dan pengamanan WPA2/WPA3."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengkonfigurasi NAT, port forwarding, dan manajemen bandwidth Simple Queue/PCQ."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menerapkan konfigurasi firewall filter rules, address list, dan proteksi serangan siber."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Membangun koneksi VPN tunneling menggunakan protokol PPTP, L2TP/IPSec, dan WireGuard."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menginstalasi dan mengonfigurasi sistem operasi server Linux (Debian/Ubuntu) berbasis CLI."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Mengkonfigurasi layanan DHCP Server dan Domain Name System (DNS Server) pada jaringan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Membangun Web Server Apache/Nginx dan mengamankan transmisi dengan sertifikat SSL HTTPS."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengkonfigurasi File Server (Samba/FTP), Database Server, dan Mail Server lengkap."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengimplementasikan virtualisasi server menggunakan hypervisor Proxmox dan Docker."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengkonfigurasi Network Monitoring System (NMS) berbasis SNMP menggunakan Zabbix."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menganalisis log sistem, mengisolasi gangguan jaringan, dan troubleshooting lapisan OSI."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Merancang arsitektur keamanan infrastruktur jaringan dan menyusun SOP pemeliharaan berkala."],
                    ],
                ],
            ],

            'KK-PMN' => [
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis tren pasar, riset kata kunci, dan menyusun strategi bauran pemasaran digital."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Membuat konten visual promosi profesional menggunakan software desain grafis."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Memproduksi video promosi pendek (TikTok/Reels) dengan storytelling dan copywriting memikat."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengoptimalkan search engine optimization (SEO) On-Page dan Off-Page pada website bisnis."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengoperasikan toko online resmi di marketplace terkemuka beserta katalog produk."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengelola pesanan online, inventarisasi barang (stock), dan jasa ekspedisi terpercaya."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengimplementasikan kampanye iklan berbayar (Meta Ads dan Google Ads) dengan presisi."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Membangun interaksi pelanggan melalui WhatsApp Business API dan email marketing."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Membangun toko online mandiri platform CMS terintegrasi payment gateway nasional."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Mengkonfigurasi pembayaran digital: QRIS, transfer VA, e-wallet, dan fitur keamanan transaksi."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis traffic web dan performa campaign menggunakan dashboard Google Analytics."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Mengevaluasi metrik konversi: Return on Ad Spend (ROAS) dan Customer Acquisition Cost."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengembangkan strategi program loyalitas pelanggan (loyalty points, voucher diskon)."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun pembukuan keuangan digital, arus kas, dan laba rugi berbasis cloud accounting."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Merancang proposal ekspansi usaha startup pitch deck di hadapan calon investor."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengevaluasi kepatuhan hukum transaksi digital, perlindungan data pribadi, dan etika dagang."],
                    ],
                ],
            ],

            'KK-HTL' => [
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Mengoperasikan Property Management System untuk reservasi kamar individu dan grup."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Memproses registrasi kedatangan tamu (check-in), registration card, dan kunci kamar."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menangani penanganan barang bawaan tamu (bellboy service) dan penitipan barang."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Memberikan informasi fasilitas hotel, rekomendasi atraksi wisata, dan transportasi."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Melaksanakan prosedur pembersihan dan penataan kamar occupied room dan vacant dirty."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengidentifikasi jenis cleaning chemical, peralatan manual/mekanikal, dan K3 Housekeeping."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Melaksanakan operasional pencucian pakaian tamu dan linen hotel (laundry & dry cleaning)."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Menyajikan layanan makanan minuman di restoran hotel (American/French service) sesuai SOP."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Melaksanakan proses tutup buku harian (night audit) dan mencetak laporan okupansi kamar."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Memproses pembayaran keberangkatan tamu (check-out billing) tunai, kartu debit/kredit."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menangani penukaran valuta asing (forex) dan penyelesaian keluhan penagihan tamu."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Melaksanakan pemeliharaan kebersihan area publik hotel dan merangkai bunga meja."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Menyiapkan dan meracik minuman mocktail, smoothie, kopi espresso dengan mixology."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Merancang tata letak ruangan rapat dan menyelenggarakan jamuan resmi (banquet event)."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Mengembangkan strategi promosi paket menginap untuk meningkatkan average daily rate."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mengevaluasi kepuasan tamu secara berkala dan peningkatan mutu layanan berkelanjutan."],
                    ],
                ],
            ],

            // 13. PROJEK KREATIF DAN KEWIRAUSAHAAN (PKK)
            'PKK' => [
                'XI' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menganalisis peluang usaha produk/jasa kejuruan menggunakan analisis SWOT yang komprehensif."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Merancang konsep inovasi produk dan menyusun Business Model Canvas (BMC) berdaya saing."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menentukan standarisasi desain, kemasan (packaging), dan merek dagang (branding) memikat."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menghitung biaya operasional, harga pokok produksi (HPP), dan menetapkan harga jual."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Membuat prototype (purwarupa) produk barang/jasa sesuai rancangan spesifikasi teknis."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Melakukan pengujian mutu dan kesesuaian fungsi prototipe produk dengan target pengguna."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Merancang strategi promosi pemasaran langsung dan digital untuk memasarkan produk."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Melakukan penjualan perdana serta mencatat transaksi pemasukan dan pengeluaran kas transparan."],
                    ],
                ],
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menyusun rencana produksi massal produk kejuruan dengan efisiensi sumber daya dan waktu."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Mengawasi pengendalian mutu (quality control) selama proses produksi untuk mutu konsisten."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis prosedur pengajuan izin usaha mikro (NIB/PIRT) dan pendaftaran HAKI merek."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menghitung titik impas (Break Even Point / BEP) dan rasio keuntungan (ROI) usaha secara presisi."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengembangkan jaringan distribusi produk dan kemitraan konsinyasi dengan toko mitra."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Menyusun laporan keuangan berkala: laba rugi, perubahan modal, dan neraca keuangan."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Merumuskan strategi diversifikasi produk dan pengembangan skala usaha yang berkelanjutan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mempresentasikan laporan akhir pertanggungjawaban usaha kewirausahaan di depan penguji."],
                    ],
                ],
            ],

            // 14. PRAKTIK KERJA LAPANGAN (PKL - KELAS XII SEMESTER 1 GANJIL)
            'PKL' => [
                'XII' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Menerapkan etika profesi, kedisiplinan kerja, kerja sama tim, dan kepatuhan K3LH di industri mitra."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Melaksanakan pekerjaan teknis kejuruan secara mandiri sesuai Standar Operasional Prosedur (SOP) DUDI."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menganalisis dan memecahkan permasalahan teknis lapangan (troubleshooting) menggunakan peralatan kerja."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menyusun laporan komprehensif magang industri dan mempresentasikan hasil portofolio di hadapan penguji."],
                    ],
                    'genap' => [],
                ],
            ],

            // 15. KODING DAN KECERDASAN ARTIFICIAL (KKA)
            'KKA' => [
                'ALL' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Memahami konsep dasar kecerdasan artificial (AI), machine learning, dan etika teknologi AI."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menguasai dasar pemrograman Python untuk manipulasi data (NumPy, Pandas) dan komputasi."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Menerapkan visualisasi data analitis (Matplotlib, Seaborn) untuk eksplorasi pola dataset."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Memahami algoritma supervised learning (klasifikasi dan regresi) serta pelatihan model AI."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Mengimplementasikan algoritma unsupervised learning (clustering K-Means) dan evaluasi model."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Mengintegrasikan Large Language Model (LLM API) dan prompt engineering dalam aplikasi cerdas."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Membangun aplikasi computer vision sederhana (deteksi objek) menggunakan pustaka OpenCV."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Membangun prototipe aplikasi cerdas terintegrasi web/mobile untuk otomatisasi industri nyata."],
                    ],
                ],
            ],

            // 16. MATA PELAJARAN PILIHAN
            'MAPEL-PIL' => [
                'ALL' => [
                    'ganjil' => [
                        ['kode' => 'TP 1', 'alokasi_ah' => 'AH 1', 'deskripsi' => "Memahami ruang lingkup keahlian pilihan industri dan perkembangan teknologi terapan terkini."],
                        ['kode' => 'TP 2', 'alokasi_ah' => 'AH 2', 'deskripsi' => "Menerapkan prinsip dan metodologi kerja teknis sesuai standar operasional bidang pilihan."],
                        ['kode' => 'TP 3', 'alokasi_ah' => 'AH 3', 'deskripsi' => "Mengoperasikan piranti lunak dan peralatan spesialisasi untuk menyelesaikan studi kasus kejuruan."],
                        ['kode' => 'TP 4', 'alokasi_ah' => 'AH 4', 'deskripsi' => "Menganalisis hasil penerapan teknologi pilihan dalam meningkatkan efisiensi proses kerja."],
                    ],
                    'genap' => [
                        ['kode' => 'TP 5', 'alokasi_ah' => 'AH 5', 'deskripsi' => "Merancang purwarupa produk/layanan spesifik yang memadukan keahlian utama dengan bidang pilihan."],
                        ['kode' => 'TP 6', 'alokasi_ah' => 'AH 6', 'deskripsi' => "Melakukan pengujian mutu dan verifikasi performa solusi terapan bidang pilihan secara objektif."],
                        ['kode' => 'TP 7', 'alokasi_ah' => 'AH 7', 'deskripsi' => "Menyusun dokumentasi teknis dan panduan operasional proyek terpadu mata pelajaran pilihan."],
                        ['kode' => 'TP 8', 'alokasi_ah' => 'AH 8', 'deskripsi' => "Mempresentasikan portofolio karya inovatif bidang pilihan di hadapan tim penguji industri mitra."],
                    ],
                ],
            ],
        ];
    }
}
