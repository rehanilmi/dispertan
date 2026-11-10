<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Pendaftaran extends Model
{
    protected $table = 'pendaftarans';
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date', 'waktu_update' => 'datetime'];

    /** Nomor harian: REG-PUSKESWAN-YYYYMMDD-XXX (urut, reset tiap hari) - sama dengan generateNoPendaftaran_ di Code.gs */
    public static function nomorBerikut(Carbon $now): string
    {
        $prefix = config('sipandai.prefix').$now->format('Ymd');
        $last = static::where('no_pendaftaran', 'like', $prefix.'-%')->lockForUpdate()->orderByDesc('no_pendaftaran')->value('no_pendaftaran');
        $n = $last ? ((int) substr($last, -3)) + 1 : 1;
        return $prefix.'-'.str_pad((string) $n, 3, '0', STR_PAD_LEFT);
    }
}
