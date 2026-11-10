<?php
namespace App\Support;

use App\Models\Unit;

/** Pusat aturan hak akses: superadmin = semua; admin/operator = hanya menu yang dicentang superadmin. */
class Akses
{
    public static function isSuper($u): bool { return $u && $u->role === 'superadmin'; }

    public static function menus($u): array
    {
        $m = $u ? json_decode((string) ($u->menus ?? '[]'), true) : [];
        return is_array($m) ? $m : [];
    }

    public static function bisa($u, string $key): bool
    {
        if (! $u) return false;
        if (self::isSuper($u)) return true;
        return isset(config('akses.menu')[$key]) && in_array($key, self::menus($u), true);   // menu "khusus" tidak pernah bisa diberikan
    }

    /** Admin bidang/UPTD boleh menghapus; operator hanya menambah dan mengubah. */
    public static function bisaHapus($u): bool { return $u && in_array($u->role, ['superadmin', 'admin'], true); }

    /** Menu yang tampil di sidebar & dashboard: [grup => [item...]] */
    public static function sidebar($u): array
    {
        $semua = config('akses.menu') + (self::isSuper($u) ? config('akses.khusus') : []);
        $out = [];
        foreach ($semua as $k => $m) {
            if (self::bisa($u, $k)) $out[$m['grup']][] = ['key' => $k] + $m;
        }
        return collect(config('akses.grup'))->filter(fn ($g) => isset($out[$g]))->mapWithKeys(fn ($g) => [$g => $out[$g]])->all();
    }

    public static function url(array $m): string { return route(...(array) $m['route']); }

    public static function aktif(array $m): bool
    {
        return isset($m['segmen']) ? request()->segment(2) === $m['segmen'] : request()->routeIs(...(array) ($m['aktif'] ?? []));
    }

    public static function label($u): string
    {
        $p = config('akses.peran')[$u->role] ?? $u->role;
        $unit = $u->unit_id ? Unit::find($u->unit_id)?->nama : null;
        return $p.($unit ? ' · '.$unit : '');
    }
}
