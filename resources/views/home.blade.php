@extends('layouts.app')

@section('title', __('home.title'))

@section('content')

<!-- Hero Section mit Hellblauem Design -->
<section class="w-full relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC41IiBvcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>

    <div class="max-w-[1200px] mx-auto px-5 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 class="text-5xl lg:text-7xl font-black text-white mb-6 animate-fade-in">
                {{ __('home.hero.title') }}
            </h1>
            <p class="text-xl lg:text-2xl text-blue-100 mb-10 max-w-3xl mx-auto">
                {{ __('home.hero.subtitle') }}
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('shop') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                    <span class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        {{ __('home.hero.shop_now') }}
                    </span>
                </a>
                <a href="{{ route('shop') }}" class="px-8 py-4 bg-blue-700 text-white font-bold rounded-xl hover:bg-blue-800 transition-all duration-300 border-2 border-white/20">
                    {{ __('home.hero.browse') }}
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16">
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-2">{{ \App\Models\Product::count() }}+</div>
                <div class="text-blue-100">{{ __('home.stats.products') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-2">{{ \App\Models\Category::count() }}+</div>
                <div class="text-blue-100">{{ __('home.stats.categories') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-2">24/7</div>
                <div class="text-blue-100">{{ __('home.stats.support') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-2">⚡</div>
                <div class="text-blue-100">{{ __('home.stats.instant') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="w-full py-16 bg-gray-50">
    <div class="max-w-[1200px] mx-auto px-5 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-2">
                    {{ __('home.featured.title') }}
                </h2>
                <p class="text-gray-600">{{ __('home.featured.subtitle') }}</p>
            </div>
            <a href="{{ route('shop') }}" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                {{ __('home.view_all') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @php
                $featuredProducts = \App\Models\Product::with('category')
                    ->where('is_active', true)
                    ->where('available', true)
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get();
            @endphp

            @forelse($featuredProducts as $product)
                <a href="{{ route('product.show', $product->id) }}" class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <div class="aspect-square bg-gradient-to-br from-blue-100 to-blue-200 relative overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($product->discount > 0)
                            <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                -{{ $product->discount }}%
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-black text-blue-600">
                                    {{ number_format($product->sell_price, 2) }} {{ $product->currency }}
                                </div>
                                @if($product->original_price && $product->original_price > $product->sell_price)
                                    <div class="text-sm text-gray-400 line-through">
                                        {{ number_format($product->original_price, 2) }} {{ $product->currency }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-gray-500">{{ __('home.no_products') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Top Categories Section -->
<section class="w-full py-16 bg-white">
    <div class="max-w-[1200px] mx-auto px-5 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-3">
                {{ __('home.categories.title') }}
            </h2>
            <p class="text-gray-600 text-lg">{{ __('home.categories.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $topCategories = \App\Models\Category::whereNull('parent_id')
                    ->withCount('products')
                    ->having('products_count', '>', 0)
                    ->orderBy('products_count', 'desc')
                    ->take(12)
                    ->get();
            @endphp

            @forelse($topCategories as $category)
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="group">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 text-center hover:from-blue-500 hover:to-blue-600 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-16 h-16 mx-auto mb-3 object-contain group-hover:scale-110 transition-transform">
                        @else
                            <div class="w-16 h-16 mx-auto mb-3 bg-blue-200 rounded-full flex items-center justify-center group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        @endif
                        <h3 class="font-bold text-gray-900 group-hover:text-white transition-colors text-sm">
                            {{ $category->name }}
                        </h3>
                        <p class="text-xs text-gray-600 group-hover:text-blue-100 transition-colors mt-1">
                            {{ $category->products_count }} {{ __('home.products') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">{{ __('home.no_categories') }}</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                {{ __('home.browse_all_categories') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Best Selling Products -->
<section class="w-full py-16 bg-gradient-to-br from-blue-50 to-blue-100">
    <div class="max-w-[1200px] mx-auto px-5 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-3">
                🔥 {{ __('home.bestsellers.title') }}
            </h2>
            <p class="text-gray-600 text-lg">{{ __('home.bestsellers.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $bestSellers = \App\Models\Product::with('category')
                    ->where('is_active', true)
                    ->where('available', true)
                    ->take(6)
                    ->get();
            @endphp

            @forelse($bestSellers as $product)
                <a href="{{ route('product.show', $product->id) }}" class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex gap-4 p-4">
                        <div class="w-24 h-24 flex-shrink-0 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $product->name }}
                            </h3>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-black text-blue-600">
                                    {{ number_format($product->sell_price, 2) }}
                                </span>
                                <span class="text-gray-600 font-semibold">{{ $product->currency }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">{{ __('home.no_products') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section class="w-full py-12 bg-white">
    <div class="max-w-[1200px] mx-auto px-5 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('home.trust.instant.title') }}</h3>
                <p class="text-sm text-gray-600">{{ __('home.trust.instant.desc') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('home.trust.secure.title') }}</h3>
                <p class="text-sm text-gray-600">{{ __('home.trust.secure.desc') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('home.trust.support.title') }}</h3>
                <p class="text-sm text-gray-600">{{ __('home.trust.support.desc') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ __('home.trust.price.title') }}</h3>
                <p class="text-sm text-gray-600">{{ __('home.trust.price.desc') }}</p>
            </div>
        </div>
    </div>
</section>

@endsection
