@extends('layouts.app')

@section('title', __('favorites.title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="container mx-auto px-4 pt-24 pb-16">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">{{ __('favorites.title') }}</h1>
            <p class="text-slate-400">{{ __('favorites.subtitle') }}</p>
        </div>

        @if($favorites->count() > 0)
        <!-- Favorites Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($favorites as $favorite)
            <div class="group bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl overflow-hidden hover:border-cyan-500 transition-all">
                <!-- Product Image -->
                <div class="relative aspect-square bg-slate-900 overflow-hidden">
                    @if($favorite->product->image)
                        <img src="{{ $favorite->product->image }}"
                             alt="{{ $favorite->product->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-600">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- Remove from Favorites -->
                    <form action="{{ route('favorites.remove', $favorite) }}" method="POST" class="absolute top-4 right-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-2 bg-red-500/80 backdrop-blur-sm text-white rounded-lg hover:bg-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </form>

                    <!-- Stock Badge -->
                    @if($favorite->product->stock_quantity !== null && $favorite->product->stock_quantity <= 5)
                    <span class="absolute top-4 left-4 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                        {{ __('app.shop.low_stock', ['count' => $favorite->product->stock_quantity]) }}
                    </span>
                    @elseif(!$favorite->product->is_available)
                    <span class="absolute top-4 left-4 px-2 py-1 bg-slate-700 text-white text-xs font-bold rounded">
                        {{ __('app.shop.out_of_stock') }}
                    </span>
                    @endif

                    <!-- Quick View Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <a href="{{ route('product.show', $favorite->product->slug) }}"
                           class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all transform -translate-y-4 group-hover:translate-y-0">
                            {{ __('app.shop.view_details') }}
                        </a>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-6">
                    <!-- Category -->
                    <p class="text-xs text-cyan-400 font-semibold mb-2">{{ $favorite->product->category->name }}</p>

                    <!-- Product Name -->
                    <h3 class="font-bold text-white mb-2 group-hover:text-cyan-400 transition-colors line-clamp-2 min-h-[3rem]">
                        <a href="{{ route('product.show', $favorite->product->slug) }}">
                            {{ $favorite->product->name }}
                        </a>
                    </h3>

                    <!-- Rating -->
                    @if($favorite->product->reviews->count() > 0)
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex items-center">
                            @php
                                $avgRating = $favorite->product->reviews->avg('rating');
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-slate-600' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                        <span class="text-xs text-slate-400">({{ $favorite->product->reviews->count() }})</span>
                    </div>
                    @endif

                    <!-- Price -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            @if($favorite->product->original_price && $favorite->product->original_price > $favorite->product->selling_price)
                            <span class="text-sm text-slate-500 line-through">${{ number_format($favorite->product->original_price, 2) }}</span>
                            @endif
                            <span class="text-2xl font-black text-cyan-400">${{ number_format($favorite->product->selling_price, 2) }}</span>
                        </div>
                        @if($favorite->product->original_price && $favorite->product->original_price > $favorite->product->selling_price)
                        <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                            -{{ round((($favorite->product->original_price - $favorite->product->selling_price) / $favorite->product->original_price) * 100) }}%
                        </span>
                        @endif
                    </div>

                    <!-- Add to Cart -->
                    @if($favorite->product->is_available && ($favorite->product->stock_quantity === null || $favorite->product->stock_quantity > 0))
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $favorite->product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-lg hover:from-cyan-600 hover:to-blue-600 transition-all">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ __('app.shop.add_to_cart') }}
                        </button>
                    </form>
                    @else
                    <button disabled
                            class="w-full px-4 py-2 bg-slate-700 text-slate-500 font-bold rounded-lg cursor-not-allowed">
                        {{ __('app.shop.out_of_stock') }}
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $favorites->links() }}
        </div>
        @else
        <!-- Empty Favorites -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-slate-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-white mb-2">{{ __('favorites.empty_title') }}</h2>
                <p class="text-slate-400 mb-8">{{ __('favorites.empty_message') }}</p>
                <a href="{{ route('shop') }}"
                   class="inline-block px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all transform hover:scale-105">
                    {{ __('favorites.browse_products') }}
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
