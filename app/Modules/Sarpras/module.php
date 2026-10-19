<?php

return [
    'name' => 'Sarpras',
    'label' => 'Sarana & Prasarana',
    'icon' => 'archive-box',
    'order' => 45,
    'menu' => [
        [
            'label' => 'Dashboard Sarpras',
            'route' => 'sarpras.index',
            'permission' => 'sarpras.dashboard.lihat',
        ],
        [
            'label' => 'Data Aset Tetap',
            'route' => 'sarpras.aset',
            'permission' => 'sarpras.aset.lihat',
        ],
        [
            'label' => 'Bahan Habis Pakai (BHP)',
            'route' => 'sarpras.bhp',
            'permission' => 'sarpras.bhp.lihat',
        ],
        [
            'label' => 'Peminjaman Aset',
            'route' => 'sarpras.peminjaman',
            'permission' => 'sarpras.peminjaman.lihat',
        ],
        [
            'label' => 'Pemeliharaan & Servis',
            'route' => 'sarpras.pemeliharaan',
            'permission' => 'sarpras.pemeliharaan.lihat',
        ],
        [
            'label' => 'Pengajuan Pembelian',
            'route' => 'sarpras.pengajuan',
            'permission' => 'sarpras.pengajuan.lihat',
        ],
        [
            'label' => 'Master Ruangan',
            'route' => 'sarpras.ruangan',
            'permission' => 'sarpras.ruangan.lihat',
        ],
        [
            'label' => 'Master Kategori',
            'route' => 'sarpras.kategori',
            'permission' => 'sarpras.kategori.lihat',
        ],
        [
            'label' => 'Sumber Dana',
            'route' => 'sarpras.sumber-dana',
            'permission' => 'sarpras.sumber.lihat',
        ],
        [
            'label' => 'Laporan & Rekapitulasi',
            'route' => 'sarpras.laporan',
            'permission' => 'sarpras.laporan.lihat',
        ],
    ],
    'permissions' => [
        'sarpras.dashboard' => [
            'label' => 'Dashboard Sarpras',
            'actions' => ['lihat'],
        ],
        'sarpras.aset' => [
            'label' => 'Data Aset Tetap',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak'],
        ],
        'sarpras.bhp' => [
            'label' => 'Bahan Habis Pakai',
            'actions' => ['lihat', 'tambah', 'edit', 'mutasi'],
        ],
        'sarpras.ruangan' => [
            'label' => 'Master Ruangan',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus'],
        ],
        'sarpras.kategori' => [
            'label' => 'Master Kategori',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus'],
        ],
        'sarpras.sumber' => [
            'label' => 'Master Sumber Dana',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus'],
        ],
        'sarpras.pengajuan' => [
            'label' => 'Pengajuan Pembelian',
            'actions' => ['lihat', 'tambah', 'review', 'approve', 'realisasi', 'hapus'],
        ],
        'sarpras.peminjaman' => [
            'label' => 'Peminjaman Aset',
            'actions' => ['lihat', 'tambah', 'kembali', 'cetak'],
        ],
        'sarpras.pemeliharaan' => [
            'label' => 'Pemeliharaan Aset',
            'actions' => ['lihat', 'tambah', 'proses', 'selesai'],
        ],
        'sarpras.laporan' => [
            'label' => 'Laporan & Rekap Sarpras',
            'actions' => ['lihat', 'export', 'cetak'],
        ],
    ],
];
