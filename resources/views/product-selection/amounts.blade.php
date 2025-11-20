@extends('layouts.app')

@section('title', $baseName . ' - ' . ($country ?? '') . ' - ' . (app()->getLocale() == 'ar' ? 'اختر المبلغ' : 'Select Amount'))

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Header with Image -->
    <div style="text-align: center; margin-bottom: 50px;">
        @php
            $imageMap = [
                'Apple Gift Card' => 'itunes realod.png',
                'Google Play' => 'google play realod.png',
                'PlayStation' => 'play realod.png',
                'XBOX' => 'xbox realod.png',
                'Steam' => 'steam realod.png',
                'Razer Gold' => 'razer gpld realod.png',
            ];
            $productImage = $imageMap[$baseName] ?? null;
        @endphp

        @if($productImage)
        <div style="margin-bottom: 30px;">
            <img src="{{ asset('images/catgorie/' . $productImage) }}"
                 alt="{{ $baseName }}"
                 style="max-width: 300px; height: auto; border-radius: 20px; box-shadow: 0 10px 40px rgba(46, 35, 112, 0.3);">
        </div>
        @endif

        <h1 style="font-size: 2.8rem; font-weight: 800; color: var(--purple-main); margin-bottom: 15px;">
            {{ $baseName }}
            @if($country)
                <span style="color: var(--gold-main);">- {{ $country }}</span>
            @endif
        </h1>
        <p style="font-size: 1.3rem; color: #666; font-weight: 500;">
            {{ app()->getLocale() == 'ar' ? 'اختر المبلغ الذي تريده' : 'Select the amount you want' }}
        </p>
    </div>

    <!-- Products Grid -->
    <div class="products-grid-amounts">
        @foreach($products as $product)
        <div class="product-amount-card scroll-animate">
            <div class="product-amount-header">
                @if($product['image'])
                <div class="product-amount-image">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" onerror="this.style.display='none'">
                </div>
                @endif
            </div>

            <div class="product-amount-body">
                <div class="amount-badge">
                    {{ $product['amount'] }}
                </div>

                <div class="product-price-large">
                    <span class="currency">{{ $product['currency'] == 'SAR' ? 'SAR' : ($product['currency'] == 'AED' ? 'AED' : '$') }}</span>
                    <span class="amount-price">{{ number_format($product['price'], 2) }}</span>
                </div>

                @if($product['name'] && $product['name'] !== $product['amount'])
                <p class="product-name-small">{{ $product['name'] }}</p>
                @endif
            </div>

            <div class="product-amount-footer">
                <a href="{{ route('product.show', $product['slug']) }}" class="btn-buy-amount">
                    {{ app()->getLocale() == 'ar' ? 'اشتر الآن' : 'Buy Now' }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-left: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Back Button -->
    <div style="text-align: center; margin-top: 50px;">
        <a href="{{ route('product.select-country', $productSlug) }}" class="btn-back">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 8px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ app()->getLocale() == 'ar' ? 'العودة لاختيار البلد' : 'Back to Country Selection' }}
        </a>
    </div>

</div>

@endsection

@push('styles')
<style>
/* Products Grid for Amounts */
.products-grid-amounts {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    padding: 20px 0;
    max-width: 1400px;
    margin: 0 auto;
}

@media (max-width: 1200px) {
    .products-grid-amounts {
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
}

@media (max-width: 768px) {
    .products-grid-amounts {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .products-grid-amounts {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

/* Product Amount Card */
.product-amount-card {
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 2px solid rgba(46, 35, 112, 0.1);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(46, 35, 112, 0.1);
    display: flex;
    flex-direction: column;
}

.product-amount-card:hover {
    transform: translateY(-8px);
    border-color: var(--gold-main);
    box-shadow: 0 12px 35px rgba(244, 196, 48, 0.3);
}

.product-amount-header {
    background: linear-gradient(135deg, #2E2370, #1F1851);
    padding: 20px;
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-amount-image {
    max-width: 80px;
    max-height: 80px;
}

.product-amount-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 10px;
}

.product-amount-body {
    padding: 30px 20px;
    text-align: center;
    flex: 1;
}

.amount-badge {
    display: inline-block;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 1.3rem;
    font-weight: 800;
    margin-bottom: 20px;
    box-shadow: 0 3px 10px rgba(244, 196, 48, 0.3);
}

.product-price-large {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--purple-main);
    margin-bottom: 15px;
    line-height: 1;
}

.product-price-large .currency {
    font-size: 1.5rem;
    vertical-align: top;
    color: #666;
}

.product-price-large .amount-price {
    color: var(--purple-main);
}

.product-name-small {
    font-size: 0.9rem;
    color: #666;
    margin: 15px 0 0 0;
    line-height: 1.4;
}

.product-amount-footer {
    padding: 0 20px 20px 20px;
}

.btn-buy-amount {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 18px 30px;
    border-radius: 50px;
    font-size: 1.15rem;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(244, 196, 48, 0.3);
}

.btn-buy-amount:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(244, 196, 48, 0.5);
    background: linear-gradient(135deg, var(--gold-light), var(--gold-main));
    color: var(--purple-dark);
}

/* Back Button */
.btn-back {
    display: inline-flex;
    align-items: center;
    padding: 15px 35px;
    background: linear-gradient(135deg, #2E2370, #1F1851);
    color: white;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(46, 35, 112, 0.3);
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(46, 35, 112, 0.5);
    background: linear-gradient(135deg, #3d2f90, #2E2370);
    color: white;
}

/* Scroll Animation */
.scroll-animate {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.scroll-animate:nth-child(1) { animation-delay: 0.05s; }
.scroll-animate:nth-child(2) { animation-delay: 0.1s; }
.scroll-animate:nth-child(3) { animation-delay: 0.15s; }
.scroll-animate:nth-child(4) { animation-delay: 0.2s; }
.scroll-animate:nth-child(5) { animation-delay: 0.25s; }
.scroll-animate:nth-child(6) { animation-delay: 0.3s; }
.scroll-animate:nth-child(7) { animation-delay: 0.35s; }
.scroll-animate:nth-child(8) { animation-delay: 0.4s; }
.scroll-animate:nth-child(9) { animation-delay: 0.45s; }
.scroll-animate:nth-child(10) { animation-delay: 0.5s; }
</style>
@endpush
