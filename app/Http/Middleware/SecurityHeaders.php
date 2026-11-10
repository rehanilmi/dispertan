<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $r = $next($request);
        $r->headers->add([
            'X-Frame-Options' => 'SAMEORIGIN', 'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ]);
        if ($request->isSecure()) $r->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        return $r;
    }
}
