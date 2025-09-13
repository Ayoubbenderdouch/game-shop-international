@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900">
    <!-- Breadcrumb -->
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition-colors">{{ __('app.nav.home') }}</a>
            <span class="text-slate-600">/</span>
            <a href="{{ route('shop') }}" class="text-slate-400 hover:text-white transition-colors">{{ __('app.shop.title') }}</a>
            <span class="text-slate-600">/</span>
            <a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="text-slate-400 hover:text-white transition-colors">{{ $product->category->name }}</a>
            <span class="text-slate-600">/</span>
            <span class="text-white">{{ $product->name }}</span>
        </nav>
    </div>

    <!-- Product Details -->
    <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Image -->
            <div>
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-purple-500 rounded-3xl blur-3xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-3xl p-8 overflow-hidden">
                        @if($product->image)
                        <img src="{{ $product->image }}"
                             alt="{{ $product->name }}"
                             class="w-full h-auto rounded-2xl">
                        @else
                        <div class="w-full aspect-square bg-gradient-to-br from-cyan-500/20 to-purple-500/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-32 h-32 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if($product->is_available)
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                {{ __('app.shop.in_stock') }}
                            </span>
                            @endif

                            @php
                                $discountPercentage = 0;
                                if($product->original_price && $product->original_price > $product->selling_price) {
                                    $discountPercentage = round((($product->original_price - $product->selling_price) / $product->original_price) * 100);
                                }
                            @endphp

                            @if($discountPercentage > 0)
                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                -{{ $discountPercentage }}%
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <!-- Category -->
                <p class="text-cyan-400 font-semibold mb-2">{{ $product->category->name }}</p>

                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-black text-white mb-4">{{ $product->name }}</h1>

                <!-- Rating -->
                @if($product->reviews && $product->reviews->count() > 0)
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex items-center">
                        @php
                            $avgRating = round($product->reviews->avg('rating') ?? 0);
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $avgRating ? 'text-yellow-400' : 'text-slate-600' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <span class="text-slate-400">{{ number_format($product->reviews->avg('rating'), 1) }} ({{ $product->reviews->count() }} {{ __('app.shop.reviews') }})</span>
                </div>
                @endif

                <!-- Price -->
                <div class="flex items-baseline gap-4 mb-6">
                    @if($product->original_price && $product->original_price > $product->selling_price)
                    <span class="text-2xl text-slate-500 line-through">${{ number_format($product->original_price, 2) }}</span>
                    @endif
                    <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                        ${{ number_format($product->selling_price, 2) }}
                    </span>
                </div>

                <!-- Description -->
                @if($product->description)
                <div class="prose prose-invert mb-8">
                    <p class="text-slate-300 leading-relaxed">
                        {{ $product->description }}
                    </p>
                </div>
                @endif

                <!-- Optional Fields -->
                @if($product->optional_fields && is_array($product->optional_fields) && count($product->optional_fields) > 0)
                <div class="mb-8 space-y-4">
                    <h3 class="text-lg font-bold text-white mb-4">{{ __('app.shop.additional_info') }}</h3>
                    @foreach($product->optional_fields as $field)
                        @if(is_array($field) && isset($field['name']))
                        <div class="flex items-start gap-2">
                            <label class="text-slate-400 font-medium min-w-[150px]">
                                {{ $field['name'] }}:
                            </label>
                            <span class="text-white">
                                @if(isset($field['value']))
                                    @if(is_array($field['value']))
                                        {{ implode(', ', $field['value']) }}
                                    @else
                                        {{ $field['value'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        @elseif(!is_array($field))
                        <div class="text-slate-300">
                            {{ $field }}
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                <!-- Redemption Instructions -->
                @if($product->redemption_instructions)
                <div class="mb-8 p-4 bg-slate-800/50 border border-slate-700 rounded-xl">
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('app.shop.how_to_redeem') }}</h3>
                    <p class="text-slate-300">
                        @if(is_array($product->redemption_instructions))
                            {{ implode(' ', $product->redemption_instructions) }}
                        @else
                            {{ $product->redemption_instructions }}
                        @endif
                    </p>
                </div>
                @endif

                <!-- Forbidden Countries -->
                @if($product->forbidden_countries && is_array($product->forbidden_countries) && count($product->forbidden_countries) > 0)
                <div class="mb-8 p-4 bg-red-900/20 border border-red-800 rounded-xl">
                    <h3 class="text-lg font-bold text-red-400 mb-2">{{ __('app.shop.not_available_in') }}</h3>
                    <p class="text-red-300">
                        {{ implode(', ', $product->forbidden_countries) }}
                    </p>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    @auth
                        @if($product->is_available && ($product->stock_quantity === null || $product->stock_quantity > 0))
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- Quantity Selector -->
                            <div class="flex items-center gap-4 mb-4">
                                <label for="quantity" class="text-slate-400 font-medium">{{ __('app.shop.quantity') }}:</label>
                                <div class="flex items-center">
                                    <button type="button" onclick="decreaseQuantity()" class="px-3 py-1 bg-slate-800 text-white rounded-l-lg hover:bg-slate-700">-</button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1"
                                           @if($product->stock_quantity !== null) max="{{ $product->stock_quantity }}" @endif
                                           class="w-16 px-3 py-1 bg-slate-900 text-white text-center border-t border-b border-slate-700">
                                    <button type="button" onclick="increaseQuantity()" class="px-3 py-1 bg-slate-800 text-white rounded-r-lg hover:bg-slate-700">+</button>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full px-8 py-4 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-cyan-500/25 transition-all transform hover:scale-105">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ __('app.shop.add_to_cart') }}
                            </button>
                        </form>

                        <!-- Favorite Button -->
                        <button onclick="toggleFavorite({{ $product->id }})"
                                id="favorite-btn-{{ $product->id }}"
                                class="px-6 py-4 bg-slate-800 border border-slate-700 text-white font-bold rounded-xl hover:bg-slate-700 transition-all">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        @else
                        <div class="flex-1 text-center px-8 py-4 bg-slate-800 text-slate-400 font-bold rounded-xl cursor-not-allowed">
                            {{ __('app.shop.out_of_stock') }}
                        </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="flex-1 text-center px-8 py-4 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-cyan-500/25 transition-all">
                            {{ __('app.auth.login_to_purchase') }}
                        </a>
                    @endauth
                </div>

                <!-- Product Meta -->
                <div class="border-t border-slate-700 pt-6 mt-8 space-y-2">
                    @if($product->sku)
                    <p class="text-slate-400">
                        <span class="font-medium">{{ __('app.shop.sku') }}:</span> {{ $product->sku }}
                    </p>
                    @endif

                    <p class="text-slate-400">
                        <span class="font-medium">{{ __('app.shop.availability') }}:</span>
                        @if($product->is_available && ($product->stock_quantity === null || $product->stock_quantity > 0))
                            <span class="text-green-400">{{ __('app.shop.in_stock') }}</span>
                            @if($product->stock_quantity !== null)
                                ({{ $product->stock_quantity }} {{ __('app.shop.units_available') }})
                            @endif
                        @else
                            <span class="text-red-400">{{ __('app.shop.out_of_stock') }}</span>
                        @endif
                    </p>

                    <p class="text-slate-400">
                        <span class="font-medium">{{ __('app.shop.category') }}:</span>
                        <a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="text-cyan-400 hover:text-cyan-300">
                            {{ $product->category->name }}
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        @if($product->reviews && $product->reviews->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-black text-white mb-8">{{ __('app.shop.customer_reviews') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($product->reviews as $review)
                <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-bold text-white">{{ $review->user->name ?? 'Anonymous' }}</h4>
                            <p class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-600' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                    <p class="text-slate-300">{{ $review->comment }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Review Form -->
        @auth
            @if($canReview && !$hasReviewed)
            <div class="mt-8 bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-4">{{ __('app.shop.leave_review') }}</h3>
                <form action="{{ route('reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="mb-4">
                        <label class="block text-slate-400 mb-2">{{ __('app.shop.rating') }}</label>
                        <div class="flex gap-2">
                            @for($i = 5; $i >= 1; $i--)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}" class="hidden" required>
                                <span class="star-rating text-3xl text-slate-600 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">★</span>
                            </label>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="block text-slate-400 mb-2">{{ __('app.shop.comment') }}</label>
                        <textarea name="comment" id="comment" rows="4"
                                  class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20"
                                  placeholder="{{ __('app.shop.share_experience') }}"></textarea>
                    </div>

                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-purple-500 text-white font-bold rounded-xl hover:shadow-lg transition-all">
                        {{ __('app.shop.submit_review') }}
                    </button>
                </form>
            </div>
            @endif
        @endauth

        <!-- Related Products -->
        @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-black text-white mb-8">{{ __('app.shop.related_products') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <div class="group">
                    <div class="relative bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden hover:border-cyan-500/50 transition-all hover:scale-105">
                        <!-- Product Image -->
                        <div class="relative aspect-square overflow-hidden">
                            @if($related->image)
                            <img src="{{ $related->image }}"
                                 alt="{{ $related->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-cyan-500/20 to-purple-500/20 flex items-center justify-center">
                                <svg class="w-16 h-16 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="font-bold text-white mb-2 line-clamp-2">{{ $related->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-black text-cyan-400">${{ number_format($related->selling_price, 2) }}</span>
                                <a href="{{ route('product.show', $related->slug) }}"
                                   class="px-3 py-1 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700 transition-colors">
                                    {{ __('app.shop.view') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = input.getAttribute('max');
    const currentValue = parseInt(input.value);

    if (!max || currentValue < parseInt(max)) {
        input.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);

    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

function toggleFavorite(productId) {
    fetch(`/favorites/toggle/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const btn = document.getElementById(`favorite-btn-${productId}`);
        if (data.favorited) {
            btn.classList.add('bg-red-500');
            btn.classList.remove('bg-slate-800');
        } else {
            btn.classList.remove('bg-red-500');
            btn.classList.add('bg-slate-800');
        }
    });
}

// Star rating functionality
document.querySelectorAll('.star-rating').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.dataset.rating;
        document.querySelectorAll('.star-rating').forEach((s, index) => {
            if (5 - index >= rating) {
                s.classList.add('text-yellow-400');
                s.classList.remove('text-slate-600');
            } else {
                s.classList.remove('text-yellow-400');
                s.classList.add('text-slate-600');
            }
        });
    });
});
</script>
@endpush
@endsection
