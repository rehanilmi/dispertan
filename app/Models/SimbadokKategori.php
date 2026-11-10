<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimbadokKategori extends Model
{
    protected $table = 'simbadok_kategoris';
    protected $guarded = [];
    private const REVISI = ['penuh' => ['murni', 'pergeseran', 'perubahan'], 'murni_perubahan' => ['murni', 'perubahan']];

    /** [nama => ['varian' => [...]]] - bentuk sama dengan konfigurasi lama. Jika tabel kosong, pakai config. */
    public static function peta(): array
    {
        static $cache = null;
        return $cache ??= (function () {
            $rows = static::orderBy('urutan')->orderBy('nama')->get();
            if ($rows->isEmpty()) return (array) config('layanan.simbadok.kategori');
            return $rows->mapWithKeys(fn ($k) => [$k->nama => ['varian' => self::REVISI[$k->revisi] ?? []]])->all();
        })();
    }

    protected static function booted(): void
    {
        // mengganti nama kategori ikut memperbarui dokumen yang sudah memakainya
        static::updating(function ($k) {
            if ($k->isDirty('nama')) BankDokumen::where('kategori', $k->getOriginal('nama'))->update(['kategori' => $k->nama]);
        });
        static::deleting(function ($k) {
            abort_if(BankDokumen::where('kategori', $k->nama)->exists(), 422, __('Kategori masih dipakai oleh dokumen. Pindahkan atau hapus dokumennya dahulu.'));
        });
    }
}
