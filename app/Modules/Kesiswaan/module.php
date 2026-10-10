<?php

return [
    'name' => 'Kesiswaan',
    'label' => 'Kesiswaan & Siswa',
    'order' => 40,
    'menu' => [
        ['label' => 'Data Siswa', 'route' => 'kesiswaan.siswa', 'permission' => 'kesiswaan.siswa.lihat'],
        ['label' => 'Cetak Absensi', 'route' => 'kesiswaan.cetak-absensi', 'permission' => 'kesiswaan.siswa.lihat'],
        ['label' => 'Import & Export DAPODIK', 'route' => 'kesiswaan.import', 'permission' => 'kesiswaan.siswa.tambah'],
        ['label' => 'Plotting Rombel (Kelas)', 'route' => 'kesiswaan.plotting-rombel', 'permission' => 'kesiswaan.siswa.edit'],
        ['label' => 'Plotting Program (Unggulan/Reguler)', 'route' => 'kesiswaan.plotting-program', 'permission' => 'kesiswaan.siswa.edit'],
        ['label' => 'Plotting Rayon Massal', 'route' => 'kesiswaan.plotting-rayon', 'permission' => 'kesiswaan.siswa.edit'],
    ],
    'permissions' => [
        'kesiswaan.siswa' => [
            'label' => 'Data Siswa & DAPODIK',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak', 'lihat_sensitif'],
        ],
    ],
];
