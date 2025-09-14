@extends('layouts.app')

@section('title', ($product->name ?? 'Product') . ' - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">Product Details</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">Home</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('shop') }}" class="text-gray-400 hover:text-white transition-all">Products</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">{{ $product->name ?? 'Product' }}</span>
    </nav>
@endsection

@section('content')
<!-- Product Details Section -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div>
                <div class="bg-black border border-[#23262B] rounded-lg p-8">
                    <div class="relative">
                        @if($product->discount_percentage > 0)
                        <span class="absolute top-4 right-4 px-3 py-1 bg-red-500 text-white text-sm font-bold rounded z-10">
                            -{{ $product->discount_percentage }}%
                        </span>
                        @endif

                        @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                        @else
                        <div class="w-full h-96 bg-gradient-to-br from-[#23262B] to-black rounded-lg flex items-center justify-center">
                            <div class="text-8xl">🎮</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Gallery (if multiple images) -->
                @if(isset($product->images) && count($product->images) > 1)
                <div class="grid grid-cols-4 gap-4 mt-4">
                    @foreach($product->images as $image)
                    <button class="bg-black border border-[#23262B] rounded-lg p-2 hover:border-primary-blue transition-all">
                        <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full rounded">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-white mb-4">{{ $product->name }}</h1>

                <!-- Rating -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $product->average_rating)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @else
                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-gray-400">{{ $product->average_rating ?? 0 }} ({{ $product->reviews_count ?? 0 }} reviews)</span>
                </div>

                <!-- Price -->
                <div class="mb-6">
                    @if($product->discount_percentage > 0)
                    <span class="text-gray-500 line-through text-xl">${{ number_format($product->original_price, 2) }}</span>
                    <span class="text-primary-blue font-bold text-4xl ml-3">
                        ${{ number_format($product->price, 2) }}
                    </span>
                    <span class="text-red-500 text-sm ml-2">Save ${{ number_format($product->original_price - $product->price, 2) }}</span>
                    @else
                    <span class="text-primary-blue font-bold text-4xl">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <!-- Availability -->
                <div class="mb-6">
                    @if($product->is_available)
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 text-green-400 rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        In Stock - Instant Delivery
                    </span>
                    @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 text-red-400 rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Out of Stock
                    </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-white mb-3">Description</h3>
                    <p class="text-gray-400 leading-relaxed">
                        {{ $product->description ?? 'Experience premium gaming with this amazing product. Get instant access to your digital content after purchase.' }}
                    </p>
                </div>

                <!-- Add to Cart -->
                @auth
                    @if($product->is_available)
                    <div class="flex gap-4 mb-8">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="flex gap-4">
                                <div class="flex items-center border border-[#23262B] rounded-lg">
                                    <button type="button" onclick="decreaseQuantity()" class="px-4 py-3 text-gray-400 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1"
                                           class="w-16 text-center bg-transparent text-white focus:outline-none">
                                    <button type="button" onclick="increaseQuantity()" class="px-4 py-3 text-gray-400 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <button type="submit" class="flex-1 bg-primary-blue text-black font-bold py-3 px-8 rounded-lg hover:bg-[#3fda74] transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Add to Cart
                                </button>
                            </div>
                        </form>

                        <button onclick="toggleFavorite({{ $product->id }})"
                                id="favorite-btn-{{ $product->id }}"
                                class="px-6 py-3 border border-[#23262B] text-white rounded-lg hover:border-primary-blue transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                    @else
                    <button disabled class="w-full bg-gray-700 text-gray-400 font-bold py-3 px-8 rounded-lg cursor-not-allowed">
                        Out of Stock
                    </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-block w-full text-center bg-primary-blue text-black font-bold py-3 px-8 rounded-lg hover:bg-[#3fda74] transition-all">
                        Login to Purchase
                    </a>
                @endauth

                <!-- Product Info Tabs -->
                <div class="border-t border-[#23262B] pt-8">
                    <div class="flex gap-6 mb-6">
                        <button onclick="showTab('features')" id="tab-features" class="text-primary-blue font-semibold pb-2 border-b-2 border-primary-blue">
                            Features
                        </button>
                        <button onclick="showTab('delivery')" id="tab-delivery" class="text-gray-400 hover:text-white transition-all pb-2">
                            Delivery
                        </button>
                        <button onclick="showTab('support')" id="tab-support" class="text-gray-400 hover:text-white transition-all pb-2">
                            Support
                        </button>
                    </div>

                    <div id="content-features" class="text-gray-400">
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary-blue mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Instant digital delivery to your email</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary-blue mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>100% authentic and legitimate codes</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary-blue mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Works worldwide - no region restrictions</span>
                            </li>
                        </ul>
                    </div>

                    <div id="content-delivery" class="text-gray-400 hidden">
                        <p>Your code will be delivered instantly after payment confirmation. Check your email for the activation code and instructions.</p>
                    </div>

                    <div id="content-support" class="text-gray-400 hidden">
                        <p>24/7 customer support available. Contact us anytime if you need help with your purchase.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="w-full py-[60px] bg-black">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <h2 class="text-3xl font-black text-white mb-8">Related Products</h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts ?? [] as $related)
            <div class="product-card border border-[#3C3E42] rounded-lg overflow-hidden bg-[#0b0e13] hover:border-primary-blue transition-all duration-300">
                <a href="{{ route('product.show', $related->slug ?? $related->id) }}">
                    <div class="relative h-48 bg-gradient-to-br from-[#23262B] to-black flex items-center justify-center">
                        @if($related->discount_percentage > 0)
                        <span class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                            -{{ $related->discount_percentage }}%
                        </span>
                        @endif
                        @if($related->image)
                        <img src="{{ $related->image }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                        @else
                        <div class="text-6xl">🎮</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-white font-semibold mb-2">{{ $related->name }}</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-primary-blue font-bold text-lg">${{ number_format($related->price, 2) }}</span>
                            <button class="p-2 bg-primary-blue text-black rounded hover:bg-[#3fda74] transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function showTab(tabName) {
    // Hide all content
    document.querySelectorAll('[id^="content-"]').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tabs
    document.querySelectorAll('[id^="tab-"]').forEach(tab => {
        tab.classList.remove('text-primary-blue', 'border-b-2', 'border-primary-blue');
        tab.classList.add('text-gray-400');
    });

    // Show selected content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Activate selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('text-gray-400');
    activeTab.classList.add('text-primary-blue', 'border-b-2', 'border-primary-blue');
}

@auth
function toggleFavorite(productId) {
    fetch(`/api/favorites/toggle/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast(data.is_favorite ? 'Added to favorites!' : 'Removed from favorites!', 'success');
            updateFavoritesCount();
        }
    })
    .catch(error => console.error('Error:', error));
}
@endauth
</script>
@endpush
