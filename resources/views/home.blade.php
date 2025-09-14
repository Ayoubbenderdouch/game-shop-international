@extends('layouts.app')

@section('title', 'GameShop - Your Ultimate Gaming Destination')

@section('content')
<!-- Hero Section -->
<section class="w-full relative">
    <div class="swiper main-hero-slider">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="relative h-[500px] md:h-[600px] bg-gradient-to-br from-[#0b0e13] to-black">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(69,248,130,0.15),transparent_70%)]"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 h-full flex items-center">
                        <div class="relative z-10 w-full md:w-1/2">
                            <span class="inline-block px-4 py-2 bg-primary-blue/20 text-primary-blue text-sm font-semibold rounded-full mb-4">
                                Best Sellers
                            </span>
                            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
                                Level Up Your<br>Gaming Experience
                            </h1>
                            <p class="text-gray-400 text-lg mb-8">
                                Get instant access to game cards, gift cards, and premium subscriptions
                            </p>
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-8 py-4 bg-primary-blue text-black font-bold rounded-lg hover:bg-[#3fda74] transition-all duration-300 transform hover:scale-105">
                                Explore Collection
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="relative h-[500px] md:h-[600px] bg-gradient-to-br from-black to-[#0b0e13]">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(147,51,234,0.15),transparent_70%)]"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 h-full flex items-center">
                        <div class="relative z-10 w-full md:w-1/2">
                            <span class="inline-block px-4 py-2 bg-purple-500/20 text-purple-400 text-sm font-semibold rounded-full mb-4">
                                Hot Deals
                            </span>
                            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
                                Exclusive<br>Gaming Deals
                            </h1>
                            <p class="text-gray-400 text-lg mb-8">
                                Save big on your favorite games and digital content
                            </p>
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                View Deals
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="relative h-[500px] md:h-[600px] bg-gradient-to-br from-[#0b0e13] to-black">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(239,68,68,0.15),transparent_70%)]"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 h-full flex items-center">
                        <div class="relative z-10 w-full md:w-1/2">
                            <span class="inline-block px-4 py-2 bg-red-500/20 text-red-400 text-sm font-semibold rounded-full mb-4">
                                Lightning Fast
                            </span>
                            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
                                Instant<br>Delivery 24/7
                            </h1>
                            <p class="text-gray-400 text-lg mb-8">
                                Receive your codes instantly after purchase, automated delivery
                            </p>
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-500 to-orange-500 text-white font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                Get Started
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- Categories Section -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="text-center mb-12">
            <p class="text-primary-blue text-lg font-medium mb-2">Shop by Category</p>
            <h2 class="text-3xl md:text-4xl font-black text-white">Browse Our Collection</h2>
        </div>

        <div class="grid xl:grid-cols-8 lg:grid-cols-6 sm:grid-cols-5 grid-cols-4 sm:gap-5 gap-3">
            @php
            $categories = [
                ['name' => 'Steam', 'icon' => '🎮', 'color' => 'from-blue-500 to-cyan-500'],
                ['name' => 'PlayStation', 'icon' => '🎯', 'color' => 'from-blue-600 to-indigo-600'],
                ['name' => 'Xbox', 'icon' => '🎲', 'color' => 'from-green-500 to-emerald-500'],
                ['name' => 'Nintendo', 'icon' => '🎪', 'color' => 'from-red-500 to-pink-500'],
                ['name' => 'iTunes', 'icon' => '🎵', 'color' => 'from-purple-500 to-pink-500'],
                ['name' => 'Google Play', 'icon' => '📱', 'color' => 'from-green-400 to-blue-500'],
                ['name' => 'Netflix', 'icon' => '📺', 'color' => 'from-red-600 to-red-800'],
                ['name' => 'Amazon', 'icon' => '🛒', 'color' => 'from-orange-400 to-yellow-500'],
            ];
            @endphp

            @foreach($categories as $category)
            <a href="{{ route('shop', ['category' => strtolower($category['name'])]) }}"
               class="group text-center">
                <div class="w-full aspect-square bg-black border border-[#23262B] rounded-lg flex flex-col items-center justify-center hover:border-primary-blue transition-all duration-300 group-hover:scale-105">
                    <div class="text-3xl mb-2">{{ $category['icon'] }}</div>
                    <p class="text-xs text-gray-400 group-hover:text-white transition-colors">{{ $category['name'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Best Selling Products -->
<section class="w-full py-[60px] relative">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="flex justify-between items-center mb-8">
            <div>
                <p class="text-primary-blue text-lg font-medium">Top Picks</p>
                <h2 class="text-3xl md:text-4xl font-black text-white">Best Selling Products</h2>
            </div>
            <a href="{{ route('shop') }}" class="text-gray-400 hover:text-primary-blue transition-all">
                View All →
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            // This would normally come from the database
            $products = [
                ['name' => 'Steam Wallet $50', 'price' => 50, 'image' => null, 'discount' => 0],
                ['name' => 'PlayStation Plus 12 Months', 'price' => 59.99, 'image' => null, 'discount' => 10],
                ['name' => 'Xbox Game Pass Ultimate', 'price' => 44.99, 'image' => null, 'discount' => 15],
                ['name' => 'Nintendo eShop $25', 'price' => 25, 'image' => null, 'discount' => 0],
            ];
            @endphp

            @foreach($products as $product)
            <div class="product-card border border-[#3C3E42] rounded-lg overflow-hidden bg-black hover:border-primary-blue transition-all duration-300">
                <div class="relative h-48 bg-gradient-to-br from-[#23262B] to-black flex items-center justify-center">
                    @if($product['discount'] > 0)
                    <span class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                        -{{ $product['discount'] }}%
                    </span>
                    @endif
                    <div class="text-6xl">🎮</div>
                </div>
                <div class="p-4">
                    <h3 class="text-white font-semibold mb-2">{{ $product['name'] }}</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product['discount'] > 0)
                            <span class="text-gray-500 line-through text-sm">${{ number_format($product['price'], 2) }}</span>
                            <span class="text-primary-blue font-bold text-lg ml-2">
                                ${{ number_format($product['price'] * (1 - $product['discount']/100), 2) }}
                            </span>
                            @else
                            <span class="text-primary-blue font-bold text-lg">${{ number_format($product['price'], 2) }}</span>
                            @endif
                        </div>
                        <button class="p-2 bg-primary-blue text-black rounded hover:bg-[#3fda74] transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="w-full py-[60px] bg-black">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-blue/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-2">Instant Delivery</h3>
                <p class="text-gray-400 text-sm">Get your codes instantly after payment</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-blue/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-2">Secure Payment</h3>
                <p class="text-gray-400 text-sm">100% secure transactions with SSL</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-blue/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-2">Best Prices</h3>
                <p class="text-gray-400 text-sm">Competitive prices and regular discounts</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-blue/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-2">24/7 Support</h3>
                <p class="text-gray-400 text-sm">Customer support available anytime</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="relative bg-gradient-to-r from-primary-blue to-[#3fda74] rounded-2xl p-12 text-center overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-black text-black mb-4">
                    Ready to Start Gaming?
                </h2>
                <p class="text-black/80 text-lg mb-8">
                    Join thousands of gamers and get instant access to digital content
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('shop') }}" class="inline-flex items-center justify-center px-8 py-4 bg-black text-white font-bold rounded-lg hover:bg-gray-900 transition-all">
                        Browse Shop
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-black font-bold rounded-lg hover:bg-gray-100 transition-all">
                        Create Account
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Initialize Swiper
var swiper = new Swiper('.main-hero-slider', {
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    }
});
</script>
@endpush
