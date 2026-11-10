<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Support\Sipandai;
use Illuminate\Http\{JsonResponse, Request};

class SipandaiAdminController extends Controller
{
    public function dashboard() { return view('admin.sipandai.dashboard'); }
    public function data(): JsonResponse { return response()->json(Sipandai::rekap()); }
    public function bulanan(Request $r): JsonResponse
    {
        return response()->json(Sipandai::rekapBulanan($this->tahun($r)));
    }

    private function tahun(Request $r): int
    {
        $t = (int) $r->query('tahun', date('Y'));
        return $t >= 2000 && $t <= 2100 ? $t : (int) date('Y');
    }

    /** Unduh tabel rekap bulanan sebagai Excel (susunan kolom sama dengan tabel di layar). */
    public function bulananExport(Request $r)
    {
        abort_unless(class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class), 500, 'Jalankan: composer require phpoffice/phpspreadsheet');
        $d = Sipandai::rekapBulanan($this->tahun($r));
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $ss->getActiveSheet();
        $ws->setTitle('Rekap '.$d['tahun']);
        $ws->fromArray(['No', 'Bulan', 'Pelayanan Umum', 'Vaksinasi Rabies', 'Jenis Pelayanan Kesehatan Hewan', '', '', '', 'Verifikasi SKKH', 'Total Pelayanan'], null, 'A1');
        $ws->fromArray(['', '', '', '', 'Sterilisasi', '', 'Operasi', ''], null, 'A2');
        $ws->fromArray(['', '', '', '', 'OH ♀', 'Kastrasi ♂', 'Penjahitan Luka', 'Operasi Lainnya'], null, 'A3');
        foreach (['A', 'B', 'C', 'D', 'I', 'J'] as $c) $ws->mergeCells("{$c}1:{$c}3");
        $ws->mergeCells('E1:H1'); $ws->mergeCells('E2:F2'); $ws->mergeCells('G2:H2');
        $k = ['umum', 'rabies', 'oh', 'kastrasi', 'jahit', 'operasi', 'skkh', 'total'];
        $r0 = 4;
        foreach ($d['baris'] as $i => $b) $ws->fromArray(array_merge([$b['no'], $b['bulan']], array_map(fn ($x) => $b[$x], $k)), null, 'A'.($r0 + $i));
        $rt = $r0 + 12;
        $ws->fromArray(array_merge(['', 'Total'], array_map(fn ($x) => $d['total'][$x], $k)), null, 'A'.$rt);
        $ws->getStyle("A1:J$rt")->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
        $ws->getStyle('A1:J3')->getFont()->setBold(true); $ws->getStyle("A$rt:J$rt")->getFont()->setBold(true);
        $ws->getStyle("A1:J$rt")->getBorders()->getAllBorders()->setBorderStyle('thin');
        $ws->getColumnDimension('B')->setWidth(14); foreach (['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $c) $ws->getColumnDimension($c)->setWidth(15);
        return response()->streamDownload(fn () => \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx')->save('php://output'),
            'rekap-pelayanan-puskeswan-'.$d['tahun'].'.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function pemeriksaan() { return view('admin.sipandai.pemeriksaan', ['tindakan' => config('sipandai.tindakan')]); }

    /** Port cariPendaftaran(keyword): kosong = antrian hari ini berstatus Menunggu. */
    public function cari(Request $r): JsonResponse
    {
        $kw = trim((string) $r->query('q', ''));
        $q = Pendaftaran::query();
        if ($kw === '') {
            $q->whereDate('tanggal', now(config('sipandai.timezone'))->toDateString())->whereIn('status_pelayanan', ['Terdaftar', 'Menunggu', 'Sedang diperiksa'])->orderBy('id');
        } else {
            $l = '%'.addcslashes($kw, '%_\\').'%';
            $q->where(fn ($w) => $w->where('no_pendaftaran', 'like', $l)->orWhere('nama_pemilik', 'like', $l)->orWhere('nama_hewan', 'like', $l))->orderByDesc('id');
        }
        return response()->json(['success' => true, 'data' => $q->limit(40)->get()->map(fn ($x) => [
            'idPendaftaran' => $x->uid, 'noPendaftaran' => $x->no_pendaftaran, 'urutanHewan' => $x->urutan_hewan, 'namaPemilik' => $x->nama_pemilik,
            'namaHewan' => $x->nama_hewan, 'jenisHewan' => $x->jenis_hewan, 'jenisPelayanan' => $x->jenis_pelayanan, 'keluhanUtama' => $x->keluhan_utama,
            'statusPelayanan' => $x->status_pelayanan, 'diagnosa' => (string) $x->diagnosa, 'jenisTindakan' => (string) $x->jenis_tindakan,
            'catatanPemeriksaan' => (string) $x->catatan_pemeriksaan,
        ])->values()]);
    }

    /** Port simpanPemeriksaan(payload): isi diagnosa + tindakan + catatan, status jadi Selesai. */
    public function simpan(Request $r, string $uid): JsonResponse
    {
        $diagnosa = trim(strip_tags((string) $r->input('diagnosa')));
        $tindakan = trim(strip_tags((string) $r->input('jenisTindakan')));
        if ($diagnosa === '') return response()->json(['success' => false, 'message' => __('Diagnosa wajib diisi.')], 422);
        if ($tindakan === '') return response()->json(['success' => false, 'message' => __('Jenis tindakan wajib diisi.')], 422);

        $p = Pendaftaran::where('uid', $uid)->first();
        if (! $p) return response()->json(['success' => false, 'message' => __('Data pendaftaran tidak ditemukan (mungkin sudah diperbarui petugas lain).')], 404);

        $p->update([
            'diagnosa' => $diagnosa, 'jenis_tindakan' => $tindakan, 'catatan_pemeriksaan' => trim(strip_tags((string) $r->input('catatan'))) ?: null,
            'status_pelayanan' => 'Selesai', 'petugas_update' => auth()->user()->email, 'waktu_update' => now(),
        ]);
        return response()->json(['success' => true]);
    }
}
