<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDokumenVersi extends Model
{
    protected $table = 'bank_dokumen_versis';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime'];

    public function dokumen() { return $this->belongsTo(BankDokumen::class, 'bank_dokumen_id'); }
    public function getUkuranMbAttribute(): string { return number_format($this->ukuran / 1048576, 2, ',', '.').' MB'; }
}
