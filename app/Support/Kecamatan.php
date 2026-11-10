<?php
namespace App\Support;

class Kecamatan
{
    /** "Kecamatan Bungus Teluk Kabung " -> "BUNGUS TELUK KABUNG" (seragam dengan SIMLUHTAN) */
    public static function norm(?string $s): string
    {
        $s = preg_replace('/\s+/u', ' ', trim((string) $s));
        return mb_strtoupper(preg_replace('/^kecamatan\s+/iu', '', $s));
    }
}
