<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Keuangan\Models\PosBiaya;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Tarif;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class KeuanganInitialSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Buat Permissions Keuangan
        $permissions = [
            'keuangan.lihat' => 'Melihat Dashboard & Laporan Keuangan',
            'keuangan.transaksi' => 'Melayani Transaksi Kasir Pembayaran Siswa',
            'keuangan.verifikasi' => 'Memverifikasi Bukti Pembayaran Online Ortu',
            'keuangan.tagihan' => 'Menerbitkan Tagihan & Notifikasi WhatsApp',
            'keuangan.tarif' => 'Mengatur Pos Biaya & Skema Tarif',
            'keuangan.alumni' => 'Mengelola Piutang Alumni & Penahanan Ijazah',
            'keuangan.binaan' => 'Memantau Pembayaran Siswa Rayon Binaan',
            'keuangan.laporan' => 'Mengakses Rekapitulasi Kas & Ekspor Data',
            'keuangan.cetak' => 'Mencetak Kwitansi & Surat Tagihan Resmi',
            'keuangan.ortu' => 'Melihat Tagihan & Upload Bukti Transfer di Portal Ortu',
        ];

        foreach ($permissions as $name => $desc) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // 2. Assign ke Roles
        if ($bendahara = Role::where('name', 'Bendahara')->first()) {
            $bendahara->givePermissionTo([
                'keuangan.lihat',
                'keuangan.transaksi',
                'keuangan.verifikasi',
                'keuangan.tagihan',
                'keuangan.tarif',
                'keuangan.alumni',
                'keuangan.laporan',
                'keuangan.cetak',
            ]);
        }

        if ($tu = Role::where('name', 'Tata Usaha')->first()) {
            $tu->givePermissionTo([
                'keuangan.lihat',
                'keuangan.transaksi',
                'keuangan.tagihan',
                'keuangan.cetak',
            ]);
        }

        if ($kepsek = Role::where('name', 'Kepala Sekolah')->first()) {
            $kepsek->givePermissionTo([
                'keuangan.lihat',
                'keuangan.laporan',
                'keuangan.cetak',
            ]);
        }

        if ($guru = Role::where('name', 'Guru')->first()) {
            $guru->givePermissionTo([
                'keuangan.binaan',
            ]);
        }

        if ($ortu = Role::where('name', 'Orang Tua')->first()) {
            $ortu->givePermissionTo([
                'keuangan.ortu',
            ]);
        }

        // 3. Rekening Kas & Bank
        Rekening::firstOrCreate(
            ['nama_bank' => 'Kas Tunai Bendahara'],
            [
                'nomor_rekening' => null,
                'atas_nama' => 'Bendahara Sekolah',
                'is_kas_tunai' => true,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_aktif' => true,
                'keterangan' => 'Kas fisik loket pembayaran tunai sekolah',
            ]
        );

        Rekening::firstOrCreate(
            ['nama_bank' => 'Bank Syariah Indonesia (BSI)'],
            [
                'nomor_rekening' => '7182938475',
                'atas_nama' => 'SMK WIKRAMA 1 GARUT',
                'is_kas_tunai' => false,
                'saldo_awal' => 0,
                'saldo_saat_ini' => 0,
                'is_aktif' => true,
                'keterangan' => 'Rekening utama penampungan transfer SPP & DSP',
            ]
        );

        // 4. Pos Biaya Dasar
        $posSpp = PosBiaya::firstOrCreate(
            ['kode' => 'SPP'],
            [
                'nama' => 'SPP Bulanan',
                'tipe' => 'bulanan',
                'is_aktif' => true,
                'deskripsi' => 'Iuran pembinaan pendidikan rutin bulanan (12 bulan/TA)',
            ]
        );

        $posDsp = PosBiaya::firstOrCreate(
            ['kode' => 'DSP'],
            [
                'nama' => 'Dana Sumbangan Pendidikan (DSP)',
                'tipe' => 'bebas',
                'is_aktif' => true,
                'deskripsi' => 'Uang gedung / sarana awal masuk yang dapat dicicil',
            ]
        );

        $posLsp = PosBiaya::firstOrCreate(
            ['kode' => 'LSP'],
            [
                'nama' => 'Uji Sertifikasi Kejuruan (LSP)',
                'tipe' => 'insidental',
                'is_aktif' => true,
                'deskripsi' => 'Biaya sertifikasi kompetensi keahlian dan asesor BNSP',
            ]
        );

        // 5. Tarif Standar per Angkatan & Program pada Tahun Ajaran Aktif
        $ta = TahunAjaran::first();
        $progReguler = Program::where('kode', 'REG')->first();
        $progUnggulan = Program::where('kode', 'UNG')->first();

        if ($ta) {
            $tarifDefaults = [
                // Tingkat X dibedakan antara Unggulan dan Reguler
                ['pos' => $posSpp, 'tingkat' => 'X', 'program_id' => $progUnggulan?->id, 'nominal' => 450000],
                ['pos' => $posSpp, 'tingkat' => 'X', 'program_id' => $progReguler?->id, 'nominal' => 350000],
                ['pos' => $posDsp, 'tingkat' => 'X', 'program_id' => $progUnggulan?->id, 'nominal' => 4500000],
                ['pos' => $posDsp, 'tingkat' => 'X', 'program_id' => $progReguler?->id, 'nominal' => 3500000],

                // Tingkat XI & XII sama untuk semua program (program_id = null)
                ['pos' => $posSpp, 'tingkat' => 'XI', 'program_id' => null, 'nominal' => 325000],
                ['pos' => $posSpp, 'tingkat' => 'XII', 'program_id' => null, 'nominal' => 300000],
                ['pos' => $posDsp, 'tingkat' => 'XI', 'program_id' => null, 'nominal' => 3000000],
                ['pos' => $posDsp, 'tingkat' => 'XII', 'program_id' => null, 'nominal' => 2500000],
                ['pos' => $posLsp, 'tingkat' => 'XII', 'program_id' => null, 'nominal' => 650000],
            ];

            foreach ($tarifDefaults as $t) {
                Tarif::updateOrCreate(
                    [
                        'pos_biaya_id' => $t['pos']->id,
                        'tahun_ajaran_id' => $ta->id,
                        'tingkat' => $t['tingkat'],
                        'program_id' => $t['program_id'],
                    ],
                    [
                        'nominal' => $t['nominal'],
                    ]
                );
            }
        }
    }
}

