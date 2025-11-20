<?php

namespace App\Services;

use App\Models\CurrencyRate;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    /**
     * Get user's preferred currency
     */
    public function getUserCurrency()
    {
        if (auth()->check() && auth()->user()->currency) {
            return auth()->user()->currency;
        }

        return session('currency', $this->getDefaultCurrency());
    }

    /**
     * Get default currency based on user's location or system default
     */
    public function getDefaultCurrency()
    {
        // Try to detect from IP or use USD as fallback
        $countryCode = $this->detectCountryFromIP();
        
        if ($countryCode) {
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                return $country->default_currency;
            }
        }

        return config('app.currency', 'USD');
    }

    /**
     * Set user's currency preference
     */
    public function setUserCurrency($currency)
    {
        // Validate currency exists and is active
        $currencyRate = CurrencyRate::where('currency', $currency)
            ->where('is_active', true)
            ->first();

        if (!$currencyRate) {
            return false;
        }

        // Update session
        session(['currency' => $currency]);

        // Update user if authenticated
        if (auth()->check()) {
            auth()->user()->update(['currency' => $currency]);
        }

        return true;
    }

    /**
     * Convert price from USD to user's currency
     */
    public function convertPrice($usdPrice, $targetCurrency = null)
    {
        if (!$targetCurrency) {
            $targetCurrency = $this->getUserCurrency();
        }

        if ($targetCurrency === 'USD') {
            return $usdPrice;
        }

        return CurrencyRate::convert($usdPrice, 'USD', $targetCurrency);
    }

    /**
     * Format price with currency symbol
     */
    public function formatPrice($price, $currency = null)
    {
        if (!$currency) {
            $currency = $this->getUserCurrency();
        }

        $currencyRate = CurrencyRate::getByCurrency($currency);
        
        if (!$currencyRate) {
            return '$' . number_format($price, 2);
        }

        return $currencyRate->formatAmount($price);
    }

    /**
     * Get all active currencies
     */
    public function getActiveCurrencies()
    {
        return CurrencyRate::getActiveCurrencies();
    }

    /**
     * Update exchange rates from external API
     * Using ExchangeRate-API (free tier available)
     */
    public function updateExchangeRates()
    {
        try {
            $apiKey = config('services.exchangerate_api.key');
            
            if (!$apiKey) {
                Log::warning('ExchangeRate API key not configured');
                return false;
            }

            $response = Http::get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

            if (!$response->successful()) {
                Log::error('Failed to fetch exchange rates', ['response' => $response->body()]);
                return false;
            }

            $data = $response->json();

            if ($data['result'] !== 'success') {
                Log::error('Exchange rate API returned error', ['data' => $data]);
                return false;
            }

            $rates = $data['conversion_rates'];

            // Update all active currencies
            $currencies = CurrencyRate::active()->get();

            foreach ($currencies as $currency) {
                if (isset($rates[$currency->currency])) {
                    $currency->update([
                        'rate_to_usd' => $rates[$currency->currency],
                        'last_updated' => now(),
                    ]);
                }
            }

            // Clear currency cache
            CurrencyRate::clearCache();

            Log::info('Exchange rates updated successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Error updating exchange rates', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Detect country from IP address
     */
    public function detectCountryFromIP()
    {
        try {
            $ip = request()->ip();

            // Skip for local/private IPs
            if ($ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
                return null;
            }

            // Use ip-api.com (free, no key required for basic usage)
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['countryCode'] ?? null;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::warning('Failed to detect country from IP', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get regional price for a product
     */
    public function getRegionalPrice($product, $countryCode = null, $currency = null)
    {
        if (!$countryCode) {
            $countryCode = auth()->check() ? auth()->user()->country_code : $this->detectCountryFromIP();
        }

        if (!$currency) {
            $currency = $this->getUserCurrency();
        }

        // Check if product has regional pricing
        if ($product->regional_prices && isset($product->regional_prices[$countryCode])) {
            $regionalPrice = $product->regional_prices[$countryCode];
            
            // Convert if needed
            if ($regionalPrice['currency'] !== $currency) {
                return $this->convertPrice($regionalPrice['price'], $currency);
            }

            return $regionalPrice['price'];
        }

        // Fall back to standard price conversion
        return $this->convertPrice($product->selling_price, $currency);
    }

    /**
     * Initialize default currencies and countries
     */
    public function initializeDefaults()
    {
        // Create default currencies
        $currencies = [
            ['currency' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'rate' => 1.00],
            ['currency' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'rate' => 0.92],
            ['currency' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'rate' => 0.79],
            ['currency' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'rate' => 3.67],
            ['currency' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'rate' => 3.75],
            ['currency' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => 'ج.م', 'rate' => 30.90],
            ['currency' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'rate' => 149.50],
            ['currency' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'rate' => 1.36],
            ['currency' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'rate' => 1.53],
        ];

        foreach ($currencies as $currencyData) {
            CurrencyRate::updateOrCreate(
                ['currency' => $currencyData['currency']],
                [
                    'currency_name' => $currencyData['name'],
                    'currency_symbol' => $currencyData['symbol'],
                    'rate_to_usd' => $currencyData['rate'],
                    'is_active' => true,
                    'last_updated' => now(),
                ]
            );
        }

        // Create default countries
        $countries = [
            [
                'code' => 'US',
                'name' => 'United States',
                'name_de' => 'Vereinigte Staaten',
                'name_fr' => 'États-Unis',
                'name_es' => 'Estados Unidos',
                'name_it' => 'Stati Uniti',
                'currency' => 'USD',
                'language' => 'en',
                'vat_rate' => 0,
                'tax_name' => 'Sales Tax',
            ],
            [
                'code' => 'GB',
                'name' => 'United Kingdom',
                'name_de' => 'Vereinigtes Königreich',
                'name_fr' => 'Royaume-Uni',
                'name_es' => 'Reino Unido',
                'name_it' => 'Regno Unito',
                'currency' => 'GBP',
                'language' => 'en',
                'vat_rate' => 20,
                'tax_name' => 'VAT',
            ],
            [
                'code' => 'DE',
                'name' => 'Germany',
                'name_de' => 'Deutschland',
                'name_fr' => 'Allemagne',
                'name_es' => 'Alemania',
                'name_it' => 'Germania',
                'currency' => 'EUR',
                'language' => 'de',
                'vat_rate' => 19,
                'tax_name' => 'MwSt',
            ],
            [
                'code' => 'FR',
                'name' => 'France',
                'name_de' => 'Frankreich',
                'name_fr' => 'France',
                'name_es' => 'Francia',
                'name_it' => 'Francia',
                'currency' => 'EUR',
                'language' => 'fr',
                'vat_rate' => 20,
                'tax_name' => 'TVA',
            ],
            [
                'code' => 'ES',
                'name' => 'Spain',
                'name_de' => 'Spanien',
                'name_fr' => 'Espagne',
                'name_es' => 'España',
                'name_it' => 'Spagna',
                'currency' => 'EUR',
                'language' => 'es',
                'vat_rate' => 21,
                'tax_name' => 'IVA',
            ],
            [
                'code' => 'IT',
                'name' => 'Italy',
                'name_de' => 'Italien',
                'name_fr' => 'Italie',
                'name_es' => 'Italia',
                'name_it' => 'Italia',
                'currency' => 'EUR',
                'language' => 'it',
                'vat_rate' => 22,
                'tax_name' => 'IVA',
            ],
            [
                'code' => 'AE',
                'name' => 'United Arab Emirates',
                'name_ar' => 'الإمارات العربية المتحدة',
                'name_de' => 'Vereinigte Arabische Emirate',
                'name_fr' => 'Émirats Arabes Unis',
                'name_es' => 'Emiratos Árabes Unidos',
                'name_it' => 'Emirati Arabi Uniti',
                'currency' => 'AED',
                'language' => 'ar',
                'vat_rate' => 5,
                'tax_name' => 'VAT',
            ],
            [
                'code' => 'SA',
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'name_de' => 'Saudi-Arabien',
                'name_fr' => 'Arabie Saoudite',
                'name_es' => 'Arabia Saudita',
                'name_it' => 'Arabia Saudita',
                'currency' => 'SAR',
                'language' => 'ar',
                'vat_rate' => 15,
                'tax_name' => 'VAT',
            ],
        ];

        foreach ($countries as $countryData) {
            Country::updateOrCreate(
                ['code' => $countryData['code']],
                [
                    'name' => $countryData['name'],
                    'name_ar' => $countryData['name_ar'] ?? null,
                    'name_de' => $countryData['name_de'] ?? null,
                    'name_fr' => $countryData['name_fr'] ?? null,
                    'name_es' => $countryData['name_es'] ?? null,
                    'name_it' => $countryData['name_it'] ?? null,
                    'default_currency' => $countryData['currency'],
                    'default_language' => $countryData['language'],
                    'vat_rate' => $countryData['vat_rate'],
                    'tax_name' => $countryData['tax_name'],
                    'is_active' => true,
                ]
            );
        }

        return true;
    }
}
