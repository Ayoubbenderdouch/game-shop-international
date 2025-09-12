<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply CSP to HTML responses
        if ($response instanceof \Illuminate\Http\Response &&
            str_contains($response->headers->get('Content-Type', 'text/html'), 'text/html')) {

            // Development CSP - more permissive
            if (app()->environment('local', 'development')) {
                $csp = "default-src 'self'; " .
                       "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com; " .
                       "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.jsdelivr.net; " .
                       "font-src 'self' https://fonts.bunny.net data:; " .
                       "img-src 'self' data: https: http:; " .
                       "connect-src 'self'; " .
                       "frame-src 'self'; " .
                       "object-src 'none'; " .
                       "base-uri 'self'; " .
                       "form-action 'self'; " .
                       "upgrade-insecure-requests;";
            } else {
                // Production CSP - more restrictive but still allows Alpine.js
                $csp = "default-src 'self'; " .
                       "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com; " .
                       "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.jsdelivr.net; " .
                       "font-src 'self' https://fonts.bunny.net data:; " .
                       "img-src 'self' data: https:; " .
                       "connect-src 'self'; " .
                       "frame-src 'self'; " .
                       "object-src 'none'; " .
                       "base-uri 'self'; " .
                       "form-action 'self'; " .
                       "upgrade-insecure-requests;";
            }

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
