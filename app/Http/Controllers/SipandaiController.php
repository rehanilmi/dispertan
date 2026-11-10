<?php
namespace App\Http\Controllers;

use App\Support\Pendaftar;
use Illuminate\Database\QueryException;
use Illuminate\Http\{JsonResponse, Request};

class SipandaiController extends Controller
{
    public function form() { return view('sipandai.form', ['cfg' => config('sipandai')]); }

    public function store(Request $r): JsonResponse
    {
        $d = $r->json()->all() ?: $r->all();
        if (! empty($d['website'])) return response()->json(['success' => false, 'message' => __('Permintaan ditolak.')], 422);   // honeypot
        if ($e = Pendaftar::cek($d)) return response()->json(['success' => false, 'message' => $e], 422);
        try {
            return response()->json(['success' => true] + Pendaftar::simpan($d, 'Terdaftar'));
        } catch (QueryException $e) {
            report($e);
            return response()->json(['success' => false, 'message' => __('Terjadi kesalahan sistem. Silakan coba lagi beberapa saat lagi.')], 500);
        }
    }

    /** Data antrean hari ini untuk tabel di beranda (tanpa aksi, nama pemilik disamarkan). */
    public function antrian(): JsonResponse { return response()->json(['success' => true] + Pendaftar::antrianHariIni(true)); }
}
