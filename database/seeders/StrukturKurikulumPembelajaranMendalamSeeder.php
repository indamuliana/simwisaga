<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\KelompokMapel;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrukturKurikulumPembelajaranMendalamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Berdasarkan:
     * 1. Permendikbudristek No. 12 Tahun 2024 tentang Kurikulum pada PAUD, Dikdas, dan Dikmen (Lampiran II: Struktur Kurikulum SMK/MAK)
     * 2. Kepmendikbudristek No. 244/M/2024 tentang Spektrum Keahlian dan Konversi Spektrum Keahlian SMK/MAK pada Kurikulum Merdeka
     * 3. Konsep Pembelajaran Mendalam (Deep Learning: Mindful, Meaningful, Joyful & 8 Dimensi Profil Lulusan Kemendikdasmen RI)
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. KELOMPOK MATA PELAJARAN RESMI
            $kelompokDefinitions = [
                'A' => ['nama' => 'Muatan Umum', 'urutan' => 1],
                'B' => ['nama' => 'Muatan Kejuruan', 'urutan' => 2],
                'C' => ['nama' => 'Muatan Lokal', 'urutan' => 3],
                'D' => ['nama' => 'Projek Penguatan Profil Pelajar Pancasila (P5) & Kokurikuler', 'urutan' => 4],
            ];

            $kelompokMap = [];
            foreach ($kelompokDefinitions as $kode => $kd) {
                $km = KelompokMapel::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'nama' => $kd['nama'],
                        'urutan' => $kd['urutan'],
                        'is_active' => true,
                    ]
                );
                $kelompokMap[$kode] = $km->id;
            }

            // 2. DAFTAR MATA PELAJARAN PEMBELAJARAN MENDALAM SMK
            $mapelDefinitions = [
                // Kelompok A: Muatan Umum
                ['kode' => 'PAIBP', 'nama' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'umum', 'kelompok_mapel_id' => $kelompokMap['A'], 'urutan' => 1, 'is_active' => true],
                ['kode' => 'PPKN',  'nama' => 'Pendidikan Pancasila',              'kelompok' => 'umum', 'kelompok_mapel_id' => $kelompokMap['A'], 'urutan' => 2, 'is_active' => true],
                ['kode' => 'BINDO', 'nama' => 'Bahasa Indonesia',                  'kelompok' => 'umum', 'kelompok_mapel_id' => $kelompokMap['A'], 'urutan' => 3, 'is_active' => true],
                ['kode' => 'PJOK',  'nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kelompok' => 'umum', 'kelompok_mapel_id' => $kelompokMap['A'], 'urutan' => 4, 'is_active' => true],
                ['kode' => 'SEJA',  'nama' => 'Sejarah',                           'kelompok' => 'umum', 'kelompok_mapel_id' => $kelompokMap['A'], 'urutan' => 5, 'is_active' => true],
                ['kode' => 'SENI',  'nama' => 'Seni Budaya',                       'kelompok' => 'umum', 'kelompok_mapel_id' => $kelompokMap['A'], 'urutan' => 6, 'is_active' => true],

                // Kelompok B: Muatan Kejuruan (Di SMK: Matematika, B. Inggris, Informatika, & IPAS masuk Kejuruan)
                ['kode' => 'MATE',  'nama' => 'Matematika',                        'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 7, 'is_active' => true],
                ['kode' => 'BING',  'nama' => 'Bahasa Inggris',                     'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 8, 'is_active' => true],
                ['kode' => 'INFO',  'nama' => 'Informatika',                       'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 9, 'is_active' => true],
                ['kode' => 'IPAS',  'nama' => 'Projek Ilmu Pengetahuan Alam dan Sosial (IPAS)', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 10, 'is_active' => true],
                ['kode' => 'PKK',   'nama' => 'Projek Kreatif dan Kewirausahaan',   'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 11, 'is_active' => true],
                ['kode' => 'PKL',   'nama' => 'Praktik Kerja Lapangan (PKL)',      'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 12, 'is_active' => true],
                ['kode' => 'KKA',   'nama' => 'Koding dan Kecerdasan Artificial',  'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 13, 'is_active' => true],
                ['kode' => 'MAPEL-PIL', 'nama' => 'Mata Pelajaran Pilihan',        'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 14, 'is_active' => true],

                // Dasar-dasar Program Keahlian (Kelas X)
                ['kode' => 'DDK-PPLG', 'nama' => 'Dasar-dasar Pengembangan Perangkat Lunak dan Gim', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 15, 'is_active' => true],
                ['kode' => 'DDK-TJKT', 'nama' => 'Dasar-dasar Teknik Jaringan Komputer dan Telekomunikasi', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 16, 'is_active' => true],
                ['kode' => 'DDK-PMN',  'nama' => 'Dasar-dasar Pemasaran',          'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 17, 'is_active' => true],
                ['kode' => 'DDK-HTL',  'nama' => 'Dasar-dasar Perhotelan',         'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 18, 'is_active' => true],

                // Konsentrasi Keahlian (Kelas XI & XII)
                ['kode' => 'KK-PPLG',  'nama' => 'Konsentrasi Keahlian Rekayasa Perangkat Lunak', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 19, 'is_active' => true],
                ['kode' => 'KK-TJKT',  'nama' => 'Konsentrasi Keahlian Teknik Komputer dan Jaringan', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 20, 'is_active' => true],
                ['kode' => 'KK-PMN',   'nama' => 'Konsentrasi Keahlian Bisnis Digital', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 21, 'is_active' => true],
                ['kode' => 'KK-HTL',   'nama' => 'Konsentrasi Keahlian Perhotelan', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 22, 'is_active' => true],

                // Penunjang Kejuruan Spesifik
                ['kode' => 'PWEB',     'nama' => 'Pemrograman Web & Perangkat Bergerak', 'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 23, 'is_active' => true],
                ['kode' => 'BD',       'nama' => 'Basis Data',                     'kelompok' => 'kejuruan', 'kelompok_mapel_id' => $kelompokMap['B'], 'urutan' => 24, 'is_active' => true],

                // Kelompok C: Muatan Lokal
                ['kode' => 'BSUNDA',   'nama' => 'Bahasa Sunda',                   'kelompok' => 'muatan_lokal', 'kelompok_mapel_id' => $kelompokMap['C'], 'urutan' => 25, 'is_active' => true],
                ['kode' => 'PLH',      'nama' => 'Pendidikan Lingkungan Hidup (CBT PLH)', 'kelompok' => 'muatan_lokal', 'kelompok_mapel_id' => $kelompokMap['C'], 'urutan' => 26, 'is_active' => true],

                // Kelompok D: Projek Penguatan Profil Pelajar Pancasila
                ['kode' => 'P5',       'nama' => 'Projek Penguatan Profil Pelajar Pancasila', 'kelompok' => 'p5_pkl', 'kelompok_mapel_id' => $kelompokMap['D'], 'urutan' => 27, 'is_active' => true],
            ];

            $savedMapels = [];
            foreach ($mapelDefinitions as $mDef) {
                $m = Mapel::updateOrCreate(['kode' => $mDef['kode']], $mDef);
                $savedMapels[$mDef['kode']] = $m;
            }

            // Inactive redundant legacy duplicates if existing
            foreach (['BIND', 'MTK', 'DPPLG', 'PWPB'] as $lc) {
                $leg = Mapel::where('kode', $lc)->first();
                if ($leg) {
                    $leg->update(['is_active' => false]);
                }
            }

            // 3. STRUKTUR KURIKULUM PER JURUSAN & TINGKAT
            // Target: Isi seluruh Tahun Ajaran termasuk Tahun Ajaran Sebelumnya (2024/2025) dan Berjalan (2025/2026)
            $targetTas = TahunAjaran::orderBy('id')->get();
            if ($targetTas->isEmpty()) {
                $targetTas = TahunAjaran::take(1)->get();
            }

            $jurusans = Jurusan::all()->keyBy('kode');

            // Catatan Pengaturan:
            // 1. Seni Budaya (Kelas X): 2 JP/minggu diatur secara terpusat melalui modul Seni Budaya (http://127.0.0.1:8000/seni-budaya)
            // 2. PKL di Kelas XII: Dilaksanakan di Semester 1 (Ganjil) secara blok industri penuh (44 JP),
            //    sedangkan Semester 2 (Genap) difokuskan pada pembelajaran tatap muka di sekolah (48 JP).
            $kurikulumMatrix = [
                'PPLG' => [
                    'X' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'PJOK',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SENI',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'INFO',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'IPAS',     'ganjil' => 6, 'genap' => 6],
                        ['kode' => 'DDK-PPLG', 'ganjil' => 12, 'genap' => 12],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XI' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PJOK',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'KK-PPLG',  'ganjil' => 18, 'genap' => 18],
                        ['kode' => 'PKK',      'ganjil' => 5, 'genap' => 5],
                        ['kode' => 'KKA',      'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XII' => [
                        ['kode' => 'PKL',      'ganjil' => 44, 'genap' => 0], // PKL di Kelas XII Semester 1 (Ganjil)
                        ['kode' => 'PAIBP',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 0,  'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'MATE',     'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 0,  'genap' => 4],
                        ['kode' => 'KK-PPLG',  'ganjil' => 0,  'genap' => 22],
                        ['kode' => 'PKK',      'ganjil' => 0,  'genap' => 5],
                        ['kode' => 'KKA',      'ganjil' => 0,  'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 0,  'genap' => 2],
                    ],
                ],
                'TJKT' => [
                    'X' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'PJOK',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SENI',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'INFO',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'IPAS',     'ganjil' => 6, 'genap' => 6],
                        ['kode' => 'DDK-TJKT', 'ganjil' => 12, 'genap' => 12],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XI' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PJOK',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'KK-TJKT',  'ganjil' => 18, 'genap' => 18],
                        ['kode' => 'PKK',      'ganjil' => 5, 'genap' => 5],
                        ['kode' => 'MAPEL-PIL', 'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XII' => [
                        ['kode' => 'PKL',      'ganjil' => 44, 'genap' => 0], // PKL di Kelas XII Semester 1 (Ganjil)
                        ['kode' => 'PAIBP',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 0,  'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'MATE',     'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 0,  'genap' => 4],
                        ['kode' => 'KK-TJKT',  'ganjil' => 0,  'genap' => 22],
                        ['kode' => 'PKK',      'ganjil' => 0,  'genap' => 5],
                        ['kode' => 'MAPEL-PIL', 'ganjil' => 0, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 0,  'genap' => 2],
                    ],
                ],
                'PMN' => [
                    'X' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'PJOK',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SENI',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'INFO',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'IPAS',     'ganjil' => 6, 'genap' => 6],
                        ['kode' => 'DDK-PMN',  'ganjil' => 12, 'genap' => 12],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XI' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PJOK',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'KK-PMN',   'ganjil' => 18, 'genap' => 18],
                        ['kode' => 'PKK',      'ganjil' => 5, 'genap' => 5],
                        ['kode' => 'MAPEL-PIL', 'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XII' => [
                        ['kode' => 'PKL',      'ganjil' => 44, 'genap' => 0], // PKL di Kelas XII Semester 1 (Ganjil)
                        ['kode' => 'PAIBP',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 0,  'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'MATE',     'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 0,  'genap' => 4],
                        ['kode' => 'KK-PMN',   'ganjil' => 0,  'genap' => 22],
                        ['kode' => 'PKK',      'ganjil' => 0,  'genap' => 5],
                        ['kode' => 'MAPEL-PIL', 'ganjil' => 0, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 0,  'genap' => 2],
                    ],
                ],
                'HTL' => [
                    'X' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'PJOK',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SENI',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'INFO',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'IPAS',     'ganjil' => 6, 'genap' => 6],
                        ['kode' => 'DDK-HTL',  'ganjil' => 12, 'genap' => 12],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XI' => [
                        ['kode' => 'PAIBP',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'PJOK',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'SEJA',     'ganjil' => 2, 'genap' => 2],
                        ['kode' => 'MATE',     'ganjil' => 3, 'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'KK-HTL',   'ganjil' => 18, 'genap' => 18],
                        ['kode' => 'PKK',      'ganjil' => 5, 'genap' => 5],
                        ['kode' => 'MAPEL-PIL', 'ganjil' => 4, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 2, 'genap' => 2],
                    ],
                    'XII' => [
                        ['kode' => 'PKL',      'ganjil' => 44, 'genap' => 0], // PKL di Kelas XII Semester 1 (Ganjil)
                        ['kode' => 'PAIBP',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'PPKN',     'ganjil' => 0,  'genap' => 2],
                        ['kode' => 'BINDO',    'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'MATE',     'ganjil' => 0,  'genap' => 3],
                        ['kode' => 'BING',     'ganjil' => 0,  'genap' => 4],
                        ['kode' => 'KK-HTL',   'ganjil' => 0,  'genap' => 22],
                        ['kode' => 'PKK',      'ganjil' => 0,  'genap' => 5],
                        ['kode' => 'MAPEL-PIL', 'ganjil' => 0, 'genap' => 4],
                        ['kode' => 'BSUNDA',   'ganjil' => 0,  'genap' => 2],
                    ],
                ],
            ];

            foreach ($targetTas as $ta) {
                foreach ($kurikulumMatrix as $jurKode => $tingkats) {
                    $jur = $jurusans[$jurKode] ?? null;
                    if (! $jur) {
                        continue;
                    }

                    foreach ($tingkats as $tkt => $items) {
                        $urut = 1;
                        $validMapelIds = [];
                        foreach ($items as $it) {
                            if (isset($savedMapels[$it['kode']])) {
                                $validMapelIds[] = $savedMapels[$it['kode']]->id;
                            }
                        }

                        StrukturKurikulum::where('tahun_ajaran_id', $ta->id)
                            ->where('jurusan_id', $jur->id)
                            ->where('tingkat', $tkt)
                            ->whereNotIn('mapel_id', $validMapelIds)
                            ->delete();

                        foreach ($items as $it) {
                            $mapel = $savedMapels[$it['kode']] ?? null;
                            if (! $mapel) {
                                continue;
                            }

                            $ganjil = $it['ganjil'];
                            $genap = $it['genap'];
                            $maxJp = max($ganjil, $genap);

                            StrukturKurikulum::updateOrCreate(
                                [
                                    'tahun_ajaran_id' => $ta->id,
                                    'jurusan_id' => $jur->id,
                                    'tingkat' => $tkt,
                                    'mapel_id' => $mapel->id,
                                ],
                                [
                                    'jp_ganjil' => $ganjil,
                                    'jp_genap' => $genap,
                                    'jp_per_minggu' => $maxJp,
                                    'urutan' => $urut++,
                                ]
                            );
                        }
                    }
                }
            }
        });
    }
}
