<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = [
            "default-src" => "'self'",
            "script-src" => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com",
            "style-src" => "'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdn.tailwindcss.com",
            "font-src" => "'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
            "img-src" => "'self' data: https: http: blob:",
            "connect-src" => "'self' https://cdn.jsdelivr.net",
        ];

        $cspString = collect($csp)->map(function ($value, $key) {
            return "{$key} {$value}";
        })->implode('; ');

        $response->headers->set('Content-Security-Policy', $cspString);

        return $response;
    }
}
