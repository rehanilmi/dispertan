<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaKomoditas extends Model
{
    protected $table = 'harga_komoditas';
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];
}
