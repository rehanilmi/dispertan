<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bantuan extends Model
{
    protected $table = 'kelompok_bantuans';
    protected $guarded = [];

    public function kelompok() { return $this->belongsTo(Kelompok::class); }
}
