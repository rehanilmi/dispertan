<?php
namespace App\Http\Middleware;

use App\Support\Translator;
use Closure;
use Illuminate\Http\Request;

/** Bahasa Inggris: terjemahkan teks halaman HTML memakai kamus lang/en.json (cocok persis per teks). */
class LocalizeResponse
{
    public function handle(Request $request, Closure $next)
    {
        $res = $next($request);
        if (app()->getLocale() !== 'en') return $res;
        if (! str_contains((string) $res->headers->get('Content-Type', ''), 'text/html')) return $res;
        $html = $res->getContent();
        if (is_string($html) && $html !== '') $res->setContent(Translator::html($html));
        return $res;
    }
}
