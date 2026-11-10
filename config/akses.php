<?php
/**
 * Daftar menu admin. 'menu' = menu yang BISA dicentang superadmin untuk admin/operator bidang & UPTD.
 * 'khusus' = hanya superadmin. 'route' = [nama_rute, ...parameter]; 'segmen' = nama sumber daya CRUD, 'aktif' = pola rute.
 */
return [
    'grup' => ['Konten Website', 'Layanan & Data', 'SIMBADOK', 'SIPANDAI', 'Sistem'],
    'peran' => [
        'superadmin' => 'Superadmin (akses penuh)',
        'admin' => 'Admin Bidang/UPTD',
        'operator' => 'Operator / Pegawai Bidang/UPTD',
    ],
    // saran isian cepat di form pengguna (hanya pilihan awal; tetap bisa diubah)
    'preset' => [
        'admin' => ['berita', 'galeri', 'dokumen', 'reel', 'pesan', 'simbadok'],
        'operator' => ['berita', 'galeri', 'simbadok'],
    ],
    'menu' => [
        'berita'   => ['label' => 'Berita', 'icon' => 'fa-newspaper', 'route' => ['admin.index', 'berita'], 'segmen' => 'berita', 'grup' => 'Konten Website'],
        'banner'   => ['label' => 'Banner Beranda', 'icon' => 'fa-image', 'route' => ['admin.index', 'banner'], 'segmen' => 'banner', 'grup' => 'Konten Website'],
        'galeri'   => ['label' => 'Galeri Foto', 'icon' => 'fa-images', 'route' => ['admin.index', 'galeri'], 'segmen' => 'galeri', 'grup' => 'Konten Website'],
        'dokumen'  => ['label' => 'Dokumen Unduhan', 'icon' => 'fa-file-download', 'route' => ['admin.index', 'dokumen'], 'segmen' => 'dokumen', 'grup' => 'Konten Website'],
        'reel'     => ['label' => 'Reels Instagram', 'icon' => 'fa-video', 'route' => ['admin.index', 'reel'], 'segmen' => 'reel', 'grup' => 'Konten Website'],
        'page'     => ['label' => 'Halaman Statis', 'icon' => 'fa-file-alt', 'route' => ['admin.index', 'page'], 'segmen' => 'page', 'grup' => 'Konten Website'],
        'unit'     => ['label' => 'Bidang & UPTD', 'icon' => 'fa-sitemap', 'route' => ['admin.index', 'unit'], 'segmen' => 'unit', 'grup' => 'Konten Website'],
        'menu'     => ['label' => 'Menu Navigasi', 'icon' => 'fa-bars', 'route' => ['admin.index', 'menu'], 'segmen' => 'menu', 'grup' => 'Konten Website'],
        'layanan'  => ['label' => 'Layanan Publik', 'icon' => 'fa-concierge-bell', 'route' => ['admin.index', 'layanan'], 'segmen' => 'layanan', 'grup' => 'Konten Website'],
        'supplier' => ['label' => 'Supplier Benih Padi', 'icon' => 'fa-seedling', 'route' => ['admin.index', 'supplier'], 'segmen' => 'supplier', 'grup' => 'Layanan & Data'],
        'harga'    => ['label' => 'Harga Komoditas', 'icon' => 'fa-tags', 'route' => ['admin.index', 'harga'], 'segmen' => 'harga', 'grup' => 'Layanan & Data'],
        'pesan'    => ['label' => 'Pesan & Pengaduan', 'icon' => 'fa-inbox', 'route' => ['admin.index', 'pesan'], 'segmen' => 'pesan', 'grup' => 'Layanan & Data'],
        'kelompok' => ['label' => 'Kelompok Tani (SIMLUHTAN)', 'icon' => 'fa-users-cog', 'route' => ['admin.kelompok.index'], 'aktif' => ['admin.kelompok.*'], 'grup' => 'Layanan & Data'],
        'monitoring' => ['label' => 'Monitoring Bantuan Poktan', 'icon' => 'fa-chart-line', 'route' => ['admin.monitoring'], 'aktif' => ['admin.monitoring*'], 'grup' => 'Layanan & Data'],
        'simbadok' => ['label' => 'SIMBADOK (Bank Dokumen)', 'icon' => 'fa-folder-open', 'route' => ['admin.simbadok.index'], 'aktif' => ['admin.simbadok.*'], 'grup' => 'SIMBADOK'],
        'sipandai_pendaftaran' => ['label' => 'SIPANDAI — Pendaftaran', 'icon' => 'fa-clipboard-list', 'route' => ['admin.sipandai.pendaftaran.index'], 'aktif' => ['admin.sipandai.pendaftaran.*'], 'grup' => 'SIPANDAI'],
        'sipandai_antrian' => ['label' => 'SIPANDAI — Antrian', 'icon' => 'fa-list-ol', 'route' => ['admin.sipandai.antrian.index'], 'aktif' => ['admin.sipandai.antrian.*'], 'grup' => 'SIPANDAI'],
        'sipandai_pemeriksaan' => ['label' => 'SIPANDAI — Pemeriksaan', 'icon' => 'fa-stethoscope', 'route' => ['admin.sipandai.pemeriksaan'], 'aktif' => ['admin.sipandai.pemeriksaan', 'admin.sipandai.cari', 'admin.sipandai.simpan'], 'grup' => 'SIPANDAI'],
        'sipandai_pasien' => ['label' => 'SIPANDAI — Data Pasien', 'icon' => 'fa-paw', 'route' => ['admin.sipandai.pasien.index'], 'aktif' => ['admin.sipandai.pasien.*'], 'grup' => 'SIPANDAI'],
        'sipandai_rekap' => ['label' => 'SIPANDAI — Rekap', 'icon' => 'fa-chart-pie', 'route' => ['admin.sipandai'], 'aktif' => ['admin.sipandai', 'admin.sipandai.data', 'admin.sipandai.bulanan*'], 'grup' => 'SIPANDAI'],
    ],
    'khusus' => [
        'user'     => ['label' => 'Pengguna & Hak Akses', 'icon' => 'fa-user-shield', 'route' => ['admin.users.index'], 'aktif' => ['admin.users.*'], 'grup' => 'Sistem'],
        'kategori' => ['label' => 'Kategori SIMBADOK', 'icon' => 'fa-layer-group', 'route' => ['admin.index', 'kategori'], 'segmen' => 'kategori', 'grup' => 'Sistem'],
        'setting'  => ['label' => 'Pengaturan Situs', 'icon' => 'fa-cog', 'route' => ['admin.setting'], 'aktif' => ['admin.setting*'], 'grup' => 'Sistem'],
        'log'      => ['label' => 'Log Aktivitas', 'icon' => 'fa-history', 'route' => ['admin.index', 'log'], 'segmen' => 'log', 'grup' => 'Sistem'],
    ],
];
