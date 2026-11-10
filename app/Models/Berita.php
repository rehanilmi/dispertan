<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];

    protected static function booted(): void
    {
        static::creating(fn ($b) => $b->slug = $b->slug ?: Str::slug($b->judul).'-'.Str::lower(Str::random(5)));
    }
}
