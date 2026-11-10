<?php
namespace App\Http\Controllers;

use App\Models\{Berita, Dokumen, Galeri, Page, Pesan, Unit};
use Illuminate\Http\Request;

class SiteController extends Controller
{
    private const JENIS = [
        'kontak'    => ['Hubungi Kami', 'Subjek'],
        'pengaduan' => ['Pengaduan Masyarakat', 'Judul pengaduan'],
        'ppid'      => ['Permohonan Informasi Publik (PPID)', 'Informasi yang diminta'],
        'hama'      => ['Lapor Hama & Penyakit', 'Komoditas/ternak & lokasi (kecamatan)'],
    ];

    public function beritaIndex() { return view('berita.index', ['berita' => Berita::latest('tanggal')->paginate(9)]); }

    public function beritaShow(string $slug)
    {
        $b = Berita::where('slug', $slug)->firstOrFail();
        return view('berita.show', ['b' => $b, 'lain' => Berita::where('id', '!=', $b->id)->latest('tanggal')->limit(4)->get()]);
    }

    public function page(string $slug) { return view('page', ['p' => Page::where('slug', $slug)->firstOrFail()]); }

    public function dokumen(Request $r)
    {
        $k = $r->query('kategori');
        return view('dokumen', [
            'docs' => Dokumen::when($k, fn ($q) => $q->where('kategori', $k))->latest()->paginate(15)->withQueryString(),
            'kat' => $k, 'kats' => Dokumen::distinct()->pluck('kategori'),
        ]);
    }

    public function galeri() { return view('galeri', ['fotos' => Galeri::latest()->paginate(12)]); }

    public function form(string $jenis)
    {
        abort_unless(isset(self::JENIS[$jenis]), 404);
        return view('kirim', ['jenis' => $jenis, 'j' => self::JENIS[$jenis]]);
    }

    public function kirim(Request $r, string $jenis)
    {
        abort_unless(isset(self::JENIS[$jenis]), 404);
        $r->validate([
            'nama' => 'required|max:100', 'email' => 'required|email|max:150', 'telepon' => 'nullable|max:30',
            'subjek' => 'required|max:200', 'isi' => 'required|min:10|max:3000', 'website' => 'max:0', // honeypot
        ]);
        Pesan::create($r->only('nama', 'email', 'telepon', 'subjek', 'isi') + ['jenis' => $jenis]);
        return back()->with('ok', 'Terima kasih, pesan Anda sudah kami terima.');
    }

    public function sitemap()
    {
        return response()->view('sitemap', [
            'berita' => Berita::latest('tanggal')->get(['slug', 'updated_at']),
            'units' => Unit::all(), 'pages' => Page::all(['slug']),
        ])->header('Content-Type', 'application/xml');
    }
}
