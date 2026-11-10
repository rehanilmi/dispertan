<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];
}
