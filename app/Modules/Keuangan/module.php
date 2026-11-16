<?php

return [
    'name' => 'Keuangan',
    'label' => 'Pembayaran Siswa',
    'group' => 'KEUANGAN',
    'icon' => 'banknotes',
    'order' => 45,
    'menu' => [
        [
            'label' => 'Dashboard & Piutang',
            'route' => 'keuangan.index',
            'permission' => 'keuangan.lihat',
        ],
        [
            'label' => 'Transaksi Kasir',
            'route' => 'keuangan.kasir',
            'permission' => 'keuangan.transaksi',
        ],
        [
            'label' => 'Verifikasi Transfer Ortu',
            'route' => 'keuangan.verifikasi',
            'permission' => 'keuangan.verifikasi',
        ],
        [
            'label' => 'Terbitkan Tagihan & WA',
            'route' => 'keuangan.tagihan',
            'permission' => 'keuangan.tagihan',
        ],
        [
            'label' => 'Pos & Tarif Biaya',
            'route' => 'keuangan.tarif',
            'permission' => 'keuangan.tarif',
        ],
        [
            'label' => 'Piutang Alumni',
            'route' => 'keuangan.alumni',
            'permission' => 'keuangan.alumni',
        ],
        [
            'label' => 'Pantauan Binaan Rayon',
            'route' => 'keuangan.binaan',
            'permission' => 'keuangan.binaan',
        ],
        [
            'label' => 'Rekapitulasi & Buku Kas',
            'route' => 'keuangan.rekap',
            'permission' => 'keuangan.laporan',
        ],
        [
            'label' => 'Rekening Sekolah',
            'route' => 'keuangan.rekening',
            'permission' => 'keuangan.tarif',
        ],
    ],
    'permissions' => [
        'keuangan' => [
            'label' => 'Keuangan & Pembayaran Siswa',
            'actions' => ['lihat', 'transaksi', 'verifikasi', 'tagihan', 'tarif', 'alumni', 'binaan', 'laporan', 'cetak'],
        ],
    ],
];

