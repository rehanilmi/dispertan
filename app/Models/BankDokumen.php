<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDokumen extends Model
{
    protected $table = 'bank_dokumens';
    protected $guarded = [];

    public function versis() { return $this->hasMany(BankDokumenVersi::class)->orderByDesc('versi'); }
    public function latestVersi() { return $this->hasOne(BankDokumenVersi::class)->latestOfMany('versi'); }
    public function unit() { return $this->belongsTo(Unit::class); }

    /** Urutan dalam satu tahun: Murni -> Pergeseran 1..n -> Perubahan */
    public function getUrutAttribute(): int
    {
        return match ($this->varian) { 'pergeseran' => 10 + $this->nomor, 'perubahan' => 100, default => 0 };
    }

    public function getVarianLabelAttribute(): string
    {
        return match ($this->varian) { 'pergeseran' => 'Pergeseran '.$this->nomor, 'perubahan' => 'Perubahan', default => 'Murni' };
    }

    public static function buatJudul(array $d): string
    {
        $x = match ($d['varian']) { 'pergeseran' => ' Pergeseran '.$d['nomor'], 'perubahan' => ' Perubahan', default => '' };
        return $d['kategori'].$x.' '.$d['tahun'];
    }

    /** Admin: semua. Operator: publik + internal + dokumen bidangnya sendiri. */
    public function scopeVisibleTo($q, $u)
    {
        if (\App\Support\Akses::isSuper($u)) return $q;   // hanya superadmin yang melihat semuanya
        return $q->where(fn ($w) => $w->whereIn('akses', ['publik', 'internal'])
            ->orWhere(fn ($x) => $x->where('akses', 'bidang')->whereNotNull('unit_id')->where('unit_id', $u->unit_id)));
    }

    /** Admin: semua. Operator: hanya dokumen milik bidangnya. */
    public function bisaDiubah($u): bool
    {
        return \App\Support\Akses::isSuper($u) || ($u->unit_id && $this->unit_id == $u->unit_id);
    }
}
