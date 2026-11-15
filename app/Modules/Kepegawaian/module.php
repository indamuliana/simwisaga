<?php

return [
    'name' => 'Kepegawaian',
    'label' => 'Kepegawaian (GTK)',
    'group' => 'KEPEGAWAIAN & SDM',
    'order' => 30,
    'menu' => [
        ['label' => 'Data Guru & Karyawan', 'route' => 'kepegawaian.pegawai', 'permission' => 'kepegawaian.pegawai.lihat'],
        ['label' => 'Struktur Organisasi', 'route' => 'kepegawaian.struktur-organisasi', 'permission' => 'kepegawaian.struktur_organisasi.lihat'],
    ],
    'permissions' => [
        'kepegawaian.pegawai' => ['label' => 'Data Guru & Karyawan', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak', 'import', 'ekspor']],
        'kepegawaian.struktur_organisasi' => ['label' => 'Struktur Organisasi', 'actions' => ['lihat', 'kelola']],
    ],
];
