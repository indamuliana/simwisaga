<?php

return [
    'name' => 'Alumni',
    'label' => 'Pengelolaan Alumni',
    'group' => 'KESISWAAN',
    'icon' => 'academic-cap',
    'order' => 45,
    'menu' => [
        ['label' => 'Data Alumni', 'route' => 'alumni.index', 'permission' => 'alumni.alumni.lihat'],
    ],
    'permissions' => [
        'alumni.alumni' => [
            'label' => 'Data Alumni & Tracer Study',
            'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'cetak'],
        ],
    ],
];
