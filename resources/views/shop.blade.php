@extends('layouts.app')

@section('title', 'Shop - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">Products Page</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">Home</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">Products</span>
    </nav>
@endsection

@section('content')
<!-- Products Section -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="lg:flex lg:space-x-[30px]">
            <!-- Sidebar -->
            <div class="lg:w-[270px] mb-8 lg:mb-0">
                <div class="w-full px-5 py-4 rounded-lg border border-[#23262B] bg-black">
                    <!-- Categories -->
                    <div class="mb-8">
                        <p class="text-xl leading-8 font-semibold text-white pb-[6px] border-b border-primary-border mb-2.5">
                            Categories
                        </p>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('shop') }}" class="flex items-center justify-between text-gray-400 hover:text-primary-blue transition-all {{ !request()->has('category') ? 'text-primary-blue' : '' }}">
                                    <span>All Products</span>
                                    <span class="text-xs">({{ $products->total() ?? 0 }})</span>
                                </a>
                            </li>
                            @foreach($categories ?? [] as $category)
                            <li>
                                <a href="{{ route('shop', ['category' => $category->slug]) }}"
                                   class="flex items-center justify-between text-gray-400 hover:text-primary-blue transition-all {{ request('category') == $category->slug ? 'text-primary-blue' : '' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-xs">({{ $category->products_count ?? 0 }})</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-8">
                        <p class="text-xl leading-8 font-semibold text-white pb-[6px] border-b border-primary-border mb-2.5">
                            Price Range
                        </p>
                        <form method="GET" action="{{ route('shop') }}">
                            @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <div class="space-y-3">
                                <input type="number" name="min_price" placeholder="Min price"
                                       value="{{ request('min_price') }}"
                                       class="w-full bg-transparent border border-[#23262B] rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                                <input type="number" name="max_price" placeholder="Max price"
                                       value="{{ request('max_price') }}"
                                       class="w-full bg-transparent border border-[#23262B] rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                                <button type="submit" class="w-full bg-primary-blue text-black font-semibold py-2 rounded-lg hover:bg-[#3fda74] transition-all">
                                    Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <p class="text-xl leading-8 font-semibold text-white pb-[6px] border-b border-primary-border mb-2.5">
                            Sort By
                        </p>
                        <select onchange="window.location.href=this.value" class="w-full bg-transparent border border-[#23262B] rounded-lg px-3 py-2 text-white focus:border-primary-blue focus:outline-none">
                            <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Latest</option>
                            <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'price_low'])) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'price_high'])) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="{{ route('shop', array_merge(request()->except('sort'), ['sort' => 'popular'])) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Search Bar -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('shop') }}" class="relative">
                        @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="text" name="search" placeholder="Search products..."
                               value="{{ request('search') }}"
                               class="w-full bg-black border border-[#23262B] rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-blue transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Results Info -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-400">
                        Showing <span class="text-white">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span>
                        of <span class="text-white">{{ $products->total() ?? 0 }}</span> results
                    </p>
                </div>

                <!-- Products Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products ?? [] as $product)
                    <div class="product-card border border-[#3C3E42] rounded-lg overflow-hidden bg-black transition-all duration-300">
                        <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="block">
                            <div class="relative h-48 bg-gradient-to-br from-[#23262B] to-black flex items-center justify-center group overflow-hidden">
                                @if($product->discount_percentage > 0)
                                <span class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded z-10">
                                    -{{ $product->discount_percentage }}%
                                </span>
                                @endif

                                @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                <div class="text-6xl">🎮</div>
                                @endif

                                <!-- Quick Actions Overlay -->
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                                    <button onclick="event.preventDefault(); addToCart({{ $product->id }})"
                                            class="p-3 bg-primary-blue text-black rounded-lg hover:bg-[#3fda74] transition-all transform scale-0 group-hover:scale-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="event.preventDefault(); toggleFavorite({{ $product->id }})"
                                            class="p-3 bg-white text-black rounded-lg hover:bg-gray-200 transition-all transform scale-0 group-hover:scale-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </a>

                        <div class="p-4">
                            <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="block">
                                <h3 class="text-white font-semibold mb-2 hover:text-primary-blue transition-colors">{{ $product->name }}</h3>
                            </a>

                            <!-- Rating -->
                            <div class="flex items-center gap-1 mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->average_rating)
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @endif
                                @endfor
                                <span class="text-gray-400 text-xs ml-1">({{ $product->reviews_count ?? 0 }})</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->discount_percentage > 0)
                                    <span class="text-gray-500 line-through text-sm">${{ number_format($product->original_price, 2) }}</span>
                                    <span class="text-primary-blue font-bold text-lg ml-2">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    @else
                                    <span class="text-primary-blue font-bold text-lg">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>

                                @if($product->is_available)
                                <span class="text-xs text-green-400">In Stock</span>
                                @else
                                <span class="text-xs text-red-400">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">😕</div>
                        <h3 class="text-2xl font-bold text-white mb-2">No products found</h3>
                        <p class="text-gray-400 mb-6">Try adjusting your filters or search terms</p>
                        <a href="{{ route('shop') }}" class="inline-block px-6 py-3 bg-primary-blue text-black font-bold rounded-lg hover:bg-[#3fda74] transition-all">
                            Clear Filters
                        </a>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products && $products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
@auth
function addToCart(productId) {
    fetch(`/api/cart/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('Product added to cart!', 'success');
            updateCartCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

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
@else
function addToCart(productId) {
    window.location.href = "{{ route('login') }}";
}

function toggleFavorite(productId) {
    window.location.href = "{{ route('login') }}";
}
@endauth
</script>
@endpush
