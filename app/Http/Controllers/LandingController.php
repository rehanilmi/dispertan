<?php
namespace App\Http\Controllers;

use App\Models\{Banner, Berita, Kelompok, Layanan, Reel, Supplier, Unit, Visit};

class LandingController extends Controller
{
    public function index()
    {
        if (! session('counted')) { Visit::hit(); session(['counted' => true]); }

        $urut = fn () => Unit::orderByRaw("tipe = 'uptd'")->orderBy('urutan');
        $units = $urut()->where('beranda', true)->limit(6)->get();
        if ($units->isEmpty()) $units = $urut()->limit(6)->get();

        return view('landing', [
            'antrian' => \App\Support\Pendaftar::antrianHariIni(true),
            'banners' => Banner::where('aktif', true)->orderBy('urutan')->get(),
            'layanan' => Layanan::orderBy('urutan')->limit(6)->get(),
            'berita'  => Berita::latest('tanggal')->limit(4)->get(),
            'units'   => $units,
            'reels'   => Reel::orderBy('urutan')->limit(4)->get(),
            'stat'    => [
                'kelompok' => Kelompok::count(), 'anggota' => (int) Kelompok::sum('jumlah_anggota'),
                'kecamatan' => Kelompok::whereNotNull('kecamatan')->distinct()->count('kecamatan'),
                'supplier' => Supplier::where('aktif', true)->count(),
            ],
        ]);
    }

    public function unit(string $tipe, string $slug)
    {
        return view('unit', [
            'unit'  => Unit::where(compact('tipe', 'slug'))->firstOrFail(),
            'units' => Unit::orderByRaw("tipe = 'uptd'")->orderBy('urutan')->get(),
        ]);
    }

    public function unitSemua()
    {
        return view('units', [
            'bidang' => Unit::where('tipe', 'bidang')->orderBy('urutan')->get(),
            'uptd'   => Unit::where('tipe', 'uptd')->orderBy('urutan')->get(),
        ]);
    }
}
