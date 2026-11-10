<?php
namespace Database\Seeders;

use App\Models\{Berita, Layanan, Reel, Unit, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LandingSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@dispertan.test'],
            ['name' => 'Administrator', 'password' => Hash::make('admin12345')]);

        $l = ['SIPETANI','SI PENYULUH','E-Pupuk','Info Harga Pangan','SIMLUHTAN','Layanan Benih',
              'Klinik Tani','Lapor Hama & Penyakit','Antrian Layanan','Ayo Tani (Mobile)'];
        foreach ($l as $i => $n) Layanan::create(['nama'=>$n,'urutan'=>$i,'warna'=>$i % 4 == 3 ? 'blue' : 'red']);

        $b = [
            ['Kota Padang Panen Raya Padi Sawah di Kecamatan Koto Tangah','Dinas Pertanian Kota Padang bersama kelompok tani melaksanakan panen raya padi sawah dengan hasil di atas rata-rata.','2026-10-06'],
            ['Dispertan Salurkan Bantuan Bibit Hortikultura untuk Petani','Ratusan paket bibit cabai, bawang, dan sayuran dibagikan kepada kelompok tani di sembilan kecamatan.','2026-10-06'],
            ['Pelatihan Penyuluh Pertanian Tingkatkan Produktivitas Lahan','Penyuluh pertanian lapangan mengikuti pelatihan budidaya ramah lingkungan dan pemanfaatan teknologi.','2026-10-05'],
            ['Vaksinasi Ternak Sapi dan Kambing Capai Target Triwulan','Tim kesehatan hewan memastikan ternak warga terlindungi dari penyakit menjelang Idul Adha.','2026-10-05'],
        ];
        foreach ($b as [$j,$r,$t]) Berita::create(['judul'=>$j,'ringkasan'=>$r,'konten'=>'<p>'.$r.'</p>','tanggal'=>$t]);

        $bidang = ['Sekretariat','Peternakan','Perkebunan','TPH','Prasarana Pertanian','Keswan dan Kesmavet','Penyuluhan'];
        $uptd = ['Puskeswan','Pembibitan','Rumah Potong Hewan','Perlintan','Balai Pembibitan Agrowisata'];
        foreach ([['bidang',$bidang],['uptd',$uptd]] as [$tipe,$list]) {
            foreach ($list as $i => $n) Unit::create([
                'tipe'=>$tipe,'nama'=>$n,'urutan'=>$i,
                'ringkasan'=>"Halaman $n Dinas Pertanian Kota Padang.",
                'konten'=>"<p>Isi halaman <b>$n</b> belum diisi. Ubah melalui menu Admin &rarr; Bidang &amp; UPTD.</p>",
            ]);
        }

        foreach (range(1,4) as $i) Reel::create(['judul'=>"Reels Dispertan Padang $i",'url'=>'https://www.instagram.com/','urutan'=>$i]);

        $this->call(SiteSeeder::class);
    }
}
