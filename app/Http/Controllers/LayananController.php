<?php
namespace App\Http\Controllers;

use App\Models\{BankDokumen, BankDokumenVersi, HargaKomoditas, Kelompok, KelompokAnggota, Layanan, Supplier, Unit};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\{Arr, Str};
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\{Rule, ValidationException};

class LayananController extends Controller
{
    private const HP = '/^(\+62|62|0)8[0-9]{7,12}$/';

    /* ================= SIMBADOK (hanya dokumen akses publik) ================= */
    public function simbadok(Request $r)
    {
        $docs = BankDokumen::with(['latestVersi', 'unit'])->where('akses', 'publik')
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('judul', 'like', "%$s%")
                ->orWhere('deskripsi', 'like', "%$s%")->orWhere('kategori', 'like', "%$s%")
                ->when(ctype_digit((string) $s), fn ($z) => $z->orWhere('tahun', (int) $s))))
            ->when($r->kategori, fn ($x, $v) => $x->where('kategori', $v))
            ->when($r->tahun, fn ($x, $v) => $x->where('tahun', (int) $v))
            ->when($r->unit, fn ($x, $v) => $x->where('unit_id', (int) $v))
            ->get();

        // Kategori -> Tahun (terbaru dulu) -> Murni, Pergeseran 1..n, Perubahan
        $groups = $docs->groupBy('kategori')->map(fn ($g) => $g->groupBy('tahun')->sortKeysDesc()->map(fn ($y) => $y->sortBy('urut')->values()));
        $pub = fn () => BankDokumen::where('akses', 'publik');
        return view('layanan.simbadok', [
            'groups' => $groups, 'total' => $docs->count(),
            'tahuns' => $pub()->distinct()->orderByDesc('tahun')->pluck('tahun'),
            'units' => Unit::whereIn('id', $pub()->whereNotNull('unit_id')->pluck('unit_id'))->get(),
        ]);
    }

    public function simbadokShow(BankDokumen $dok)
    {
        abort_unless($dok->akses === 'publik', 404);
        $dok->load('versis', 'unit');
        $keluarga = BankDokumen::with('latestVersi')->where(['akses' => 'publik', 'kategori' => $dok->kategori, 'tahun' => $dok->tahun])->get()->sortBy('urut')->values();
        return view('layanan.simbadok_show', compact('dok', 'keluarga'));
    }

    public function simbadokUnduh(BankDokumenVersi $versi)
    {
        $d = $versi->dokumen;
        abort_unless($d && $d->akses === 'publik' && Storage::disk('local')->exists($versi->file), 404);
        return Storage::disk('local')->download($versi->file, Str::slug($d->judul).'-v'.$versi->versi.'.'.pathinfo($versi->file, PATHINFO_EXTENSION));
    }

    /* ================= KELOMPOK TANI (SIMLUHTAN) - data diimpor admin, publik hanya melihat ================= */
    public function kelompok(Request $r)
    {
        $items = Kelompok::query()
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('nama', 'like', "%$s%")->orWhere('id_poktan', 'like', "%$s%")
                ->orWhere('desa', 'like', "%$s%")->orWhere('ketua', 'like', "%$s%")))
            ->when($r->kecamatan, fn ($x, $v) => $x->where('kecamatan', $v))
            ->orderBy('nama')->paginate(15)->withQueryString();
        $stat = [
            'total' => Kelompok::count(), 'anggota' => (int) Kelompok::sum('jumlah_anggota'),
            'desa' => Kelompok::whereNotNull('desa')->distinct()->count('desa'),
        ];
        $kecamatans = Kelompok::whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        return view('layanan.kelompok', compact('items', 'stat', 'kecamatans'));
    }

    /* ================= BENIH PADI BERSERTIFIKAT (peta) ================= */
    public function benih()
    {
        $s = Supplier::where('aktif', true)->get()->map(fn ($x) => [
            'id' => $x->id, 'nama' => $x->nama, 'alamat' => $x->alamat.', '.$x->kecamatan, 'lat' => (float) $x->lat, 'lng' => (float) $x->lng,
            'kontak' => $x->kontak, 'sertifikat' => $x->no_sertifikat, 'berlaku' => $x->berlaku_sampai?->format('d-m-Y'),
            'stok' => $x->stok ?? [], 'update' => $x->updated_at->format('d-m-Y'),
        ]);
        return view('layanan.benih', ['suppliers' => $s]);
    }

    /* ================= HARGA KOMODITAS ================= */
    public function harga()
    {
        $rows = HargaKomoditas::where('tanggal', '>=', now()->subDays(14))->orderByDesc('tanggal')->get()->groupBy('komoditas')
            ->map(function ($g) {
                $now = $g->first();
                return ['now' => $now, 'prev' => $g->first(fn ($x) => $x->tanggal->lt($now->tanggal))];
            });
        return view('layanan.harga', compact('rows'));
    }

    /* ================= SEMUA LAYANAN ================= */
    public function semua() { return view('layanan.semua', ['layanan' => Layanan::orderBy('urutan')->get()]); }

    /* ================= MONITORING KELOMPOK TANI (tabel kecamatan + peta) ================= */
    public function monitoringKelompok()
    {
        $kec = "coalesce(k.kecamatan,'TANPA KECAMATAN')";
        $rows = DB::table('kelompoks as k')->leftJoin('kelompok_anggotas as a', 'a.kelompok_id', '=', 'k.id')
            ->selectRaw("$kec as kecamatan, count(distinct k.id) as kelompok, coalesce(sum(a.jk='L'),0) as l, coalesce(sum(a.jk='P'),0) as p")
            ->groupByRaw($kec)->orderBy('kecamatan')->get();

        // Satu anggota yang punya koordinat dipakai sebagai wakil titik kelompok
        $wakil = KelompokAnggota::whereNotNull('lat')->whereBetween('lat', [-1.3, -0.6])->whereBetween('lng', [100.1, 100.8])
            ->selectRaw('kelompok_id, min(id) as aid')->groupBy('kelompok_id')->pluck('aid', 'kelompok_id');
        $pos = KelompokAnggota::whereIn('id', $wakil->values())->get(['id', 'kelompok_id', 'lat', 'lng'])->keyBy('kelompok_id');
        $agg = KelompokAnggota::selectRaw("kelompok_id, count(*) n, coalesce(sum(jk='L'),0) l, coalesce(sum(jk='P'),0) p, coalesce(sum(luas_diusahakan),0) luas")
            ->groupBy('kelompok_id')->get()->keyBy('kelompok_id');
        $kom = KelompokAnggota::whereNotNull('komoditas_1')->selectRaw('kelompok_id, komoditas_1, count(*) c')->groupBy('kelompok_id', 'komoditas_1')
            ->get()->groupBy('kelompok_id')->map(fn ($g) => $g->sortByDesc('c')->first()->komoditas_1);

        $titik = Kelompok::whereIn('id', $pos->keys())->get()->map(fn ($k) => [
            'nama' => $k->nama, 'id' => $k->id_poktan, 'kec' => $k->kecamatan ?: 'TANPA KECAMATAN', 'desa' => $k->desa, 'ketua' => $k->ketua,
            'anggota' => (int) $k->jumlah_anggota, 'terdata' => (int) ($agg[$k->id]->n ?? 0), 'l' => (int) ($agg[$k->id]->l ?? 0), 'p' => (int) ($agg[$k->id]->p ?? 0),
            'luas' => round((float) ($agg[$k->id]->luas ?? 0), 2), 'komoditas' => $kom[$k->id] ?? null,
            // dibulatkan 3 desimal (~100 m) agar lokasi rumah/lahan perorangan tidak terbuka persis di peta publik
            'lat' => round((float) $pos[$k->id]->lat, 3), 'lng' => round((float) $pos[$k->id]->lng, 3),
        ])->values();

        return view('layanan.monitoring_kelompok', [
            'rows' => $rows, 'titik' => $titik,
            'tot' => ['kelompok' => $rows->sum('kelompok'), 'l' => (int) $rows->sum('l'), 'p' => (int) $rows->sum('p')],
        ]);
    }
}
