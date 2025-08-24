@extends('layouts.app')

@section('title', 'Shop - Game Shop')

@section('content')
<div class="flex gap-8">
    <!-- Sidebar Filters -->
    <div class="w-64">
        <div class="bg-gray-800 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Categories</h3>
            <ul class="space-y-2">
                <li>
                    <a href="/shop" class="block hover:text-[#49baee] transition
                        {{ !request('category') ? 'text-[#49baee]' : '' }}">
                        All Categories
                    </a>
                </li>
                @foreach($categories as $category)
                <li>
                    <a href="/shop?category={{ $category->slug }}"
                       class="block hover:text-[#49baee] transition
                        {{ request('category') == $category->slug ? 'text-[#49baee]' : '' }}">
                        {{ $category->name }}
                    </a>
                </li>
                @endforeach
            </ul>

            <h3 class="text-lg font-semibold mb-4 mt-6">Sort By</h3>
            <select onchange="window.location.href='/shop?category={{ request('category') }}&sort=' + this.value"
                    class="w-full bg-gray-700 rounded px-3 py-2">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="flex-1">
        <div class="mb-6">
            <form action="/shop" method="GET" class="flex gap-4">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search products..."
                       class="flex-1 bg-gray-800 rounded px-4 py-2">
                <button type="submit" class="neon-button px-6 py-2 rounded">Search</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
            <div class="bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-700 transition">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                        <span class="text-gray-500">No Image</span>
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold mb-2">{{ $product->title }}</h3>
                    <p class="text-sm text-gray-400 mb-2">{{ $product->category->name }}</p>
                    <p class="text-[#49baee] text-xl font-bold mb-2">${{ $product->price }}</p>

                    @if($product->available_stock > 0)
                        <p class="text-sm text-green-500 mb-3">In Stock ({{ $product->available_stock }})</p>
                    @else
                        <p class="text-sm text-red-500 mb-3">Out of Stock</p>
                    @endif

                    <div class="flex justify-between items-center">
                        <a href="/product/{{ $product->id }}" class="text-sm text-gray-400 hover:text-white">
                            View Details
                        </a>
                        @if($product->available_stock > 0)
                            <form action="/cart/add" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="bg-[#49baee] px-3 py-1 rounded text-sm hover:bg-[#38a8dc] transition">
                                    Add to Cart
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
