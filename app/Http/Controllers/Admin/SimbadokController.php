<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{BankDokumen, BankDokumenVersi, Unit};
use Illuminate\Http\{Request, UploadedFile};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\{Rule, ValidationException};

class SimbadokController extends Controller
{
    private function izinBuat(): void
    {
        $u = auth()->user();
        abort_unless(\App\Support\Akses::isSuper($u) || $u->unit_id, 403, __('Akun Anda belum terhubung ke bidang/UPTD. Hubungi superadmin.'));
    }

    private function izinUbah(BankDokumen $d): void
    {
        abort_unless($d->bisaDiubah(auth()->user()), 403, 'Dokumen ini milik bidang lain.');
    }

    public function index(Request $r)
    {
        $rows = BankDokumen::with(['latestVersi', 'unit'])->visibleTo(auth()->user())
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('judul', 'like', "%$s%")->orWhere('deskripsi', 'like', "%$s%")))
            ->when($r->kategori, fn ($x, $v) => $x->where('kategori', $v))
            ->when($r->tahun, fn ($x, $v) => $x->where('tahun', (int) $v))
            ->orderByDesc('tahun')->orderBy('kategori')->orderBy('varian')->orderBy('nomor')
            ->paginate(20)->withQueryString();
        return view('admin.simbadok.index', compact('rows'));
    }

    public function create()
    {
        $this->izinBuat();
        return view('admin.simbadok.form', ['dok' => new BankDokumen(['tahun' => date('Y'), 'akses' => 'publik', 'varian' => 'murni', 'nomor' => 0]), 'units' => Unit::opsi()]);
    }

    public function store(Request $r)
    {
        $this->izinBuat();
        $d = $this->validasi($r, null);
        $file = $d['file']; $catatan = $d['catatan'] ?? null;
        unset($d['file'], $d['catatan']);
        $dok = BankDokumen::create($d + ['user_id' => auth()->id()]);
        $this->simpanVersi($dok, $file, $catatan ?: 'Versi awal');
        return redirect()->route('admin.simbadok.edit', $dok)->with('ok', 'Dokumen dibuat sebagai versi 1.');
    }

    public function edit(BankDokumen $dok)
    {
        abort_unless(BankDokumen::visibleTo(auth()->user())->whereKey($dok->id)->exists(), 403);
        $dok->load('versis');
        return view('admin.simbadok.form', ['dok' => $dok, 'units' => Unit::opsi()]);
    }

    public function update(Request $r, BankDokumen $dok)
    {
        $this->izinUbah($dok);
        $dok->update($this->validasi($r, $dok));
        return back()->with('ok', 'Informasi dokumen diperbarui.');
    }

    /** Dokumen diperbarui -> unggah file baru sebagai versi berikutnya (riwayat lama tetap tersimpan). */
    public function versi(Request $r, BankDokumen $dok)
    {
        $this->izinUbah($dok);
        $d = $r->validate(['file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip|max:20480', 'catatan' => 'required|max:255']);
        $this->simpanVersi($dok, $d['file'], $d['catatan']);
        return back()->with('ok', 'Versi baru tersimpan.');
    }

    public function destroy(BankDokumen $dok)
    {
        $this->izinUbah($dok);
        abort_unless(\App\Support\Akses::bisaHapus(auth()->user()), 403, __('Operator tidak dapat menghapus data.'));
        foreach ($dok->versis as $v) Storage::disk('local')->delete($v->file);
        BankDokumenVersi::where('bank_dokumen_id', $dok->id)->delete();
        $dok->delete();
        return redirect()->route('admin.simbadok.index')->with('ok', 'Dokumen beserta seluruh versinya dihapus.');
    }

    public function unduh(BankDokumenVersi $versi)
    {
        $d = $versi->dokumen;
        abort_unless($d && BankDokumen::visibleTo(auth()->user())->whereKey($d->id)->exists(), 403);
        abort_unless(Storage::disk('local')->exists($versi->file), 404);
        return Storage::disk('local')->download($versi->file, Str::slug($d->judul).'-v'.$versi->versi.'.'.pathinfo($versi->file, PATHINFO_EXTENSION));
    }

    private function simpanVersi(BankDokumen $dok, UploadedFile $f, ?string $catatan): void
    {
        $v = (int) BankDokumenVersi::where('bank_dokumen_id', $dok->id)->max('versi') + 1;
        BankDokumenVersi::create([
            'bank_dokumen_id' => $dok->id, 'versi' => $v, 'file' => $f->store('simbadok', 'local'),
            'nama_asli' => $f->getClientOriginalName(), 'ukuran' => $f->getSize(), 'catatan' => $catatan, 'user_id' => auth()->id(),
        ]);
        $dok->touch();
    }

    private function validasi(Request $r, ?BankDokumen $dok): array
    {
        $u = auth()->user();
        $kats = \App\Models\SimbadokKategori::peta();
        $rules = [
            'kategori' => ['required', Rule::in(array_keys($kats))], 'tahun' => 'required|integer|min:2000|max:2100',
            'varian' => 'nullable|in:murni,pergeseran,perubahan', 'nomor' => 'nullable|integer|min:1|max:9',
            'judul' => [Rule::requiredIf(fn () => empty($kats[$r->kategori]['varian'] ?? [])), 'nullable', 'max:200'],
            'deskripsi' => 'nullable|max:2000', 'akses' => 'required|in:publik,internal,bidang', 'unit_id' => 'nullable|exists:units,id',
        ];
        if (! $dok) $rules += ['file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip|max:20480', 'catatan' => 'nullable|max:255'];
        $d = $r->validate($rules);

        // Logika varian: DPA/RKA/Renja -> judul dibuat otomatis, mis. "DPA Pergeseran 2 2026"
        $izin = $kats[$d['kategori']]['varian'] ?? [];
        if ($izin) {
            $d['varian'] = in_array($d['varian'] ?? '', $izin) ? $d['varian'] : $izin[0];
            $d['nomor'] = $d['varian'] === 'pergeseran' ? max(1, (int) ($d['nomor'] ?? 1)) : 0;
            $d['judul'] = BankDokumen::buatJudul($d);
        } else {
            $d['varian'] = 'murni'; $d['nomor'] = 0;
        }

        $d['unit_id'] = \App\Support\Akses::isSuper($u) ? ($d['unit_id'] ?? null) : $u->unit_id;   // non-superadmin: selalu bidang/UPTD sendiri
        if ($d['akses'] === 'bidang' && ! $d['unit_id']) {
            throw ValidationException::withMessages(['unit_id' => 'Pilih bidang untuk dokumen berakses "Bidang".']);
        }

        $kembar = BankDokumen::where(Arr::only($d, ['kategori', 'tahun', 'varian', 'nomor', 'judul']))
            ->when($dok, fn ($q) => $q->where('id', '!=', $dok->id))->exists();
        if ($kembar) throw ValidationException::withMessages(['judul' => 'Dokumen "'.$d['judul'].'" sudah ada. Untuk pembaruan isi, gunakan "Unggah Versi Baru" pada dokumen tersebut.']);
        return $d;
    }
}
