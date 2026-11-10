<?php
return [
    'kecamatan' => ['Bungus Teluk Kabung','Koto Tangah','Kuranji','Lubuk Begalung','Lubuk Kilangan','Nanggalo','Padang Barat','Padang Selatan','Padang Timur','Padang Utara','Pauh'],
    // Struktur kelembagaan petani mengikuti data umum SIMLUH Kementan; cocokkan dengan template resmi bila ada.
    'jenis_kelompok' => ['Kelompok Tani (Poktan)','Gabungan Kelompok Tani (Gapoktan)','Kelompok Wanita Tani (KWT)','Kelompok Ternak','Kelompok Perkebunan','P3A (Petani Pemakai Air)','Koperasi Tani','Lainnya'],
    'sumber_dana' => ['APBD Kota', 'APBD Provinsi', 'APBN'],
    'tahun_awal'  => 2020,   // tahun akhir otomatis mengikuti tahun berjalan / data terbaru
    'warna' => ['emerald' => 'Hijau padi', 'sky' => 'Biru', 'amber' => 'Kuning padi', 'rose' => 'Merah'],
    // Ikon Lucide (https://lucide.dev/icons) yang bisa dipilih untuk layanan
    'ikon' => [
        'folder-open' => 'Folder / dokumen', 'file-text' => 'Berkas', 'notebook-text' => 'Buku catatan', 'book-open' => 'Buku', 'clipboard-list' => 'Daftar periksa',
        'users' => 'Kelompok / orang', 'user-round-check' => 'Petugas / penyuluh', 'map-pin' => 'Lokasi', 'map' => 'Peta', 'sprout' => 'Tunas / tanaman',
        'flower-2' => 'Bunga', 'trees' => 'Pohon', 'wheat' => 'Padi / gandum', 'tractor' => 'Traktor', 'warehouse' => 'Gudang', 'leaf' => 'Daun',
        'sun' => 'Matahari', 'cloud-sun' => 'Cuaca', 'droplets' => 'Air / irigasi', 'shopping-basket' => 'Keranjang panen', 'shopping-bag' => 'Kantong',
        'shopping-cart' => 'Keranjang belanja', 'tags' => 'Harga', 'wallet' => 'Dompet / dana', 'trending-up' => 'Grafik naik', 'activity' => 'Aktivitas / statistik',
        'bug' => 'Hama', 'pill' => 'Obat', 'syringe' => 'Vaksinasi', 'heart-pulse' => 'Kesehatan', 'stethoscope' => 'Pemeriksaan', 'paw-print' => 'Hewan / Puskeswan',
        'ticket' => 'Antrian / tiket', 'calendar-check' => 'Jadwal', 'megaphone' => 'Pengumuman', 'message-circle' => 'Konsultasi', 'phone' => 'Telepon',
        'smartphone' => 'Aplikasi HP', 'truck' => 'Distribusi', 'wrench' => 'Alat / mesin', 'shield-check' => 'Perlindungan', 'search' => 'Pencarian',
        'building-2' => 'Gedung', 'globe' => 'Website',
    ],

    'sektor' => ['Tanaman Pangan','Hortikultura','Perkebunan','Peternakan'],
    'kelas'  => ['Pemula','Lanjut','Madya','Utama'],

    // 'varian' kosong = judul diisi manual. Isi = jenis revisi yang diizinkan untuk kategori itu.
    'simbadok' => ['kategori' => [
        'DPA'                => ['varian' => ['murni', 'pergeseran', 'perubahan']],
        'RKA'                => ['varian' => ['murni', 'pergeseran', 'perubahan']],
        'Renja'              => ['varian' => ['murni', 'perubahan']],
        'Renstra'            => [],
        'Perjanjian Kinerja' => [],
        'LKjIP'              => [],
        'Laporan Keuangan'   => [],
        'Regulasi'           => [],
        'SOP'                => [],
        'Lainnya'            => [],
    ]],
];
