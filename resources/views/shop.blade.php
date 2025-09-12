@extends('layouts.app')

@section('title', __('app.nav.shop') . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
    <!-- Dynamic Background Effects -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-900/90 to-slate-950/95"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(73,186,238,0.1),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(147,51,234,0.08),transparent_50%)]"></div>
        <!-- Animated gradient orbs -->
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-cyan-500/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/3 left-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>

    <!-- Page Header -->
    <section class="relative pt-24 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-4">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400">
                        {{ __('app.shop.title') }}
                    </span>
                </h1>
                <p class="text-slate-400 text-lg">Discover amazing digital products at unbeatable prices</p>
            </div>
        </div>
    </section>

    <!-- Filters and Search Section -->
    <section class="relative py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6">
                <form method="GET" action="{{ route('shop') }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Search Input -->
                        <div class="md:col-span-2">
                            <div class="relative">
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('app.shop.search_placeholder') }}"
                                       class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select name="category"
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all appearance-none cursor-pointer">
                                <option value="">{{ __('app.categories.all') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->products_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div>
                            <select name="sort"
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all appearance-none cursor-pointer">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('app.shop.newest') }}</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('app.shop.price_low_high') }}</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('app.shop.price_high_low') }}</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('app.shop.most_popular') }}</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('app.shop.best_rated') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters -->
                    @if(request()->anyFilled(['search', 'category', 'sort']))
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-slate-400 text-sm">Active filters:</span>

                        @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-cyan-500/20 text-cyan-400 rounded-lg text-sm">
                            Search: {{ request('search') }}
                            <a href="{{ route('shop', array_merge(request()->except('search'), ['category' => request('category'), 'sort' => request('sort')])) }}" class="hover:text-cyan-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                        @endif

                        @if(request('category'))
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-500/20 text-purple-400 rounded-lg text-sm">
                            Category: {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}
                            <a href="{{ route('shop', array_merge(request()->except('category'), ['search' => request('search'), 'sort' => request('sort')])) }}" class="hover:text-purple-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                        @endif

                        <a href="{{ route('shop') }}" class="text-slate-400 hover:text-white text-sm underline">Clear all</a>
                    </div>
                    @endif

                    <!-- Submit Button (for non-JS browsers) -->
                    <noscript>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-lg transition-all">
                            Apply Filters
                        </button>
                    </noscript>
                </form>
            </div>
        </div>
    </section>

    <!-- Categories Quick Access -->
    <section class="relative py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center gap-4 overflow-x-auto pb-4 scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-slate-900">
                <a href="{{ route('shop') }}"
                   class="flex-shrink-0 px-6 py-3 {{ !request('category') ? 'bg-gradient-to-r from-cyan-500 to-purple-500 text-white' : 'bg-slate-800/50 text-slate-400 hover:text-white border border-slate-700' }} rounded-xl font-semibold transition-all">
                    All Products
                </a>
                @foreach($categories->take(6) as $category)
                <a href="{{ route('shop', ['category' => $category->slug]) }}"
                   class="flex-shrink-0 px-6 py-3 {{ request('category') == $category->slug ? 'bg-gradient-to-r from-cyan-500 to-purple-500 text-white' : 'bg-slate-800/50 text-slate-400 hover:text-white border border-slate-700' }} rounded-xl font-semibold transition-all">
                    {{ $category->name }}
                </a>
                @endforeach
                <a href="{{ route('pubg-uc') }}"
                   class="flex-shrink-0 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all animate-pulse">
                    🔥 PUBG UC
                </a>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="relative py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Results Count -->
            <div class="mb-6 flex items-center justify-between">
                <p class="text-slate-400">
                    Showing <span class="text-white font-semibold">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span>
                    of <span class="text-white font-semibold">{{ $products->total() }}</span> products
                </p>

                <!-- View Toggle (Grid/List) -->
                <div class="flex items-center gap-2" x-data="{ view: 'grid' }">
                    <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-slate-700 text-white' : 'bg-slate-800/50 text-slate-400'" class="p-2 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button @click="view = 'list'" :class="view === 'list' ? 'bg-slate-700 text-white' : 'bg-slate-800/50 text-slate-400'" class="p-2 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Products Grid/List -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($products as $product)
                <div class="group relative product-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <!-- Hover Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-purple-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>

                    <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden hover:border-cyan-500/50 transition-all duration-300 transform hover:scale-[1.02]">
                        <!-- Product Badge -->
                        @if($product->is_available && $product->stock_quantity > 0 && $product->stock_quantity <= 10)
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-lg">
                                Only {{ $product->stock_quantity }} left!
                            </span>
                        </div>
                        @elseif(!$product->is_available || $product->stock_quantity === 0)
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                                {{ __('app.shop.out_of_stock') }}
                            </span>
                        </div>
                        @elseif($product->created_at->diffInDays(now()) <= 7)
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-3 py-1 bg-gradient-to-r from-cyan-500 to-purple-500 text-white text-xs font-bold rounded-lg">
                                NEW
                            </span>
                        </div>
                        @endif

                        <!-- Discount Badge -->
                        @php
                            $discountPercentage = 0;
                            if($product->cost_price > 0) {
                                $originalPrice = $product->cost_price * 1.5; // Assumed original markup
                                if($originalPrice > $product->selling_price) {
                                    $discountPercentage = round((($originalPrice - $product->selling_price) / $originalPrice) * 100);
                                }
                            }
                        @endphp
                        @if($discountPercentage > 0)
                        <div class="absolute top-4 right-4 z-10">
                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-lg animate-pulse">
                                -{{ $discountPercentage }}%
                            </span>
                        </div>
                        @endif

                        <!-- Product Image -->
                        <div class="relative h-48 bg-gradient-to-br from-slate-800 to-slate-900 overflow-hidden">
                            @if($product->image)
                            <img src="{{ $product->image }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 loading="lazy">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            @endif

                            <!-- Quick View Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all transform -translate-y-4 group-hover:translate-y-0">
                                    Quick View
                                </a>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6">
                            <!-- Category -->
                            <p class="text-xs text-cyan-400 font-semibold mb-2">{{ $product->category->name }}</p>

                            <!-- Product Name -->
                            <h3 class="font-bold text-white mb-2 group-hover:text-cyan-400 transition-colors line-clamp-2 min-h-[3rem]">
                                {{ $product->name }}
                            </h3>

                            <!-- Rating -->
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
                            @else
                            <div class="h-7 mb-3"></div>
                            @endif

                            <!-- Price -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    @if($discountPercentage > 0)
                                    <span class="text-sm text-slate-500 line-through">${{ number_format($product->cost_price * 1.5, 2) }}</span>
                                    @endif
                                    <p class="text-2xl font-black">
                                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                                            ${{ number_format($product->selling_price, 2) }}
                                        </span>
                                    </p>
                                </div>
                                @if($product->vat_percentage > 0)
                                <span class="text-xs text-slate-500">+{{ $product->vat_percentage }}% VAT</span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="flex-1 text-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-all">
                                    {{ __('app.shop.view_details') }}
                                </a>

                                @if($product->is_available && ($product->stock_quantity === null || $product->stock_quantity > 0))
                                    @auth
                                    <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                                class="w-full px-4 py-2 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-lg hover:scale-105 transition-all">
                                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('login') }}"
                                       class="flex-1 text-center px-4 py-2 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-lg hover:scale-105 transition-all">
                                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </a>
                                    @endauth
                                @else
                                <button disabled
                                        class="flex-1 text-center px-4 py-2 bg-slate-800/50 text-slate-500 rounded-lg cursor-not-allowed">
                                    {{ __('app.shop.out_of_stock') }}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="text-center py-16">
                        <svg class="w-24 h-24 mx-auto text-slate-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-white mb-2">{{ __('app.shop.no_products_found') }}</h3>
                        <p class="text-slate-400 mb-6">Try adjusting your filters or search terms</p>
                        <a href="{{ route('shop') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-xl hover:shadow-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Browse All Products
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-12">
                <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6">
                    {{ $products->withQueryString()->links('pagination.custom') }}
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Newsletter CTA -->
    <section class="relative py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-900/30 via-slate-900/50 to-cyan-900/30 backdrop-blur-sm border border-slate-800 p-8 md:p-12">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(147,51,234,0.15),transparent_70%)]"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl"></div>

                <div class="relative z-10 text-center max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-black text-white mb-4">
                        Stay Updated with New Arrivals
                    </h2>
                    <p class="text-slate-400 text-lg mb-8">
                        Get exclusive deals and be the first to know about new products
                    </p>
                    <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input type="email"
                               placeholder="Enter your email"
                               class="flex-1 px-6 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all">
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-xl hover:shadow-lg hover:scale-105 transition-all">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
// Auto-submit form on change
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const selects = form.querySelectorAll('select');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            form.submit();
        });
    });

    // Search input debounce
    let searchTimeout;
    const searchInput = form.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length === 0 || this.value.length >= 3) {
                    form.submit();
                }
            }, 500);
        });
    }
});

// Initialize AOS (Animate On Scroll)
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 600,
        once: true,
        offset: 50
    });
}
</script>
@endpush

@push('styles')
<style>
/* Custom scrollbar for horizontal scroll */
.scrollbar-thin::-webkit-scrollbar {
    height: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #1e293b;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Product card animations */
.product-card {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Gradient text animation on hover */
.group:hover .text-transparent {
    animation: gradient-shift 3s ease infinite;
}

@keyframes gradient-shift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Delay animation for staggered effect */
.delay-1000 {
    animation-delay: 1s;
}
</style>
@endpush
@endsection
