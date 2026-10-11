<?php

namespace Database\Seeders;

use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\Sistem\Models\PeriodeRapor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PopulateXHTL1DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai pengisian data dummy Nilai & Absensi Rombel X HTL 1 (2025/2026 Ganjil & Genap)...');

        // 1. Validasi Target Tahun Ajaran, Semester, dan Rombel
        $ta = TahunAjaran::where('nama', '2025/2026')->firstOrFail();
        $semGanjil = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'ganjil')->firstOrFail();
        $semGenap = Semester::where('tahun_ajaran_id', $ta->id)->where('semester', 'genap')->firstOrFail();
        
        $rombel = Rombel::where('tahun_ajaran_id', $ta->id)
            ->where('nama', 'X HTL 1')
            ->firstOrFail();

        $siswas = Siswa::inRombel($rombel->id)->orderBy('id')->get();
        if ($siswas->isEmpty()) {
            $this->command->error('Tidak ada siswa di rombel X HTL 1!');
            return;
        }

        // Pastikan seluruh 19 siswa terikat langsung ke X HTL 1
        foreach ($siswas as $s) {
            $s->update(['rombel_id' => $rombel->id]);
        }

        $this->command->info("Rombel: {$rombel->nama} (ID: {$rombel->id}) | Jumlah Siswa: {$siswas->count()}");

        // 2. Setup Wali Kelas: Dewi Anggraeni, S.Tr.Par. (guru Perhotelan)
        $dewiPegawai = Pegawai::where('nama_lengkap', 'like', '%Dewi Anggraeni%')->first();
        if (!$dewiPegawai) {
            $dewiUser = User::firstOrCreate(
                ['email' => 'guru.htl@wikrama.sch.id'],
                [
                    'username' => 'guru_htl',
                    'name' => 'Dewi Anggraeni, S.Tr.Par.',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            $dewiPegawai = Pegawai::create([
                'user_id' => $dewiUser->id,
                'nama_lengkap' => 'Dewi Anggraeni, S.Tr.Par.',
                'nip' => '198809152014022003',
                'jenis_kelamin' => 'P',
                'jenis_gtk' => 'guru',
                'status_kepegawaian' => 'gty',
                'is_active' => true,
            ]);
        }
        $rombel->update(['wali_kelas_id' => $dewiPegawai->user_id]);

        // 3. Setup Periode Rapor 4 Periode (Titi Mangsa)
        $periodeData = [
            [
                'semester' => 'ganjil',
                'kode' => 'GANJIL-PTS',
                'nama' => 'Rapor Penilaian Tengah Semester Ganjil',
                'sebutan' => '9 Minggu ke-1',
                'tanggal_mulai' => '2025-07-14',
                'tanggal_selesai' => '2025-09-20',
                'tanggal_bagi' => '2025-09-26',
                'tempat' => 'Garut',
                'is_active' => false,
                'urutan' => 1,
            ],
            [
                'semester' => 'ganjil',
                'kode' => 'GANJIL-PAS',
                'nama' => 'Rapor Penilaian Akhir Semester Ganjil',
                'sebutan' => '9 Minggu ke-2',
                'tanggal_mulai' => '2025-09-22',
                'tanggal_selesai' => '2025-12-13',
                'tanggal_bagi' => '2025-12-19',
                'tempat' => 'Garut',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'semester' => 'genap',
                'kode' => 'GENAP-PTS',
                'nama' => 'Rapor Penilaian Tengah Semester Genap',
                'sebutan' => '9 Minggu ke-3',
                'tanggal_mulai' => '2026-01-05',
                'tanggal_selesai' => '2026-03-21',
                'tanggal_bagi' => '2026-03-27',
                'tempat' => 'Garut',
                'is_active' => false,
                'urutan' => 3,
            ],
            [
                'semester' => 'genap',
                'kode' => 'GENAP-PAS',
                'nama' => 'Rapor Penilaian Akhir Semester Genap',
                'sebutan' => '9 Minggu ke-4',
                'tanggal_mulai' => '2026-03-23',
                'tanggal_selesai' => '2026-06-13',
                'tanggal_bagi' => '2026-06-19',
                'tempat' => 'Garut',
                'is_active' => false,
                'urutan' => 4,
            ],
        ];

        foreach ($periodeData as $p) {
            PeriodeRapor::updateOrCreate(
                ['tahun_ajaran_id' => $ta->id, 'kode' => $p['kode']],
                $p
            );
        }

        // 4. Konfigurasi Mapel & Guru Pengampu untuk X HTL 1
        $mapelGuruConfig = [
            [
                'nama_mapel' => 'Pendidikan Agama dan Budi Pekerti',
                'guru_query' => 'M. Ridwan',
                'jp' => 3,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Memahami ayat Al-Qur\'an tentang toleransi, moderasi, dan persaudaraan sesama manusia'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Meneladani peran ulama dan kearifan lokal dalam dakwah Islam di Nusantara'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Menerapkan ketentuan syariat Islam tentang zakat, infak, dan wakaf dalam kehidupan sosial'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Menganalisis sejarah kemajuan peradaban Islam dan tokoh-tokoh pembaharu muslim'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Mempraktikkan tata cara perawatan jenazah dan adab ta\'ziyah sesuai syariat'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menghindari akhlak tercela (riya, israf, dan hasad) dalam interaksi pergaulan remaja'],
                ],
            ],
            [
                'nama_mapel' => 'Pendidikan Pancasila',
                'guru_query' => 'Yanti Sumiati',
                'jp' => 2,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Menganalisis kedudukan Pancasila sebagai pandangan hidup dan ideologi terbuka bangsa'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Menelaah pasal-pasal hak dan kewajiban asasi warga negara dalam UUD NRI 1945'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Mempraktikkan musyawarah untuk mufakat dalam pengambilan keputusan di lingkungan sekolah'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Memahami sistem peradilan nasional dan peran lembaga penegak hukum di Indonesia'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Membangun kesadaran dan ketaatan hukum dalam mencegah perilaku pelanggaran norma'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menghargai keragaman suku, ras, dan budaya dalam semboyan Bhinneka Tunggal Ika'],
                ],
            ],
            [
                'nama_mapel' => 'Bahasa Indonesia',
                'guru_query' => 'Asep Gunawan',
                'jp' => 4,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Menganalisis isi, struktur, dan kaidah kebahasaan teks laporan hasil observasi (LHO)'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Mengonstruksi makna tersirat dan kritik sosial dalam teks anekdot'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Mengidentifikasi nilai-nilai moral dan karakter dalam hikayat serta cerpen sastra'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Menyusun teks negosiasi dengan santun untuk mencapai kesepakatan bersama'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Menyampaikan argumen kritis, santun, dan berbasis fakta dalam kegiatan debat'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menulis teks biografi tokoh inspiratif nasional dengan struktur yang sistematis'],
                ],
            ],
            [
                'nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
                'guru_query' => 'Budi Santoso',
                'jp' => 3,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Mempraktikkan gerak spesifik passing bawah, passing atas, dan servis permainan bola voli'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Melakukan rangkaian gerak dasar senam lantai roll depan dan lenting tubuh'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Menganalisis tingkat kebugaran jasmani melalui tes daya tahan dan kekuatan otot'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Mempraktikkan pola penyerangan dan pertahanan dalam permainan bola basket'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Menerapkan pola hidup aktif, gizi seimbang, dan pencegahan bahaya zat adiktif'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Merancang aktivitas gerak berirama (senam kebugaran) secara beregu'],
                ],
            ],
            [
                'nama_mapel' => 'Sejarah',
                'guru_query' => 'Fajar Nugraha',
                'jp' => 2,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Memahami konsep dasar ilmu sejarah, cara berpikir diakronis dan sinkronis'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Menganalisis pengaruh jalur rempah dan perkembangan kerajaan Hindu-Buddha'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Menjelaskan akulturasi kebudayaan Islam dan bukti peninggalannya di Nusantara'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Menganalisis latar belakang imperialisme barat dan perlawanan rakyat di Nusantara'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Menjelaskan pergerakan nasional dan peristiwa penting lahirnya Sumpah Pemuda 1928'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menelaah dinamika detik-detik proklamasi dan perjuangan awal kemerdekaan Indonesia'],
                ],
            ],
            [
                'nama_mapel' => 'Bahasa Inggris',
                'guru_query' => 'Sarah Lestari',
                'jp' => 3,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Identify context, main ideas, and detailed information in oral descriptive texts of tourist attractions'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Communicate intentions and future travel arrangements using appropriate grammatical structures'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Analyze character traits and moral lessons in legendary folklore reading passages'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Apply polite hospitality expressions in hotel guest welcoming and service requests'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Produce concise procedure texts explaining hotel housekeeping and front desk standard operations'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Deliver persuasive oral presentations promoting regional tourism and culinary destinations'],
                ],
            ],
            [
                'nama_mapel' => 'Matematika',
                'guru_query' => 'Rina Marlina',
                'jp' => 4,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Menerapkan sifat eksponen dan logaritma dalam perhitungan model matematis'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Menyelesaikan permasalahan kontekstual barisan dan deret aritmatika serta geometri'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Menerapkan konsep bunga tunggal dan bunga majemuk dalam bidang keuangan sederhana'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Menentukan nilai perbandingan trigonometri sinus, kosinus, tangen pada segitiga siku-siku'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Menganalisis ukuran pemusatan data kelompok (mean, median, modus) pada data statistik'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menghitung peluang kejadian saling lepas dan frekuensi harapan dalam peristiwa riil'],
                ],
            ],
            [
                'nama_mapel' => 'Informatika',
                'guru_query' => 'Beni Iskandar',
                'jp' => 3,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Menerapkan computational thinking untuk merancang solusi masalah algoritma komputasi'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Mengolah data menggunakan fitur advance formula dan visual chart pada spreadsheet'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Memahami fungsi sistem operasi, integrasi hardware, dan manajemen file komputer'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Memahami arsitektur jaringan komputer lokal (LAN), internet, dan protokol keamanan data'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Melakukan analisis data serta visualisasi tren digital menggunakan perangkat komputasi'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menerapkan etika perlindungan privasi data pribadi dan jejak digital di era modern'],
                ],
            ],
            [
                'nama_mapel' => 'Dasar-dasar Kejuruan Perhotelan',
                'guru_query' => 'Dewi Anggraeni',
                'jp' => 12,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Memahami profil industri perhotelan, klasifikasi hotel, dan departemen operasional hotel'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Menerapkan grooming standard, etika profesi hospitality, dan komunikasi pelanggan prima'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Menerapkan prinsip hygiene, sanitasi, dan prosedur K3 (Keselamatan dan Kesehatan Kerja) hotel'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Mempraktikkan penanganan reservasi, check-in, dan check-out tamu pada departemen Front Office'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Mempraktikkan standar penataan dan pembersihan kamar tamu (make up room) pada Housekeeping'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menerapkan keterampilan dasar Food & Beverage Service dan tata meja makan (table setting)'],
                ],
            ],
            [
                'nama_mapel' => 'Bahasa Sunda',
                'guru_query' => 'Cecep Supriatna',
                'jp' => 2,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Nalarapkeun tatakrama undak-usuk basa Sunda luyu jeung situasi komunikasi sapopoe'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Nganalisis unsur intrinsik jeung ajen moral dina carita pondok (carpon) basa Sunda'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Nulis teks biantara (pidato) basa Sunda kalawan nitenan struktur bubuka, eusi, jeung panutup'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Ngalaksanakeun paguneman (percakapan formal) sarta nepikeun warta dina basa Sunda'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Maham kabeungharan budaya, tradisi sunda, jeung tatakrama pergaulan masyarakat Pasundan'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Nulis sajak atawa ngarang pupuh Sunda anu merenah dumasar aturan guru lagu jeung wilangan'],
                ],
            ],
            [
                'nama_mapel' => 'Koding dan Kecerdasan Artificial',
                'guru_query' => 'Ilham Maulana',
                'jp' => 2,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Memahami logika logika algoritma dasar, flowchart perancangan, dan pseudocode solusi program'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Mempraktikkan struktur dasar koding (variabel, tipe data, kondisi percabangan, dan loop)'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Memahami konsep dasar Kecerdasan Buatan (Artificial Intelligence) dan contoh penerapannya'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Membangun aplikasi koding terapan berbasis Python untuk perhitungan otomatisasi layanan'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Menerapkan prompt engineering yang efektif dalam memanfaatkan generative AI secara etis'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Menyelesaikan mini-project koding solusi digital untuk penanganan data reservasi tamu'],
                ],
            ],
        ];

        // Ensure Struktur Kurikulum for all these mapel exist for Tingkat X Jurusan 4
        foreach ($mapelGuruConfig as $cfg) {
            $mapel = Mapel::where('nama', $cfg['nama_mapel'])->first();
            if ($mapel) {
                StrukturKurikulum::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $ta->id,
                        'jurusan_id' => $rombel->jurusan_id,
                        'tingkat' => 'X',
                        'mapel_id' => $mapel->id,
                    ],
                    [
                        'jp_per_minggu' => $cfg['jp'],
                        'jp_ganjil' => $cfg['jp'],
                        'jp_genap' => $cfg['jp'],
                    ]
                );
            }
        }

        // Ambil User Admin untuk authoring absen
        $adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first() ?? User::first();

        // Profil Siswa (Variasi Kemampuan untuk nilai yang realistis)
        // Skor dasar dari 80 s.d. 95
        $siswaProfiles = [];
        foreach ($siswas as $idx => $s) {
            // Distribusi: sebagian siswa pintar (88-95), sebagian menengah (82-87), sebagian cukup (76-81)
            $tier = $idx % 5;
            $baseScore = match ($tier) {
                0 => 91, // Bintang kelas
                1 => 87, // Sangat baik
                2 => 84, // Baik
                3 => 81, // Cukup
                4 => 78, // Perlu dorongan
            };
            $siswaProfiles[$s->id] = $baseScore;
        }

        $semesters = [
            'ganjil' => $semGanjil,
            'genap' => $semGenap,
        ];

        // 5. Looping Setiap Semester
        foreach ($semesters as $semKey => $sem) {
            $this->command->info(PHP_EOL . ">>> Memproses Semester " . strtoupper($semKey) . " (ID: {$sem->id}) <<<");

            // Tanggal Pertemuan Mapel (12 pertemuan per semester)
            $meetingDates = $semKey === 'ganjil'
                ? [
                    '2025-08-04', '2025-08-11', '2025-08-18', '2025-08-25',
                    '2025-09-01', '2025-09-08', '2025-09-15', '2025-09-29',
                    '2025-10-06', '2025-10-20', '2025-10-27', '2025-11-10',
                ]
                : [
                    '2026-01-12', '2026-01-19', '2026-01-26', '2026-02-02',
                    '2026-02-09', '2026-02-23', '2026-03-02', '2026-03-09',
                    '2026-04-06', '2026-04-13', '2026-04-20', '2026-05-04',
                ];

            foreach ($mapelGuruConfig as $cfg) {
                $mapel = Mapel::where('nama', $cfg['nama_mapel'])->first();
                if (!$mapel) {
                    continue;
                }

                $guru = Pegawai::where('nama_lengkap', 'like', "%{$cfg['guru_query']}%")->first()
                    ?? Pegawai::first();

                // 5a. Buat / Update Penugasan Mengajar
                $pm = PenugasanMengajar::updateOrCreate(
                    [
                        'semester_id' => $sem->id,
                        'rombel_id' => $rombel->id,
                        'mapel_id' => $mapel->id,
                    ],
                    [
                        'pegawai_id' => $guru->id,
                        'jp' => $cfg['jp'],
                        'keterangan' => 'Penugasan Reguler Kurikulum Merdeka',
                    ]
                );

                // 5b. Buat / Update Tujuan Pembelajaran (TP)
                $tpList = $semKey === 'ganjil' ? $cfg['tps_ganjil'] : $cfg['tps_genap'];
                $createdTps = [];
                foreach ($tpList as $idx => $tData) {
                    $ahCode = $semKey === 'ganjil' ? 'AH ' . ($idx + 1) : 'AH ' . ($idx + 5);
                    $tp = TujuanPembelajaran::updateOrCreate(
                        [
                            'penugasan_mengajar_id' => $pm->id,
                            'kode' => $tData['kode'],
                        ],
                        [
                            'deskripsi' => $tData['deskripsi'],
                            'alokasi_ah' => $ahCode,
                            'is_active' => true,
                        ]
                    );
                    $createdTps[] = $tp;
                }

                // 5c. Buat Nilai Formatif, Sumatif (STS & SAS), dan Nilai Akhir untuk setiap Siswa
                foreach ($siswas as $siswa) {
                    $base = $siswaProfiles[$siswa->id];
                    // Tambah sedikit variasi per mapel
                    $mapelOffset = (($siswa->id + $mapel->id) % 7) - 3; // -3 to +3
                    $studentSkill = max(75, min(97, $base + $mapelOffset));

                    $tpScores = [];
                    foreach ($createdTps as $tpIdx => $tp) {
                        $tpOffset = (($siswa->id + $tpIdx * 3) % 5) - 2; // -2 to +2
                        $score = max(74, min(98, $studentSkill + $tpOffset));
                        $tpScores[$tp->id] = $score;

                        NilaiFormatif::updateOrCreate(
                            [
                                'siswa_id' => $siswa->id,
                                'tujuan_pembelajaran_id' => $tp->id,
                            ],
                            [
                                'nilai' => $score,
                            ]
                        );

                        if ($tp->alokasi_ah) {
                            \App\Modules\Penilaian\Models\NilaiAsesmenHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'penugasan_mengajar_id' => $pm->id,
                                    'kode_ah' => $tp->alokasi_ah,
                                ],
                                [
                                    'nilai' => $score,
                                ]
                            );
                        }
                    }

                    // Sumatif STS & SAS
                    $stsScore = max(74, min(98, $studentSkill + (($siswa->id % 4) - 2)));
                    $sasScore = max(75, min(98, $studentSkill + ((($siswa->id + 2) % 4) - 2)));

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

                    // Nilai Akhir Calculation
                    $avgFormatif = count($tpScores) > 0 ? array_sum($tpScores) / count($tpScores) : $studentSkill;
                    $finalScore = round(($avgFormatif * 0.5) + ($stsScore * 0.25) + ($sasScore * 0.25));

                    // TP Tertinggi & Terendah
                    $highestTpId = array_keys($tpScores, max($tpScores))[0] ?? null;
                    $lowestTpId = array_keys($tpScores, min($tpScores))[0] ?? null;

                    $highestTp = collect($createdTps)->firstWhere('id', $highestTpId);
                    $lowestTp = collect($createdTps)->firstWhere('id', $lowestTpId);

                    $descTinggi = $highestTp
                        ? "Menunjukkan penguasaan yang sangat baik dalam " . lcfirst($highestTp->deskripsi) . "."
                        : "";
                    $descRendah = ($lowestTp && $lowestTpId !== $highestTpId && $tpScores[$lowestTpId] < 85)
                        ? "Perlu bimbingan dan penguatan dalam " . lcfirst($lowestTp->deskripsi) . "."
                        : "";

                    NilaiAkhir::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'penugasan_mengajar_id' => $pm->id,
                        ],
                        [
                            'rata_formatif' => round($avgFormatif, 2),
                            'sts' => $stsScore,
                            'sas' => $sasScore,
                            'nilai_akhir' => $finalScore,
                            'deskripsi_tertinggi' => $descTinggi,
                            'deskripsi_terendah' => $descRendah,
                        ]
                    );
                }

                // 5d. Buat Absensi Mapel (12 pertemuan untuk setiap siswa)
                $absensiInserts = [];
                foreach ($meetingDates as $dateIdx => $mDate) {
                    foreach ($siswas as $sIdx => $siswa) {
                        // Pola absensi: 95% Hadir, occasional Sakit/Izin/Dispen
                        $hash = ($siswa->id * 17 + $dateIdx * 13 + $mapel->id * 7);
                        $status = StatusAbsensi::HADIR->value;
                        $ket = null;

                        if ($hash % 37 === 0) {
                            $status = StatusAbsensi::SAKIT->value;
                            $ket = 'Sakit demam / flu';
                        } elseif ($hash % 43 === 0) {
                            $status = StatusAbsensi::IZIN->value;
                            $ket = 'Izin keperluan keluarga';
                        } elseif ($hash % 71 === 0) {
                            $status = StatusAbsensi::DISPEN_CBT_PLH->value;
                            $ket = 'Dispen tugas kepramukaan / OSIS';
                        }

                        $absensiInserts[] = [
                            'tanggal' => $mDate,
                            'siswa_id' => $siswa->id,
                            'penugasan_mengajar_id' => $pm->id,
                            'status' => $status,
                            'keterangan' => $ket,
                            'diinput_oleh' => $adminUser?->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Chunk insert absensi mapel
                foreach (array_chunk($absensiInserts, 100) as $chunk) {
                    DB::table('absensi_mapels')->upsert(
                        $chunk,
                        ['tanggal', 'siswa_id', 'penugasan_mengajar_id'],
                        ['status', 'keterangan', 'diinput_oleh', 'updated_at']
                    );
                }
            }

            // 6. Absensi Harian (Hari Efektif Sekolah Mon-Fri)
            $harianDates = $semKey === 'ganjil'
                ? CarbonPeriod::create('2025-07-21', '2025-12-05')
                : CarbonPeriod::create('2026-01-05', '2026-05-29');

            $harianInserts = [];
            foreach ($harianDates as $carbonDate) {
                if ($carbonDate->isWeekend()) {
                    continue;
                }
                $strDate = $carbonDate->format('Y-m-d');

                foreach ($siswas as $sIdx => $siswa) {
                    $daySeed = ($siswa->id * 23 + $carbonDate->dayOfYear * 7);
                    $status = StatusAbsensi::HADIR->value;
                    $ket = null;

                    if ($daySeed % 83 === 0) {
                        $status = StatusAbsensi::SAKIT->value;
                        $ket = 'Sakit flu';
                    } elseif ($daySeed % 97 === 0) {
                        $status = StatusAbsensi::IZIN->value;
                        $ket = 'Izin acara keluarga';
                    }

                    $harianInserts[] = [
                        'semester_id' => $sem->id,
                        'rombel_id' => $rombel->id,
                        'siswa_id' => $siswa->id,
                        'tanggal' => $strDate,
                        'status' => $status,
                        'keterangan' => $ket,
                        'diinput_oleh' => $adminUser?->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            foreach (array_chunk($harianInserts, 200) as $chunk) {
                DB::table('absensi_harians')->upsert(
                    $chunk,
                    ['siswa_id', 'tanggal'],
                    ['semester_id', 'rombel_id', 'status', 'keterangan', 'diinput_oleh', 'updated_at']
                );
            }

            // 7. Catatan Wali Kelas untuk setiap siswa
            $catatanTemplatesGanjil = [
                'Pertahankan prestasi belajar dan keaktifanmu di kelas. Terus asah kompetensi dasar perhotelan dan kedisiplinan diri.',
                'Menunjukkan sikap santun dan etos kerja hospitality yang sangat baik. Tingkatkan kepercayaan diri dalam berbahasa asing.',
                'Rajin, bertanggung jawab, dan memiliki kemampuan komunikasi yang unggul. Terus kembangkan potensi kepemimpinanmu.',
                'Capaian akademik semester ini sangat memuaskan. Jaga konsistensi belajar dan semangat dalam setiap praktik kejuruan.',
                'Memiliki potensi besar dalam bidang pelayanan hotel. Tingkatkan kehadiran dan ketepatan waktu dalam mengumpulkan tugas.',
            ];

            $catatanTemplatesGenap = [
                'Selamat atas pencapaian hasil belajar yang sangat baik di semester genap ini! Siap melangkah ke tingkat XI dengan percaya diri.',
                'Kemampuan praktik perhotelan dan pemahaman teori semakin matang. Pertahankan integritas dan kedisiplinanmu.',
                'Menunjukkan peningkatan motivasi belajar yang nyata. Selalu optimis dan persiapkan diri menghadapi tantangan di tingkat berikutnya.',
                'Hasil belajar memuaskan dengan etika hospitality yang prima. Tetap rendah hati dan giat mengeksplorasi ilmu baru.',
                'Sikap kerja sama dan kepedulian terhadap lingkungan sekolah patut diapresiasi. Sukses selalu dalam meraih cita-cita.',
            ];

            $catatanTemplates = $semKey === 'ganjil' ? $catatanTemplatesGanjil : $catatanTemplatesGenap;

            foreach ($siswas as $sIdx => $siswa) {
                $text = $catatanTemplates[$sIdx % count($catatanTemplates)];
                CatatanWaliKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'semester_id' => $sem->id,
                    ],
                    [
                        'catatan' => $text,
                    ]
                );
            }

            // 8. Ekstrakurikuler untuk setiap siswa
            $ekskuls = Ekstrakurikuler::all();
            if ($ekskuls->isNotEmpty()) {
                foreach ($siswas as $sIdx => $siswa) {
                    $primaryEkskul = $ekskuls[$sIdx % $ekskuls->count()];
                    EkstrakurikulerAnggota::updateOrCreate(
                        [
                            'ekstrakurikuler_id' => $primaryEkskul->id,
                            'siswa_id' => $siswa->id,
                            'semester_id' => $sem->id,
                        ],
                        [
                            'nilai_angka' => 88 + ($sIdx % 8),
                            'nilai_huruf' => 'A',
                            'deskripsi' => "Sangat aktif, berdedikasi tinggi, dan menunjukkan kemampuan kerja sama yang hebat dalam kegiatan {$primaryEkskul->nama}.",
                        ]
                    );
                }
            }

            $this->command->info("Selesai memproses Semester " . strtoupper($semKey) . ".");
        }

        $this->command->info(PHP_EOL . '✅ SELURUH DATA DUMMY NILAI & ABSENSI ROMBEL X HTL 1 (GANJIL & GENAP 2025/2026) BERHASIL DIISI!');
    }
}
