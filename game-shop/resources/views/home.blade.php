@extends('layouts.app')

@section('title', 'Home - Game Shop')

@section('content')
<div class="space-y-24 -mt-8">
    <!-- Hero Section with Carousel -->
    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
        <!-- Dynamic Background -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(73,186,238,0.1),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(73,186,238,0.05),transparent_50%)]"></div>
        </div>

        <!-- Carousel Container -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4">
            <div class="relative h-[600px] flex items-center justify-center" id="carousel-container">
                <!-- Carousel Slides -->
                <div class="slide-item absolute w-[90%] h-full transition-all duration-500 cursor-pointer" data-slide="0">
                    <div class="relative w-full h-full rounded-3xl overflow-hidden group">
                        <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1200&h=600&fit=crop"
                             alt="Gaming Gift Cards"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-12">
                            <span class="inline-block px-4 py-2 bg-[#49baee] text-slate-950 font-bold rounded-lg text-sm mb-4">
                                BEST SELLERS
                            </span>
                            <h2 class="text-5xl font-black text-white mb-2">Gaming Gift Cards</h2>
                            <p class="text-xl text-slate-300 mb-6">Steam, PlayStation, Xbox & More</p>
                            <a href="/shop" class="slide-cta inline-flex items-center gap-2 px-8 py-4 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] hover:shadow-[0_0_30px_rgba(73,186,238,0.5)] transition-all duration-300">
                                Explore Collection
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#49baee]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>

                <div class="slide-item absolute w-[90%] h-full transition-all duration-500 cursor-pointer" data-slide="1">
                    <div class="relative w-full h-full rounded-3xl overflow-hidden group">
                        <img src="https://images.unsplash.com/photo-1556656793-08538906a9f8?w=1200&h=600&fit=crop"
                             alt="Streaming Services"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-12">
                            <span class="inline-block px-4 py-2 bg-[#49baee] text-slate-950 font-bold rounded-lg text-sm mb-4">
                                TRENDING
                            </span>
                            <h2 class="text-5xl font-black text-white mb-2">Streaming Services</h2>
                            <p class="text-xl text-slate-300 mb-6">Netflix, Spotify, Disney+ & More</p>
                            <a href="/shop" class="slide-cta inline-flex items-center gap-2 px-8 py-4 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] hover:shadow-[0_0_30px_rgba(73,186,238,0.5)] transition-all duration-300">
                                Explore Collection
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#49baee]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>

                <div class="slide-item absolute w-[90%] h-full transition-all duration-500 cursor-pointer" data-slide="2">
                    <div class="relative w-full h-full rounded-3xl overflow-hidden group">
                        <img src="https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=1200&h=600&fit=crop"
                             alt="Digital Subscriptions"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-12">
                            <span class="inline-block px-4 py-2 bg-[#49baee] text-slate-950 font-bold rounded-lg text-sm mb-4">
                                HOT DEALS
                            </span>
                            <h2 class="text-5xl font-black text-white mb-2">Digital Subscriptions</h2>
                            <p class="text-xl text-slate-300 mb-6">Premium Services at Best Prices</p>
                            <a href="/shop" class="slide-cta inline-flex items-center gap-2 px-8 py-4 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] hover:shadow-[0_0_30px_rgba(73,186,238,0.5)] transition-all duration-300">
                                Explore Collection
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#49baee]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button onclick="prevSlide()" class="absolute left-4 z-20 p-3 bg-slate-900/80 backdrop-blur-sm rounded-full text-white hover:bg-slate-800 transition-colors group">
                    <svg class="w-6 h-6 rotate-180 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <button onclick="nextSlide()" class="absolute right-4 z-20 p-3 bg-slate-900/80 backdrop-blur-sm rounded-full text-white hover:bg-slate-800 transition-colors group">
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Dots Indicator -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                    <button onclick="goToSlide(0)" class="dot-indicator w-8 h-2 rounded-full bg-[#49baee] transition-all duration-300"></button>
                    <button onclick="goToSlide(1)" class="dot-indicator w-2 h-2 rounded-full bg-slate-600 hover:bg-slate-500 transition-all duration-300"></button>
                    <button onclick="goToSlide(2)" class="dot-indicator w-2 h-2 rounded-full bg-slate-600 hover:bg-slate-500 transition-all duration-300"></button>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="mt-12 flex flex-wrap items-center justify-center gap-8 text-slate-400">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#49baee]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                    </svg>
                    <span class="text-sm font-medium">Instant Delivery</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#49baee]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-sm font-medium">Trusted Service</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#49baee]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">Best Prices</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#49baee]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    <span class="text-sm font-medium">Wide Selection</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="relative">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stats-card bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-[#49baee]/30 transition-all duration-300 group">
                <svg class="w-8 h-8 mx-auto mb-4 text-[#49baee] group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <h3 class="text-3xl font-black text-[#49baee] mb-2">50K+</h3>
                <p class="text-slate-500 text-sm">Happy Customers</p>
            </div>
            <div class="stats-card bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-[#49baee]/30 transition-all duration-300 group">
                <svg class="w-8 h-8 mx-auto mb-4 text-[#49baee] group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
                <h3 class="text-3xl font-black text-[#49baee] mb-2">100+</h3>
                <p class="text-slate-500 text-sm">Digital Products</p>
            </div>
            <div class="stats-card bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-[#49baee]/30 transition-all duration-300 group">
                <svg class="w-8 h-8 mx-auto mb-4 text-[#49baee] group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-3xl font-black text-[#49baee] mb-2">24/7</h3>
                <p class="text-slate-500 text-sm">Instant Delivery</p>
            </div>
            <div class="stats-card bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-[#49baee]/30 transition-all duration-300 group">
                <svg class="w-8 h-8 mx-auto mb-4 text-[#49baee] group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-3xl font-black text-[#49baee] mb-2">30+</h3>
                <p class="text-slate-500 text-sm">Countries Served</p>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section>
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-black mb-4">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#49baee] to-[#7dd3fc]">
                    Shop by Category
                </span>
            </h2>
            <p class="text-slate-400 text-lg">Choose from our wide selection of digital products</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($categories as $category)
            <a href="/shop?category={{ $category->slug }}" class="category-card-link">
                <div class="category-card group hover:scale-105 transition-transform duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-[#49baee] to-[#38a8dc] p-4 shadow-lg group-hover:shadow-[0_0_30px_rgba(73,186,238,0.3)] transition-all duration-300">
                        @if($category->slug == 'game-cards')
                            <svg class="w-full h-full text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        @elseif($category->slug == 'gift-cards')
                            <svg class="w-full h-full text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                            </svg>
                        @elseif($category->slug == 'subscriptions')
                            <svg class="w-full h-full text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"></path>
                                <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"></path>
                                <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"></path>
                            </svg>
                        @else
                            <svg class="w-full h-full text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            </svg>
                        @endif
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-white">{{ $category->name }}</h3>
                    <p class="text-slate-500 text-sm">Browse our collection</p>
                </div>
            </a>
            @endforeach

            <!-- PUBG UC Special Category -->
            <a href="/pubg-uc" class="category-card-link">
                <div class="category-card group hover:scale-105 transition-transform duration-300 bg-gradient-to-br from-yellow-900/20 to-orange-900/20 border-2 border-yellow-500/50">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-yellow-500 to-orange-500 p-4 shadow-lg group-hover:shadow-[0_0_30px_rgba(250,204,21,0.3)] transition-all duration-300">
                        <svg class="w-full h-full text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-yellow-400">PUBG UC Top-Up</h3>
                    <p class="text-yellow-300/70 text-sm">Instant UC delivery</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <section>
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-4xl md:text-5xl font-black">
                <span class="text-[#49baee]">🔥</span> Hot Deals
            </h2>
            <a href="/shop" class="text-[#49baee] hover:text-[#5cc5f5] transition-colors flex items-center gap-2 group font-semibold">
                View All Products
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="product-card bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden hover:border-[#49baee]/30 transition-all duration-300 group">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                        <svg class="w-16 h-16 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-2 text-white group-hover:text-[#49baee] transition-colors">{{ $product->title }}</h3>
                    <p class="text-sm text-slate-500 mb-2">{{ $product->category->name }}</p>
                    <div class="flex items-center mb-4">
                        <span class="text-2xl font-black text-[#49baee]">${{ $product->price }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <a href="/product/{{ $product->id }}" class="text-sm text-slate-400 hover:text-[#49baee] transition-colors">
                            View Details
                        </a>
                        <form action="/cart/add" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="px-4 py-2 bg-[#49baee] text-slate-950 font-bold rounded-lg hover:bg-[#5cc5f5] hover:shadow-[0_0_20px_rgba(73,186,238,0.3)] transition-all duration-300">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/95 to-slate-900 border border-slate-800 p-12">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(73,186,238,0.1),transparent_70%)]"></div>

        <div class="relative z-10 text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#49baee]/20 mb-6">
                <svg class="w-8 h-8 text-[#49baee]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                </svg>
            </div>

            <h2 class="text-4xl font-black mb-4">
                Ready to <span class="text-[#49baee]">Level Up</span>?
            </h2>
            <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                Join thousands of gamers who trust our shop for their digital needs.
                Instant delivery, secure payments, and 24/7 customer support.
            </p>

            <a href="/shop" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-[#49baee] to-[#5cc5f5] text-slate-950 font-bold rounded-xl hover:shadow-[0_0_30px_rgba(73,186,238,0.5)] hover:scale-105 transition-all duration-300">
                Get Started Now
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </section>
</div>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide-item');
    const dots = document.querySelectorAll('.dot-indicator');

    function updateSlides() {
        slides.forEach((slide, index) => {
            const slideElement = slide;
            const cta = slide.querySelector('.slide-cta');

            if (index === currentSlide) {
                // Active slide
                slideElement.style.transform = 'translateX(0) scale(1)';
                slideElement.style.opacity = '1';
                slideElement.style.zIndex = '10';
                if (cta) cta.style.display = 'inline-flex';
            } else if (index === (currentSlide - 1 + slides.length) % slides.length) {
                // Previous slide
                slideElement.style.transform = 'translateX(-85%) scale(0.8)';
                slideElement.style.opacity = '0.5';
                slideElement.style.zIndex = '5';
                if (cta) cta.style.display = 'none';
            } else if (index === (currentSlide + 1) % slides.length) {
                // Next slide
                slideElement.style.transform = 'translateX(85%) scale(0.8)';
                slideElement.style.opacity = '0.5';
                slideElement.style.zIndex = '5';
                if (cta) cta.style.display = 'none';
            } else {
                // Hidden slides
                slideElement.style.transform = 'translateX(0) scale(0.8)';
                slideElement.style.opacity = '0';
                slideElement.style.zIndex = '1';
                if (cta) cta.style.display = 'none';
            }
        });

        // Update dots
        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.classList.add('w-8', 'bg-[#49baee]');
                dot.classList.remove('w-2', 'bg-slate-600');
            } else {
                dot.classList.remove('w-8', 'bg-[#49baee]');
                dot.classList.add('w-2', 'bg-slate-600');
            }
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlides();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlides();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlides();
    }

    // Initialize slides
    updateSlides();

    // Auto-slide
    setInterval(nextSlide, 5000);

    // Add click handlers to slides
    slides.forEach((slide, index) => {
        slide.addEventListener('click', function(e) {
            if (!e.target.closest('a')) {
                if (index === (currentSlide - 1 + slides.length) % slides.length) {
                    prevSlide();
                } else if (index === (currentSlide + 1) % slides.length) {
                    nextSlide();
                }
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements
    document.querySelectorAll('.stats-card, .category-card, .product-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
</script>
@endsection
