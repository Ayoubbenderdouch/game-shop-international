@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 pt-24 pb-8">
        <nav class="flex items-center space-x-2 text-sm text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-cyan-400 transition-colors">{{ __('app.home') }}</a>
            <span>/</span>
            <a href="{{ route('shop') }}" class="hover:text-cyan-400 transition-colors">{{ __('app.shop') }}</a>
            @if($product->category)
            <span>/</span>
            <a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="hover:text-cyan-400 transition-colors">{{ $product->category->name }}</a>
            @endif
            <span>/</span>
            <span class="text-white">{{ $product->name }}</span>
        </nav>
    </div>

    <!-- Product Details -->
    <div class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Image -->
            <div>
                <div class="relative rounded-2xl overflow-hidden bg-slate-800/50 backdrop-blur-sm border border-slate-700">
                    @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}"
                             class="w-full h-auto object-cover">
                    @else
                        <div class="aspect-square flex items-center justify-center text-slate-500">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @endif

                    @if($product->stock_quantity !== null && $product->stock_quantity <= 5)
                    <span class="absolute top-4 left-4 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                        {{ __('app.shop.low_stock', ['count' => $product->stock_quantity]) }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-4xl font-black text-white mb-4">{{ $product->name }}</h1>

                <!-- Category -->
                @if($product->category)
                <a href="{{ route('shop', ['category' => $product->category->slug]) }}"
                   class="inline-block text-cyan-400 hover:text-cyan-300 transition-colors mb-4">
                    {{ $product->category->name }}
                </a>
                @endif

                <!-- Rating -->
                @if($product->reviews->count() > 0)
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center">
                        @php
                            $avgRating = $product->reviews->avg('rating');
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-slate-600' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <span class="text-white font-medium">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-slate-400">({{ $product->reviews->count() }} {{ __('app.reviews') }})</span>
                </div>
                @endif

                <!-- Price -->
                <div class="mb-8">
                    <div class="flex items-end gap-4">
                        <span class="text-5xl font-black text-cyan-400">${{ number_format($product->selling_price, 2) }}</span>
                        @if($product->original_price && $product->original_price > $product->selling_price)
                        <span class="text-2xl text-slate-500 line-through">${{ number_format($product->original_price, 2) }}</span>
                        <span class="px-3 py-1 bg-red-500 text-white text-sm font-bold rounded-lg">
                            -{{ round((($product->original_price - $product->selling_price) / $product->original_price) * 100) }}%
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="prose prose-invert max-w-none mb-8">
                    <p class="text-slate-300 leading-relaxed">{{ $product->description }}</p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    @auth
                        <!-- Add to Cart Form -->
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="flex items-center gap-4 mb-4">
                                <label for="quantity" class="text-white font-medium">{{ __('app.shop.quantity') }}:</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                       @if($product->stock_quantity !== null) max="{{ $product->stock_quantity }}" @endif
                                       class="w-20 px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-cyan-500">
                            </div>

                            <button type="submit"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all transform hover:scale-105">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ __('app.shop.add_to_cart') }}
                            </button>
                        </form>

                        <!-- Favorite Button -->
                        <button onclick="toggleFavorite({{ $product->id }})"
                                id="favorite-btn-{{ $product->id }}"
                                class="px-6 py-3 bg-slate-800 border border-slate-700 text-white font-bold rounded-xl hover:bg-slate-700 transition-all">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex-1 text-center px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all">
                            {{ __('app.auth.login_to_purchase') }}
                        </a>
                    @endauth
                </div>

                <!-- Product Meta -->
                <div class="border-t border-slate-700 pt-6 space-y-2">
                    @if($product->sku)
                    <p class="text-slate-400">
                        <span class="font-medium">{{ __('app.shop.sku') }}:</span> {{ $product->sku }}
                    </p>
                    @endif

                    <p class="text-slate-400">
                        <span class="font-medium">{{ __('app.shop.availability') }}:</span>
                        @if($product->is_available && ($product->stock_quantity === null || $product->stock_quantity > 0))
                            <span class="text-green-400">{{ __('app.shop.in_stock') }}</span>
                        @else
                            <span class="text-red-400">{{ __('app.shop.out_of_stock') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-16">
            <h2 class="text-3xl font-black text-white mb-8">{{ __('app.reviews') }}</h2>

            @auth
                @if($canReview)
                <!-- Add Review Form -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 mb-8">
                    <h3 class="text-xl font-bold text-white mb-4">{{ __('reviews.write_review') }}</h3>
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="mb-4">
                            <label class="block text-white mb-2">{{ __('reviews.rating') }}</label>
                            <div class="flex gap-2">
                                @for($i = 5; $i >= 1; $i--)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                                    <span class="inline-block px-4 py-2 bg-slate-700 text-white rounded-lg peer-checked:bg-cyan-500 hover:bg-slate-600 transition-colors">
                                        {{ $i }} ★
                                    </span>
                                </label>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="block text-white mb-2">{{ __('reviews.comment') }}</label>
                            <textarea name="comment" id="comment" rows="4"
                                      class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500"
                                      placeholder="{{ __('reviews.comment_placeholder') }}"></textarea>
                        </div>

                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all">
                            {{ __('reviews.submit_review') }}
                        </button>
                    </form>
                </div>
                @elseif($hasReviewed)
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 mb-8">
                    <p class="text-slate-400">{{ __('reviews.already_reviewed') }}</p>
                </div>
                @endif
            @endauth

            <!-- Reviews List -->
            <div class="space-y-4">
                @forelse($product->reviews->where('is_approved', true) as $review)
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="text-white font-bold">{{ $review->user->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-600' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @endfor
                                </div>
                                @if($review->is_verified_purchase)
                                <span class="text-xs text-green-400">{{ __('reviews.verified_purchase') }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="text-sm text-slate-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    @if($review->comment)
                    <p class="text-slate-300">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-slate-400">
                    {{ __('reviews.no_reviews_yet') }}
                </div>
                @endforelse
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-3xl font-black text-white mb-8">{{ __('app.shop.related_products') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <div class="group bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl overflow-hidden hover:border-cyan-500 transition-all">
                    <a href="{{ route('product.show', $related->slug) }}">
                        <div class="aspect-square bg-slate-900 relative overflow-hidden">
                            @if($related->image)
                                <img src="{{ $related->image }}" alt="{{ $related->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600">
                                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-white mb-2 line-clamp-2">{{ $related->name }}</h3>
                            <p class="text-2xl font-black text-cyan-400">${{ number_format($related->selling_price, 2) }}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleFavorite(productId) {
    fetch('{{ route('favorites.toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('favorite-btn-' + productId);
            if (data.is_favorited) {
                btn.classList.add('bg-red-500');
                btn.querySelector('svg').setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('bg-red-500');
                btn.querySelector('svg').setAttribute('fill', 'none');
            }
        }
    });
}
</script>
@endpush
@endsection
