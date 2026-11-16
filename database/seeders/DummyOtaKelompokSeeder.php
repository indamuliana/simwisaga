<?php

namespace Database\Seeders;

use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KelompokAnggota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyOtaKelompokSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Buat / Ambil Donatur Inda Muliana (terdaftar di Kelompok 1, 2, 3)
            $inda = Donatur::updateOrCreate(
                ['nama' => 'Inda Muliana'],
                [
                    'tipe' => 'rutin',
                    'nomor_telepon' => '081234567890',
                    'email' => 'inda@wisaga.sch.id',
                    'alamat' => 'Garut',
                    'is_aktif' => true,
                    'keterangan' => 'Donatur Rutin multi-kelompok (Kelompok 1, 2, 3)',
                ]
            );

            // 2. Daftar Kelompok Beserta Koordinator & Anggota
            $kelompoksData = [
                [
                    'kode' => 'KEL-01',
                    'nama' => 'Kelompok Peduli Siswa 1',
                    'target' => 2000000,
                    'koordinator' => [
                        'nama' => 'H. Ahmad Fauzi',
                        'telp' => '081211110001',
                        'komitmen' => 200000,
                    ],
                    'anggota_lain' => [
                        ['nama' => 'Siti Aminah', 'telp' => '081211110002', 'komitmen' => 150000],
                        ['nama' => 'Budi Hartono', 'telp' => '081211110003', 'komitmen' => 100000],
                    ],
                    'sertakan_inda' => true,
                    'komitmen_inda' => 150000,
                ],
                [
                    'kode' => 'KEL-02',
                    'nama' => 'Kelompok Peduli Siswa 2',
                    'target' => 2000000,
                    'koordinator' => [
                        'nama' => 'Dr. Rahmat Hidayat',
                        'telp' => '081222220001',
                        'komitmen' => 250000,
                    ],
                    'anggota_lain' => [
                        ['nama' => 'Dewi Lestari', 'telp' => '081222220002', 'komitmen' => 150000],
                        ['nama' => 'Hendra Saputra', 'telp' => '081222220003', 'komitmen' => 100000],
                    ],
                    'sertakan_inda' => true,
                    'komitmen_inda' => 150000,
                ],
                [
                    'kode' => 'KEL-03',
                    'nama' => 'Kelompok Peduli Siswa 3',
                    'target' => 2000000,
                    'koordinator' => [
                        'nama' => 'Ir. Bambang Sugiarto',
                        'telp' => '081233330001',
                        'komitmen' => 200000,
                    ],
                    'anggota_lain' => [
                        ['nama' => 'Nurul Fatimah', 'telp' => '081233330002', 'komitmen' => 150000],
                        ['nama' => 'Agus Setiawan', 'telp' => '081233330003', 'komitmen' => 100000],
                    ],
                    'sertakan_inda' => true,
                    'komitmen_inda' => 150000,
                ],
                [
                    'kode' => 'KEL-04',
                    'nama' => 'Kelompok Peduli Siswa 4',
                    'target' => 2000000,
                    'koordinator' => [
                        'nama' => 'Hj. Maryam',
                        'telp' => '081244440001',
                        'komitmen' => 250000,
                    ],
                    'anggota_lain' => [
                        ['nama' => 'Wahyu Pratama', 'telp' => '081244440002', 'komitmen' => 150000],
                        ['nama' => 'Dian Kusuma', 'telp' => '081244440003', 'komitmen' => 150000],
                        ['nama' => 'Rizky Ramadan', 'telp' => '081244440004', 'komitmen' => 100000],
                    ],
                    'sertakan_inda' => false,
                    'komitmen_inda' => 0,
                ],
            ];

            foreach ($kelompoksData as $kd) {
                // 1. Buat Koordinator Donatur
                $koor = Donatur::updateOrCreate(
                    ['nama' => $kd['koordinator']['nama']],
                    [
                        'tipe' => 'rutin',
                        'nomor_telepon' => $kd['koordinator']['telp'],
                        'is_aktif' => true,
                    ]
                );

                // 2. Buat / Update Kelompok
                $kelompok = Kelompok::updateOrCreate(
                    ['kode_kelompok' => $kd['kode']],
                    [
                        'nama_kelompok' => $kd['nama'],
                        'target_nominal' => $kd['target'],
                        'koordinator_donatur_id' => $koor->id,
                        'koordinator_nama' => $koor->nama,
                        'koordinator_telepon' => $koor->nomor_telepon,
                        'is_aktif' => true,
                        'keterangan' => 'Kelompok dummy beasiswa OTA',
                    ]
                );

                // 3. Daftarkan Koordinator sebagai Anggota Kelompok
                KelompokAnggota::updateOrCreate(
                    ['kelompok_id' => $kelompok->id, 'donatur_id' => $koor->id],
                    [
                        'nominal_komitmen' => $kd['koordinator']['komitmen'],
                        'keterangan' => 'Koordinator Kelompok',
                    ]
                );

                // 4. Daftarkan Anggota Lain
                foreach ($kd['anggota_lain'] as $al) {
                    $donaturAnggota = Donatur::updateOrCreate(
                        ['nama' => $al['nama']],
                        [
                            'tipe' => 'rutin',
                            'nomor_telepon' => $al['telp'],
                            'is_aktif' => true,
                        ]
                    );

                    KelompokAnggota::updateOrCreate(
                        ['kelompok_id' => $kelompok->id, 'donatur_id' => $donaturAnggota->id],
                        [
                            'nominal_komitmen' => $al['komitmen'],
                            'keterangan' => 'Anggota Rutin',
                        ]
                    );
                }

                // 5. Daftarkan Inda Muliana jika kelompok 1, 2, atau 3
                if ($kd['sertakan_inda']) {
                    KelompokAnggota::updateOrCreate(
                        ['kelompok_id' => $kelompok->id, 'donatur_id' => $inda->id],
                        [
                            'nominal_komitmen' => $kd['komitmen_inda'],
                            'keterangan' => 'Anggota Rutin',
                        ]
                    );
                }
            }
        });
    }
}
