<?php
namespace App\Http\Middleware;

use App\Support\Akses;
use Closure;
use Illuminate\Http\Request;

class CekMenu
{
    public function handle(Request $request, Closure $next, string $menu)
    {
        abort_unless(Akses::bisa($request->user(), $menu), 403, __('Anda tidak memiliki akses ke menu ini.'));
        return $next($request);
    }
}
