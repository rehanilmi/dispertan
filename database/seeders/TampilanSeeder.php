<?php
namespace Database\Seeders;

use App\Models\{Banner, Layanan, Unit};
use Illuminate\Database\Seeder;

/** Jalankan SEKALI setelah migrasi tampilan. Mengatur ulang daftar layanan (ikon, deskripsi, urutan), unit beranda, dan banner awal. */
class TampilanSeeder extends Seeder
{
    public function run(): void
    {
        Layanan::query()->delete();
        $l = [
            ['SIMBADOK (Bank Dokumen)', '/layanan/simbadok', 'folder-open', 'sky', 'Dokumen publik: DPA, Renja, regulasi, dan laporan.'],
            ['Data Kelompok Tani', '/layanan/kelompok-tani', 'users', 'emerald', 'Daftar kelompok tani sesuai data SIMLUHTAN.'],
            ['Monitoring Kelompok Tani', '/layanan/monitoring-kelompok-tani', 'map-pin', 'amber', 'Jumlah kelompok dan anggota per kecamatan, plus peta sebaran.'],
            ['Benih Padi Bersertifikat', '/layanan/benih-padi', 'sprout', 'emerald', 'Peta supplier, jenis benih, stok, dan kontak.'],
            ['SIPANDAI', '/layanan/sipandai', 'paw-print', 'rose', 'Pendaftaran pasien hewan Puskeswan secara online.'],
            ['Info Harga Komoditas', '/layanan/harga', 'trending-up', 'sky', 'Harga pangan dan hasil pertanian terbaru.'],
            ['Lapor Hama & Penyakit', '/layanan/lapor-hama', 'bug', 'amber', 'Laporkan serangan hama atau penyakit ternak.'],
            ['Konsultasi dengan Penyuluh', '/kontak', 'message-circle', 'sky', 'Tanya langsung seputar budidaya dan peternakan.'],
            ['E-Pupuk', '#', 'shopping-basket', 'amber', 'Informasi pupuk bersubsidi.'],
            ['Ayo Tani (Mobile)', '#', 'smartphone', 'emerald', 'Layanan pertanian dalam aplikasi HP.'],
        ];
        foreach ($l as $i => [$n, $u, $ic, $w, $d]) Layanan::create(['nama' => $n, 'url' => $u, 'icon' => $ic, 'warna' => $w, 'deskripsi' => $d, 'urutan' => $i]);

        // 3 bidang + 3 UPTD pertama tampil di beranda (ubah lewat Admin > Bidang & UPTD)
        Unit::query()->update(['beranda' => false]);
        foreach (['bidang', 'uptd'] as $t) Unit::whereIn('id', Unit::where('tipe', $t)->orderBy('urutan')->limit(3)->pluck('id'))->update(['beranda' => true]);

        if (Banner::count() === 0) {
            foreach ([
                ['Satujuan untuk Kejayaan Pertanian Kota Padang', 'Pertanian kota yang tangguh, modern, dan berkelanjutan demi ketahanan pangan dan kesejahteraan petani.', 'Profil dinas', '/halaman/visi-misi', 'Lihat layanan', '/layanan'],
                ['Daftarkan hewan Anda ke Puskeswan secara online', 'Isi data sekali, daftarkan lebih dari satu hewan, dan simpan struk pendaftaran Anda.', 'Daftar lewat SIPANDAI', '/layanan/sipandai', null, null],
                ['Cari benih padi bersertifikat terdekat', 'Lihat supplier, jenis benih, stok, dan kontaknya langsung di peta Kota Padang.', 'Buka peta benih', '/layanan/benih-padi', null, null],
            ] as $i => [$j, $s, $l1, $u1, $l2, $u2]) {
                Banner::create(['judul' => $j, 'subjudul' => $s, 'tombol1_label' => $l1, 'tombol1_url' => $u1, 'tombol2_label' => $l2, 'tombol2_url' => $u2, 'urutan' => $i]);
            }
        }
    }
}
