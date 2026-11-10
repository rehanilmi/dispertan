<?php
namespace Database\Seeders;

use App\Models\{HargaKomoditas, Layanan, Supplier};
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        Layanan::query()->delete();
        $l = [
            ['SIMBADOK (Bank Dokumen)', '/layanan/simbadok', 'blue'],
            ['SIMLUHTAN (Kelompok Tani)', '/layanan/kelompok-tani', 'red'],
            ['Benih Padi Bersertifikat', '/layanan/benih-padi', 'red'],
            ['SIPANDAI', '/layanan/sipandai', 'red'],
            ['Info Harga Komoditas', '/layanan/harga', 'blue'],
            ['Lapor Hama & Penyakit', '/layanan/lapor-hama', 'red'],
            ['Konsultasi Penyuluh', '/kontak', 'blue'],
            ['E-Pupuk', '#', 'red'],
            ['Ayo Tani (Mobile)', '#', 'red'],
        ];
        foreach ($l as $i => [$n, $u, $w]) Layanan::create(['nama' => $n, 'url' => $u, 'warna' => $w, 'urutan' => $i]);

        // DATA CONTOH untuk uji tampilan - ganti/hapus lewat Admin > Supplier Benih Padi & Harga Komoditas
        $s = [
            ['CONTOH - UD Tani Makmur', 'Jl. Contoh No. 1', 'Koto Tangah', -0.865000, 100.365000, [['Inpari 32', 'SS', 1200], ['Ciherang', 'SS', 400]]],
            ['CONTOH - Penangkar Benih Pauh', 'Jl. Contoh No. 2', 'Pauh', -0.930000, 100.470000, [['Inpari 42', 'SS', 150]]],
            ['CONTOH - Kios Tani Kuranji', 'Jl. Contoh No. 3', 'Kuranji', -0.900000, 100.390000, [['Mekongga', 'SS', 0]]],
            ['CONTOH - Gapoktan Bungus', 'Jl. Contoh No. 4', 'Bungus Teluk Kabung', -1.033300, 100.383300, [['Inpari 32', 'SS', 2500], ['Cisokan', 'SS', 800]]],
        ];
        foreach ($s as [$n, $a, $k, $lat, $lng, $st]) {
            Supplier::create(['nama' => $n, 'alamat' => $a, 'kecamatan' => $k, 'lat' => $lat, 'lng' => $lng, 'kontak' => '08xx-xxxx-xxxx',
                'stok' => collect($st)->map(fn ($r) => ['varietas' => $r[0], 'kelas' => $r[1], 'kg' => $r[2]])->all()]);
        }
        foreach ([['Beras Medium', 'kg', 13500, 13400], ['Cabai Merah', 'kg', 42000, 39000], ['Bawang Merah', 'kg', 36000, 36000], ['Telur Ayam Ras', 'kg', 29000, 29500]] as [$k, $sat, $h, $kemarin]) {
            HargaKomoditas::create(['komoditas' => $k, 'satuan' => $sat, 'harga' => $kemarin, 'tanggal' => today()->subDay(), 'pasar' => 'CONTOH']);
            HargaKomoditas::create(['komoditas' => $k, 'satuan' => $sat, 'harga' => $h, 'tanggal' => today(), 'pasar' => 'CONTOH']);
        }
    }
}
