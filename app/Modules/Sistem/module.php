<?php

return [
    'name' => 'Sistem',
    'label' => 'Pengaturan Sistem',
    'order' => 90,
    'menu' => [
        ['label' => 'Identitas Sekolah', 'route' => 'sistem.sekolah', 'permission' => 'sistem.sekolah.lihat'],
        ['label' => 'Template Dokumen PDF', 'route' => 'sistem.template', 'permission' => 'sistem.template.lihat'],
        ['label' => 'Titi Mangsa Rapor', 'route' => 'sistem.titimangsa', 'permission' => 'sistem.titimangsa.lihat'],
        ['label' => 'Database', 'route' => 'sistem.database', 'permission' => 'sistem.database.lihat'],
        ['label' => 'Pengguna', 'route' => 'sistem.pengguna', 'permission' => 'sistem.pengguna.lihat'],
        ['label' => 'Hak Akses', 'route' => 'sistem.hakakses', 'permission' => 'sistem.hakakses.lihat'],
        ['label' => 'Log Aktivitas', 'route' => 'sistem.audit', 'permission' => 'sistem.audit.lihat'],
    ],
    'permissions' => [
        'sistem.sekolah' => ['label' => 'Identitas Sekolah', 'actions' => ['lihat', 'edit']],
        'sistem.template' => ['label' => 'Template Dokumen PDF', 'actions' => ['lihat', 'edit']],
        'sistem.titimangsa' => ['label' => 'Titi Mangsa Rapor', 'actions' => ['lihat', 'tambah', 'edit', 'hapus']],
        'sistem.database' => ['label' => 'Database', 'actions' => ['lihat', 'backup', 'restore', 'hapus']],
        'sistem.pengguna' => ['label' => 'Pengguna', 'actions' => ['lihat', 'tambah', 'edit', 'hapus']],
        'sistem.hakakses' => ['label' => 'Hak Akses', 'actions' => ['lihat', 'edit']],
        'sistem.audit' => ['label' => 'Log Aktivitas', 'actions' => ['lihat']],
    ],
];
