<?php

namespace Database\Seeders;

use App\Enums\StatusAbsensi;
use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiHarian;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanHarian;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\PengaturanPenilaian;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Services\PenilaianScoreCalculator;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Models\SeniBudayaTp;
use App\Modules\Sistem\Models\PeriodeRapor;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PopulateXHTL1DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai pengisian data dummy Nilai & Absensi Rombel X HTL 1...');

        // 1. Validasi Target Tahun Ajaran, Semester, dan Rombel
        $ta = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::firstOrFail();
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

        // Pastikan seluruh siswa terikat langsung ke X HTL 1
        foreach ($siswas as $s) {
            $s->update(['rombel_id' => $rombel->id]);
        }

        $this->command->info("Rombel: {$rombel->nama} (ID: {$rombel->id}) | Tahun Ajaran: {$ta->nama} | Jumlah Siswa: {$siswas->count()}");

        // 2. Setup Wali Kelas: Dewi Anggraeni, S.Tr.Par. (guru Perhotelan)
        $dewiPegawai = Pegawai::where('nama_lengkap', 'like', '%Dewi Anggraeni%')->first();
        if (! $dewiPegawai) {
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
                'tanggal_mulai' => '2026-07-14',
                'tanggal_selesai' => '2026-09-20',
                'tanggal_bagi' => '2026-09-26',
                'tempat' => 'Garut',
                'is_active' => false,
                'urutan' => 1,
            ],
            [
                'semester' => 'ganjil',
                'kode' => 'GANJIL-PAS',
                'nama' => 'Rapor Penilaian Akhir Semester Ganjil',
                'sebutan' => '9 Minggu ke-2',
                'tanggal_mulai' => '2026-09-22',
                'tanggal_selesai' => '2026-12-13',
                'tanggal_bagi' => '2026-12-19',
                'tempat' => 'Garut',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'semester' => 'genap',
                'kode' => 'GENAP-PTS',
                'nama' => 'Rapor Penilaian Tengah Semester Genap',
                'sebutan' => '9 Minggu ke-3',
                'tanggal_mulai' => '2027-01-05',
                'tanggal_selesai' => '2027-03-20',
                'tanggal_bagi' => '2027-03-26',
                'tempat' => 'Garut',
                'is_active' => false,
                'urutan' => 3,
            ],
            [
                'semester' => 'genap',
                'kode' => 'GENAP-PAS',
                'nama' => 'Rapor Penilaian Akhir Semester Genap',
                'sebutan' => '9 Minggu ke-4',
                'tanggal_mulai' => '2027-03-22',
                'tanggal_selesai' => '2027-06-12',
                'tanggal_bagi' => '2027-06-18',
                'tempat' => 'Garut',
                'is_active' => false,
                'urutan' => 4,
            ],
        ];

        $createdPeriodes = [];
        foreach ($periodeData as $p) {
            $createdPeriodes[$p['kode']] = PeriodeRapor::updateOrCreate(
                ['tahun_ajaran_id' => $ta->id, 'kode' => $p['kode']],
                $p
            );
        }

        // Pastikan pembimbing rayon terisi untuk rayon siswa X HTL 1
        $rayonIds = $siswas->pluck('rayon_id')->filter()->unique();
        $adminUser = User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first() ?? User::first();
        $anyPegawaiUsers = User::whereHas('pegawai')->pluck('id');
        $userPool = $anyPegawaiUsers->isNotEmpty() ? $anyPegawaiUsers : collect([$adminUser->id]);

        foreach ($rayonIds as $rIdx => $rId) {
            $rayon = Rayon::find($rId);
            if ($rayon && ! $rayon->pembimbing_id) {
                $assignedUserId = $userPool[$rIdx % $userPool->count()] ?? $adminUser->id;
                $rayon->update(['pembimbing_id' => $assignedUserId]);
            }
        }

        // 4. Konfigurasi Mapel & Guru Pengampu untuk X HTL 1 (Sesuai Kurikulum Merdeka)
        $mapelGuruConfig = [
            [
                'nama_mapel' => 'Pendidikan Agama Islam dan Budi Pekerti',
                'guru_query' => 'Arif Hidayat',
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
                'guru_query' => 'Annisa Nurul Huda',
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
                'guru_query' => 'Intan Mutiara Tsani',
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
                'nama_mapel' => 'Pendidikan Jasmani, Olahraga dan Kesehatan',
                'guru_query' => 'Arie Maulana',
                'jp' => 2,
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
                'nama_mapel' => 'Sejarah Indonesia',
                'guru_query' => 'Jenal Migdad',
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
                'nama_mapel' => 'Matematika',
                'guru_query' => 'Melinda Arista Yusuf',
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
                'guru_query' => 'Agustian Hardi',
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
                'nama_mapel' => 'Progek Ilmu Pengetahuan Alam dan Sosial',
                'guru_query' => 'Kunedi',
                'jp' => 4,
                'tps_ganjil' => [
                    ['kode' => 'TP 1', 'deskripsi' => 'Menjelaskan fenomena zat dan perubahannya serta pemanfaatannya dalam kehidupan sehari-hari'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Menganalisis interaksi keruangan dan konektivitas antar ruang dan waktu dalam ekosistem'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Menerapkan prinsip pelestarian lingkungan dan mitigasi perubahan iklim di sekitar sekolah'],
                ],
                'tps_genap' => [
                    ['kode' => 'TP 4', 'deskripsi' => 'Menganalisis dinamika kependudukan dan dampaknya terhadap pembangunan berkelanjutan'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Merancang penyelidikan ilmiah sederhana tentang pengolahan limbah ramah lingkungan'],
                    ['kode' => 'TP 6', 'deskripsi' => 'Memahami pengaruh revolusi industri dan perkembangan teknologi terhadap perilaku sosial'],
                ],
            ],
            [
                'nama_mapel' => 'Dasar Program Keahlian Perhotelan',
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
                'nama_mapel' => 'Bahasa Inggris',
                'guru_query' => 'Putri Aulia Dalillah',
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
                'nama_mapel' => 'Bahasa Sunda',
                'guru_query' => 'Muhamad Nurjalil',
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
        ];

        // Ensure Struktur Kurikulum for all these mapel exist
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

        // Profil Siswa (Kemampuan untuk nilai yang realistis dan kompeten)
        $siswaProfiles = [];
        foreach ($siswas as $idx => $s) {
            $tier = $idx % 5;
            $baseScore = match ($tier) {
                0 => 92, // Bintang kelas
                1 => 88, // Sangat baik
                2 => 85, // Baik
                3 => 82, // Cukup baik
                4 => 79, // Cukup
            };
            $siswaProfiles[$s->id] = $baseScore;
        }

        $calc = new PenilaianScoreCalculator();
        $settings = PengaturanPenilaian::getActive();

        // 5. Ekstrakurikuler: Pastikan ada master dan keanggotaan
        $ekskulMasterData = [
            ['nama' => 'Pramuka (Wajib)', 'is_wajib' => true, 'hari_latihan' => 'Jumat'],
            ['nama' => 'Palang Merah Remaja (PMR)', 'is_wajib' => false, 'hari_latihan' => 'Sabtu'],
            ['nama' => 'Futsal Club', 'is_wajib' => false, 'hari_latihan' => 'Kamis'],
            ['nama' => 'English Club', 'is_wajib' => false, 'hari_latihan' => 'Selasa'],
            ['nama' => 'Rohis & Seni Baca Quran', 'is_wajib' => false, 'hari_latihan' => 'Rabu'],
        ];
        foreach ($ekskulMasterData as $emd) {
            Ekstrakurikuler::firstOrCreate(['nama' => $emd['nama']], [
                'is_active' => true,
                'is_wajib' => $emd['is_wajib'],
                'hari_latihan' => $emd['hari_latihan'],
                'bobot_presensi' => 40,
                'bobot_penilaian' => 60,
                'threshold_a' => 88,
                'threshold_b' => 78,
                'threshold_c' => 70,
                'deskripsi' => 'Pengembangan minat bakat siswa',
            ]);
        }
        $allEkskuls = Ekstrakurikuler::where('is_active', true)->get();

        // 6. Seni Budaya: Pastikan cabang dan TP ada
        $seniCabangs = SeniBudaya::where('is_active', true)->get();
        if ($seniCabangs->isEmpty()) {
            SeniBudaya::create([
                'kode' => 'TARI',
                'nama' => 'Tari Tradisional',
                'kkm' => 75,
                'bobot_presensi' => 30,
                'bobot_penilaian' => 70,
                'hari_latihan' => 'Rabu',
                'is_active' => true,
            ]);
            SeniBudaya::create([
                'kode' => 'MUSIK',
                'nama' => 'Musik Modern',
                'kkm' => 75,
                'bobot_presensi' => 30,
                'bobot_penilaian' => 70,
                'hari_latihan' => 'Rabu',
                'is_active' => true,
            ]);
            $seniCabangs = SeniBudaya::where('is_active', true)->get();
        }

        $semesters = [
            'ganjil' => $semGanjil,
            'genap' => $semGenap,
        ];

        // 7. Looping Setiap Semester
        foreach ($semesters as $semKey => $sem) {
            $this->command->info(PHP_EOL . ">>> Memproses Semester " . strtoupper($semKey) . " (ID: {$sem->id}) <<<");

            $b1Key = $semKey === 'ganjil' ? '9M1' : '9M3';
            $b2Key = $semKey === 'ganjil' ? '9M2' : '9M4';

            // Tanggal Pertemuan Mapel (12 pertemuan per semester)
            $meetingDates = $semKey === 'ganjil'
                ? [
                    '2026-08-03', '2026-08-10', '2026-08-17', '2026-08-24',
                    '2026-08-31', '2026-09-07', '2026-09-14', '2026-09-28',
                    '2026-10-05', '2026-10-19', '2026-10-26', '2026-11-09',
                ]
                : [
                    '2027-01-11', '2027-01-18', '2027-01-25', '2027-02-01',
                    '2027-02-08', '2027-02-22', '2027-03-01', '2027-03-08',
                    '2027-04-05', '2027-04-12', '2027-04-19', '2027-05-03',
                ];

            foreach ($mapelGuruConfig as $cfg) {
                $mapel = Mapel::where('nama', $cfg['nama_mapel'])->first();
                if (! $mapel) {
                    continue;
                }

                $guru = Pegawai::where('nama_lengkap', 'like', "%{$cfg['guru_query']}%")->first()
                    ?? Pegawai::first();

                // 7a. Buat / Update Penugasan Mengajar
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

                // 7b. Buat / Update Tujuan Pembelajaran (TP)
                $tpList = $semKey === 'ganjil' ? $cfg['tps_ganjil'] : $cfg['tps_genap'];
                $createdTps = [];
                foreach ($tpList as $idx => $tData) {
                    $ahCode = $semKey === 'ganjil' ? 'AH ' . ($idx + 1) : 'AH ' . ($idx + 5);
                    $targetP = $semKey === 'ganjil' ? ($idx < 2 ? '9M1' : '9M2') : ($idx < 2 ? '9M3' : '9M4');

                    $tp = TujuanPembelajaran::updateOrCreate(
                        [
                            'penugasan_mengajar_id' => $pm->id,
                            'kode' => $tData['kode'],
                        ],
                        [
                            'deskripsi' => $tData['deskripsi'],
                            'alokasi_ah' => $ahCode,
                            'target_periode' => $targetP,
                            'is_active' => true,
                            'urutan' => $idx + 1,
                        ]
                    );
                    $createdTps[] = $tp;
                }

                $tpsBlock1 = collect($createdTps)->filter(fn ($t) => $t->target_periode === $b1Key);
                $tpsBlock2 = collect($createdTps)->filter(fn ($t) => $t->target_periode === $b2Key);
                $kkm = (float) ($mapel->kkm_default ?? 75);

                // 7c. Buat Nilai Formatif, NilaiHarian, Sumatif (STS & SAS), dan Nilai Akhir
                foreach ($siswas as $siswa) {
                    $base = $siswaProfiles[$siswa->id];
                    $mapelOffset = (($siswa->id + $mapel->id) % 7) - 3;
                    $studentSkill = max(76, min(97, $base + $mapelOffset));

                    $tpScores = [];
                    foreach ($createdTps as $tpIdx => $tp) {
                        $tpOffset = (($siswa->id + $tpIdx * 3) % 5) - 2;
                        $score = max(76, min(98, $studentSkill + $tpOffset));
                        $tpScores[$tp->id] = $score;

                        NilaiFormatif::updateOrCreate(
                            [
                                'siswa_id' => $siswa->id,
                                'tujuan_pembelajaran_id' => $tp->id,
                            ],
                            ['nilai' => $score]
                        );

                        if ($tp->alokasi_ah) {
                            NilaiAsesmenHarian::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'penugasan_mengajar_id' => $pm->id,
                                    'kode_ah' => $tp->alokasi_ah,
                                ],
                                ['nilai' => $score]
                            );
                        }
                    }

                    // Nilai Harian (Presensi/Harian) untuk Blok 1 & Blok 2
                    $nh1Score = max(85, min(100, 95 + (($siswa->id % 5) - 2)));
                    $nh2Score = max(85, min(100, 96 + (($siswa->id % 4) - 2)));

                    NilaiHarian::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $pm->id, 'target_periode' => $b1Key],
                        ['nilai' => $nh1Score]
                    );
                    NilaiHarian::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $pm->id, 'target_periode' => $b2Key],
                        ['nilai' => $nh2Score]
                    );

                    // Sumatif STS & SAS
                    $stsScore = max(76, min(98, $studentSkill + (($siswa->id % 4) - 2)));
                    $sasScore = max(76, min(98, $studentSkill + ((($siswa->id + 2) % 4) - 2)));

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

                    // Evaluasi Blok 1 & Blok 2 untuk data_9m
                    $resBlock1 = $calc->calculate9MBlock(
                        $tpScores,
                        $nh1Score,
                        $stsScore,
                        $kkm,
                        $tpsBlock1,
                        null,
                        $settings
                    );

                    $resBlock2 = $calc->calculate9MBlock(
                        $tpScores,
                        $nh2Score,
                        $sasScore,
                        $kkm,
                        $tpsBlock2,
                        null,
                        $settings
                    );

                    $data9m = [
                        $b1Key => $resBlock1,
                        $b2Key => $resBlock2,
                    ];

                    $semResult = $calc->calculateSemester($resBlock1, $resBlock2, $kkm, $settings);

                    $avgFormatif = count($tpScores) > 0 ? array_sum($tpScores) / count($tpScores) : $studentSkill;

                    NilaiAkhir::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'penugasan_mengajar_id' => $pm->id,
                        ],
                        [
                            'rata_formatif' => round($avgFormatif, 2),
                            'rata_tp' => $resBlock2['rata_tp'] ?? $resBlock1['rata_tp'],
                            'nh' => $nh2Score,
                            'sumatif' => $sasScore,
                            'sts' => $stsScore,
                            'sas' => $sasScore,
                            'nilai_akhir' => $semResult['nilai_akhir'] ?? round(($avgFormatif * 0.5) + ($stsScore * 0.25) + ($sasScore * 0.25)),
                            'predikat' => $semResult['predikat'] ?? $calc->resolvePredikat($semResult['nilai_akhir'] ?? 85, $kkm, $settings),
                            'status' => $semResult['status'] ?? 'kompeten',
                            'deskripsi_tertinggi' => $resBlock2['deskripsi_tertinggi'] ?: $resBlock1['deskripsi_tertinggi'],
                            'deskripsi_terendah' => $resBlock2['deskripsi_terendah'] ?: $resBlock1['deskripsi_terendah'],
                            'data_9m' => $data9m,
                        ]
                    );
                }

                // 7d. Absensi Mapel
                $absensiInserts = [];
                foreach ($meetingDates as $dateIdx => $mDate) {
                    foreach ($siswas as $sIdx => $siswa) {
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

                foreach (array_chunk($absensiInserts, 100) as $chunk) {
                    DB::table('absensi_mapels')->upsert(
                        $chunk,
                        ['tanggal', 'siswa_id', 'penugasan_mengajar_id'],
                        ['status', 'keterangan', 'diinput_oleh', 'updated_at']
                    );
                }
            }

            // 8. Absensi Harian (Hari Efektif Sekolah Mon-Fri)
            $harianDates = $semKey === 'ganjil'
                ? CarbonPeriod::create('2026-07-20', '2026-12-04')
                : CarbonPeriod::create('2027-01-04', '2027-05-28');

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

                    if ($daySeed % 79 === 0) {
                        $status = StatusAbsensi::SAKIT->value;
                        $ket = 'Sakit flu & demam';
                    } elseif ($daySeed % 89 === 0) {
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

            // 9. Catatan Wali Kelas (Umum & Per Periode)
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

            $p1 = $semKey === 'ganjil' ? ($createdPeriodes['GANJIL-PTS'] ?? null) : ($createdPeriodes['GENAP-PTS'] ?? null);
            $p2 = $semKey === 'ganjil' ? ($createdPeriodes['GANJIL-PAS'] ?? null) : ($createdPeriodes['GENAP-PAS'] ?? null);

            foreach ($siswas as $sIdx => $siswa) {
                $text = $catatanTemplates[$sIdx % count($catatanTemplates)];
                // Catatan semester umum
                CatatanWaliKelas::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'semester_id' => $sem->id, 'periode_rapor_id' => null],
                    ['catatan' => $text]
                );

                // Catatan per periode
                if ($p1) {
                    CatatanWaliKelas::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'semester_id' => $sem->id, 'periode_rapor_id' => $p1->id],
                        ['catatan' => $text . ' (Evaluasi Tengah Semester)']
                    );
                }
                if ($p2) {
                    CatatanWaliKelas::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'semester_id' => $sem->id, 'periode_rapor_id' => $p2->id],
                        ['catatan' => $text . ' (Evaluasi Akhir Semester)']
                    );
                }
            }

            // 10. Ekstrakurikuler untuk setiap siswa (1-2 ekskul per siswa)
            foreach ($siswas as $sIdx => $siswa) {
                $primaryEkskul = $allEkskuls[$sIdx % $allEkskuls->count()];
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
                        'status_keanggotaan' => 'aktif',
                    ]
                );

                // Tambah ekskul wajib Pramuka jika belum
                $pramuka = $allEkskuls->firstWhere('is_wajib', true);
                if ($pramuka && $pramuka->id !== $primaryEkskul->id) {
                    EkstrakurikulerAnggota::updateOrCreate(
                        [
                            'ekstrakurikuler_id' => $pramuka->id,
                            'siswa_id' => $siswa->id,
                            'semester_id' => $sem->id,
                        ],
                        [
                            'nilai_angka' => 86 + (($sIdx + 1) % 6),
                            'nilai_huruf' => 'A',
                            'deskripsi' => 'Disiplin dan aktif mengikuti seluruh kegiatan kepramukaan dengan penuh tanggung jawab.',
                            'status_keanggotaan' => 'aktif',
                        ]
                    );
                }
            }

            // 11. Seni Budaya: Daftarkan seluruh siswa ke salah satu cabang Seni Budaya
            foreach ($siswas as $sIdx => $siswa) {
                $cabang = $seniCabangs[$sIdx % $seniCabangs->count()];

                // Pastikan TP Seni Budaya ada
                $sbTp = SeniBudayaTp::firstOrCreate(
                    [
                        'seni_budaya_id' => $cabang->id,
                        'semester_id' => $sem->id,
                        'kode' => 'TP 1',
                    ],
                    [
                        'deskripsi' => 'Mengekspresikan gagasan dan karya seni budaya dengan memperhatikan estetika dan teknik dasar.',
                        'target_asesmen' => 'sts',
                        'is_active' => true,
                    ]
                );

                $sbSts = 85 + ($sIdx % 10);
                $sbSas = 87 + (($sIdx + 2) % 9);

                SeniBudayaAnggota::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'semester_id' => $sem->id,
                    ],
                    [
                        'seni_budaya_id' => $cabang->id,
                        'sts_1' => $sbSts,
                        'nilai_akhir_sts_1' => $sbSts,
                        'sas' => $sbSas,
                        'nilai_akhir_sas' => $sbSas,
                        'sts_2' => $sbSts,
                        'nilai_akhir_sts_2' => $sbSts,
                        'sat' => $sbSas,
                        'nilai_akhir_sat' => $sbSas,
                        'nilai_presensi' => 100,
                        'nilai_akhir' => round(($sbSts * 0.3) + ($sbSas * 0.4) + (100 * 0.3)),
                        'nilai_huruf' => 'A',
                        'status_keanggotaan' => 'aktif',
                        'deskripsi_tertinggi' => 'Menunjukkan penguasaan yang sangat baik dalam berkarya dan mengapresiasi ' . $cabang->nama . '.',
                        'deskripsi_terendah' => '',
                    ]
                );
            }

            // 12. Mapel Pilihan (Koding dan Kecerdasan Artifisial)
            $mapilKka = MapelPilihan::where('kode', 'KKA')->first();
            if ($mapilKka) {
                $mapilTpsData = $semKey === 'ganjil' ? [
                    ['kode' => 'TP 1', 'deskripsi' => 'Memahami logika algoritma dasar, flowchart perancangan, dan pseudocode solusi program', 'target' => '9M1', 'ah' => 'AH 1'],
                    ['kode' => 'TP 2', 'deskripsi' => 'Mempraktikkan struktur dasar koding Python (variabel, tipe data, kondisional, dan loop)', 'target' => '9M1', 'ah' => 'AH 2'],
                    ['kode' => 'TP 3', 'deskripsi' => 'Memahami konsep dasar Kecerdasan Buatan (Artificial Intelligence) dan prompt engineering', 'target' => '9M2', 'ah' => 'AH 3'],
                ] : [
                    ['kode' => 'TP 4', 'deskripsi' => 'Membangun aplikasi terapan koding terintegrasi modul AI untuk otomasi pelayanan perhotelan', 'target' => '9M3', 'ah' => 'AH 5'],
                    ['kode' => 'TP 5', 'deskripsi' => 'Menyelesaikan mini-project koding solusi digital untuk penanganan reservasi dan data tamu', 'target' => '9M4', 'ah' => 'AH 7'],
                ];

                $createdMapilTps = [];
                foreach ($mapilTpsData as $tIdx => $tData) {
                    $tp = MapelPilihanTp::updateOrCreate(
                        [
                            'mapel_pilihan_id' => $mapilKka->id,
                            'semester_id' => $sem->id,
                            'kode_tp' => $tData['kode'],
                        ],
                        [
                            'deskripsi_tp' => $tData['deskripsi'],
                            'lingkup_materi' => $tData['deskripsi'],
                            'kode_ah' => $tData['ah'],
                            'target_periode' => $tData['target'],
                            'urutan' => $tIdx + 1,
                        ]
                    );
                    $createdMapilTps[] = $tp;
                }

                $mapilTpsBlock1 = collect($createdMapilTps)->filter(fn ($t) => $t->target_periode === $b1Key);
                $mapilTpsBlock2 = collect($createdMapilTps)->filter(fn ($t) => $t->target_periode === $b2Key);
                $mapilKkm = (float) ($mapilKka->kkm_default ?? 75);

                foreach ($siswas as $sIdx => $siswa) {
                    // Pastikan anggota aktif
                    MapelPilihanAnggota::firstOrCreate(
                        ['mapel_pilihan_id' => $mapilKka->id, 'siswa_id' => $siswa->id],
                        ['tahun_ajaran_id' => $ta->id, 'status' => 'aktif']
                    );

                    $base = $siswaProfiles[$siswa->id];
                    $mapilScores = [];
                    foreach ($createdMapilTps as $tIdx => $tp) {
                        $sScore = max(78, min(97, $base + (($siswa->id + $tIdx * 2) % 5) - 2));
                        $mapilScores[$tp->id] = $sScore;

                        NilaiMapelPilihanFormatif::updateOrCreate(
                            [
                                'mapel_pilihan_id' => $mapilKka->id,
                                'siswa_id' => $siswa->id,
                                'semester_id' => $sem->id,
                                'mapel_pilihan_tp_id' => $tp->id,
                            ],
                            [
                                'kode_ah' => $tp->kode_ah,
                                'nilai' => $sScore,
                            ]
                        );
                    }

                    $mapilNh1 = 95;
                    $mapilNh2 = 96;
                    NilaiMapelPilihanHarian::updateOrCreate(
                        ['mapel_pilihan_id' => $mapilKka->id, 'siswa_id' => $siswa->id, 'target_periode' => $b1Key],
                        ['nilai' => $mapilNh1]
                    );
                    NilaiMapelPilihanHarian::updateOrCreate(
                        ['mapel_pilihan_id' => $mapilKka->id, 'siswa_id' => $siswa->id, 'target_periode' => $b2Key],
                        ['nilai' => $mapilNh2]
                    );

                    $mapilSts = max(78, min(96, $base + (($siswa->id % 4) - 2)));
                    $mapilSas = max(80, min(97, $base + ((($siswa->id + 1) % 4) - 2)));

                    NilaiMapelPilihanSumatif::updateOrCreate(
                        ['mapel_pilihan_id' => $mapilKka->id, 'siswa_id' => $siswa->id, 'semester_id' => $sem->id],
                        [
                            'sts_1' => $mapilSts,
                            'sas' => $mapilSas,
                            'sts_2' => $mapilSts,
                            'sat' => $mapilSas,
                        ]
                    );

                    $resMapilB1 = $calc->calculate9MBlock(
                        $mapilScores,
                        $mapilNh1,
                        $mapilSts,
                        $mapilKkm,
                        $mapilTpsBlock1,
                        null,
                        $settings
                    );
                    $resMapilB2 = $calc->calculate9MBlock(
                        $mapilScores,
                        $mapilNh2,
                        $mapilSas,
                        $mapilKkm,
                        $mapilTpsBlock2,
                        null,
                        $settings
                    );

                    $mapilData9m = [
                        $b1Key => $resMapilB1,
                        $b2Key => $resMapilB2,
                    ];

                    $semResMapil = $calc->calculateSemester($resMapilB1, $resMapilB2, $mapilKkm, $settings);

                    NilaiMapelPilihanAkhir::updateOrCreate(
                        ['mapel_pilihan_id' => $mapilKka->id, 'siswa_id' => $siswa->id, 'semester_id' => $sem->id],
                        [
                            'nilai_akhir' => $semResMapil['nilai_akhir'] ?? round(($mapilSts + $mapilSas) / 2),
                            'predikat' => $semResMapil['predikat'] ?? 'Cakap',
                            'status' => 'kompeten',
                            'data_9m' => $mapilData9m,
                            'deskripsi_tertinggi' => $resMapilB2['deskripsi_tertinggi'] ?: $resMapilB1['deskripsi_tertinggi'],
                            'deskripsi_terendah' => $resMapilB2['deskripsi_terendah'] ?: $resMapilB1['deskripsi_terendah'],
                        ]
                    );
                }
            }

            $this->command->info("Selesai memproses Semester " . strtoupper($semKey) . ".");
        }

        $this->command->info(PHP_EOL . '✅ SELURUH DATA DUMMY NILAI & ABSENSI ROMBEL X HTL 1 BERHASIL DIISI!');
    }
}
