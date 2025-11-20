<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CurrencyService;

class InternationalController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Switch language
     */
    public function switchLanguage(Request $request, $locale)
    {
        $availableLocales = ['en', 'de', 'fr', 'es', 'it', 'ar'];

        if (!in_array($locale, $availableLocales)) {
            return redirect()->back()->with('error', 'Invalid language selection');
        }

        // Update session
        session(['locale' => $locale]);
        app()->setLocale($locale);

        // Update user if authenticated
        if (auth()->check()) {
            auth()->user()->update(['preferred_language' => $locale]);
        }

        return redirect()->back()->with('success', __('app.common.success'));
    }

    /**
     * Switch currency
     */
    public function switchCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
        ]);

        $currency = strtoupper($request->currency);

        if (!$this->currencyService->setUserCurrency($currency)) {
            return redirect()->back()->with('error', 'Invalid currency selection');
        }

        return redirect()->back()->with('success', __('app.common.success'));
    }

    /**
     * Get currency data for AJAX requests
     */
    public function getCurrencyData($currency)
    {
        $currencyRate = \App\Models\CurrencyRate::where('currency', $currency)
            ->where('is_active', true)
            ->first();

        if (!$currencyRate) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        return response()->json([
            'currency' => $currencyRate->currency,
            'name' => $currencyRate->currency_name,
            'symbol' => $currencyRate->currency_symbol,
            'rate' => $currencyRate->rate_to_usd,
        ]);
    }

    /**
     * Convert price - AJAX endpoint
     */
    public function convertPrice(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $amount = $request->amount;
        $from = strtoupper($request->from);
        $to = strtoupper($request->to);

        $converted = \App\Models\CurrencyRate::convert($amount, $from, $to);

        return response()->json([
            'original_amount' => $amount,
            'original_currency' => $from,
            'converted_amount' => $converted,
            'converted_currency' => $to,
            'formatted' => $this->currencyService->formatPrice($converted, $to),
        ]);
    }

    /**
     * Get all active currencies - API endpoint
     */
    public function getActiveCurrencies()
    {
        $currencies = $this->currencyService->getActiveCurrencies();

        return response()->json([
            'currencies' => $currencies->map(function ($currency) {
                return [
                    'code' => $currency->currency,
                    'name' => $currency->currency_name,
                    'symbol' => $currency->currency_symbol,
                    'rate' => $currency->rate_to_usd,
                ];
            }),
        ]);
    }

    /**
     * Update exchange rates (Admin only)
     */
    public function updateExchangeRates()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($this->currencyService->updateExchangeRates()) {
            return redirect()->back()->with('success', 'Exchange rates updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update exchange rates');
    }
}
