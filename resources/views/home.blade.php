@extends('shop')

@section('title', 'Home - ' . config('app.name'))

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    Welcome to {{ config('app.name') }}
                </h1>
                <p class="text-xl md:text-2xl mb-8">
                    Your one-stop shop for digital products
                </p>
                <div class="space-x-4">
                    <a href="{{ route('shop') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Browse Shop
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="inline-block bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-400 transition">
                        Get Started
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] ?? '100+' }}</div>
                    <div class="text-gray-600">Products</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_customers'] ?? '5000+' }}</div>
                    <div class="text-gray-600">Happy Customers</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">24/7</div>
                    <div class="text-gray-600">Support</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['countries_served'] ?? '30+' }}</div>
                    <div class="text-gray-600">Countries</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Shop by Category</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @forelse($categories ?? [] as $category)
                <a href="{{ route('shop', ['category' => $category->slug]) }}"
                   class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 text-center">
                    <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                    <p class="text-gray-600 text-sm mt-2">{{ $category->products_count ?? 0 }} products</p>
                </a>
                @empty
                <div class="col-span-full text-center text-gray-500">
                    No categories available
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Featured Products</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($featuredProducts ?? [] as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                         class="w-full h-48 object-cover rounded-t-lg">
                    @else
                    <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                        <span class="text-gray-400">No Image</span>
                    </div>
                    @endif

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                        <p class="text-2xl font-bold text-blue-600 mb-4">${{ number_format($product->selling_price, 2) }}</p>

                        <div class="flex gap-2">
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
                                View
                            </a>
                            @auth
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit"
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                    Add to Cart
                                </button>
                            </form>
                            @else
                            <a href="{{ route('login') }}"
                               class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                Add to Cart
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center text-gray-500">
                    No products available
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    @guest
    <div class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="text-xl mb-8">Join thousands of satisfied customers today!</p>
            <a href="{{ route('register') }}"
               class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Create Your Account
            </a>
        </div>
    </div>
    @endguest
</div>
@endsection
