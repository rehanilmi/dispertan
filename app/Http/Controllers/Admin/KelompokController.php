<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Bantuan, Kelompok, KelompokAnggota};
use App\Support\{Kecamatan, KelompokImport, Monitoring};
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class KelompokController extends Controller
{
    private function izin(): void
    {
        abort_unless(\App\Support\Akses::bisa(auth()->user(), 'kelompok'), 403, __('Anda tidak memiliki akses ke menu ini.'));
    }

    public function index(Request $r)
    {
        $this->izin();
        $rows = Kelompok::withCount('anggotas')
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('nama', 'like', "%$s%")->orWhere('id_poktan', 'like', "%$s%")
                ->orWhere('desa', 'like', "%$s%")->orWhere('ketua', 'like', "%$s%")))
            ->orderBy('nama')->paginate(25)->withQueryString();
        return view('admin.kelompok.index', ['rows' => $rows, 'adaLib' => class_exists(IOFactory::class)]);
    }

    public function show(Request $r, Kelompok $kelompok)
    {
        $this->izin();
        $anggota = $kelompok->anggotas()
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('nama', 'like', "%$s%")->orWhere('nik', 'like', "%$s%")))
            ->orderBy('nama')->paginate(25)->withQueryString();
        $stat = $kelompok->anggotas()->selectRaw("count(*) as n, coalesce(sum(luas_diusahakan),0) as diusahakan, coalesce(sum(luas_dimiliki),0) as dimiliki, coalesce(sum(jk='L'),0) as l, coalesce(sum(jk='P'),0) as p")->first();
        $bm = [];
        foreach ($kelompok->bantuans as $b) $bm[$b->tahun][$b->sumber] = $b->keterangan;
        $awal = (int) config('layanan.tahun_awal');
        $tahunList = range(max((int) date('Y'), (int) $kelompok->bantuans->max('tahun')), min($awal, (int) ($kelompok->bantuans->min('tahun') ?: $awal)));
        return view('admin.kelompok.show', compact('kelompok', 'anggota', 'stat', 'bm', 'tahunList'));
    }

    public function importPoktan(Request $r)
    {
        $this->izin();
        $r->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480']);
        $hasil = [$this->jalankan(fn () => KelompokImport::poktan($r->file('file')->getRealPath(), $r->file('file')->getClientOriginalName()), $r->file('file')->getClientOriginalName())];
        $this->log('Impor List Kelompok Tani: '.$hasil[0]['ringkas']);
        return back()->with('hasil', $hasil);
    }

    public function importAnggota(Request $r)
    {
        $this->izin();
        $r->validate(['files' => 'required|array', 'files.*' => 'file|mimes:xlsx,xls,csv,txt|max:20480']);
        $hasil = [];
        foreach ($r->file('files') as $f) {
            $hasil[] = $this->jalankan(fn () => KelompokImport::anggota($f->getRealPath(), $r->boolean('sinkron'), $f->getClientOriginalName()), $f->getClientOriginalName());
        }
        $this->log('Impor Anggota Kelompok Tani: '.implode(', ', array_map(fn ($h) => $h['judul'], $hasil)));
        if ($r->wantsJson()) return response()->json(['hasil' => $hasil]);
        return back()->with('hasil', $hasil);
    }

    private function jalankan(callable $fn, string $nama): array
    {
        if (! class_exists(IOFactory::class)) {
            return ['ok' => false, 'judul' => $nama, 'ringkas' => 'Library Excel belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet', 'err' => []];
        }
        @set_time_limit(300);
        try {
            return $fn();
        } catch (\Throwable $e) {
            return ['ok' => false, 'judul' => $nama, 'ringkas' => 'Gagal membaca/menyimpan file: '.$e->getMessage(), 'err' => []];
        }
    }

    public function destroy(Kelompok $kelompok)
    {
        $this->izin();
        KelompokAnggota::where('kelompok_id', $kelompok->id)->delete();
        Bantuan::where('kelompok_id', $kelompok->id)->delete();
        $kelompok->delete();
        $this->log('Hapus kelompok '.$kelompok->nama);
        return redirect()->route('admin.kelompok.index')->with('ok', 'Kelompok beserta anggotanya dihapus.');
    }

    public function hapusAnggota(Kelompok $kelompok)
    {
        $this->izin();
        KelompokAnggota::where('kelompok_id', $kelompok->id)->delete();
        $this->log('Hapus semua anggota '.$kelompok->nama);
        return back()->with('ok', 'Seluruh anggota kelompok dihapus.');
    }

    /** Template Excel dengan format yang sama seperti ekspor SIMLUHTAN. */
    public function template(string $jenis)
    {
        $this->izin();
        abort_unless(in_array($jenis, ['poktan', 'anggota', 'bantuan']), 404);
        if ($jenis === 'bantuan') {
            $y = (int) date('Y');
            $contoh = [['kecamatan' => 'BUNGUS TELUK KABUNG', 'poktan' => 'CONTOH Kelompok Tani', 'idPoktan' => '1234567', 'jumlahAnggota' => 20, 'desa' => 'NAMA DESA',
                'ketua' => 'Nama Ketua', 'alamat' => 'Alamat contoh', 'bantuan' => [$y => ['detail' => [['sumber' => 'APBD Kota', 'keterangan' => 'Contoh: bibit padi 100 kg']]]]]];
            return Monitoring::stream('template-bantuan-kelompok-tani.xlsx', $contoh, range($y - 6, $y));
        }
        $ss = new Spreadsheet(); $ws = $ss->getActiveSheet();
        $str = DataType::TYPE_STRING;
        if ($jenis === 'poktan') {
            $ws->setCellValue('A1', 'List Kelompok Tani');
            $ws->fromArray(['No', 'Nama Poktan', 'ID Poktan', 'Jumlah Anggota', 'Nama Desa', 'Nama Ketua', 'Alamat Sekretariat'], null, 'A3');
            $ws->setCellValue('A4', 1); $ws->setCellValue('B4', 'CONTOH Kelompok Tani'); $ws->setCellValueExplicit('C4', '1234567', $str);
            $ws->setCellValue('D4', 20); $ws->setCellValue('E4', 'NAMA DESA'); $ws->setCellValue('F4', 'Nama Ketua'); $ws->setCellValue('G4', 'Alamat sekretariat');
        } else {
            $ws->setCellValue('A1', 'DAFTAR ANGGOTA KELOMPOK TANI');
            foreach ([['Nama Kelompok Tani', 'CONTOH Kelompok Tani'], ['ID Kelompok Tani', '1234567'], ['Provinsi', 'SUMATERA BARAT'], ['Kabupaten', 'KOTA PADANG'], ['Kecamatan', 'NAMA KECAMATAN']] as $i => [$l, $v]) {
                $ws->setCellValue('B'.(3 + $i), $l); $ws->setCellValueExplicit('C'.(3 + $i), $v, $str);
            }
            $ws->fromArray(['No', 'Nama', 'NIK', 'Tempat/Tgl Lahir', 'Alamat', 'Jenis Kelamin', 'No Hp', 'StatusKenggotaan', 'KomoditasYangDiusahakan(1)', 'Volume(Ha,Ekor,unit)', 'KomoditasYangDiusahakan(2)', 'Volume(Ha,Ekor,unit)', 'KomoditasYangDiusahakan(3)', 'Volume(Ha,Ekor,unit)', 'KomoditasLainnya', 'Total LuasLahan YangDiuasahakan(Ha)', 'Total LuasLahan YangDimiliki (Ha)', 'Kategori Petani', 'Titik Koordinat'], null, 'A10');
            $ws->setCellValue('A11', 1); $ws->setCellValue('B11', 'CONTOH NAMA'); $ws->setCellValueExplicit('C11', '3200000000000000', $str);
            $ws->setCellValue('D11', 'PADANG, 1-1-1980'); $ws->setCellValue('E11', 'Alamat contoh'); $ws->setCellValue('F11', 'Laki-laki');
            $ws->setCellValueExplicit('G11', '081200000000', $str); $ws->setCellValue('H11', 'Anggota'); $ws->setCellValue('I11', 'Padi'); $ws->setCellValue('J11', 1);
            $ws->setCellValue('P11', 1); $ws->setCellValue('Q11', 1); $ws->setCellValue('R11', 'Pemilik dan Penggarap'); $ws->setCellValue('S11', '-1,02279,100,41349,4,4m');
        }
        return response()->streamDownload(function () use ($ss) { IOFactory::createWriter($ss, 'Xlsx')->save('php://output'); },
            'template-'.$jenis.'-simluhtan.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }


    public function importBantuan(Request $r)
    {
        $this->izin();
        $r->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480']);
        $f = $r->file('file');
        $hasil = [$this->jalankan(fn () => KelompokImport::bantuan($f->getRealPath(), $r->boolean('sinkron'), $f->getClientOriginalName()), $f->getClientOriginalName())];
        $this->log('Impor Riwayat Bantuan: '.$hasil[0]['ringkas']);
        return back()->with('hasil', $hasil);
    }

    private function dataForm(Request $r, ?Kelompok $k = null): array
    {
        $d = $r->validate([
            'nama' => 'required|max:200', 'id_poktan' => ['nullable', 'max:20', Rule::unique('kelompoks', 'id_poktan')->ignore($k?->id)],
            'jumlah_anggota' => 'required|integer|min:0', 'kecamatan' => 'nullable|max:100', 'desa' => 'nullable|max:150',
            'ketua' => 'nullable|max:150', 'alamat_sekretariat' => 'nullable|max:500',
        ]);
        $d['kecamatan'] = $d['kecamatan'] ? Kecamatan::norm($d['kecamatan']) : null;
        return $d;
    }

    public function create() { $this->izin(); return view('admin.kelompok.form', ['kelompok' => new Kelompok(['jumlah_anggota' => 0])]); }
    public function edit(Kelompok $kelompok) { $this->izin(); return view('admin.kelompok.form', compact('kelompok')); }

    public function store(Request $r)
    {
        $this->izin();
        $k = Kelompok::create($this->dataForm($r));
        $this->log('Tambah kelompok '.$k->nama);
        return redirect()->route('admin.kelompok.show', $k)->with('ok', 'Kelompok ditambahkan. Silakan isi riwayat bantuan.');
    }

    public function update(Request $r, Kelompok $kelompok)
    {
        $this->izin();
        $kelompok->update($this->dataForm($r, $kelompok));
        $this->log('Ubah kelompok '.$kelompok->nama);
        return redirect()->route('admin.kelompok.show', $kelompok)->with('ok', 'Data kelompok diperbarui.');
    }

    /** Simpan riwayat bantuan per tahun & sumber dana. Kolom dikosongkan = bantuan dihapus. */
    public function updateBantuan(Request $r, Kelompok $kelompok)
    {
        $this->izin();
        $sumber = config('layanan.sumber_dana');
        $simpan = function (int $th, array $row) use ($kelompok, $sumber) {
            if ($th < 1990 || $th > 2100) return;
            foreach ($sumber as $s) {
                $v = trim((string) ($row[$s] ?? ''));
                $key = ['kelompok_id' => $kelompok->id, 'tahun' => $th, 'sumber' => $s];
                if ($v !== '' && $v !== '-') Bantuan::updateOrCreate($key, ['keterangan' => mb_substr($v, 0, 500)]);
                else Bantuan::where($key)->delete();
            }
        };
        foreach ((array) $r->input('bantuan', []) as $th => $row) $simpan((int) $th, (array) $row);
        if ((int) $r->input('tahun_baru')) $simpan((int) $r->input('tahun_baru'), (array) $r->input('baru', []));
        $this->log('Ubah riwayat bantuan '.$kelompok->nama);
        return back()->with('ok', 'Riwayat bantuan disimpan.');
    }

    private function log(string $d): void
    {
        ActivityLog::create(['user_id' => auth()->id(), 'aksi' => 'impor', 'deskripsi' => mb_substr(auth()->user()->name.' - '.$d, 0, 250)]);
    }
}
