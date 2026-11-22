<?php

return [
    'name' => 'PortalSiswa',
    'label' => 'Portal Siswa',
    'group' => 'Portal Siswa',
    'icon' => 'academic-cap',
    'order' => 15,
    'menu' => [
        [
            'label' => 'Beranda & Agenda',
            'route' => 'portal-siswa.beranda',
            'permission' => 'portal_siswa.agenda.lihat',
            'icon' => 'home',
        ],
        [
            'label' => 'Profil Saya',
            'route' => 'portal-siswa.profil',
            'permission' => 'portal_siswa.profil.lihat',
            'icon' => 'user',
        ],
        [
            'label' => 'Ledger Nilai & Rapor',
            'route' => 'portal-siswa.nilai-rapor',
            'permission' => 'portal_siswa.nilai.lihat',
            'icon' => 'document-chart-bar',
        ],
        [
            'label' => 'Kehadiran',
            'route' => 'portal-siswa.kehadiran',
            'permission' => 'portal_siswa.kehadiran.lihat',
            'icon' => 'calendar',
        ],
        [
            'label' => 'Unit Pengembangan Diri (UPD)',
            'route' => 'portal-siswa.upd',
            'permission' => 'portal_siswa.upd_seni.lihat',
            'icon' => 'sparkles',
        ],
        [
            'label' => 'Seni Budaya',
            'route' => 'portal-siswa.seni-budaya',
            'permission' => 'portal_siswa.upd_seni.lihat',
            'icon' => 'paint-brush',
        ],
        [
            'label' => 'Prestasi',
            'route' => 'portal-siswa.prestasi',
            'permission' => 'portal_siswa.prestasi.lihat',
            'icon' => 'trophy',
        ],
        [
            'label' => 'Beasiswa & OTA',
            'route' => 'portal-siswa.beasiswa',
            'permission' => 'portal_siswa.beasiswa.lihat',
            'icon' => 'heart',
        ],
        [
            'label' => 'Kepesantrenan & Hafalan',
            'route' => 'portal-siswa.kepesantrenan',
            'permission' => 'portal_siswa.agenda.lihat',
            'icon' => 'moon',
        ],
        [
            'label' => 'Kontak Guru & Pegawai',
            'route' => 'portal-siswa.kontak',
            'permission' => 'portal_siswa.agenda.lihat',
            'icon' => 'phone',
        ],
        [
            'label' => 'Keuangan & Tagihan (Ortu)',
            'route' => 'portal-siswa.keuangan',
            'permission' => 'keuangan.ortu',
            'icon' => 'banknotes',
        ],
    ],
    'permissions' => [
        'portal_siswa.agenda' => [
            'label' => 'Agenda Publik Siswa',
            'actions' => ['lihat'],
        ],
        'portal_siswa.kontak' => [
            'label' => 'Kontak Guru & Pegawai',
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
        'portal_siswa.upd' => [
            'label' => 'Unit Pengembangan Diri (UPD) Siswa',
            'actions' => ['lihat'],
        ],
        'portal_siswa.seni_budaya' => [
            'label' => 'Seni Budaya Siswa',
            'actions' => ['lihat'],
        ],
        'portal_siswa.upd_seni' => [
            'label' => 'UPD & Seni Budaya Siswa (Legacy)',
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
