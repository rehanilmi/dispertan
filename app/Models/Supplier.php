<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $guarded = [];
    protected $casts = ['stok' => 'array', 'berlaku_sampai' => 'date', 'aktif' => 'boolean'];

    /** Input admin: satu baris per varietas -> "Varietas | Kelas | Stok kg" */
    public function getStokTextAttribute(): string
    {
        return collect($this->stok ?? [])->map(fn ($r) => $r['varietas'].' | '.$r['kelas'].' | '.$r['kg'])->implode("\n");
    }

    public function setStokTextAttribute(?string $v): void
    {
        $rows = collect(preg_split('/\r\n|\r|\n/', (string) $v))
            ->map(fn ($l) => array_map('trim', explode('|', $l)))
            ->filter(fn ($p) => count($p) >= 3 && $p[0] !== '')
            ->map(fn ($p) => ['varietas' => $p[0], 'kelas' => $p[1], 'kg' => (int) preg_replace('/\D/', '', $p[2])])
            ->values()->all();
        $this->attributes['stok'] = json_encode($rows);
    }
}
