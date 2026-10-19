<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Bhp;
use App\Modules\Sarpras\Models\BhpMutasi;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Pengajuan;
use App\Modules\Sarpras\Models\Ruangan;
use App\Modules\Sarpras\Models\SumberDana;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SarprasInitialSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Roles
        $roleSarpras = Role::findOrCreate('Koordinator Sarpras', 'web');
        $roleKepsek = Role::findOrCreate('Kepala Sekolah', 'web');
        $roleBendahara = Role::findOrCreate('Bendahara', 'web');
        $roleGuru = Role::findOrCreate('Guru', 'web');
        $roleTU = Role::findOrCreate('Tata Usaha', 'web');

        // 2. Permissions
        $permissions = [
            'sarpras.dashboard.lihat',
            'sarpras.aset.lihat',
            'sarpras.aset.tambah',
            'sarpras.aset.edit',
            'sarpras.aset.hapus',
            'sarpras.aset.cetak',
            'sarpras.bhp.lihat',
            'sarpras.bhp.tambah',
            'sarpras.bhp.edit',
            'sarpras.bhp.mutasi',
            'sarpras.ruangan.lihat',
            'sarpras.ruangan.tambah',
            'sarpras.ruangan.edit',
            'sarpras.ruangan.hapus',
            'sarpras.kategori.lihat',
            'sarpras.kategori.tambah',
            'sarpras.kategori.edit',
            'sarpras.kategori.hapus',
            'sarpras.sumber.lihat',
            'sarpras.sumber.tambah',
            'sarpras.sumber.edit',
            'sarpras.sumber.hapus',
            'sarpras.pengajuan.lihat',
            'sarpras.pengajuan.tambah',
            'sarpras.pengajuan.review',
            'sarpras.pengajuan.approve',
            'sarpras.pengajuan.realisasi',
            'sarpras.pengajuan.hapus',
            'sarpras.peminjaman.lihat',
            'sarpras.peminjaman.tambah',
            'sarpras.peminjaman.kembali',
            'sarpras.peminjaman.cetak',
            'sarpras.pemeliharaan.lihat',
            'sarpras.pemeliharaan.tambah',
            'sarpras.pemeliharaan.proses',
            'sarpras.pemeliharaan.selesai',
            'sarpras.laporan.lihat',
            'sarpras.laporan.export',
            'sarpras.laporan.cetak',
        ];

        foreach ($permissions as $p) {
            Permission::findOrCreate($p, 'web');
        }

        // Assign to Koordinator Sarpras (Semua hak akses)
        $roleSarpras->syncPermissions($permissions);

        // Assign to Kepala Sekolah
        $roleKepsek->givePermissionTo([
            'sarpras.dashboard.lihat',
            'sarpras.aset.lihat',
            'sarpras.aset.cetak',
            'sarpras.bhp.lihat',
            'sarpras.sumber.lihat',
            'sarpras.pengajuan.lihat',
            'sarpras.pengajuan.approve',
            'sarpras.laporan.lihat',
            'sarpras.laporan.cetak',
        ]);

        // Assign to Bendahara
        $roleBendahara->givePermissionTo([
            'sarpras.dashboard.lihat',
            'sarpras.pengajuan.lihat',
            'sarpras.pengajuan.realisasi',
            'sarpras.laporan.lihat',
        ]);

        // Assign to Guru & TU
        foreach ([$roleGuru, $roleTU] as $r) {
            $r->givePermissionTo([
                'sarpras.dashboard.lihat',
                'sarpras.pengajuan.lihat',
                'sarpras.pengajuan.tambah',
                'sarpras.peminjaman.lihat',
                'sarpras.peminjaman.tambah',
                'sarpras.pemeliharaan.lihat',
                'sarpras.pemeliharaan.tambah',
            ]);
        }

        // 3. Master Kategori Aset
        $kategoris = [
            ['kode' => 'ELK', 'nama' => 'Elektronik & TIK', 'jenis' => 'aset_tetap', 'deskripsi' => 'Komputer, laptop, printer, proyektor, server, jaringan'],
            ['kode' => 'MSN', 'nama' => 'Mesin & Peralatan Praktik', 'jenis' => 'aset_tetap', 'deskripsi' => 'Mesin bubut, mesin las, kompresor, genset'],
            ['kode' => 'ALT', 'nama' => 'Perkakas & Alat Ukur', 'jenis' => 'aset_tetap', 'deskripsi' => 'Multitester, solder, obeng set, tang, osiloskop'],
            ['kode' => 'MBL', 'nama' => 'Mebel & Perlengkapan Ruang', 'jenis' => 'aset_tetap', 'deskripsi' => 'Meja siswa, kursi guru, lemari arsip, whiteboard'],
            ['kode' => 'KND', 'nama' => 'Kendaraan Operasional', 'jenis' => 'aset_tetap', 'deskripsi' => 'Motor dinas, mobil operasional'],
            ['kode' => 'BHP', 'nama' => 'Bahan Habis Pakai (BHP)', 'jenis' => 'habis_pakai', 'deskripsi' => 'ATK, kertas HVS, spidol boardmarker, tinta printer, reagen/oli'],
        ];

        foreach ($kategoris as $k) {
            Kategori::firstOrCreate(['kode' => $k['kode']], $k);
        }

        // 4. Master Ruangan Minimalis
        $ruangans = [
            ['kode_ruangan' => 'LAB-KOMP-1', 'nama_ruangan' => 'Laboratorium Komputer 1', 'lokasi' => 'Gedung B Lantai 2', 'keterangan' => 'Lab Praktik Rekayasa Perangkat Lunak'],
            ['kode_ruangan' => 'LAB-KOMP-2', 'nama_ruangan' => 'Laboratorium Komputer 2', 'lokasi' => 'Gedung B Lantai 2', 'keterangan' => 'Lab Praktik Jaringan Komputer & Multimedia'],
            ['kode_ruangan' => 'BKL-MESIN', 'nama_ruangan' => 'Bengkel Teknik Mesin', 'lokasi' => 'Gedung C Lantai 1', 'keterangan' => 'Workshop Bubut & Fabrikasi Logam'],
            ['kode_ruangan' => 'R-GURU', 'nama_ruangan' => 'Ruang Guru Utama', 'lokasi' => 'Gedung A Lantai 1', 'keterangan' => 'Ruang Kerja Dewan Guru'],
            ['kode_ruangan' => 'R-TU', 'nama_ruangan' => 'Ruang Tata Usaha & Administrasi', 'lokasi' => 'Gedung A Lantai 1', 'keterangan' => 'Pusat Pelayanan Administrasi'],
            ['kode_ruangan' => 'R-KEPSEK', 'nama_ruangan' => 'Ruang Kepala Sekolah', 'lokasi' => 'Gedung A Lantai 1', 'keterangan' => 'Kantor Kepala Sekolah'],
            ['kode_ruangan' => 'PERPUS', 'nama_ruangan' => 'Perpustakaan Sekolah', 'lokasi' => 'Gedung D Lantai 1', 'keterangan' => 'Pusat Sumber Belajar'],
            ['kode_ruangan' => 'GUDANG-SARPRAS', 'nama_ruangan' => 'Gudang Inventaris Sarpras', 'lokasi' => 'Gedung E Belakang', 'keterangan' => 'Penyimpanan persediaan & aset non-aktif'],
        ];

        foreach ($ruangans as $r) {
            Ruangan::firstOrCreate(['kode_ruangan' => $r['kode_ruangan']], $r);
        }

        // 5. Master Sumber Dana
        $sumberDanas = [
            ['kode' => 'BS26', 'nama' => 'BOSP Reguler 2026', 'keterangan' => 'Dana Bantuan Operasional Satuan Pendidikan 2026', 'is_active' => true],
            ['kode' => 'BS25', 'nama' => 'BOSP Reguler 2025', 'keterangan' => 'Dana Bantuan Operasional Satuan Pendidikan 2025', 'is_active' => true],
            ['kode' => 'YYSN', 'nama' => 'Yayasan Pendidikan', 'keterangan' => 'Anggaran Mandiri Yayasan / Lembaga Penyelenggara', 'is_active' => true],
            ['kode' => 'KMTE', 'nama' => 'Komite Sekolah', 'keterangan' => 'Sumbangan Partisipasi Komite & Orang Tua Siswa', 'is_active' => true],
            ['kode' => 'HIBA', 'nama' => 'Hibah / DAK Fisik', 'keterangan' => 'Bantuan Pemerintah Pusat / Pemprov / Industri', 'is_active' => true],
            ['kode' => 'LAIN', 'nama' => 'Sumber Lainnya', 'keterangan' => 'Pendapatan Usaha & Sumber Sah Lainnya', 'is_active' => true],
        ];

        foreach ($sumberDanas as $s) {
            SumberDana::firstOrCreate(['kode' => $s['kode']], $s);
        }

        // 6. Data Contoh Aset Tetap
        $katElk = Kategori::where('kode', 'ELK')->first();
        $katMbl = Kategori::where('kode', 'MBL')->first();
        $katMsn = Kategori::where('kode', 'MSN')->first();
        $rLab1 = Ruangan::where('kode_ruangan', 'LAB-KOMP-1')->first();
        $rGuru = Ruangan::where('kode_ruangan', 'R-GURU')->first();
        $rBkl = Ruangan::where('kode_ruangan', 'BKL-MESIN')->first();

        $sumberBS26 = SumberDana::where('kode', 'BS26')->first();
        $sumberBS25 = SumberDana::where('kode', 'BS25')->first();
        $sumberHIBA = SumberDana::where('kode', 'HIBA')->first();

        $admin = User::first();

        if ($katElk && $rLab1 && Aset::count() === 0) {
            Aset::create([
                'kode_aset' => 'AST/BS26/ELK/2026/0001',
                'nama_barang' => 'PC All-in-One Core i5 16GB',
                'merk_tipe' => 'Lenovo IdeaCentre AIO 3',
                'nomor_seri' => 'LNV-AIO-2026-091',
                'kategori_id' => $katElk->id,
                'ruangan_id' => $rLab1->id,
                'sumber_dana_id' => $sumberBS26?->id,
                'tahun_perolehan' => 2026,
                'tanggal_pengadaan' => '2026-02-10',
                'sumber_dana' => 'BOSP Reguler 2026',
                'harga_perolehan' => 11500000,
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Unit PC Praktik RPL Siswa',
            ]);

            Aset::create([
                'kode_aset' => 'AST/BS25/ELK/2025/0001',
                'nama_barang' => 'Proyektor Epson EB-E500 3300 Lumens',
                'merk_tipe' => 'Epson EB-E500',
                'nomor_seri' => 'EPS-PRJ-8821',
                'kategori_id' => $katElk->id,
                'ruangan_id' => $rGuru->id,
                'sumber_dana_id' => $sumberBS25?->id,
                'tahun_perolehan' => 2025,
                'tanggal_pengadaan' => '2025-08-15',
                'sumber_dana' => 'BOSP Reguler 2025',
                'harga_perolehan' => 5800000,
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Proyektor Portabel Pembelajaran',
            ]);

            if ($katMsn && $rBkl) {
                Aset::create([
                    'kode_aset' => 'AST/HIBA/MSN/2024/0001',
                    'nama_barang' => 'Mesin Bubut Konvensional Heavy Duty',
                    'merk_tipe' => 'Grizzly Industrial G0709',
                    'nomor_seri' => 'GRIZ-MB-771',
                    'kategori_id' => $katMsn->id,
                    'ruangan_id' => $rBkl->id,
                    'sumber_dana_id' => $sumberHIBA?->id,
                    'tahun_perolehan' => 2024,
                    'tanggal_pengadaan' => '2024-05-20',
                    'sumber_dana' => 'Hibah / DAK Fisik',
                    'harga_perolehan' => 45000000,
                    'kondisi' => 'Baik',
                    'status' => 'Tersedia',
                    'keterangan' => 'Mesin Praktik Utama Bengkel',
                ]);
            }
        }

        // 7. Data Contoh Bahan Habis Pakai (BHP) & Kartu Stok
        $katBhp = Kategori::where('kode', 'BHP')->first();
        if ($katBhp && Bhp::count() === 0) {
            $hvs = Bhp::create([
                'kode_barang' => 'BHP-0001',
                'nama_barang' => 'Kertas HVS A4 80gr PaperOne',
                'kategori_id' => $katBhp->id,
                'satuan' => 'Rim',
                'stok_saat_ini' => 45,
                'stok_minimum' => 10,
                'keterangan' => 'Kebutuhan administrasi cetak kantor dan ujian',
            ]);

            $spidol = Bhp::create([
                'kode_barang' => 'BHP-0002',
                'nama_barang' => 'Spidol Boardmarker Hitam Snowman',
                'kategori_id' => $katBhp->id,
                'satuan' => 'Pcs',
                'stok_saat_ini' => 6,
                'stok_minimum' => 12, // Stok menipis
                'keterangan' => 'Spidol kelas pembelajaran reguler',
            ]);

            if ($admin) {
                BhpMutasi::create([
                    'bhp_id' => $hvs->id,
                    'jenis' => 'masuk',
                    'tanggal' => now()->toDateString(),
                    'jumlah' => 50,
                    'sisa_stok_setelahnya' => 50,
                    'penerima' => 'Gudang Sarpras',
                    'keterangan' => 'Pengadaan triwulan ATK sekolah',
                    'created_by' => $admin->id,
                ]);

                BhpMutasi::create([
                    'bhp_id' => $hvs->id,
                    'jenis' => 'keluar',
                    'tanggal' => now()->toDateString(),
                    'jumlah' => 5,
                    'sisa_stok_setelahnya' => 45,
                    'ruangan_id' => $rGuru->id ?? null,
                    'penerima' => 'Staf Tata Usaha',
                    'keterangan' => 'Distribusi berkas ujian semester',
                    'created_by' => $admin->id,
                ]);
            }
        }

        // 7. Data Contoh Pengajuan Pembelian
        if ($admin && $katElk && Pengajuan::count() === 0) {
            Pengajuan::create([
                'nomor_pengajuan' => 'PP-20261020-0001',
                'user_id' => $admin->id,
                'nama_barang' => 'Kabel UTP Cat6 Belden (1 Roll 305m)',
                'kategori_id' => $katElk->id,
                'spesifikasi' => 'Original Belden Cat6 High Speed, Jack RJ45 Crimper kit',
                'jumlah' => 2,
                'estimasi_harga_satuan' => 1850000,
                'total_estimasi' => 3700000,
                'urgensi' => 'Tinggi',
                'ruangan_id' => $rLab1->id ?? null,
                'status' => 'Review Sarpras',
                'catatan_sarpras' => 'Dibutuhkan untuk peremajaan kabel jaringan Lab RPL menjelang UKK',
            ]);
        }
    }
}
