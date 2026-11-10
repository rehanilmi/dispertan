<?php
namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Img
{
    /** Simpan gambar: perkecil (maks 1600px) & konversi WebP bila GD tersedia. */
    public static function store(UploadedFile $f, string $dir, int $max = 1600): string
    {
        if (function_exists('imagewebp') && ($im = @imagecreatefromstring(file_get_contents($f->getRealPath())))) {
            imagepalettetotruecolor($im);
            if (imagesx($im) > $max) $im = imagescale($im, $max);
            ob_start(); imagewebp($im, null, 82); $data = ob_get_clean(); imagedestroy($im);
            $name = $dir.'/'.Str::random(24).'.webp';
            Storage::disk('public')->put($name, $data);
            return $name;
        }
        return $f->store($dir, 'public');
    }

    /** Gambar berita/reels/galeri. Bila belum diunggah, pakai ilustrasi lokal (tanpa internet) yang rasionya mengikuti $w x $h. */
    public static function url(?string $path, int $i = 0, int $w = 800, int $h = 450): string
    {
        if ($path) return asset('storage/'.$path);
        return asset('images/ph/'.($h > $w * 1.2 ? 'p' : 'l').(($i % 4) + 1).'.svg');
    }

    /** Banner beranda: unggahan admin; bila kosong -> public/images/background.png; bila file itu belum ada -> ilustrasi bawaan. */
    public static function banner(?string $path): string
    {
        if ($path) return asset('storage/'.$path);
        $f = public_path('images/background.png');
        return is_file($f) ? asset('images/background.png').'?v='.filemtime($f) : asset('images/ph/hero.svg');   // ?v= agar browser memuat ulang bila file diganti
    }
}
