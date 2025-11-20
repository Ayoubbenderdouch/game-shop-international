@extends('layouts.app')

@section('title', 'Home - Gaming Store')

@section('content')

<!-- Gaming Banner mit Icons -->
<section class="w-full">
    <div style="background: linear-gradient(135deg, #FFC107 0%, #FFD54F 100%); padding: 50px 20px; position: relative; overflow: hidden; min-height: 200px;">
        <!-- Game Icons Pattern -->
        <div style="position: absolute; top: 0; left: -100px; width: calc(100% + 200px); height: 100%; display: flex; align-items: center; gap: 30px; opacity: 0.4;">
            @php
                $gameIcons = [
                    'images/catgorie/PUBG realod.png',
                    'images/catgorie/mobile legends.png',
                    'images/catgorie/FREE FIRE realod.png',
                    'images/catgorie/genshin impact realod.png',
                    'images/catgorie/yala ludo realod.png',
                    'images/catgorie/fc26.png',
                    'images/catgorie/steam realod.png',
                    'images/catgorie/play realod.png',
                    'images/catgorie/xbox realod.png',
                    'images/catgorie/google play realod.png',
                    'images/catgorie/itunes realod.png',
                    'images/catgorie/razer gpld realod.png'
                ];
            @endphp
            @foreach($gameIcons as $icon)
                @if(file_exists(public_path($icon)))
                    <div style="flex-shrink: 0; width: 100px; height: 100px; background: white; border-radius: 20px; padding: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <img src="{{ asset($icon) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Main Text -->
        <div style="position: relative; z-index: 1; text-align: center;">
            <h1 style="font-size: 4.5rem; font-weight: 900; color: #5E35B1; margin: 0; text-shadow: 3px 3px 6px rgba(0,0,0,0.2); letter-spacing: 2px;">
                {{ app()->getLocale() == 'ar' ? 'بطاقات الألعاب' : 'Gaming Cards' }}
            </h1>
            <p style="font-size: 1.2rem; color: #4A148C; margin-top: 10px; font-weight: 600;">
                {{ app()->getLocale() == 'ar' ? 'أفضل الأسعار - توصيل فوري' : 'Best Prices - Instant Delivery' }}
            </p>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section class="w-full relative">
    <!-- Swiper Hero Slider -->
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="relative min-h-[600px] flex items-center" style="background-image: url('https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2065'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 hero-overlay"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 w-full relative z-10">
                        <div class="max-w-xl">
                            <h1 class="text-5xl lg:text-6xl font-black text-white mb-6">
                                Level Up Your Gaming Experience
                            </h1>
                            <p class="text-xl text-gray-300 mb-8">
                                Get instant access to game cards, gift cards, and premium subscriptions
                            </p>
                            <a href="{{ route('shop') }}" class="btn-neon inline-flex items-center">
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
                <div class="relative min-h-[600px] flex items-center" style="background-image: url('https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 w-full relative z-10">
                        <div class="max-w-xl">
                            <h1 class="text-5xl lg:text-6xl font-black text-white mb-6">
                                Exclusive Gaming Deals
                            </h1>
                            <p class="text-xl text-gray-300 mb-8">
                                Save big on your favorite games and digital content
                            </p>
                            <a href="{{ route('shop') }}" class="btn-neon inline-flex items-center">
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
                <div class="relative min-h-[600px] flex items-center" style="background-image: url('https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2071'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 w-full relative z-10">
                        <div class="max-w-xl">
                            <h1 class="text-5xl lg:text-6xl font-black text-white mb-6">
                                Lightning Fast Delivery
                            </h1>
                            <p class="text-xl text-gray-300 mb-8">
                                Receive your codes instantly after purchase, 24/7 automated delivery
                            </p>
                            <a href="{{ route('shop') }}" class="btn-neon inline-flex items-center">
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

<!-- Gaming Gallery Section -->
<section class="w-full py-[40px] bg-gradient-to-br from-gray-900 via-purple-900/10 to-gray-900">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-4">
                Direct Top-Up Games
            </h2>
            <p class="text-gray-400 text-lg">Click any image to see alternate designs</p>
        </div>

        <div class="grid gap-8" style="display: grid; grid-template-columns: repeat(3, 1fr) !important;">
            <!-- PUBG Card -->
            <div class="game-card group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-500/20 to-orange-500/20 p-1">
                    <img id="pubg-image"
                         src="{{ asset('images/catgorie/pubg.jpg') }}"
                         alt="PUBG Mobile"
                         class="w-full h-64 object-cover rounded-xl cursor-pointer transition-transform duration-500"
                         data-image1="{{ asset('images/catgorie/pubg.jpg') }}"
                         data-image2="{{ asset('images/catgorie/PUBG realod.png') }}"
                         onclick="switchGameImage('pubg-image')">
                    <div class="absolute top-3 right-3 bg-black/70 text-yellow-400 px-3 py-1 rounded-full text-sm font-bold">
                        UC TOP-UP
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold mt-4 mb-2">PUBG Mobile</h3>
                <p class="text-gray-400 text-sm mb-4">Instant UC delivery to your account</p>
                <a href="{{ route('mazaya.game-selection', 'pubg-mobile-direct') }}"
                   class="block w-full text-center py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold rounded-lg hover:shadow-xl transition-all">
                    Top-Up Now →
                </a>
            </div>

            <!-- Mobile Legends Card -->
            <div class="game-card group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 p-1">
                    <img id="ml-image"
                         src="{{ asset('images/catgorie/mobile legends.png') }}"
                         alt="Mobile Legends"
                         class="w-full h-64 object-cover rounded-xl cursor-pointer transition-transform duration-500"
                         data-image1="{{ asset('images/catgorie/mobile legends.png') }}"
                         data-image2="{{ asset('images/catgorie/mobile-legends.jpg') }}"
                         onclick="switchGameImage('ml-image')">
                    <div class="absolute top-3 right-3 bg-black/70 text-blue-400 px-3 py-1 rounded-full text-sm font-bold">
                        DIAMONDS
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold mt-4 mb-2">Mobile Legends</h3>
                <p class="text-gray-400 text-sm mb-4">Get diamonds instantly via ID</p>
                <a href="{{ route('mazaya.game-selection', 'mobile-legends-direct') }}"
                   class="block w-full text-center py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-xl transition-all">
                    Top-Up Now →
                </a>
            </div>

            <!-- Free Fire Card -->
            <div class="game-card group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500/20 to-pink-500/20 p-1">
                    <img id="ff-image"
                         src="{{ asset('images/catgorie/FREE FIRE realod.png') }}"
                         alt="Free Fire"
                         class="w-full h-64 object-cover rounded-xl cursor-pointer transition-transform duration-500"
                         data-image1="{{ asset('images/catgorie/FREE FIRE realod.png') }}"
                         data-image2="{{ asset('images/catgorie/FREE FIRE CODE realod.png') }}"
                         onclick="switchGameImage('ff-image')">
                    <div class="absolute top-3 right-3 bg-black/70 text-red-400 px-3 py-1 rounded-full text-sm font-bold">
                        DIAMONDS
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold mt-4 mb-2">Free Fire</h3>
                <p class="text-gray-400 text-sm mb-4">Top-up diamonds directly to your ID</p>
                <a href="{{ route('mazaya.game-selection', 'free-fire-direct') }}"
                   class="block w-full text-center py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white font-bold rounded-lg hover:shadow-xl transition-all">
                    Top-Up Now →
                </a>
            </div>

            <!-- Genshin Impact Card -->
            <div class="game-card group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 p-1">
                    <img id="genshin-image"
                         src="{{ asset('images/catgorie/genshin impact realod.png') }}"
                         alt="Genshin Impact"
                         class="w-full h-64 object-cover rounded-xl cursor-pointer transition-transform duration-500"
                         data-image1="{{ asset('images/catgorie/genshin impact realod.png') }}"
                         data-image2="{{ asset('images/catgorie/genshin impact realod.png') }}"
                         onclick="switchGameImage('genshin-image')">
                    <div class="absolute top-3 right-3 bg-black/70 text-purple-400 px-3 py-1 rounded-full text-sm font-bold">
                        PRIMOGEMS
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold mt-4 mb-2">Genshin Impact</h3>
                <p class="text-gray-400 text-sm mb-4">Get Primogems & Genesis Crystals</p>
                <a href="{{ route('mazaya.game-selection', 'genshin-impact-direct') }}"
                   class="block w-full text-center py-3 bg-gradient-to-r from-purple-500 to-indigo-500 text-white font-bold rounded-lg hover:shadow-xl transition-all">
                    Top-Up Now →
                </a>
            </div>

            <!-- Yalla Ludo Card -->
            <div class="game-card group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500/20 to-teal-500/20 p-1">
                    <img id="yalla-image"
                         src="{{ asset('images/catgorie/yala ludo realod.png') }}"
                         alt="Yalla Ludo"
                         class="w-full h-64 object-cover rounded-xl cursor-pointer transition-transform duration-500"
                         data-image1="{{ asset('images/catgorie/yala ludo realod.png') }}"
                         data-image2="{{ asset('images/catgorie/yala ludo code realod.png') }}"
                         onclick="switchGameImage('yalla-image')">
                    <div class="absolute top-3 right-3 bg-black/70 text-green-400 px-3 py-1 rounded-full text-sm font-bold">
                        DIAMONDS
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold mt-4 mb-2">Yalla Ludo</h3>
                <p class="text-gray-400 text-sm mb-4">Instant diamond delivery</p>
                <a href="{{ route('mazaya.game-selection', 'yalla-ludo-direct') }}"
                   class="block w-full text-center py-3 bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold rounded-lg hover:shadow-xl transition-all">
                    Top-Up Now →
                </a>
            </div>

            <!-- FC Mobile Card -->
            <div class="game-card group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600/20 to-green-500/20 p-1">
                    <img id="fc-image"
                         src="{{ asset('images/catgorie/fc26.png') }}"
                         alt="FC Mobile"
                         class="w-full h-64 object-cover rounded-xl cursor-pointer transition-transform duration-500"
                         data-image1="{{ asset('images/catgorie/fc26.png') }}"
                         data-image2="{{ asset('images/catgorie/fc26.png') }}"
                         onclick="switchGameImage('fc-image')">
                    <div class="absolute top-3 right-3 bg-black/70 text-green-400 px-3 py-1 rounded-full text-sm font-bold">
                        FC POINTS
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold mt-4 mb-2">FC Mobile</h3>
                <p class="text-gray-400 text-sm mb-4">Get FC Points instantly</p>
                <a href="{{ route('mazaya.game-selection', 'fc-mobile-direct') }}"
                   class="block w-full text-center py-3 bg-gradient-to-r from-blue-600 to-green-500 text-white font-bold rounded-lg hover:shadow-xl transition-all">
                    Top-Up Now →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Gift Cards Section -->
<section class="w-full py-[40px] bg-gradient-to-br from-slate-900 to-gray-900">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-4">
                Gift Cards & Vouchers
            </h2>
            <p class="text-gray-400 text-lg">Get instant delivery of digital gift cards</p>
        </div>

        <div class="grid gap-6" style="display: grid; grid-template-columns: repeat(3, 1fr) !important;">
            <!-- PlayStation Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=playstation" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-600/20 to-blue-800/20 p-1">
                        <img src="{{ asset('images/catgorie/play realod.png') }}"
                             alt="PlayStation"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">PlayStation</h3>
                            <p class="text-gray-300 text-sm">Gift Cards</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Xbox Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=xbox" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-green-600/20 to-green-800/20 p-1">
                        <img src="{{ asset('images/catgorie/xbox realod.png') }}"
                             alt="Xbox"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Xbox</h3>
                            <p class="text-gray-300 text-sm">Gift Cards</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Steam Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=steam" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-600/20 to-slate-800/20 p-1">
                        <img src="{{ asset('images/catgorie/steam realod.png') }}"
                             alt="Steam"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Steam</h3>
                            <p class="text-gray-300 text-sm">Wallet Codes</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Razer Gold Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=razer" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-600/20 to-yellow-800/20 p-1">
                        <img src="{{ asset('images/catgorie/razer gpld realod.png') }}"
                             alt="Razer Gold"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Razer Gold</h3>
                            <p class="text-gray-300 text-sm">PIN Codes</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- More Gift Cards Row -->
        <div class="grid gap-6 mt-6" style="display: grid; grid-template-columns: repeat(3, 1fr) !important;">
            <!-- Google Play Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=google-play" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-red-600/20 to-orange-800/20 p-1">
                        <img src="{{ asset('images/catgorie/google play realod.png') }}"
                             alt="Google Play"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Google Play</h3>
                            <p class="text-gray-300 text-sm">Gift Cards</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- iTunes Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=itunes" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-pink-600/20 to-purple-800/20 p-1">
                        <img src="{{ asset('images/catgorie/itunes realod.png') }}"
                             alt="iTunes"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">iTunes</h3>
                            <p class="text-gray-300 text-sm">Gift Cards</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- PUBG UC Code Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=pubg-code" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-600/20 to-amber-800/20 p-1">
                        <img src="{{ asset('images/catgorie/PUBG CODE realod.png') }}"
                             alt="PUBG UC"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">PUBG UC</h3>
                            <p class="text-gray-300 text-sm">Redeem Codes</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Free Fire Code Card -->
            <div class="gift-card-item">
                <a href="{{ route('shop') }}?category=freefire-code" class="block">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-red-600/20 to-red-800/20 p-1">
                        <img src="{{ asset('images/catgorie/FREE FIRE CODE realod.png') }}"
                             alt="Free Fire Diamonds"
                             class="w-full h-48 object-cover rounded-lg">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4">
                            <h3 class="text-white font-bold text-lg">Free Fire</h3>
                            <p class="text-gray-300 text-sm">Diamond Codes</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Shop by Category Section with Navigation Component -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="text-center mb-12">
            <p class="text-[#49b8ef] text-lg font-medium mb-2">Explore Categories</p>
            <h2 class="text-3xl md:text-4xl font-black gradient-text">Shop by Category</h2>
        </div>
    </div>

    <!-- Category Navigation Component -->
    @include('components.category-navigation')
</section>

<!-- Best Selling Products -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="w-full py-[60px] relative">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="flex justify-between items-center mb-8">
            <div>
                <p class="text-[#49b8ef] text-lg font-medium">Top Picks</p>
                <h2 class="text-3xl md:text-4xl font-black gradient-text">Best Selling Products</h2>
            </div>
            <a href="{{ route('shop') }}" class="text-gray-400 hover:text-[#49b8ef] transition-all">
                View All →
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid gap-6" style="display: grid; grid-template-columns: repeat(3, 1fr) !important;">
            @foreach($featuredProducts as $product)
            <div class="product-card-enhanced">
                <a href="{{ route('product.show', $product->slug) }}" class="block">
                    <div class="product-image-wrapper relative h-48 bg-gradient-to-br from-[#23262B] to-black flex items-center justify-center group">
                        @if($product->discount_percentage > 0)
                        <span class="absolute top-3 right-3 px-3 py-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full z-10 badge-pulse">
                            -{{ $product->discount_percentage }}%
                        </span>
                        @endif

                        @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}"
                             class="product-image w-full h-full object-cover">
                        @else
                        <div class="text-6xl float-animation">🎮</div>
                        @endif
                    </div>
                </a>

                <div class="p-4">
                    <h3 class="text-white font-semibold mb-2 line-clamp-2">{{ $product->name }}</h3>

                    <!-- Rating -->
                    @if($product->reviews_count > 0)
                    <div class="flex items-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->reviews_avg_rating ?? 0))
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endif
                        @endfor
                        <span class="text-xs text-gray-400 ml-1">({{ $product->reviews_count }})</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->original_price && $product->original_price > $product->selling_price)
                            <span class="text-gray-500 line-through text-sm">${{ number_format($product->original_price, 2) }}</span>
                            <span class="text-[#49b8ef] font-bold text-lg ml-1">${{ number_format($product->selling_price, 2) }}</span>
                            @else
                            <span class="text-[#49b8ef] font-bold text-lg">${{ number_format($product->selling_price, 2) }}</span>
                            @endif
                        </div>

                        @if($product->is_available)
                        <button onclick="addToCart({{ $product->id }})"
                                class="px-3 py-1 bg-[#49b8ef] text-black text-sm font-semibold rounded hover:bg-[#3da2d4] transition-all">
                            Add
                        </button>
                        @else
                        <span class="text-xs text-red-400">Out of Stock</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Stats Section -->
@if(isset($stats))
<section class="w-full py-[60px] bg-black/50">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="grid md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-4xl font-black text-[#49b8ef] mb-2">{{ number_format($stats['total_customers'] ?? 1000) }}+</div>
                <p class="text-gray-400">Happy Customers</p>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-[#49b8ef] mb-2">{{ number_format($stats['total_products'] ?? 500) }}+</div>
                <p class="text-gray-400">Digital Products</p>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-[#49b8ef] mb-2">24/7</div>
                <p class="text-gray-400">Instant Delivery</p>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-[#49b8ef] mb-2">{{ $stats['countries_served'] ?? 50 }}+</div>
                <p class="text-gray-400">Countries Served</p>
            </div>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="bg-gradient-to-r from-[#49b8ef] to-[#3da2d4] rounded-2xl p-12 text-center">
            <h2 class="text-4xl font-black text-black mb-4">Ready to Level Up?</h2>
            <p class="text-xl text-black/80 mb-8 max-w-2xl mx-auto">
                Join thousands of gamers who trust us for their digital gaming needs. Start shopping now and get instant delivery!
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-black text-white font-bold rounded-lg hover:bg-gray-900 transition-all duration-300 transform hover:scale-105">
                Create Account
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
/* Force 3 Columns Layout - HIGHEST PRIORITY */
.grid,
[class*="grid"] {
    display: grid !important;
}

.grid-cols-3,
.grid[style*="grid-template-columns"] {
    grid-template-columns: repeat(3, 1fr) !important;
}

/* Override ALL media queries */
@media (max-width: 1024px) {
    .grid[style*="grid-template-columns"] {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .grid[style*="grid-template-columns"] {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 480px) {
    .grid[style*="grid-template-columns"] {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

/* Game Card Styles */
.game-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.game-card:hover {
    transform: translateY(-10px);
}

.game-card img {
    transition: all 0.3s ease;
}

.game-card:hover img {
    transform: scale(1.05);
}

/* Gift Card Styles */
.gift-card-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gift-card-item:hover {
    transform: translateY(-5px);
}

.gift-card-item img {
    transition: transform 0.3s ease;
}

.gift-card-item:hover img {
    transform: scale(1.1);
}

/* Gradient Text */
.gradient-text {
    background: linear-gradient(135deg, #49b8ef, #3da2d4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
// Initialize Hero Swiper
const heroSwiper = new Swiper('.hero-swiper', {
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

// Game Image Switcher
let imageStates = {};

function switchGameImage(imageId) {
    const img = document.getElementById(imageId);
    const image1 = img.getAttribute('data-image1');
    const image2 = img.getAttribute('data-image2');

    // Initialize state if not exists
    if (!imageStates[imageId]) {
        imageStates[imageId] = 1;
    }

    // Switch images with animation
    img.style.opacity = '0';
    img.style.transform = 'scale(0.9)';

    setTimeout(() => {
        if (imageStates[imageId] === 1) {
            img.src = image2;
            imageStates[imageId] = 2;
        } else {
            img.src = image1;
            imageStates[imageId] = 1;
        }

        img.style.opacity = '1';
        img.style.transform = 'scale(1)';
    }, 200);

    // Add transition effect
    img.style.transition = 'all 0.3s ease';

    // Optional: Show notification
    // showToast('Image switched!', 'success');
}

// Add to cart function
@auth
function addToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showNotification('Product added to cart!', 'success');
            updateCartCount();
        } else {
            showNotification(data.message || 'Failed to add product', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}
@else
function addToCart(productId) {
    window.location.href = "{{ route('login') }}";
}
@endauth
</script>
@endpush
