<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Pendaftaran};
use App\Support\{Akses, Pendaftar, PendaftaranImport};
use Illuminate\Database\QueryException;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/** SIPANDAI sisi petugas: Pendaftaran (tambah & impor), Data Pasien (rekam medis), Antrian hari ini. */
class SipandaiKlinikController extends Controller
{
    /* ================= PENDAFTARAN ================= */
    public function pendaftaran(Request $r)
    {
        $rows = Pendaftaran::query()
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('no_pendaftaran', 'like', "%$s%")->orWhere('nama_pemilik', 'like', "%$s%")
                ->orWhere('nama_hewan', 'like', "%$s%")->orWhere('nik', 'like', "%$s%")))
            ->when($r->tanggal, fn ($x, $t) => $x->whereDate('tanggal', $t))
            ->orderByDesc('id')->paginate(25)->withQueryString();
        return view('admin.sipandai.pendaftaran', ['rows' => $rows, 'adaLib' => class_exists(IOFactory::class), 'kolom' => PendaftaranImport::KOLOM]);
    }

    public function tambah() { return view('admin.sipandai.tambah', ['cfg' => config('sipandai')]); }

    /** Pendaftaran oleh petugas (pasien datang langsung): langsung berstatus Menunggu. */
    public function simpan(Request $r): JsonResponse
    {
        $d = $r->json()->all() ?: $r->all();
        if ($e = Pendaftar::cek($d)) return response()->json(['success' => false, 'message' => $e], 422);
        try {
            $s = Pendaftar::simpan($d, 'Menunggu');
        } catch (QueryException $e) {
            report($e);
            return response()->json(['success' => false, 'message' => __('Terjadi kesalahan sistem. Silakan coba lagi beberapa saat lagi.')], 500);
        }
        $this->log('tambah', 'Pendaftaran '.$s['noPendaftaran']);
        return response()->json(['success' => true] + $s);
    }

    public function hapus(int $id)
    {
        abort_unless(Akses::bisaHapus(auth()->user()), 403, __('Operator tidak dapat menghapus data.'));
        $p = Pendaftaran::findOrFail($id);
        $this->log('hapus', 'Pendaftaran '.$p->no_pendaftaran.' ('.$p->urutan_hewan.')');
        $p->delete();
        return back()->with('ok', __('Data dihapus.'));
    }

    public function impor(Request $r)
    {
        $r->validate(['files' => 'required|array', 'files.*' => 'file|mimes:xlsx,xls,csv,txt|max:20480']);
        if (! class_exists(IOFactory::class)) {
            return back()->with('hasil', [['ok' => false, 'judul' => '-', 'ringkas' => 'Library Excel belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet', 'err' => []]]);
        }
        @set_time_limit(300);
        $hasil = [];
        foreach ($r->file('files') as $f) {
            try { $hasil[] = PendaftaranImport::jalankan($f->getRealPath(), $f->getClientOriginalName()); }
            catch (\Throwable $e) { $hasil[] = ['ok' => false, 'judul' => $f->getClientOriginalName(), 'ringkas' => 'Gagal membaca/menyimpan file: '.$e->getMessage(), 'err' => []]; }
        }
        $this->log('impor', 'Impor pendaftaran pasien: '.count($hasil).' file');
        return back()->with('hasil', $hasil);
    }

    public function template()
    {
        abort_unless(class_exists(IOFactory::class), 500, 'Jalankan: composer require phpoffice/phpspreadsheet');
        $ss = new Spreadsheet(); $ws = $ss->getActiveSheet(); $str = DataType::TYPE_STRING;
        $ws->fromArray(PendaftaranImport::KOLOM, null, 'A1');
        $contoh = [1, 'REG-PUSKESWAN-20260101-001', '1/1', '2026-01-01', '09:15:00', 'CONTOH Pemilik', '3200000000000000', 'Alamat contoh', '081200000000', 'Moa', 'Kucing', 'Persia', 'Betina',
            '2 Tahun', 'Belang cokelat', 'Pengobatan', 'Contoh keluhan', '', '', '', '', '', '', 'Selesai', 'Contoh diagnosa', 'Pemeriksaan Fisik, Injeksi/Suntik', 'Contoh catatan', 'drh. Contoh', '01/01/2026 09:40:00'];
        foreach ($contoh as $i => $v) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            if (in_array($i, [1, 6, 8], true)) $ws->setCellValueExplicit($col.'2', (string) $v, $str); else $ws->setCellValue($col.'2', $v);
        }
        $ws->getStyle('A1:AC1')->getFont()->setBold(true);
        return response()->streamDownload(fn () => IOFactory::createWriter($ss, 'Xlsx')->save('php://output'), 'template-impor-pasien-sipandai.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /* ================= DATA PASIEN ================= */
    /** Satu pasien = kombinasi NIK pemilik + nama hewan + jenis hewan. Baris yang ditampilkan = kunjungan terbarunya. */
    public function pasien(Request $r)
    {
        $sub = DB::table('pendaftarans')->selectRaw('max(id) as mid, count(*) as kunjungan')->groupByRaw('nik, lower(nama_hewan), lower(jenis_hewan)');
        $rows = Pendaftaran::query()->joinSub($sub, 'g', 'g.mid', '=', 'pendaftarans.id')->select('pendaftarans.*', 'g.kunjungan')
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('pendaftarans.nama_hewan', 'like', "%$s%")->orWhere('pendaftarans.nama_pemilik', 'like', "%$s%")
                ->orWhere('pendaftarans.no_pendaftaran', 'like', "%$s%")->orWhere('pendaftarans.no_hp', 'like', "%$s%")->orWhere('pendaftarans.nik', 'like', "%$s%")))
            ->orderByDesc('pendaftarans.id')->paginate(20)->withQueryString();
        return view('admin.sipandai.pasien', compact('rows'));
    }

    /** Rekam medis: semua kunjungan pasien yang sama, terbaru di atas. */
    public function riwayat(int $id): JsonResponse
    {
        $p = Pendaftaran::findOrFail($id);
        $v = Pendaftaran::where('nik', $p->nik)->whereRaw('lower(nama_hewan) = ?', [mb_strtolower($p->nama_hewan)])->whereRaw('lower(jenis_hewan) = ?', [mb_strtolower($p->jenis_hewan)])
            ->orderByDesc('tanggal')->orderByDesc('waktu')->orderByDesc('id')->get();
        $tgl = fn ($d) => $d ? $d->locale(app()->getLocale())->translatedFormat('d F Y') : null;
        return response()->json(['success' => true,
            'pasien' => ['hewan' => $p->nama_hewan, 'jenis' => __($p->jenis_hewan), 'ras' => $p->ras, 'kelamin' => __($p->jenis_kelamin), 'umur' => $p->umur_tgl_lahir, 'ciri' => $p->ciri_khusus,
                'pemilik' => $p->nama_pemilik, 'hp' => $p->no_hp, 'alamat' => $p->alamat, 'jumlah' => $v->count()],
            'kunjungan' => $v->map(fn ($x) => ['no' => $x->no_pendaftaran, 'urutan' => $x->urutan_hewan, 'tanggal' => $tgl($x->tanggal), 'waktu' => substr((string) $x->waktu, 0, 5),
                'layanan' => __($x->jenis_pelayanan), 'keluhan' => $x->keluhan_utama, 'info' => $x->info_tambahan, 'status' => $x->status_pelayanan, 'label' => __($x->status_pelayanan),
                'diagnosa' => $x->diagnosa, 'tindakan' => $x->jenis_tindakan, 'catatan' => $x->catatan_pemeriksaan, 'petugas' => $x->petugas_update,
                'waktuUpdate' => $x->waktu_update?->format('d-m-Y H:i')])->values()]);
    }

    /* ================= ANTRIAN ================= */
    public function antrian() { return view('admin.sipandai.antrian', ['status' => Pendaftar::STATUS]); }

    public function antrianData(): JsonResponse { return response()->json(['success' => true] + Pendaftar::antrianHariIni(false)); }

    /** Panggil berikutnya: pasien Menunggu paling awal; bila tidak ada, yang Terdaftar paling awal. Status menjadi Sedang diperiksa. */
    public function berikut(): JsonResponse
    {
        $hari = now(config('sipandai.timezone'))->toDateString();
        $q = fn (string $s) => Pendaftaran::whereDate('tanggal', $hari)->where('status_pelayanan', $s)->orderBy('id')->first();
        $n = $q('Menunggu') ?? $q('Terdaftar');
        if (! $n) return response()->json(['success' => false, 'message' => __('Tidak ada antrean yang menunggu.')]);
        $n->update(['status_pelayanan' => 'Sedang diperiksa', 'petugas_update' => auth()->user()->email, 'waktu_update' => now()]);
        return response()->json(['success' => true, 'message' => __('Memanggil :no — :hewan (:pemilik)', ['no' => $n->no_pendaftaran, 'hewan' => $n->nama_hewan, 'pemilik' => $n->nama_pemilik])]);
    }

    public function ubahStatus(Request $r, string $uid): JsonResponse
    {
        $s = (string) $r->input('status');
        if (! in_array($s, Pendaftar::STATUS, true)) return response()->json(['success' => false, 'message' => __('Status tidak valid.')], 422);
        $p = Pendaftaran::where('uid', $uid)->first();
        if (! $p) return response()->json(['success' => false, 'message' => __('Data pendaftaran tidak ditemukan (mungkin sudah diperbarui petugas lain).')], 404);
        $p->update(['status_pelayanan' => $s, 'petugas_update' => auth()->user()->email, 'waktu_update' => now()]);
        return response()->json(['success' => true]);
    }

    private function log(string $aksi, string $d): void
    {
        ActivityLog::create(['user_id' => auth()->id(), 'aksi' => $aksi, 'deskripsi' => mb_substr(auth()->user()->name.' - SIPANDAI '.$d, 0, 250)]);
    }
}
