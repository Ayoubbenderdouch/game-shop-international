<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'currency_name',
        'currency_symbol',
        'rate_to_usd',
        'is_active',
        'last_updated',
    ];

    protected $casts = [
        'rate_to_usd' => 'decimal:6',
        'is_active' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Scope to get only active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Convert an amount from USD to this currency
     */
    public function convertFromUSD($amount)
    {
        return round($amount * $this->rate_to_usd, 2);
    }

    /**
     * Convert an amount from this currency to USD
     */
    public function convertToUSD($amount)
    {
        if ($this->rate_to_usd == 0) {
            return 0;
        }
        return round($amount / $this->rate_to_usd, 2);
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $cacheKey = "currency_conversion_{$fromCurrency}_{$toCurrency}";
        
        $rate = Cache::remember($cacheKey, 3600, function () use ($fromCurrency, $toCurrency) {
            $fromRate = self::where('currency', $fromCurrency)->where('is_active', true)->first();
            $toRate = self::where('currency', $toCurrency)->where('is_active', true)->first();

            if (!$fromRate || !$toRate) {
                return null;
            }

            // Convert to USD first, then to target currency
            $usdAmount = $fromRate->convertToUSD(1);
            return $toRate->convertFromUSD($usdAmount);
        });

        if (!$rate) {
            return $amount; // Return original if conversion fails
        }

        return round($amount * $rate, 2);
    }

    /**
     * Format amount with currency symbol
     */
    public function formatAmount($amount)
    {
        return $this->currency_symbol . ' ' . number_format($amount, 2);
    }

    /**
     * Get all active currencies for dropdown
     */
    public static function getActiveCurrencies()
    {
        return Cache::remember('active_currencies', 3600, function () {
            return self::active()->orderBy('currency')->get();
        });
    }

    /**
     * Get currency by code
     */
    public static function getByCurrency($code)
    {
        return Cache::remember("currency_{$code}", 3600, function () use ($code) {
            return self::where('currency', $code)->where('is_active', true)->first();
        });
    }

    /**
     * Clear currency cache
     */
    public static function clearCache()
    {
        Cache::forget('active_currencies');
        
        // Clear individual currency caches
        $currencies = self::all();
        foreach ($currencies as $currency) {
            Cache::forget("currency_{$currency->currency}");
        }

        // Clear conversion caches
        foreach ($currencies as $fromCurrency) {
            foreach ($currencies as $toCurrency) {
                Cache::forget("currency_conversion_{$fromCurrency->currency}_{$toCurrency->currency}");
            }
        }
    }
}
