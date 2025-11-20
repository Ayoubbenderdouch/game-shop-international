@extends('layouts.app')

@section('title', $baseName . ' - ' . (app()->getLocale() == 'ar' ? 'اختر البلد' : 'Select Country'))

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Header -->
    <div style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 2.8rem; font-weight: 800; color: var(--purple-main); margin-bottom: 15px;">
            {{ $baseName }}
        </h1>
        <p style="font-size: 1.3rem; color: #666; font-weight: 500;">
            {{ app()->getLocale() == 'ar' ? 'اختر البلد المتاح' : 'Select Available Country' }}
        </p>
    </div>

    <!-- Countries Grid -->
    <div class="countries-grid">
        @foreach($countries as $country)
            @if($country['products_count'] > 0)
            @php
                // Map product and country to gift card images (Only KSA, UAE, USA)
                $giftCardImages = [
                    'itunes' => [
                        'USA' => 'itunes usa.jpg',
                        'KSA' => 'itunes ksa.jpg',
                        'UAE' => 'itunes uau.jpg',
                    ],
                    'google-play' => [
                        'USA' => 'google play usa.jpg',
                        'KSA' => 'google play ksa.jpg',
                        'UAE' => 'goole play uae.jpg',
                    ],
                    'playstation' => [
                        'USA' => 'psn usa.jpg',
                        'KSA' => 'psn ksa.jpg',
                        'UAE' => 'psn uau.jpg',
                    ],
                    'xbox' => [
                        'USA' => 'xbox usa.jpg',
                        'KSA' => 'xbox ksa.jpg',
                        'UAE' => 'xbox uau.jpg',
                    ],
                    'steam' => [
                        'USA' => 'steam.jpg',
                        'KSA' => 'steam.jpg',
                        'UAE' => 'steam.jpg',
                    ],
                ];

                $countryImage = $giftCardImages[$productSlug][$country['name']] ?? null;
            @endphp

            @php
                // Get first product from this category to link directly
                $firstProduct = \App\Models\Product::where('category_id', $country['category_id'])
                    ->where('is_active', true)
                    ->where('is_available', true)
                    ->orderBy('selling_price', 'asc')
                    ->first();
            @endphp

            <a href="{{ $firstProduct ? route('product.show', $firstProduct->slug) : '#' }}"
               class="country-card-with-image scroll-animate">
                @if($countryImage)
                <div class="country-card-image">
                    <img src="{{ asset('images/GiftCard/' . $countryImage) }}"
                         alt="{{ $baseName }} - {{ $country['full_name'] }}"
                         loading="lazy">
                </div>
                @endif
                <div class="country-card-content">
                    <div class="country-flag-inline">
                        {{ $country['flag'] }}
                    </div>
                    <div class="country-info-inline">
                        <h3 class="country-name-inline">{{ $country['full_name'] }}</h3>
                        <p class="country-products-count-inline">
                            {{ $country['products_count'] }} {{ app()->getLocale() == 'ar' ? 'منتجات' : 'products' }}
                        </p>
                    </div>
                </div>
            </a>
            @endif
        @endforeach
    </div>

    @if(collect($countries)->where('products_count', '>', 0)->isEmpty())
    <div style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 5rem; margin-bottom: 20px;">⏳</div>
        <h2 style="font-size: 2rem; font-weight: 800; color: #2E2370; margin-bottom: 15px;">
            {{ app()->getLocale() == 'ar' ? 'قريباً' : 'Coming Soon' }}
        </h2>
        <p style="font-size: 1.1rem; color: #666;">
            {{ app()->getLocale() == 'ar' ? 'لا توجد منتجات متاحة حالياً' : 'No products available at the moment' }}
        </p>
    </div>
    @endif

    <!-- Back Button -->
    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ route('home') }}" class="btn-back">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 8px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
        </a>
    </div>

</div>

@endsection

@push('styles')
<style>
/* Countries Grid */
.countries-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    padding: 20px 0;
    max-width: 1400px;
    margin: 0 auto;
}

@media (max-width: 1024px) {
    .countries-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .countries-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

/* Country Card with Image */
.country-card-with-image {
    display: flex;
    flex-direction: column;
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 2px solid rgba(46, 35, 112, 0.1);
    border-radius: 20px;
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(46, 35, 112, 0.1);
    position: relative;
}

.country-card-with-image:hover {
    transform: translateY(-8px);
    border-color: var(--gold-main);
    box-shadow: 0 12px 35px rgba(244, 196, 48, 0.4);
}

.country-card-image {
    width: 100%;
    height: 350px;
    overflow: hidden;
    position: relative;
}

.country-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.country-card-with-image:hover .country-card-image img {
    transform: scale(1.05);
}

.country-card-content {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    background: white;
}

.country-flag-inline {
    font-size: 3rem;
    line-height: 1;
    flex-shrink: 0;
}

.country-info-inline {
    flex: 1;
}

.country-name-inline {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--purple-dark);
    margin: 0 0 5px 0;
}

.country-products-count-inline {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
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

.scroll-animate:nth-child(1) { animation-delay: 0.1s; }
.scroll-animate:nth-child(2) { animation-delay: 0.2s; }
.scroll-animate:nth-child(3) { animation-delay: 0.3s; }
.scroll-animate:nth-child(4) { animation-delay: 0.4s; }
.scroll-animate:nth-child(5) { animation-delay: 0.5s; }
.scroll-animate:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush
