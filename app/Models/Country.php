<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'name_de',
        'name_fr',
        'name_es',
        'name_it',
        'default_currency',
        'default_language',
        'vat_rate',
        'tax_name',
        'supported_currencies',
        'supported_languages',
        'payment_methods',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'vat_rate' => 'decimal:2',
        'supported_currencies' => 'array',
        'supported_languages' => 'array',
        'payment_methods' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get users from this country
     */
    public function users()
    {
        return $this->hasMany(User::class, 'country_code', 'code');
    }

    /**
     * Get the localized country name based on current locale
     */
    public function getLocalizedNameAttribute()
    {
        $locale = app()->getLocale();
        
        $nameColumn = match($locale) {
            'ar' => 'name_ar',
            'de' => 'name_de',
            'fr' => 'name_fr',
            'es' => 'name_es',
            'it' => 'name_it',
            default => 'name',
        };

        return $this->$nameColumn ?? $this->name;
    }

    /**
     * Scope to get only active countries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get default currency rate for this country
     */
    public function getDefaultCurrencyRate()
    {
        return CurrencyRate::where('currency', $this->default_currency)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if a currency is supported in this country
     */
    public function supportsCurrency($currencyCode)
    {
        if (!$this->supported_currencies) {
            return $currencyCode === $this->default_currency;
        }

        return in_array($currencyCode, $this->supported_currencies);
    }

    /**
     * Check if a language is supported in this country
     */
    public function supportsLanguage($languageCode)
    {
        if (!$this->supported_languages) {
            return $languageCode === $this->default_language;
        }

        return in_array($languageCode, $this->supported_languages);
    }
}
