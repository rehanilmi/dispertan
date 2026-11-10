<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $l = $request->hasSession() ? $request->session()->get('locale', 'id') : 'id';
        if (! in_array($l, ['id', 'en'], true)) $l = 'id';
        app()->setLocale($l);
        Carbon::setLocale($l);
        return $next($request);
    }
}
