<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected static ?array $cache = null;

    public static function get(string $key, string $default = ''): string
    {
        self::$cache ??= static::pluck('value', 'key')->all();
        return self::$cache[$key] ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        self::$cache = null;
    }
}
