@extends('layouts.app')

@section('title', ($product->name ?? 'Product') . ' - GameShop')

@section('content')
<!-- Product Details Section -->
<section class="w-full py-[60px] min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm mb-8">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">Home</a>
            <span class="text-gray-400">/</span>
            <a href="{{ route('shop') }}" class="text-gray-400 hover:text-white transition-all">Shop</a>
            @if($product->category)
            <span class="text-gray-400">/</span>
            <a href="{{ route('category.show', $product->category->slug) }}" class="text-gray-400 hover:text-white transition-all">
                {{ $product->category->name }}
            </a>
            @endif
            <span class="text-gray-400">/</span>
            <span class="text-white">{{ $product->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div>
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-8">
                    <div class="relative">
                        @php
                            $discountPercentage = 0;
                            if($product->original_price && $product->original_price > $product->selling_price) {
                                $discountPercentage = round((($product->original_price - $product->selling_price) / $product->original_price) * 100);
                            }
                        @endphp

                        @if($discountPercentage > 0)
                        <span class="absolute top-4 right-4 px-3 py-1 bg-red-500 text-white text-sm font-bold rounded z-10">
                            -{{ $discountPercentage }}%
                        </span>
                        @endif

                        @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                        @else
                        <div class="w-full h-96 bg-gradient-to-br from-slate-900 to-slate-800 rounded-lg flex items-center justify-center">
                            <div class="text-8xl">🎮</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Gallery (if multiple images exist in metadata) -->
                @if(isset($product->metadata['images']) && is_array($product->metadata['images']) && count($product->metadata['images']) > 1)
                <div class="grid grid-cols-4 gap-4 mt-4">
                    @foreach($product->metadata['images'] as $image)
                    <button class="bg-slate-800/50 border border-slate-700 rounded-lg p-2 hover:border-primary-blue transition-all">
                        <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full rounded">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-white mb-4">{{ $product->name }}</h1>

                <!-- Category Badge -->
                @if($product->category)
                <div class="mb-4">
                    <span class="inline-block px-3 py-1 bg-slate-800 text-gray-300 text-sm rounded-lg">
                        {{ $product->category->name }}
                    </span>
                </div>
                @endif

                <!-- Rating -->
                @if($product->reviews && $product->reviews->count() > 0)
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center gap-1">
                        @php
                            $averageRating = round($product->reviews->avg('rating') ?? 0);
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $averageRating)
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
                    <span class="text-gray-400">{{ number_format($product->reviews->avg('rating'), 1) }} ({{ $product->reviews->count() }} {{ Str::plural('review', $product->reviews->count()) }})</span>
                </div>
                @endif

                <!-- Price -->
                <div class="mb-6">
                    @if($product->original_price && $product->original_price > $product->selling_price)
                    <span class="text-gray-500 line-through text-xl">${{ number_format($product->original_price, 2) }}</span>
                    <span class="text-primary-blue font-bold text-4xl ml-3">
                        ${{ number_format($product->selling_price, 2) }}
                    </span>
                    <span class="text-red-500 text-sm ml-2">Save ${{ number_format($product->original_price - $product->selling_price, 2) }}</span>
                    @else
                    <span class="text-primary-blue font-bold text-4xl">${{ number_format($product->selling_price, 2) }}</span>
                    @endif

                    @if($product->vat_percentage > 0)
                    <p class="text-gray-400 text-sm mt-1">* Includes {{ $product->vat_percentage }}% VAT</p>
                    @endif
                </div>

                <!-- Availability -->
                <div class="mb-6">
                    @if($product->is_available)
                        @if($product->stock_quantity !== null)
                            @if($product->stock_quantity > 10)
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 text-green-400 rounded-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                In Stock ({{ $product->stock_quantity }} available)
                            </span>
                            @elseif($product->stock_quantity > 0)
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500/20 text-orange-400 rounded-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Low Stock - Only {{ $product->stock_quantity }} left!
                            </span>
                            @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 text-red-400 rounded-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Out of Stock
                            </span>
                            @endif
                        @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 text-green-400 rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Digital Product - Instant Delivery
                        </span>
                        @endif
                    @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 text-red-400 rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Currently Unavailable
                    </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-white mb-3">Description</h3>
                    <p class="text-gray-400 leading-relaxed">
                        {{ $product->description ?? 'Experience premium gaming with this amazing product. Get instant access to your digital content after purchase.' }}
                    </p>

                    @if($product->redemption_instructions)
                    <div class="mt-4 p-4 bg-slate-800/50 rounded-lg border border-slate-700">
                        <h4 class="text-sm font-semibold text-white mb-2">How to Redeem:</h4>
                        <p class="text-gray-400 text-sm">{{ $product->redemption_instructions }}</p>
                    </div>
                    @endif
                </div>

                <!-- Add to Cart -->
                @auth
                    @if($product->is_available && ($product->stock_quantity === null || $product->stock_quantity > 0))
                    <div class="flex gap-4 mb-8">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="flex gap-4">
                                <div class="flex items-center border border-slate-700 rounded-lg bg-slate-800/50">
                                    <button type="button" onclick="decreaseQuantity()" class="px-4 py-3 text-gray-400 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1"
                                           @if($product->stock_quantity !== null) max="{{ $product->stock_quantity }}" @endif
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

                        @php
                            $isFavorited = auth()->user()->favorites()->where('product_id', $product->id)->exists();
                        @endphp

                        <button onclick="toggleFavorite({{ $product->id }})"
                                id="favorite-btn-{{ $product->id }}"
                                class="px-6 py-3 border border-slate-700 rounded-lg hover:border-primary-blue transition-all {{ $isFavorited ? 'text-red-500 border-red-500' : 'text-white' }}">
                            <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="border-t border-slate-700 pt-8">
                    <div class="flex gap-6 mb-6">
                        <button onclick="showTab('features')" id="tab-features" class="text-primary-blue font-semibold pb-2 border-b-2 border-primary-blue">
                            Features
                        </button>
                        <button onclick="showTab('reviews')" id="tab-reviews" class="text-gray-400 hover:text-white transition-all pb-2">
                            Reviews ({{ $product->reviews ? $product->reviews->count() : 0 }})
                        </button>
                        <button onclick="showTab('delivery')" id="tab-delivery" class="text-gray-400 hover:text-white transition-all pb-2">
                            Delivery
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
                            @if(!$product->forbidden_countries || count($product->forbidden_countries) == 0)
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary-blue mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Works worldwide - no region restrictions</span>
                            </li>
                            @endif
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary-blue mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>24/7 customer support</span>
                            </li>
                        </ul>
                    </div>

                    <div id="content-reviews" class="hidden">
                        @if($product->reviews && $product->reviews->count() > 0)
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($product->reviews->where('is_approved', true)->take(10) as $review)
                                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="text-white font-semibold">{{ $review->user->name }}</p>
                                            <div class="flex items-center gap-1 mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    @else
                                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-gray-400 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->comment)
                                    <p class="text-gray-400">{{ $review->comment }}</p>
                                    @endif
                                    @if($review->is_verified_purchase)
                                    <span class="inline-block mt-2 px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded">
                                        Verified Purchase
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            @if(isset($canReview) && $canReview && !$hasReviewed)
                            <div class="mt-4 pt-4 border-t border-slate-700">
                                <button onclick="openReviewModal()" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-[#3fda74] transition-all">
                                    Write a Review
                                </button>
                            </div>
                            @endif
                        @else
                            <p class="text-gray-400">No reviews yet. Be the first to review this product!</p>

                            @if(isset($canReview) && $canReview)
                            <button onclick="openReviewModal()" class="mt-4 px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-[#3fda74] transition-all">
                                Write the First Review
                            </button>
                            @endif
                        @endif
                    </div>

                    <div id="content-delivery" class="text-gray-400 hidden">
                        <p>Your code will be delivered instantly after payment confirmation. Check your email for the activation code and instructions.</p>
                        <ul class="mt-4 space-y-2">
                            <li>• Instant email delivery after payment</li>
                            <li>• Step-by-step activation instructions included</li>
                            <li>• Compatible with all regions (unless specified)</li>
                            <li>• 24/7 automated delivery system</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<section class="w-full py-[60px] bg-slate-900/50">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <h2 class="text-3xl font-black text-white mb-8">Related Products</h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
            <div class="product-card border border-slate-700 rounded-lg overflow-hidden bg-slate-800/50 hover:border-primary-blue transition-all duration-300">
                <a href="{{ route('product.show', $related->slug) }}">
                    <div class="relative h-48 bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center overflow-hidden group">
                        @php
                            $relatedDiscount = 0;
                            if($related->original_price && $related->original_price > $related->selling_price) {
                                $relatedDiscount = round((($related->original_price - $related->selling_price) / $related->original_price) * 100);
                            }
                        @endphp

                        @if($relatedDiscount > 0)
                        <span class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded z-10">
                            -{{ $relatedDiscount }}%
                        </span>
                        @endif

                        @if($related->image)
                        <img src="{{ $related->image }}" alt="{{ $related->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                        <div class="text-6xl">🎮</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-white font-semibold mb-2 line-clamp-2">{{ $related->name }}</h3>
                        <div class="flex items-center justify-between">
                            @if($related->original_price && $related->original_price > $related->selling_price)
                            <div>
                                <span class="text-gray-500 line-through text-sm">${{ number_format($related->original_price, 2) }}</span>
                                <span class="text-primary-blue font-bold text-lg ml-1">${{ number_format($related->selling_price, 2) }}</span>
                            </div>
                            @else
                            <span class="text-primary-blue font-bold text-lg">${{ number_format($related->selling_price, 2) }}</span>
                            @endif

                            @if($related->is_available && ($related->stock_quantity === null || $related->stock_quantity > 0))
                            <button onclick="addToCart({{ $related->id }})"
                                    class="p-2 bg-primary-blue text-black rounded hover:bg-[#3fda74] transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

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
    fetch('/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showNotification(data.is_favorited ? 'Added to favorites!' : 'Removed from favorites!', 'success');
            updateFavoritesCount();

            // Update button appearance
            const btn = document.getElementById('favorite-btn-' + productId);
            if (btn) {
                const svg = btn.querySelector('svg');
                if (data.is_favorited) {
                    // Fill the heart and add red color
                    svg.setAttribute('fill', 'currentColor');
                    btn.classList.add('text-red-500', 'border-red-500');
                    btn.classList.remove('text-white');
                } else {
                    // Empty the heart and remove red color
                    svg.setAttribute('fill', 'none');
                    btn.classList.remove('text-red-500', 'border-red-500');
                    btn.classList.add('text-white');
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
    window.location.reload();
}

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

function openReviewModal() {
    // This would open a modal for writing a review
    // For now, just show an alert
    alert('Review modal would open here. This feature needs to be implemented with a proper modal component.');
}

// Initialize favorite button state on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if button has the red color class to ensure proper initial state
    const favoriteBtn = document.querySelector('[id^="favorite-btn-"]');
    if (favoriteBtn && favoriteBtn.classList.contains('text-red-500')) {
        const svg = favoriteBtn.querySelector('svg');
        if (svg) {
            svg.setAttribute('fill', 'currentColor');
        }
    }
});
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
