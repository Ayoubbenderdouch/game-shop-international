<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        if (session()->has('locale')) {
            $locale = session('locale');
        }
        elseif (Auth::check() && Auth::user()->preferred_locale) {
            $locale = Auth::user()->preferred_locale;
            session(['locale' => $locale]);
        }
        elseif ($request->hasHeader('Accept-Language')) {
            $browserLang = substr($request->header('Accept-Language'), 0, 2);
            $availableLocales = ['en', 'ar', 'fr', 'es'];
            if (in_array($browserLang, $availableLocales)) {
                $locale = $browserLang;
            }
        }


        if (!$locale) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        view()->share('currentLocale', $locale);

        return $next($request);
    }
}
