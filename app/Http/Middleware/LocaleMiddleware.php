<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has a preferred language
        if (Auth::check() && Auth::user()->locale) {
            App::setLocale(Auth::user()->locale);
        }
        // Check session for locale
        elseif (session()->has('locale')) {
            App::setLocale(session('locale'));
        }
        // Use browser language or default
        else {
            $locale = $request->getPreferredLanguage(['en', 'ar']);
            App::setLocale($locale ?: config('app.locale'));
        }

        return $next($request);
    }
}
