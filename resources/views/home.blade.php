@extends('layouts.app')

@section('title', 'Home - Gaming Store')

@section('content')
<!-- Hero Section -->
<section class="w-full relative">
    <!-- Swiper Hero Slider -->
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="relative min-h-[600px] flex items-center" style="background-image: url('https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2065'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                    <div class="max-w-[1170px] mx-auto px-5 lg:px-0 w-full relative z-10">
                        <div class="max-w-xl">
                            <h1 class="text-5xl lg:text-6xl font-black text-white mb-6">
                                Level Up Your Gaming Experience
                            </h1>
                            <p class="text-xl text-gray-300 mb-8">
                                Get instant access to game cards, gift cards, and premium subscriptions
                            </p>
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-[#49b8ef] to-[#3da2d4] text-black font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
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
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-[#49b8ef] to-[#3da2d4] text-black font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
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
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-[#49b8ef] to-[#3da2d4] text-black font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
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
@if(isset($categories) && $categories->count() > 0)
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="text-center mb-12">
            <p class="text-[#49b8ef] text-lg font-medium mb-2">Shop by Category</p>
            <h2 class="text-3xl md:text-4xl font-black text-white">Browse Our Collection</h2>
        </div>

        <div class="grid xl:grid-cols-8 lg:grid-cols-6 sm:grid-cols-5 grid-cols-4 sm:gap-5 gap-3">
            @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}"
               class="group text-center">
                <div class="w-full aspect-square bg-black border border-[#23262B] rounded-lg flex flex-col items-center justify-center hover:border-[#49b8ef] transition-all duration-300 group-hover:scale-105">
                    @if($category->icon)
                    <div class="text-3xl mb-2">{!! $category->icon !!}</div>
                    @else
                    <div class="text-3xl mb-2">🎮</div>
                    @endif
                    <p class="text-xs text-gray-400 group-hover:text-white transition-colors">{{ $category->name }}</p>
                    @if($category->products_count > 0)
                    <span class="text-xs text-[#49b8ef] mt-1">{{ $category->products_count }} items</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Best Selling Products -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="w-full py-[60px] relative">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="flex justify-between items-center mb-8">
            <div>
                <p class="text-[#49b8ef] text-lg font-medium">Top Picks</p>
                <h2 class="text-3xl md:text-4xl font-black text-white">Best Selling Products</h2>
            </div>
            <a href="{{ route('shop') }}" class="text-gray-400 hover:text-[#49b8ef] transition-all">
                View All →
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="product-card border border-[#3C3E42] rounded-lg overflow-hidden bg-black hover:border-[#49b8ef] transition-all duration-300">
                <a href="{{ route('product.show', $product->slug) }}" class="block">
                    <div class="relative h-48 bg-gradient-to-br from-[#23262B] to-black flex items-center justify-center group overflow-hidden">
                        @if($product->discount_percentage > 0)
                        <span class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded z-10">
                            -{{ $product->discount_percentage }}%
                        </span>
                        @endif

                        @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                        <div class="text-6xl">🎮</div>
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
