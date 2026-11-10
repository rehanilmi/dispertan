<?php
namespace Database\Seeders;

use App\Models\{Menu, Page, Setting, User};
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'admin@dispertan.test')->update(['role' => 'superadmin']);

        foreach ([
            'nama_dinas' => 'Dinas Pertanian Kota Padang', 'alamat' => 'Jl. [isi alamat kantor], Kota Padang, Sumatera Barat',
            'telepon' => '0751 [isi nomor]', 'email' => '[isi email dinas]', 'jam' => '07.30 - 16.00',
            'instagram' => 'https://www.instagram.com/',
        ] as $k => $v) Setting::put($k, $v);

        foreach (['sejarah' => 'Sejarah', 'visi-misi' => 'Visi & Misi', 'struktur-organisasi' => 'Struktur Organisasi',
                  'tugas-fungsi' => 'Tugas & Fungsi', 'profil-ppid' => 'Profil PPID'] as $slug => $judul) {
            Page::updateOrCreate(['slug' => $slug], ['judul' => $judul, 'konten' => "<p>Isi halaman <b>$judul</b> belum diisi. Ubah melalui Admin &rarr; Halaman Statis.</p>"]);
        }

        Menu::query()->delete();
        $m = [
            ['Profil', [['Tentang Dinas','Sejarah','/halaman/sejarah'],['Tentang Dinas','Visi & Misi','/halaman/visi-misi'],['Tentang Dinas','Struktur Organisasi','/halaman/struktur-organisasi'],['Tentang Dinas','Tugas & Fungsi','/halaman/tugas-fungsi'],['Bidang','Bidang','@bidang'],['UPTD','UPTD','@uptd']]],
            ['Layanan Publik', [['Layanan Online','Layanan','@layanan']]],
            ['Informasi Publik', [['Informasi','Berita','/berita'],['Informasi','Galeri','/galeri'],['Informasi','Dokumen & Regulasi','/dokumen'],['Layanan Masyarakat','Pengaduan','/pengaduan'],['Layanan Masyarakat','Kontak','/kontak']]],
            ['Statistik', [['Data','Dispertan dalam Angka','#']]],
            ['PPID', [['PPID','Profil PPID','/halaman/profil-ppid'],['PPID','Permohonan Informasi Publik','/ppid/permohonan'],['PPID','Dokumen PPID','/dokumen?kategori=PPID']]],
            ['Transparansi', [['Transparansi','Laporan Kinerja','/dokumen?kategori=Laporan%20Kinerja']]],
            ['Survey Kepuasan Masyarakat', [['Survey','Isi Survey','#']]],
        ];
        foreach ($m as $i => [$top, $ch]) {
            $p = Menu::create(['label' => $top, 'url' => '#', 'urutan' => $i]);
            foreach ($ch as $j => [$kol, $lab, $url]) Menu::create(['label' => $lab, 'url' => $url, 'kolom' => $kol, 'parent_id' => $p->id, 'urutan' => $j]);
        }
    }
}
