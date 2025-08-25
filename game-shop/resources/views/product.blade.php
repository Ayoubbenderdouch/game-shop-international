@extends('layouts.app')

@section('title', $product->title . ' - Game Shop')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div>
            <img src="{{ $product->image_url }}" alt="{{ $product->title }}"
                 class="w-full rounded-lg">
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $product->title }}</h1>

            <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $product->average_rating)
                            ⭐
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <span class="ml-2 text-gray-400">({{ $product->reviews->count() }} reviews)</span>
            </div>

            <div class="text-4xl font-bold text-[#49baee] mb-6">${{ $product->price }}</div>

            <div class="mb-6">
                <p class="text-gray-300">{{ $product->description }}</p>
            </div>

            <div class="space-y-2 mb-6">
                @if($product->platform)
                <div class="flex justify-between">
                    <span class="text-gray-400">Platform:</span>
                    <span>{{ $product->platform }}</span>
                </div>
                @endif
                @if($product->region)
                <div class="flex justify-between">
                    <span class="text-gray-400">Region:</span>
                    <span>{{ $product->region }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-400">Stock:</span>
                    <span class="{{ $product->available_stock > 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $product->available_stock > 0 ? $product->available_stock . ' available' : 'Out of stock' }}
                    </span>
                </div>
            </div>

            <form action="/cart/add" method="POST" class="flex gap-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="number" name="quantity" value="1" min="1" max="{{ $product->available_stock }}"
                       class="w-20 bg-gray-800 rounded px-3 py-2">
                <button type="submit" class="flex-1 neon-button py-3 rounded-lg font-semibold"
                        {{ $product->available_stock == 0 ? 'disabled' : '' }}>
                    {{ $product->available_stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

        @if($product->reviews->count() > 0)
            <div class="space-y-4">
                @foreach($product->reviews as $review)
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <span class="font-semibold">{{ $review->user->name }}</span>
                            <div class="flex text-yellow-400 ml-4">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <span class="text-gray-400 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-300">{{ $review->comment }}</p>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400">No reviews yet. Be the first to review!</p>
        @endif
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div>
        <h2 class="text-2xl font-bold mb-6">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
            <div class="bg-gray-800 rounded-lg overflow-hidden hover:scale-105 transition-transform">
                <a href="/product/{{ $related->id }}">
                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold mb-2">{{ $related->title }}</h3>
                        <p class="text-xl font-bold text-[#49baee]">${{ $related->price }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
