<?php

return [
    'name' => 'PortalSiswa',
    'label' => 'Portal Siswa',
    'icon' => 'academic-cap',
    'order' => 15,
    'menu' => [
        [
            'label' => 'Beranda & Agenda',
            'route' => 'portal-siswa.beranda',
            'permission' => 'portal_siswa.agenda.lihat',
        ],
        [
            'label' => 'Profil Saya',
            'route' => 'portal-siswa.profil',
            'permission' => 'portal_siswa.profil.lihat',
        ],
        [
            'label' => 'Nilai & Rapor',
            'route' => 'portal-siswa.nilai-rapor',
            'permission' => 'portal_siswa.nilai.lihat',
        ],
        [
            'label' => 'Kehadiran',
            'route' => 'portal-siswa.kehadiran',
            'permission' => 'portal_siswa.kehadiran.lihat',
        ],
        [
            'label' => 'UPD & Seni Budaya',
            'route' => 'portal-siswa.upd-seni',
            'permission' => 'portal_siswa.upd_seni.lihat',
        ],
        [
            'label' => 'Prestasi',
            'route' => 'portal-siswa.prestasi',
            'permission' => 'portal_siswa.prestasi.lihat',
        ],
        [
            'label' => 'Beasiswa',
            'route' => 'portal-siswa.beasiswa',
            'permission' => 'portal_siswa.beasiswa.lihat',
        ],
        [
            'label' => 'Kepesantrenan & Hafalan',
            'route' => 'portal-siswa.kepesantrenan',
            'permission' => 'portal_siswa.agenda.lihat',
        ],
    ],
    'permissions' => [
        'portal_siswa.agenda' => [
            'label' => 'Agenda Publik Siswa',
            'actions' => ['lihat'],
        ],
        'portal_siswa.profil' => [
            'label' => 'Profil Siswa & Perbaikan Data',
            'actions' => ['lihat', 'edit'],
        ],
        'portal_siswa.nilai' => [
            'label' => 'Nilai Akademik & Rapor Siswa',
            'actions' => ['lihat', 'cetak'],
        ],
        'portal_siswa.kehadiran' => [
            'label' => 'Kehadiran Siswa',
            'actions' => ['lihat'],
        ],
        'portal_siswa.upd_seni' => [
            'label' => 'UPD & Seni Budaya Siswa',
            'actions' => ['lihat'],
        ],
        'portal_siswa.prestasi' => [
            'label' => 'Prestasi Siswa',
            'actions' => ['lihat', 'tambah'],
        ],
        'portal_siswa.beasiswa' => [
            'label' => 'Beasiswa Siswa',
            'actions' => ['lihat', 'tambah'],
        ],
    ],
];
