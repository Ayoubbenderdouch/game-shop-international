@extends('layouts.app')

@section('title', 'Home - Game Shop')

@section('content')
<div class="mb-12">
    <div class="text-center py-20 bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg neon-border">
        <h1 class="text-5xl font-bold mb-4 text-[#49baee]">Level Up Your Gaming</h1>
        <p class="text-xl text-gray-300 mb-8">Get instant access to game cards, gift cards, and subscriptions</p>
        <a href="/shop" class="neon-button px-8 py-3 rounded-lg inline-block font-semibold">
            Shop Now
        </a>
    </div>
</div>

<div class="mb-12">
    <h2 class="text-3xl font-bold mb-6">Categories</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-700 transition cursor-pointer neon-border">
            <h3 class="text-xl font-semibold mb-2">{{ $category->name }}</h3>
            <a href="/shop?category={{ $category->slug }}" class="text-[#49baee] hover:underline">
                Browse →
            </a>
        </div>
        @endforeach

        <!-- PUBG UC Special Category -->
        <div class="bg-gradient-to-br from-yellow-900 to-orange-900 rounded-lg p-6 hover:opacity-90 transition cursor-pointer border-2 border-yellow-500">
            <h3 class="text-xl font-semibold mb-2">PUBG UC Top-Up</h3>
            <p class="text-sm mb-2">Instant UC delivery</p>
            <a href="/pubg-uc" class="text-yellow-300 hover:underline font-bold">
                Charge Now →
            </a>
        </div>
    </div>
</div>

<div>
    <h2 class="text-3xl font-bold mb-6">Featured Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredProducts as $product)
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
                <p class="text-[#49baee] text-xl font-bold mb-2">${{ $product->price }}</p>
                <div class="flex justify-between items-center">
                    <a href="/product/{{ $product->id }}" class="text-sm text-gray-400 hover:text-white">
                        View Details
                    </a>
                    <form action="/cart/add" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="bg-[#49baee] px-3 py-1 rounded text-sm hover:bg-[#38a8dc] transition">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
