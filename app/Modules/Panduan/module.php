<?php

return [
    'name' => 'Panduan',
    'label' => 'Panduan Aplikasi',
    'group' => 'PENGATURAN',
    'icon' => 'book-open',
    'order' => 99, // Urutan paling bawah di sidebar navigasi
    'menu' => [
        ['label' => 'Buku Panduan SIRAGA', 'route' => 'panduan.index', 'permission' => ''],
    ],
    'permissions' => [],
];
