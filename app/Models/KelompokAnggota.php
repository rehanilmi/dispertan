<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokAnggota extends Model
{
    protected $table = 'kelompok_anggotas';
    protected $guarded = [];
    protected $casts = ['tgl_lahir' => 'date'];

    public function kelompok() { return $this->belongsTo(Kelompok::class); }
}
