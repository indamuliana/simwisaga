<?php

return [
    'name' => 'AdministrasiWeb',
    'label' => 'Administrasi Web',
    'icon' => 'globe-alt',
    'order' => 85,
    'menu' => [
        ['label' => 'Pengaturan Landing Page', 'route' => 'web-admin.landing', 'permission' => 'web.landing.lihat'],
        ['label' => 'Daftar Artikel & Berita', 'route' => 'web-admin.articles.index', 'permission' => 'web.artikel.lihat'],
        ['label' => 'Tulis Artikel Baru', 'route' => 'web-admin.articles.create', 'permission' => 'web.artikel.tambah'],
        ['label' => 'Kelola Menu Navigasi', 'route' => 'web-admin.menus.index', 'permission' => 'web.menu.lihat'],
        ['label' => 'Halaman Kustom', 'route' => 'web-admin.pages.index', 'permission' => 'web.halaman.lihat'],
        ['label' => 'Fasilitas & Lab Praktik', 'route' => 'web-admin.facilities.index', 'permission' => 'web.galeri.lihat'],
        ['label' => 'Suara Komunitas / Testimoni', 'route' => 'web-admin.testimonials.index', 'permission' => 'web.galeri.lihat'],
    ],
    'permissions' => [
        'web.landing' => ['label' => 'Pengaturan Landing Page', 'actions' => ['lihat', 'edit']],
        'web.artikel' => ['label' => 'Manajemen Artikel & Berita', 'actions' => ['lihat', 'tambah', 'edit', 'hapus', 'publikasi']],
        'web.menu' => ['label' => 'Manajemen Menu Navigasi', 'actions' => ['lihat', 'edit']],
        'web.halaman' => ['label' => 'Manajemen Halaman Statis', 'actions' => ['lihat', 'tambah', 'edit', 'hapus']],
        'web.galeri' => ['label' => 'Fasilitas & Galeri', 'actions' => ['lihat', 'tambah', 'edit', 'hapus']],
    ],
];
