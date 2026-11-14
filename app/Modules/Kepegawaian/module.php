<?php

return [
    'name' => 'Kepegawaian',
    'label' => 'Kepegawaian (GTK)',
    'group' => 'KEPEGAWAIAN & SDM',
    'order' => 30,
    'menu' => [
        ['label' => 'Data Guru & Karyawan', 'route' => 'kepegawaian.pegawai', 'permission' => 'kepegawaian.pegawai.lihat'],
    ],
    'permissions' => [
        'kepegawaian.pegawai' => ['label' => 'Data Guru & Karyawan', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak', 'import', 'ekspor']],
    ],
];
