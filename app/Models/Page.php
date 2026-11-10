<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(fn ($p) => $p->slug = Str::slug($p->slug ?: $p->judul));
    }
}
