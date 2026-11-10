<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unit extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(fn ($u) => $u->slug = Str::slug($u->slug ?: $u->nama));
    }

    public static function opsi(): array
    {
        return static::orderBy('tipe')->orderBy('urutan')->pluck('nama', 'id')->all();
    }

    public function getUrlAttribute(): string
    {
        return route('unit', [$this->tipe, $this->slug]);
    }
}
