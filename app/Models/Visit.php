<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Visit extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public static function hit(): void
    {
        DB::table('visits')->upsert([['tanggal' => today()->toDateString(), 'hits' => 1]], ['tanggal'], ['hits' => DB::raw('hits + 1')]);
    }

    public static function stats(): array
    {
        return [(int) static::sum('hits'), (int) static::where('tanggal', today()->toDateString())->value('hits')];
    }
}
