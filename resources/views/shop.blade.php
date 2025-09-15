@extends('layouts.app')

@section('title', 'Shop - GameShop')

@section('content')
<!-- Shop Section -->
<section class="w-full py-[60px] min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="lg:flex lg:space-x-[30px]">
            <!-- Sidebar -->
            <div class="lg:w-[270px] mb-8 lg:mb-0">
                <div class="w-full px-5 py-4 rounded-lg border border-slate-700 bg-slate-800/50 backdrop-blur-sm">
                    <!-- Categories -->
                    <div class="mb-8">
                        <p class="text-xl leading-8 font-semibold text-white pb-[6px] border-b border-slate-700 mb-4">
                            Categories
                        </p>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('shop') }}"
                                   class="flex items-center justify-between text-gray-400 hover:text-primary-blue transition-all {{ !request()->has('category') ? 'text-primary-blue font-semibold' : '' }}">
                                    <span>All Products</span>
                                    @if(isset($products))
                                    <span class="text-xs">({{ \App\Models\Product::active()->available()->count() }})</span>
                                    @endif
                                </a>
                            </li>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('shop', ['category' => $category->slug]) }}"
                                       class="flex items-center justify-between text-gray-400 hover:text-primary-blue transition-all {{ request('category') == $category->slug ? 'text-primary-blue font-semibold' : '' }}">
                                        <span>{{ $category->name }}</span>
                                        <span class="text-xs">({{ $category->products_count ?? 0 }})</span>
                                    </a>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-8">
                        <p class="text-xl leading-8 font-semibold text-white pb-[6px] border-b border-slate-700 mb-4">
                            Price Range
                        </p>
                        <form method="GET" action="{{ route('shop') }}">
                            @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <div class="space-y-3">
                                <input type="number" name="min_price" placeholder="Min price"
                                       value="{{ request('min_price') }}"
                                       class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                                <input type="number" name="max_price" placeholder="Max price"
                                       value="{{ request('max_price') }}"
                                       class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                                <button type="submit" class="w-full bg-primary-blue text-black font-semibold py-2 rounded-lg hover:bg-[#3fda74] transition-all">
                                    Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <p class="text-xl leading-8 font-semibold text-white pb-[6px] border-b border-slate-700 mb-4">
                            Sort By
                        </p>
                        <form method="GET" action="{{ route('shop') }}" id="sortForm">
                            @foreach(request()->except('sort') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="sort" onchange="document.getElementById('sortForm').submit()"
                                    class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-primary-blue focus:outline-none">
                                <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Latest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Best Rated</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Search Bar -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('shop') }}" class="relative">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <input type="text" name="search" placeholder="Search products..."
                               value="{{ request('search') }}"
                               class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-blue transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Results Info -->
                @if(isset($products))
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-400">
                        @if($products->total() > 0)
                            Showing <span class="text-white">{{ $products->firstItem() }}-{{ $products->lastItem() }}</span>
                            of <span class="text-white">{{ $products->total() }}</span> results
                            @if(request('search'))
                                for "<span class="text-white">{{ request('search') }}</span>"
                            @endif
                        @else
                            No products found
                        @endif
                    </p>
                </div>

                <!-- Products Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
                    <div class="product-card border border-slate-700 rounded-lg overflow-hidden bg-slate-800/50 backdrop-blur-sm hover:border-primary-blue transition-all duration-300">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <div class="relative h-48 bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center group overflow-hidden">
                                @if($product->discount_percentage > 0 || ($product->original_price && $product->original_price > $product->selling_price))
                                <span class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded z-10">
                                    -{{ $product->discount_percentage ?? round((($product->original_price - $product->selling_price) / $product->original_price) * 100) }}%
                                </span>
                                @endif

                                @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                <div class="text-6xl">🎮</div>
                                @endif

                                <!-- Quick Actions Overlay -->
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                                    @if($product->is_available && ($product->stock_quantity === null || $product->stock_quantity > 0))
                                    <button onclick="event.preventDefault(); addToCart({{ $product->id }})"
                                            class="p-3 bg-primary-blue text-black rounded-lg hover:bg-[#3fda74] transition-all transform scale-0 group-hover:scale-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </button>
                                    @endif

                                    @auth
                                    <button onclick="event.preventDefault(); toggleFavorite({{ $product->id }})"
                                            class="p-3 bg-white text-black rounded-lg hover:bg-gray-200 transition-all transform scale-0 group-hover:scale-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                    @endauth
                                </div>
                            </div>
                        </a>

                        <div class="p-4">
                            <a href="{{ route('product.show', $product->slug) }}" class="block">
                                <h3 class="text-white font-semibold mb-2 hover:text-primary-blue transition-colors line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            @if($product->category)
                            <p class="text-xs text-gray-400 mb-2">{{ $product->category->name }}</p>
                            @endif

                            <!-- Rating -->
                            @if($product->reviews_count > 0)
                            <div class="flex items-center gap-1 mb-3">
                                @php
                                    $rating = round($product->reviews_avg_rating ?? 0);
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating)
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @endif
                                @endfor
                                <span class="text-gray-400 text-xs ml-1">({{ $product->reviews_count }})</span>
                            </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->original_price && $product->original_price > $product->selling_price)
                                    <span class="text-gray-500 line-through text-sm">${{ number_format($product->original_price, 2) }}</span>
                                    <span class="text-primary-blue font-bold text-lg ml-2">
                                        ${{ number_format($product->selling_price, 2) }}
                                    </span>
                                    @else
                                    <span class="text-primary-blue font-bold text-lg">${{ number_format($product->selling_price, 2) }}</span>
                                    @endif
                                </div>

                                @if($product->is_available)
                                    @if($product->stock_quantity !== null && $product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                    <span class="text-xs text-orange-400">Only {{ $product->stock_quantity }} left</span>
                                    @elseif($product->stock_quantity === 0)
                                    <span class="text-xs text-red-400">Out of Stock</span>
                                    @else
                                    <span class="text-xs text-green-400">In Stock</span>
                                    @endif
                                @else
                                <span class="text-xs text-red-400">Unavailable</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">😕</div>
                        <h3 class="text-2xl font-bold text-white mb-2">No products found</h3>
                        <p class="text-gray-400 mb-6">
                            @if(request('search'))
                                No results for "{{ request('search') }}". Try different keywords.
                            @else
                                Try adjusting your filters or browse all products.
                            @endif
                        </p>
                        <a href="{{ route('shop') }}" class="inline-block px-6 py-3 bg-primary-blue text-black font-bold rounded-lg hover:bg-[#3fda74] transition-all">
                            Clear Filters
                        </a>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-12">
                    <p class="text-gray-400">Loading products...</p>
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
    window.location.reload();
}

function toggleFavorite(productId) {
    fetch('/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showNotification(data.is_favorited ? 'Added to favorites!' : 'Removed from favorites!', 'success');
            updateFavoritesCount();
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

function toggleFavorite(productId) {
    window.location.href = "{{ route('login') }}";
}
@endauth
</script>
@endpush
