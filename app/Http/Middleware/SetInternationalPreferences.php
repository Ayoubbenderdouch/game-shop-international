<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\CurrencyService;
use App\Models\Country;

class SetInternationalPreferences
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set locale from user preference or session
        $locale = $this->getPreferredLocale($request);
        app()->setLocale($locale);
        session(['locale' => $locale]);

        // Set currency from user preference or session
        $currency = $this->getPreferredCurrency($request);
        session(['currency' => $currency]);

        // Detect and store user's country if not set
        $this->detectUserCountry($request);

        // Share international data with views
        view()->share('currentLocale', $locale);
        view()->share('currentCurrency', $currency);
        view()->share('availableLocales', $this->getAvailableLocales());
        view()->share('availableCurrencies', $this->currencyService->getActiveCurrencies());

        return $next($request);
    }

    /**
     * Get user's preferred locale
     */
    protected function getPreferredLocale(Request $request)
    {
        // 1. Check if user is authenticated and has preference
        if (auth()->check() && auth()->user()->preferred_language) {
            return auth()->user()->preferred_language;
        }

        // 2. Check session
        if (session()->has('locale')) {
            return session('locale');
        }

        // 3. Check user's country default language
        if (session()->has('country_code')) {
            $country = Country::where('code', session('country_code'))->first();
            if ($country && $country->default_language) {
                return $country->default_language;
            }
        }

        // 4. Check browser language
        $availableLocales = array_keys($this->getAvailableLocales());
        $browserLocale = $request->getPreferredLanguage($availableLocales);
        if ($browserLocale && in_array($browserLocale, $availableLocales)) {
            return $browserLocale;
        }

        // 5. Default to English
        return config('app.locale', 'en');
    }

    /**
     * Get user's preferred currency
     */
    protected function getPreferredCurrency(Request $request)
    {
        // 1. Check if user is authenticated and has preference
        if (auth()->check() && auth()->user()->currency) {
            return auth()->user()->currency;
        }

        // 2. Check session
        if (session()->has('currency')) {
            return session('currency');
        }

        // 3. Use country default currency
        if (session()->has('country_code')) {
            $country = Country::where('code', session('country_code'))->first();
            if ($country && $country->default_currency) {
                return $country->default_currency;
            }
        }

        // 4. Default to USD
        return config('app.currency', 'USD');
    }

    /**
     * Detect user's country from IP
     */
    protected function detectUserCountry(Request $request)
    {
        // Skip if already detected
        if (session()->has('country_code')) {
            return;
        }

        // Skip for authenticated users with country set
        if (auth()->check() && auth()->user()->country_code) {
            session(['country_code' => auth()->user()->country_code]);
            return;
        }

        // Detect from IP (using CurrencyService method)
        $countryCode = $this->currencyService->getUserCurrency();
        
        if ($countryCode) {
            session(['country_code' => $countryCode]);
        }
    }

    /**
     * Get available locales
     */
    protected function getAvailableLocales()
    {
        return [
            'en' => ['name' => 'English', 'flag' => '🇬🇧'],
            'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
            'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
            'es' => ['name' => 'Español', 'flag' => '🇪🇸'],
            'it' => ['name' => 'Italiano', 'flag' => '🇮🇹'],
            'ar' => ['name' => 'العربية', 'flag' => '🇸🇦'],
        ];
    }
}
