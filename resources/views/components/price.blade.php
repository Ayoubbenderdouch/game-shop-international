@props(['price', 'currency' => null, 'class' => ''])

@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $displayCurrency = $currency ?? $currencyService->getUserCurrency();
    $convertedPrice = $currencyService->convertPrice($price, $displayCurrency);
    $formattedPrice = $currencyService->formatPrice($convertedPrice, $displayCurrency);
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>{{ $formattedPrice }}</span>
