<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class AkademikInitialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tahun Ajaran & Semester Default
        $ta = TahunAjaran::firstOrCreate(
            ['nama' => '2024/2025'],
            [
                'tgl_mulai' => '2024-07-15',
                'tgl_selesai' => '2025-06-25',
                'is_active' => true,
            ]
        );

        Semester::firstOrCreate(
            ['tahun_ajaran_id' => $ta->id, 'semester' => 'ganjil'],
            ['is_active' => true]
        );

        Semester::firstOrCreate(
            ['tahun_ajaran_id' => $ta->id, 'semester' => 'genap'],
            ['is_active' => false]
        );

        // 2. 4 Jurusan Resmi
        $jurusans = [
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'kode_alias' => ['RPL'],
                'deskripsi' => 'Rekayasa perangkat lunak, pemrograman web, mobile application, dan gim interaktif.',
            ],
            [
                'kode' => 'TJKT',
                'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi',
                'kode_alias' => ['TKJ'],
                'deskripsi' => 'Infrastruktur jaringan komputer, sistem administrasi server, dan keamanan jaringan.',
            ],
            [
                'kode' => 'PMN',
                'nama' => 'Pemasaran',
                'kode_alias' => ['BDP'],
                'deskripsi' => 'Pemasaran digital, ritel, komunikasi bisnis, dan e-commerce.',
            ],
            [
                'kode' => 'HTL',
                'nama' => 'Perhotelan',
                'kode_alias' => [],
                'deskripsi' => 'Hospitality, operasional kantor depan, tata graha, dan pelayanan tamu.',
            ],
        ];

        foreach ($jurusans as $j) {
            Jurusan::updateOrCreate(['kode' => $j['kode']], $j);
        }

        // 3. Rayon Awal SMK Wikrama 1 Garut
        $rayons = [
            ['nama' => 'Bales 1', 'wilayah_cakupan' => 'Banyuresmi, Leles', 'keterangan' => 'Wilayah Banyuresmi dan Leles'],
            ['nama' => 'Tarkid 1', 'wilayah_cakupan' => 'Tarogong Kidul 1', 'keterangan' => 'Wilayah Tarogong Kidul Zona 1'],
            ['nama' => 'Tarkid 2', 'wilayah_cakupan' => 'Tarogong Kidul 2', 'keterangan' => 'Wilayah Tarogong Kidul Zona 2'],
        ];

        foreach ($rayons as $r) {
            Rayon::updateOrCreate(['nama' => $r['nama']], $r);
        }

        // 4. Mata Pelajaran Dasar (Kurikulum Merdeka)
        $mapels = [
            // Muatan Umum
            ['kode' => 'PAIBP', 'nama' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'umum', 'urutan' => 1],
            ['kode' => 'PPKN', 'nama' => 'Pendidikan Pancasila', 'kelompok' => 'umum', 'urutan' => 2],
            ['kode' => 'BINDO', 'nama' => 'Bahasa Indonesia', 'kelompok' => 'umum', 'urutan' => 3],
            ['kode' => 'PJOK', 'nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kelompok' => 'umum', 'urutan' => 4],
            ['kode' => 'SEJ', 'nama' => 'Sejarah', 'kelompok' => 'umum', 'urutan' => 5],
            ['kode' => 'SENI', 'nama' => 'Seni Budaya', 'kelompok' => 'umum', 'urutan' => 6],
            ['kode' => 'BING', 'nama' => 'Bahasa Inggris', 'kelompok' => 'umum', 'urutan' => 7],
            ['kode' => 'MTK', 'nama' => 'Matematika', 'kelompok' => 'umum', 'urutan' => 8],
            ['kode' => 'IPAS', 'nama' => 'Projek Ilmu Pengetahuan Alam dan Sosial (IPAS)', 'kelompok' => 'umum', 'urutan' => 9],
            ['kode' => 'INF', 'nama' => 'Informatika', 'kelompok' => 'umum', 'urutan' => 10],

            // Muatan Kejuruan
            ['kode' => 'DDK-PPLG', 'nama' => 'Dasar-dasar Kejuruan PPLG', 'kelompok' => 'kejuruan', 'urutan' => 11],
            ['kode' => 'DDK-TJKT', 'nama' => 'Dasar-dasar Kejuruan TJKT', 'kelompok' => 'kejuruan', 'urutan' => 12],
            ['kode' => 'DDK-PMN', 'nama' => 'Dasar-dasar Kejuruan Pemasaran', 'kelompok' => 'kejuruan', 'urutan' => 13],
            ['kode' => 'DDK-HTL', 'nama' => 'Dasar-dasar Kejuruan Perhotelan', 'kelompok' => 'kejuruan', 'urutan' => 14],
            ['kode' => 'KK-PPLG', 'nama' => 'Konsentrasi Keahlian PPLG', 'kelompok' => 'kejuruan', 'urutan' => 15],
            ['kode' => 'KK-TJKT', 'nama' => 'Konsentrasi Keahlian TJKT', 'kelompok' => 'kejuruan', 'urutan' => 16],
            ['kode' => 'PKK', 'nama' => 'Projek Kreatif dan Kewirausahaan', 'kelompok' => 'kejuruan', 'urutan' => 17],

            // Muatan Lokal
            ['kode' => 'BSUNDA', 'nama' => 'Bahasa Sunda', 'kelompok' => 'muatan_lokal', 'urutan' => 18],
            ['kode' => 'PLH', 'nama' => 'Pendidikan Lingkungan Hidup (CBT PLH)', 'kelompok' => 'muatan_lokal', 'urutan' => 19],

            // P5 & PKL
            ['kode' => 'P5', 'nama' => 'Projek Penguatan Profil Pelajar Pancasila', 'kelompok' => 'p5_pkl', 'urutan' => 20],
            ['kode' => 'PKL', 'nama' => 'Praktik Kerja Lapangan (PKL)', 'kelompok' => 'p5_pkl', 'urutan' => 21],
        ];

        foreach ($mapels as $m) {
            Mapel::updateOrCreate(['kode' => $m['kode']], $m);
        }

        // 5. Contoh Rombel Standar [Tingkat] [Jurusan] [Nomor]
        $pplg = Jurusan::where('kode', 'PPLG')->first();
        $tjkt = Jurusan::where('kode', 'TJKT')->first();

        if ($pplg) {
            Rombel::firstOrCreate(
                ['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $pplg->id, 'tingkat' => 'X', 'nomor_urut' => 1],
                ['nama' => 'X PPLG 1']
            );
        }
        if ($tjkt) {
            Rombel::firstOrCreate(
                ['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $tjkt->id, 'tingkat' => 'X', 'nomor_urut' => 1],
                ['nama' => 'X TJKT 1']
            );
        }
    }
}
