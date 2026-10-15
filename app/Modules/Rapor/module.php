<?php

return [
    'name' => 'Rapor',
    'label' => 'Rapor Akademik',
    'icon' => 'document-text',
    'order' => 80,
    'menu' => [
        [
            'label' => 'Rapor Wikrama (9 Minggu)',
            'route' => 'rapor.wikrama',
            'permission' => 'rapor.lihat',
        ],
        [
            'label' => 'Rapor Pemerintah (Semester)',
            'route' => 'rapor.pemerintah',
            'permission' => 'rapor.lihat',
        ],
        [
            'label' => 'Catatan Wali Kelas / Rayon',
            'route' => 'rapor.catatan-wali-kelas',
            'permission' => 'rapor.lihat',
        ],
    ],
    'permissions' => [
        'rapor' => [
            'label' => 'Cetak Rapor & Catatan Wali',
            'actions' => ['lihat', 'edit', 'cetak'],
        ],
    ],
];
