<?php
namespace App\Support;

class Translator
{
    private static ?array $dict = null;

    /** Teks dengan angka/bagian dinamis */
    private const POLA = [
        '/^Hewan (\d+)$/' => 'Animal $1',
        '/^(\d+) hewan$/' => '$1 animal(s)',
        '/^(\d+) dari (\d+)$/' => '$1 of $2',
        '/^Tampilkan banner (\d+)$/' => 'Show banner $1',
        '/^Unduh versi terbaru \(v(\d+)\)$/' => 'Download latest version (v$1)',
        '/^Unduh \((.+)\)$/' => 'Download ($1)',
        '/^Rangkaian (.+)$/' => 'Series $1',
        '/^Unggah sebagai v(\d+)$/' => 'Upload as v$1',
        '/^Tahun (\d{4})$/' => 'Year $1',
        '/^(\d+) kelompok memiliki titik koordinat$/' => '$1 groups have map coordinates',
        '/^(\d+) dokumen publik ditemukan\. Dokumen internal tidak ditampilkan\.$/' => '$1 public documents found. Internal documents are not shown.',
        '/^Kantor (.+)$/' => '$1 Office',
        '/^… dan (\d+) catatan lainnya$/' => '… and $1 more notes',
    ];

    public static function dict(): array
    {
        if (self::$dict === null) {
            $f = lang_path('en.json');
            $d = is_file($f) ? json_decode((string) file_get_contents($f), true) : [];
            self::$dict = is_array($d) ? $d : [];
        }
        return self::$dict;
    }

    private static function norm(string $s): string
    {
        return trim(preg_replace('/\s+/', ' ', str_replace("\xC2\xA0", ' ', $s)));
    }

    public static function lookup(string $s): ?string
    {
        $s = self::norm($s);
        if ($s === '') return null;
        $d = self::dict();
        if (isset($d[$s])) return $d[$s];
        foreach (self::POLA as $re => $to) {
            if (preg_match($re, $s)) {
                $t = preg_replace($re, $to, $s);
                return $t;
            }
        }
        return null;
    }

    public static function html(string $html): string
    {
        $parts = preg_split('~(<script\b[^>]*>.*?</script>|<style\b[^>]*>.*?</style>|<!--.*?-->|<[^>]+>)~is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) return $html;   // halaman terlalu besar untuk diproses: tampilkan apa adanya
        $skip = false;
        foreach ($parts as &$p) {
            if ($p === '') continue;
            if ($p[0] === '<') {
                if (preg_match('~^<script\b~i', $p)) { $p = self::script($p); continue; }
                if (preg_match('~^<(style|!--)~i', $p)) continue;
                if (preg_match('~^<textarea\b~i', $p)) $skip = true;
                elseif (preg_match('~^</textarea~i', $p)) $skip = false;
                elseif (preg_match('~^<option\b~i', $p)) $skip = ! preg_match('~\bvalue\s*=~i', $p);   // teks option tanpa value = nilai data, jangan diubah
                elseif (preg_match('~^</(option|select)~i', $p)) $skip = false;
                $p = preg_replace_callback('~\b(placeholder|title|aria-label|alt)="([^"]*)"~', function ($m) {
                    $t = self::lookup(html_entity_decode($m[2], ENT_QUOTES));
                    return $t === null ? $m[0] : $m[1].'="'.htmlspecialchars($t, ENT_QUOTES).'"';
                }, $p) ?? $p;
                continue;
            }
            if ($skip) continue;
            $t = self::lookup(html_entity_decode($p, ENT_QUOTES));
            if ($t === null) continue;
            preg_match('/^\s*/u', $p, $a); preg_match('/\s*$/u', $p, $b);
            $p = $a[0].htmlspecialchars($t, ENT_NOQUOTES).$b[0];
        }
        unset($p);
        return implode('', $parts);
    }

    /** Dalam <script>, hanya teks yang dibungkus T('...') yang diterjemahkan (agar logika JS tidak ikut berubah). */
    private static function script(string $tag): string
    {
        return preg_replace_callback('~\bT\((["\'])((?:\\\\.|(?!\1).)*?)\1\)~s', function ($m) {
            $lit = stripcslashes($m[2]);
            $t = self::lookup($lit);
            if ($t === null) return $m[0];
            preg_match('/^\s*/', $lit, $a); preg_match('/\s*$/', $lit, $b);
            return 'T('.$m[1].addcslashes($a[0].$t.$b[0], $m[1].'\\'."\n\r").$m[1].')';
        }, $tag) ?? $tag;
    }
}
