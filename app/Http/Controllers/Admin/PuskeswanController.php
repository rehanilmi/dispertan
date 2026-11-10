<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use Illuminate\Http\Request;

class PuskeswanController extends Controller
{
    public function index(Request $r)
    {
        $daftar = config('layanan.puskeswan.lokasi');
        $lokasi = $r->query('lokasi', $daftar[0]);
        abort_unless(in_array($lokasi, $daftar), 404);
        $tgl = $r->query('tanggal', today()->toDateString());

        $semua = Antrian::hari($lokasi, $tgl)->orderBy('nomor')->get();
        $stat = [
            'menunggu' => $semua->where('status', 'menunggu')->count(),
            'sedang' => $semua->whereIn('status', ['dipanggil', 'dilayani'])->count(),
            'selesai' => $semua->where('status', 'selesai')->count(),
            'batal' => $semua->whereIn('status', ['batal', 'tidak_hadir'])->count(),
            'total' => $semua->count(), 'kuota' => config('layanan.puskeswan.kuota'),
            'rata' => Antrian::rataLayanan($lokasi),
        ];
        $minggu = collect(range(6, 0))->map(fn ($i) => [
            'tgl' => today()->subDays($i), 'n' => Antrian::where('lokasi', $lokasi)->whereDate('tanggal', today()->subDays($i))->count(),
        ]);
        $perLayanan = $semua->groupBy('layanan')->map->count()->sortDesc();
        return view('admin.puskeswan', compact('daftar', 'lokasi', 'tgl', 'semua', 'stat', 'minggu', 'perLayanan'));
    }

    public function berikut(Request $r)
    {
        $d = $r->validate(['lokasi' => 'required|in:'.implode(',', config('layanan.puskeswan.lokasi')), 'tanggal' => 'required|date']);
        $n = Antrian::hari($d['lokasi'], $d['tanggal'])->where('status', 'menunggu')->orderBy('nomor')->first();
        if (! $n) return back()->with('ok', 'Tidak ada antrian yang menunggu.');
        $n->update(['status' => 'dipanggil', 'dipanggil_at' => now()]);
        return back()->with('ok', 'Memanggil '.$n->no.' - '.$n->nama);
    }

    public function aksi(Antrian $antrian, string $aksi)
    {
        // aksi => [status asal yang diizinkan, status tujuan, kolom waktu]
        $alur = [
            'panggil' => [['menunggu'], 'dipanggil', 'dipanggil_at'],
            'layani' => [['dipanggil'], 'dilayani', 'mulai_at'],
            'selesai' => [['dipanggil', 'dilayani'], 'selesai', 'selesai_at'],
            'tidak_hadir' => [['menunggu', 'dipanggil'], 'tidak_hadir', null],
            'batal' => [['menunggu', 'dipanggil'], 'batal', null],
        ];
        abort_unless(isset($alur[$aksi]), 404);
        [$dari, $ke, $kolom] = $alur[$aksi];
        abort_unless(in_array($antrian->status, $dari), 422, 'Status antrian tidak sesuai.');
        $antrian->status = $ke;
        if ($kolom) $antrian->$kolom = now();
        if ($aksi === 'selesai' && ! $antrian->mulai_at) $antrian->mulai_at = $antrian->dipanggil_at ?? now();
        $antrian->save();
        return back();
    }
}
