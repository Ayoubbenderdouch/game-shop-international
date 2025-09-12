@extends('layouts.app')

@section('title', __('app.nav.home') . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
    <!-- Hero Section with Modern Carousel -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        <!-- Dynamic Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-900/90 to-slate-950/95"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(73,186,238,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(147,51,234,0.1),transparent_50%)]"></div>
            <!-- Animated gradient orbs -->
            <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <!-- Main Carousel Container -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative" id="hero-carousel" x-data="carousel()">
                <!-- Carousel Slides -->
                <div class="relative h-[500px] md:h-[600px] overflow-hidden rounded-2xl">
                    @php
                        $slides = [
                            [
                                'title' => __('app.home.hero_title_1'),
                                'subtitle' => __('app.home.hero_subtitle_1'),
                                'badge' => __('app.home.best_sellers'),
                                'image' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1200&h=600&fit=crop',
                                'gradient' => 'from-cyan-600 to-blue-600'
                            ],
                            [
                                'title' => __('app.home.hero_title_2'),
                                'subtitle' => __('app.home.hero_subtitle_2'),
                                'badge' => __('app.home.trending'),
                                'image' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=1200&h=600&fit=crop',
                                'gradient' => 'from-purple-600 to-pink-600'
                            ],
                            [
                                'title' => __('app.home.hero_title_3'),
                                'subtitle' => __('app.home.hero_subtitle_3'),
                                'badge' => __('app.home.hot_deals'),
                                'image' => 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=1200&h=600&fit=crop',
                                'gradient' => 'from-orange-600 to-red-600'
                            ]
                        ];
                    @endphp

                    @foreach($slides as $index => $slide)
                    <div class="absolute inset-0 transition-all duration-700 ease-in-out"
                         x-show="currentSlide === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 translate-x-full"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-700"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-full">
                        <div class="relative w-full h-full">
                            <img src="{{ $slide['image'] }}"
                                 alt="{{ $slide['title'] }}"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/70 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-r {{ $slide['gradient'] }} opacity-20"></div>

                            <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 lg:p-16">
                                <span class="inline-block px-4 py-2 bg-gradient-to-r {{ $slide['gradient'] }} text-white font-bold rounded-lg text-sm mb-4 animate-bounce">
                                    {{ $slide['badge'] }}
                                </span>
                                <h1 class="text-3xl md:text-5xl lg:text-6xl font-black text-white mb-4 leading-tight">
                                    {{ $slide['title'] }}
                                </h1>
                                <p class="text-lg md:text-xl text-slate-300 mb-8 max-w-2xl">
                                    {{ $slide['subtitle'] }}
                                </p>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('shop') }}"
                                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r {{ $slide['gradient'] }} text-white font-bold rounded-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                                        {{ __('app.home.explore_collection') }}
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                    <a href="#featured"
                                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300">
                                        {{ __('app.home.view_deals') }}
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Navigation Controls -->
                <button @click="previousSlide()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-3 bg-white/10 backdrop-blur-sm rounded-full text-white hover:bg-white/20 transition-all group">
                    <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button @click="nextSlide()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-3 bg-white/10 backdrop-blur-sm rounded-full text-white hover:bg-white/20 transition-all group">
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Slide Indicators -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                    @for($i = 0; $i < count($slides); $i++)
                    <button @click="currentSlide = {{ $i }}"
                            :class="currentSlide === {{ $i }} ? 'w-8 bg-white' : 'w-2 bg-white/50 hover:bg-white/70'"
                            class="h-2 rounded-full transition-all duration-300"></button>
                    @endfor
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <div class="flex items-center justify-center gap-3 p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10">
                    <svg class="w-6 h-6 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-white font-bold">{{ __('app.home.instant_delivery') }}</p>
                        <p class="text-slate-400 text-xs">24/7 Auto Delivery</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10">
                    <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <div>
                        <p class="text-white font-bold">{{ __('app.home.trusted_service') }}</p>
                        <p class="text-slate-400 text-xs">50K+ Happy Customers</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10">
                    <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-white font-bold">{{ __('app.home.secure_payment') }}</p>
                        <p class="text-slate-400 text-xs">100% Secure Checkout</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10">
                    <svg class="w-6 h-6 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                    </svg>
                    <div>
                        <p class="text-white font-bold">{{ __('app.home.best_prices') }}</p>
                        <p class="text-slate-400 text-xs">Guaranteed Best Deals</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $stats = [
                    ['number' => '50K+', 'label' => __('app.home.stats.happy_customers'), 'icon' => 'users', 'color' => 'cyan'],
                    ['number' => '100+', 'label' => __('app.home.stats.digital_products'), 'icon' => 'package', 'color' => 'purple'],
                    ['number' => '24/7', 'label' => __('app.home.stats.instant_delivery_247'), 'icon' => 'clock', 'color' => 'green'],
                    ['number' => '30+', 'label' => __('app.home.stats.countries_served'), 'icon' => 'globe', 'color' => 'orange'],
                ];
            @endphp

            @foreach($stats as $stat)
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600 rounded-2xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
                <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-{{ $stat['color'] }}-500/50 transition-all">
                    <div class="w-12 h-12 mx-auto mb-4 bg-gradient-to-br from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600 rounded-xl flex items-center justify-center">
                        @if($stat['icon'] == 'users')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        @elseif($stat['icon'] == 'package')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        @elseif($stat['icon'] == 'clock')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        @else
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"></path>
                        </svg>
                        @endif
                    </div>
                    <h3 class="text-3xl font-black text-white mb-1">{{ $stat['number'] }}</h3>
                    <p class="text-slate-400 text-sm">{{ $stat['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                    {{ __('app.home.shop_by_category') }}
                </span>
            </h2>
            <p class="text-slate-400 text-lg">{{ __('app.home.browse_categories_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($categories as $category)
            <a href="{{ route('shop', ['category' => $category->slug]) }}"
               class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-purple-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-cyan-500/50 transition-all hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-cyan-500 to-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        @if(str_contains($category->slug, 'game'))
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z"></path>
                        </svg>
                        @elseif(str_contains($category->slug, 'gift'))
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"></path>
                            <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"></path>
                        </svg>
                        @elseif(str_contains($category->slug, 'subscription'))
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"></path>
                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"></path>
                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"></path>
                        </svg>
                        @else
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        @endif
                    </div>
                    <h3 class="font-bold text-white mb-1">{{ $category->name }}</h3>
                    <p class="text-slate-400 text-sm">
                        @if($category->products_count > 0)
                            {{ $category->products_count }} {{ __('app.home.products') }}
                        @else
                            {{ __('app.home.browse_collection') }}
                        @endif
                    </p>
                </div>
            </a>
            @endforeach

            <!-- Special PUBG UC Category -->
            <a href="{{ route('pubg-uc') }}"
               class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity animate-pulse"></div>
                <div class="relative bg-gradient-to-br from-yellow-900/30 to-orange-900/30 backdrop-blur-sm border-2 border-yellow-500/50 rounded-2xl p-6 text-center hover:border-yellow-400 transition-all hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-yellow-400 mb-1">PUBG UC Top-Up</h3>
                    <p class="text-yellow-300/70 text-sm">Instant Delivery</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-2">
                    🔥 {{ __('app.home.hot_deals') }}
                </h2>
                <p class="text-slate-400">{{ __('app.home.best_selling_products') }}</p>
            </div>
            <a href="{{ route('shop') }}"
               class="hidden md:flex items-center gap-2 text-cyan-400 hover:text-cyan-300 transition-colors font-semibold group">
                {{ __('app.home.view_all_products') }}
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featuredProducts as $product)
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-purple-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden hover:border-cyan-500/50 transition-all">
                    @if($product->image_url)
                    <div class="aspect-w-16 aspect-h-9 bg-slate-800">
                        <img src="{{ $product->image_url }}"
                             alt="{{ $product->name }}"
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                        <svg class="w-16 h-16 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    @endif

                    <div class="p-6">
                        <h3 class="font-bold text-white mb-2 group-hover:text-cyan-400 transition-colors line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-slate-400 mb-1">{{ $product->category->name }}</p>

                        @if($product->reviews_count > 0)
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($product->reviews_avg_rating ?? 0) ? 'text-yellow-400' : 'text-slate-600' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-400">({{ $product->reviews_count }})</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                @if($product->original_price && $product->original_price > $product->selling_price)
                                <span class="text-sm text-slate-500 line-through">${{ number_format($product->original_price, 2) }}</span>
                                @endif
                                <span class="text-2xl font-black text-cyan-400">${{ number_format($product->selling_price, 2) }}</span>
                            </div>
                            @if($product->discount_percentage > 0)
                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                                -{{ $product->discount_percentage }}%
                            </span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="flex-1 text-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors">
                                {{ __('app.shop.view_details') }}
                            </a>
                            @auth
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-lg hover:scale-105 transition-all">
                                    {{ __('app.shop.add_to_cart') }}
                                </button>
                            </form>
                            @else
                            <a href="{{ route('login') }}"
                               class="flex-1 text-center px-4 py-2 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-lg hover:scale-105 transition-all">
                                {{ __('app.shop.add_to_cart') }}
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-slate-400">{{ __('app.home.no_products_available') }}</p>
            </div>
            @endforelse
        </div>

        <!-- Mobile View All Link -->
        <div class="mt-8 text-center md:hidden">
            <a href="{{ route('shop') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                {{ __('app.home.view_all_products') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
                    {{ __('app.home.why_choose_us') }}
                </span>
            </h2>
            <p class="text-slate-400 text-lg">{{ __('app.home.why_choose_subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $features = [
                    [
                        'icon' => 'lightning',
                        'title' => __('app.home.instant_delivery'),
                        'description' => __('app.home.instant_delivery_desc'),
                        'gradient' => 'from-cyan-500 to-blue-500'
                    ],
                    [
                        'icon' => 'shield',
                        'title' => __('app.home.secure_payment'),
                        'description' => __('app.home.secure_payment_desc'),
                        'gradient' => 'from-purple-500 to-pink-500'
                    ],
                    [
                        'icon' => 'support',
                        'title' => __('app.home.24_7_support'),
                        'description' => __('app.home.24_7_support_desc'),
                        'gradient' => 'from-green-500 to-teal-500'
                    ],
                    [
                        'icon' => 'price',
                        'title' => __('app.home.best_prices'),
                        'description' => __('app.home.best_prices_desc'),
                        'gradient' => 'from-orange-500 to-red-500'
                    ],
                    [
                        'icon' => 'global',
                        'title' => __('app.home.global_coverage'),
                        'description' => __('app.home.global_coverage_desc'),
                        'gradient' => 'from-indigo-500 to-purple-500'
                    ],
                    [
                        'icon' => 'warranty',
                        'title' => __('app.home.money_back'),
                        'description' => __('app.home.money_back_desc'),
                        'gradient' => 'from-pink-500 to-rose-500'
                    ]
                ];
            @endphp

            @foreach($features as $feature)
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r {{ $feature['gradient'] }} rounded-2xl blur-xl opacity-10 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 hover:border-slate-700 transition-all">
                    <div class="w-12 h-12 mb-4 bg-gradient-to-r {{ $feature['gradient'] }} rounded-xl flex items-center justify-center">
                        @if($feature['icon'] == 'lightning')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        @elseif($feature['icon'] == 'shield')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        @elseif($feature['icon'] == 'support')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"></path>
                        </svg>
                        @elseif($feature['icon'] == 'price')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                        @elseif($feature['icon'] == 'global')
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"></path>
                        </svg>
                        @else
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $feature['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/95 to-slate-900 border border-slate-800 p-8 md:p-12 lg:p-16">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(147,51,234,0.2),transparent_70%)]"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                    </svg>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-4">
                    {{ __('app.home.ready_to_level_up') }}
                </h2>
                <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                    {{ __('app.home.level_up_description') }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('shop') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold rounded-xl hover:shadow-2xl hover:scale-105 transition-all">
                        {{ __('app.home.get_started_now') }}
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    @guest
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                        {{ __('app.home.create_account') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
function carousel() {
    return {
        currentSlide: 0,
        slides: 3,
        autoPlayInterval: null,
        init() {
            this.startAutoPlay();
        },
        startAutoPlay() {
            this.autoPlayInterval = setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        stopAutoPlay() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
            }
        },
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.slides;
        },
        previousSlide() {
            this.currentSlide = this.currentSlide === 0 ? this.slides - 1 : this.currentSlide - 1;
        },
        goToSlide(index) {
            this.currentSlide = index;
            this.stopAutoPlay();
            this.startAutoPlay();
        }
    }
}

// Intersection Observer for animations
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                entry.target.style.opacity = '1';
            }
        });
    }, observerOptions);

    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
        section.style.opacity = '0';
        observer.observe(section);
    });
});
</script>
@endpush

@push('styles')
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.8s ease-out forwards;
}

.delay-1000 {
    animation-delay: 1s;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #1e293b;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #06b6d4, #a855f7);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #0891b2, #9333ea);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection
