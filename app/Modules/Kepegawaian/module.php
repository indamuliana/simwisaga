<?php

return [
    'name' => 'Kepegawaian',
    'label' => 'Kepegawaian (GTK)',
    'order' => 30,
    'menu' => [
        ['label' => 'Data Guru & Karyawan', 'route' => 'kepegawaian.pegawai', 'permission' => 'kepegawaian.pegawai.lihat'],
        ['label' => 'Penugasan Mengajar', 'route' => 'kepegawaian.penugasan', 'permission' => 'kepegawaian.penugasan.lihat'],
    ],
    'permissions' => [
        'kepegawaian.pegawai' => ['label' => 'Data Guru & Karyawan', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
        'kepegawaian.penugasan' => ['label' => 'Penugasan Mengajar', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak']],
    ],
];
