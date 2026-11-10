# Website Dinas Pertanian Kota Padang (Laravel 13 / PHP 8.3 / MySQL)

## Instalasi
    composer create-project laravel/laravel dispertan      # jika belum ada
    # salin isi folder ini (timpa file yang sama), atur DB MySQL di .env
    php artisan migrate
    php artisan db:seed --class=LandingSeeder              # juga memanggil SiteSeeder
    php artisan storage:link
    php artisan serve
Admin: /admin  -> admin@dispertan.test / admin12345  (GANTI SEKARANG: Admin > Pengguna Admin)
Lalu isi: Admin > Pengaturan Situs (alamat, telepon, peta, sosmed, logo).

## Peran
- Admin: semua menu (termasuk Pengguna, Menu, Halaman, Unit, Pengaturan, Log).
- Operator: Berita, Galeri, Dokumen, Reels, Pesan.

## Checklist produksi
1. .env: APP_ENV=production, APP_DEBUG=false, APP_URL=https://domain-dinas, SESSION_SECURE_COOKIE=true
2. Pasang HTTPS (Let's Encrypt / sertifikat instansi) dan arahkan root web server ke folder public/.
3. php artisan config:cache && php artisan route:cache && php artisan view:cache
4. User DB khusus aplikasi (jangan root); izin tulis hanya storage/ dan bootstrap/cache/.
5. Backup otomatis: composer require spatie/laravel-backup, jadwalkan `backup:run` harian
   (simpan di luar server), UJI pemulihan secara berkala.
6. Ganti GANTI-DOMAIN di public/robots.txt.
7. Tailwind produksi (hilangkan CDN): Laravel 13 sudah membawa Vite+Tailwind.
   npm i && tambahkan bootstrap-icons & @fontsource/inter via npm, impor di resources/css/app.css,
   ganti <script src="cdn.tailwindcss.com"> dan <link> CDN di layouts/app.blade.php dengan @vite([...]),
   lalu npm run build. Class Tailwind di Blade tidak perlu diubah.
8. Uji keamanan/pentest dan ikuti ketentuan Kominfo/persandian daerah untuk domain .go.id.

## Fitur Layanan (update 3)
    php artisan migrate
    php artisan db:seed --class=LayananSeeder     # 10 layanan + data CONTOH (hapus lewat admin)
- SIMBADOK: /layanan/simbadok | admin: /admin/simbadok. File disimpan PRIVAT (storage/app/private), diunduh lewat controller.
  Hak akses: publik (tampil di website) / internal (semua staf) / bidang (staf bidang itu saja). Hubungkan staf ke bidang di Admin > Pengguna Admin.
  Naikkan upload_max_filesize & post_max_size di php.ini (>= 25M) agar file 20 MB dapat diunggah.
- Kelompok Tani: /layanan/kelompok-tani | verifikasi di Admin > Kelompok Tani (kode KT-tahun-nomor diberikan otomatis).
- Benih padi: /layanan/benih-padi (peta Leaflet/OpenStreetMap) | Admin > Supplier Benih Padi.
- Info harga: /layanan/harga | Admin > Harga Komoditas.  Lapor hama: /layanan/lapor-hama (masuk ke Admin > Pesan).

## Kelompok Tani (impor Excel SIMLUHTAN)
    composer require phpoffice/phpspreadsheet      # perlu ekstensi PHP: zip, xml, gd
    php artisan migrate                            # migrasi 2026_10_12 MENGGANTI tabel kelompoks lama (data pendaftaran lama hilang)
Admin > Kelompok Tani (SIMLUHTAN): (1) impor List Kelompok Tani, (2) impor Daftar Anggota (boleh banyak file sekaligus).
Tombol "Unduh template" memberi file contoh berformat sama. Hanya role Admin yang dapat mengakses.

## Monitoring Bantuan Kelompok Tani (port dari Google Apps Script)
    php artisan migrate        # tabel kelompok_bantuans; ID Poktan boleh kosong
- Dashboard: Admin > Monitoring Bantuan Poktan (semua staf). Impor/ubah data: Admin > Kelompok Tani (khusus Admin).
- Impor Excel riwayat bantuan = "Langkah 3" di halaman Kelompok Tani. Ekspor Excel dari dashboard berformat sama (bisa diimpor ulang).
- Tahun otomatis bertambah mengikuti tahun berjalan; tahun awal di config/layanan.php ('tahun_awal').

## Update tampilan (banner, ikon layanan, halaman semua layanan/bidang, monitoring kelompok)
    php artisan migrate
    php artisan db:seed --class=TampilanSeeder    # JALANKAN SEKALI: set ikon/deskripsi layanan, 6 unit beranda, 3 banner awal
- Banner: Admin > Banner Beranda (gambar, teks, 2 tombol, urutan, tampil/sembunyi). Banner berganti otomatis tiap 7 detik, berhenti saat kursor di atasnya.
- Layanan: pilih ikon & warna per layanan di Admin > Layanan Publik. Beranda menampilkan 6 layanan pertama (urutan terkecil); semua ada di /layanan.
- Bidang/UPTD: kolom "Tampil di beranda" menentukan 6 yang muncul; semua ada di /bidang-uptd.
- Monitoring Kelompok Tani: /layanan/monitoring-kelompok-tani (tabel per kecamatan + peta; titik = satu anggota ber-koordinat, dibulatkan ±100 m).
- Impor anggota: Admin > Kelompok Tani > Langkah 2 mengunggah puluhan file satu per satu otomatis.

## SIPANDAI (port dari Google Apps Script) - menggantikan layanan Puskeswan lama
    php artisan migrate        # drop tabel antrians lama, buat tabel pendaftarans, ubah layanan Puskeswan -> SIPANDAI, ikon -> Lucide
- Publik: /layanan/sipandai (daftar multi-hewan, field SKKH dinamis, nomor REG-PUSKESWAN-YYYYMMDD-XXX, struk cetak).
- Admin/staf: Admin > SIPANDAI - Rekap (grafik Chart.js, auto-refresh 30 dtk) dan SIPANDAI - Pemeriksaan (diagnosa, tindakan, catatan).
- Daftar hewan, pelayanan, tindakan: config/sipandai.php.
- Tampilan: Tailwind + token gaya shadcn/ui, ikon Lucide, animasi Motion (versi vanilla Framer Motion), mode terang/gelap (tombol di navbar).

## Banner & gambar
- Foto latar banner bawaan: letakkan file **public/images/background.png** (dipakai otomatis untuk banner yang belum punya gambar sendiri).
  Banner tertentu bisa diganti lewat Admin > Banner Beranda (unggah gambar).
- Belum ada gambar berita/reels? Otomatis tampil ilustrasi lokal di public/images/ph/ (tanpa internet).
- Ikon (Lucide), animasi (Motion), dan Bootstrap Icons kini disimpan lokal di public/vendor/ (tidak lagi bergantung CDN).

## Dua bahasa (ID / EN)
- Tombol ID | EN ada di navbar website dan di bilah atas admin; pilihan tersimpan di sesi.
- Kamus terjemahan: lang/en.json (Indonesia -> Inggris, cocok persis per teks). Tambahkan baris baru di sana untuk teks baru.
  Teks di dalam <script> hanya diterjemahkan bila dibungkus T('...'). Isi yang Anda tulis sendiri (judul berita, isi halaman, dsb.) tidak diterjemahkan otomatis.
- Pesan validasi form: lang/id/validation.php dan lang/en/validation.php.

## Rekap pelayanan per bulan (SIPANDAI)
- Admin > SIPANDAI - Rekap: tabel per bulan, pilih tahun, unduh Excel. Aturan kolom ada di keterangan bawah tabel.
- Kolom Penjahitan Luka dan Operasi Lainnya mengambil dari tindakan yang dicentang dokter (pilihan "Penjahitan Luka" ditambahkan ke daftar tindakan).

## Gambar unggahan tidak tampil / tidak berganti?
1. php artisan storage:link (admin menampilkan peringatan bila belum dibuat)
2. Batas unggah PHP: public/.user.ini sudah menaikkan ke 20 MB (hosting tertentu perlu diatur di php.ini / panel hosting)
3. Admin > Banner Beranda menampilkan thumbnail; kolom "bawaan" = belum ada gambar sendiri (memakai public/images/background.png)
4. Urutan tampil banner mengikuti kolom Urutan (angka terkecil tampil pertama); banner lama masih tampil selama statusnya "Tampil"

## Peran & hak akses (update)
    php artisan migrate     # tambah kolom users.menus, ubah peran lama "admin" -> "superadmin", pindahkan kategori SIMBADOK ke database
- Peran: **superadmin** (semua akses), **admin** bidang/UPTD (tambah/ubah/hapus pada menu yang diberikan), **operator** (tambah/ubah, tanpa hapus).
- Superadmin membuat akun di Admin > Pengguna & Hak Akses: pilih peran, bidang/UPTD, dan centang menu. Hanya menu yang dicentang tampil di sidebar & dashboard,
  dan server menolak (403) akses langsung lewat URL ke menu yang tidak diberikan.
- SIMBADOK: admin/operator hanya melihat & mengelola dokumen bidang/UPTD-nya; dokumen publik/internal bidang lain hanya bisa dilihat. Hanya superadmin melihat semuanya.
- Kategori SIMBADOK: Admin > Kategori SIMBADOK (superadmin). Mengganti nama kategori ikut memperbarui dokumen; kategori yang masih dipakai dokumen tidak bisa dihapus.
- Akun lama berperan operator mendapat menu bawaan lamanya (berita, galeri, dokumen, reels, pesan) dan perlu dihubungkan ke bidang/UPTD bila memakai SIMBADOK.

## Profil, SIPANDAI Pendaftaran / Data Pasien / Antrian (update)
    php artisan migrate     # kolom biodata user (nip, jabatan, no_hp, alamat, foto) + status antrian diperlebar
- Profil Saya (Admin > Profil Saya, semua akun): nama lengkap, NIP, jabatan, no. HP, alamat, foto, dan ganti password.
- Menu SIPANDAI baru (berikan lewat Pengguna & Hak Akses): Pendaftaran, Antrian, Data Pasien (selain Pemeriksaan & Rekap yang sudah ada).
- Status antrian: Terdaftar (daftar online) -> Menunggu (pasien hadir / didaftarkan petugas) -> Sedang diperiksa -> Selesai (diisi dokter di Pemeriksaan).
  "Panggil antrean berikutnya" mengambil pasien Menunggu paling awal; bila tidak ada, yang Terdaftar paling awal.
- Impor pasien lama: Admin > SIPANDAI — Pendaftaran > Impor (Excel, format kolom sama dengan HEADER_PENDAFTARAN; tombol Unduh template).
- Beranda menampilkan tabel Antrian Puskeswan hari ini (tanpa kolom aksi; nama pemilik disamarkan), diperbarui otomatis tiap 30 detik.
