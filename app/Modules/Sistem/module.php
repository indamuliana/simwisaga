<?php

return [
    'name' => 'Sistem',
    'label' => 'Pengaturan Sistem',
    'order' => 90,
    'menu' => [
        ['label' => 'Identitas Sekolah', 'route' => 'sistem.sekolah', 'permission' => 'sistem.sekolah.lihat'],
        ['label' => 'Template Dokumen PDF', 'route' => 'sistem.template', 'permission' => 'sistem.template.lihat'],
        ['label' => 'Pengguna', 'route' => 'sistem.pengguna', 'permission' => 'sistem.pengguna.lihat'],
        ['label' => 'Hak Akses', 'route' => 'sistem.hakakses', 'permission' => 'sistem.hakakses.lihat'],
        ['label' => 'Log Aktivitas', 'route' => 'sistem.audit', 'permission' => 'sistem.audit.lihat'],
    ],
    'permissions' => [
        'sistem.sekolah' => ['label' => 'Identitas Sekolah', 'actions' => ['lihat', 'edit']],
        'sistem.template' => ['label' => 'Template Dokumen PDF', 'actions' => ['lihat', 'edit']],
        'sistem.pengguna' => ['label' => 'Pengguna', 'actions' => ['lihat', 'tambah', 'edit']],
        'sistem.hakakses' => ['label' => 'Hak Akses', 'actions' => ['lihat', 'edit']],
        'sistem.audit' => ['label' => 'Log Aktivitas', 'actions' => ['lihat']],
    ],
];
