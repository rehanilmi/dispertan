<?php
use App\Models\{ActivityLog, Berita, Dokumen, Galeri, Layanan, Menu, Page, Pesan, Banner, SimbadokKategori, Reel, Supplier, HargaKomoditas, Unit, User};

$kec = (require __DIR__.'/layanan.php');

$same = fn (array $a) => array_combine($a, $a);

return [
    'berita' => [
        'model' => Berita::class, 'title' => 'Berita', 'icon' => 'fa-newspaper',
        'order' => ['tanggal', 'desc'], 'columns' => ['judul', 'tanggal'],
        'fields' => [
            'judul'     => ['label' => 'Judul', 'type' => 'text', 'rules' => 'required|max:255'],
            'kategori'  => ['label' => 'Kategori', 'type' => 'select', 'options' => $same(['Berita','Pengumuman','Peternakan','Perkebunan','TPH','Kesehatan Hewan','Penyuluhan']), 'rules' => 'required|max:40'],
            'ringkasan' => ['label' => 'Ringkasan', 'type' => 'textarea', 'rules' => 'required'],
            'konten'    => ['label' => 'Isi Berita', 'type' => 'richtext', 'rules' => 'nullable'],
            'gambar'    => ['label' => 'Gambar', 'type' => 'image', 'rules' => 'nullable|image|max:4096'],
            'tanggal'   => ['label' => 'Tanggal', 'type' => 'date', 'rules' => 'required|date'],
        ],
    ],
    'layanan' => [
        'model' => Layanan::class, 'title' => 'Layanan Publik', 'icon' => 'fa-concierge-bell',
        'order' => ['urutan', 'asc'], 'columns' => ['nama', 'url', 'urutan'],
        'fields' => [
            'nama'   => ['label' => 'Nama Layanan', 'type' => 'text', 'rules' => 'required|max:100'],
            'url'    => ['label' => 'URL', 'type' => 'text', 'rules' => 'nullable|max:255'],
            'icon'      => ['label' => 'Ikon layanan', 'type' => 'select', 'options' => $kec['ikon'], 'rules' => 'required|in:'.implode(',', array_keys($kec['ikon']))],
            'warna'     => ['label' => 'Warna ikon', 'type' => 'select', 'options' => $kec['warna'], 'rules' => 'required|in:emerald,sky,amber,rose'],
            'deskripsi' => ['label' => 'Deskripsi singkat (tampil di kartu layanan)', 'type' => 'text', 'rules' => 'nullable|max:160'],
            'urutan' => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer|min:0'],
        ],
    ],
    'unit' => [
        'model' => Unit::class, 'title' => 'Bidang & UPTD', 'icon' => 'fa-sitemap',
        'order' => ['urutan', 'asc'], 'columns' => ['tipe', 'nama', 'urutan'],
        'fields' => [
            'tipe'      => ['label' => 'Tipe', 'type' => 'select', 'options' => ['bidang' => 'Bidang', 'uptd' => 'UPTD'], 'rules' => 'required|in:bidang,uptd'],
            'nama'      => ['label' => 'Nama', 'type' => 'text', 'rules' => 'required|max:150'],
            'slug'      => ['label' => 'Slug (kosongkan = otomatis)', 'type' => 'text', 'rules' => 'nullable|max:150'],
            'ringkasan' => ['label' => 'Ringkasan', 'type' => 'textarea', 'rules' => 'nullable'],
            'konten'    => ['label' => 'Isi Halaman', 'type' => 'richtext', 'rules' => 'nullable'],
            'beranda'   => ['label' => 'Tampil di beranda (maks. 6 yang tampil)', 'type' => 'select', 'options' => ['1' => 'Ya', '0' => 'Tidak'], 'rules' => 'required|in:0,1'],
            'gambar'    => ['label' => 'Gambar', 'type' => 'image', 'rules' => 'nullable|image|max:2048'],
            'urutan'    => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer|min:0'],
        ],
    ],
    'reel' => [
        'model' => Reel::class, 'title' => 'Reels Instagram', 'icon' => 'fa-video',
        'order' => ['urutan', 'asc'], 'columns' => ['thumbnail', 'judul', 'url', 'urutan'],
        'fields' => [
            'judul'     => ['label' => 'Judul', 'type' => 'text', 'rules' => 'required|max:150'],
            'url'       => ['label' => 'Link Reels Instagram', 'type' => 'text', 'rules' => 'required|url|max:255'],
            'thumbnail' => ['label' => 'Thumbnail (screenshot cover reels)', 'type' => 'image', 'rules' => 'nullable|image|max:2048'],
            'urutan'    => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer|min:0'],
        ],
    ],

    'page' => [
        'model' => Page::class, 'title' => 'Halaman Statis', 'icon' => 'fa-file-alt',
        'order' => ['judul', 'asc'], 'columns' => ['judul', 'slug'],
        'fields' => [
            'judul'  => ['label' => 'Judul', 'type' => 'text', 'rules' => 'required|max:200'],
            'slug'   => ['label' => 'Slug (kosongkan = otomatis)', 'type' => 'text', 'rules' => 'nullable|max:200'],
            'konten' => ['label' => 'Isi Halaman', 'type' => 'richtext', 'rules' => 'nullable'],
        ],
    ],
    'galeri' => [
        'model' => Galeri::class, 'title' => 'Galeri Foto', 'icon' => 'fa-images',
        'order' => ['id', 'desc'], 'columns' => ['gambar', 'judul'],
        'fields' => [
            'judul'  => ['label' => 'Judul Foto', 'type' => 'text', 'rules' => 'required|max:150'],
            'gambar' => ['label' => 'Foto', 'type' => 'image', 'rules' => 'required|image|max:4096'],
        ],
    ],
    'dokumen' => [
        'model' => Dokumen::class, 'title' => 'Dokumen Unduhan', 'icon' => 'fa-file-download',
        'order' => ['id', 'desc'], 'columns' => ['judul', 'kategori', 'tahun'],
        'fields' => [
            'judul'    => ['label' => 'Judul Dokumen', 'type' => 'text', 'rules' => 'required|max:200'],
            'kategori' => ['label' => 'Kategori', 'type' => 'select', 'options' => $same(['Regulasi','Laporan Kinerja','PPID','Data & Statistik','Lainnya']), 'rules' => 'required|max:40'],
            'tahun'    => ['label' => 'Tahun', 'type' => 'number', 'rules' => 'nullable|integer|min:1990|max:2100'],
            'file'     => ['label' => 'File (PDF/Word/Excel, maks 10 MB)', 'type' => 'file', 'rules' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip|max:10240'],
        ],
    ],
    'pesan' => [
        'model' => Pesan::class, 'title' => 'Pesan & Pengaduan', 'icon' => 'fa-inbox', 'no_create' => true,
        'order' => ['id', 'desc'], 'columns' => ['jenis', 'nama', 'subjek', 'status'],
        'fields' => [
            'jenis'   => ['label' => 'Jenis', 'type' => 'text', 'ro' => true],
            'nama'    => ['label' => 'Nama', 'type' => 'text', 'ro' => true],
            'email'   => ['label' => 'Email', 'type' => 'text', 'ro' => true],
            'telepon' => ['label' => 'Telepon', 'type' => 'text', 'ro' => true],
            'subjek'  => ['label' => 'Subjek', 'type' => 'text', 'ro' => true],
            'isi'     => ['label' => 'Isi Pesan', 'type' => 'textarea', 'ro' => true],
            'status'  => ['label' => 'Status', 'type' => 'select', 'options' => $same(['baru','diproses','selesai']), 'rules' => 'required|in:baru,diproses,selesai'],
        ],
    ],
    'menu' => [
        'model' => Menu::class, 'title' => 'Menu Navigasi', 'icon' => 'fa-bars',
        'order' => ['parent_id', 'asc'], 'columns' => ['label', 'kolom', 'urutan'],
        'fields' => [
            'label'     => ['label' => 'Label', 'type' => 'text', 'rules' => 'required|max:100'],
            'url'       => ['label' => 'URL (/halaman/xxx, https://..., atau @bidang @uptd @layanan)', 'type' => 'text', 'rules' => 'nullable|max:255'],
            'kolom'     => ['label' => 'Judul Kolom Mega Menu (untuk sub-menu)', 'type' => 'text', 'rules' => 'nullable|max:100'],
            'parent_id' => ['label' => 'Induk Menu (kosong = menu utama)', 'type' => 'select', 'source' => [Menu::class, 'tops'], 'blank' => '— menu utama —', 'rules' => 'nullable|exists:menus,id'],
            'urutan'    => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer|min:0'],
        ],
    ],
    'log' => [
        'model' => ActivityLog::class, 'title' => 'Log Aktivitas', 'icon' => 'fa-history',
        'no_create' => true, 'no_edit' => true, 'no_delete' => true,
        'order' => ['id', 'desc'], 'columns' => ['created_at', 'aksi', 'deskripsi'],
        'fields' => [
            'created_at' => ['label' => 'Waktu', 'type' => 'text', 'ro' => true],
            'aksi'       => ['label' => 'Aksi', 'type' => 'text', 'ro' => true],
            'deskripsi'  => ['label' => 'Keterangan', 'type' => 'text', 'ro' => true],
        ],
    ],

    'supplier' => [
        'model' => Supplier::class, 'title' => 'Supplier Benih Padi', 'icon' => 'fa-seedling',
        'order' => ['nama', 'asc'], 'columns' => ['nama', 'kecamatan', 'aktif'],
        'fields' => [
            'nama'          => ['label' => 'Nama Supplier / Penangkar', 'type' => 'text', 'rules' => 'required|max:150'],
            'alamat'        => ['label' => 'Alamat', 'type' => 'text', 'rules' => 'required|max:255'],
            'kecamatan'     => ['label' => 'Kecamatan', 'type' => 'select', 'options' => array_combine($kec['kecamatan'], $kec['kecamatan']), 'rules' => 'required'],
            'lat'           => ['label' => 'Latitude (klik kanan titik di Google Maps untuk menyalin, mis. -0.947100)', 'type' => 'text', 'rules' => 'required|numeric|between:-1.3,-0.5'],
            'lng'           => ['label' => 'Longitude (mis. 100.417200)', 'type' => 'text', 'rules' => 'required|numeric|between:100.1,100.8'],
            'kontak'        => ['label' => 'Kontak (HP/WA)', 'type' => 'text', 'rules' => 'required|max:50'],
            'no_sertifikat' => ['label' => 'No. Sertifikat/Izin (BPSB)', 'type' => 'text', 'rules' => 'nullable|max:100'],
            'berlaku_sampai'=> ['label' => 'Berlaku Sampai', 'type' => 'date', 'rules' => 'nullable|date'],
            'stok_text'     => ['label' => 'Stok (satu baris per varietas: Varietas | Kelas | Stok kg) mis. "Inpari 32 | SS | 500"', 'type' => 'textarea', 'rules' => 'nullable'],
            'aktif'         => ['label' => 'Status', 'type' => 'select', 'options' => ['1' => 'Aktif (tampil di peta)', '0' => 'Nonaktif'], 'rules' => 'required|in:0,1'],
        ],
    ],
    'harga' => [
        'model' => HargaKomoditas::class, 'title' => 'Harga Komoditas', 'icon' => 'fa-tags',
        'order' => ['tanggal', 'desc'], 'columns' => ['komoditas', 'harga', 'tanggal', 'pasar'],
        'fields' => [
            'komoditas' => ['label' => 'Komoditas', 'type' => 'text', 'rules' => 'required|max:100'],
            'satuan'    => ['label' => 'Satuan', 'type' => 'text', 'rules' => 'required|max:20'],
            'harga'     => ['label' => 'Harga (Rp)', 'type' => 'number', 'rules' => 'required|integer|min:0'],
            'tanggal'   => ['label' => 'Tanggal', 'type' => 'date', 'rules' => 'required|date'],
            'pasar'     => ['label' => 'Pasar/Sumber', 'type' => 'text', 'rules' => 'nullable|max:150'],
        ],
    ],

    'banner' => [
        'model' => Banner::class, 'title' => 'Banner Beranda', 'icon' => 'fa-images',
        'order' => ['urutan', 'asc'], 'columns' => ['gambar', 'judul', 'urutan', 'aktif'],
        'fields' => [
            'judul'         => ['label' => 'Judul banner', 'type' => 'text', 'rules' => 'required|max:120'],
            'subjudul'      => ['label' => 'Teks pendukung', 'type' => 'textarea', 'rules' => 'nullable|max:300'],
            'gambar'        => ['label' => 'Gambar (disarankan 1920x900, lanskap)', 'type' => 'image', 'max' => 2000, 'rules' => 'nullable|image|max:6144'],
            'tombol1_label' => ['label' => 'Tombol 1 - teks', 'type' => 'text', 'rules' => 'nullable|max:40'],
            'tombol1_url'   => ['label' => 'Tombol 1 - alamat (mis. /layanan atau https://...)', 'type' => 'text', 'rules' => 'nullable|max:255'],
            'tombol2_label' => ['label' => 'Tombol 2 - teks', 'type' => 'text', 'rules' => 'nullable|max:40'],
            'tombol2_url'   => ['label' => 'Tombol 2 - alamat', 'type' => 'text', 'rules' => 'nullable|max:255'],
            'urutan'        => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer|min:0'],
            'aktif'         => ['label' => 'Status', 'type' => 'select', 'options' => ['1' => 'Tampil', '0' => 'Disembunyikan'], 'rules' => 'required|in:0,1'],
        ],
    ],
    'kategori' => [
        'model' => SimbadokKategori::class, 'title' => 'Kategori SIMBADOK', 'icon' => 'fa-layer-group',
        'order' => ['urutan', 'asc'], 'columns' => ['nama', 'revisi', 'urutan'],
        'fields' => [
            'nama'   => ['label' => 'Nama kategori', 'type' => 'text', 'rules' => 'required|max:40|unique:simbadok_kategoris,nama,{id}'],
            'revisi' => ['label' => 'Jenis revisi yang diizinkan', 'type' => 'select', 'options' => ['' => 'Tanpa revisi (judul diisi manual)', 'penuh' => 'Murni, Pergeseran, Perubahan (seperti DPA)', 'murni_perubahan' => 'Murni dan Perubahan (seperti Renja)'], 'rules' => 'nullable|in:penuh,murni_perubahan'],
            'urutan' => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer|min:0'],
        ],
    ],
];
