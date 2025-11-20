@extends('layouts.app')

@section('title', __('shop.title'))

@section('content')

<!-- Page Header -->
<section class="w-full bg-gradient-to-r from-blue-600 to-blue-700 py-12">
    <div class="max-w-[1200px] mx-auto px-5 lg:px-8">
        <h1 class="text-4xl lg:text-5xl font-black text-white mb-4">
            {{ __('shop.header.title') }}
        </h1>
        <p class="text-blue-100 text-lg">
            {{ __('shop.header.subtitle') }}
        </p>
    </div>
</section>

<!-- Shop Content -->
<section class="w-full py-12 bg-gray-50 min-h-screen">
    <div class="max-w-[1200px] mx-auto px-5 lg:px-8">

        @if(request()->has('category'))
            <!-- Category Products View -->
            @php
                $category = \App\Models\Category::where('id', request('category'))
                    ->orWhere('slug', request('category'))
                    ->first();

                $products = $category ?
                    \App\Models\Product::where('category_id', $category->id)
                        ->where('is_active', true)
                        ->where('available', true)
                        ->paginate(24)
                    : collect();
            @endphp

            @if($category)
                <!-- Breadcrumb -->
                <div class="mb-8">
                    <nav class="flex items-center gap-2 text-sm text-gray-600">
                        <a href="{{ route('shop') }}" class="hover:text-blue-600">{{ __('shop.all_categories') }}</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-gray-900 font-semibold">{{ $category->name }}</span>
                    </nav>
                </div>

                <!-- Category Header -->
                <div class="bg-white rounded-2xl p-8 mb-8 shadow-sm">
                    <div class="flex items-center gap-6">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-20 h-20 object-contain">
                        @else
                            <div class="w-20 h-20 bg-blue-100 rounded-xl flex items-center justify-center">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h2 class="text-3xl font-black text-gray-900 mb-2">{{ $category->name }}</h2>
                            <p class="text-gray-600">{{ $products->total() }} {{ __('shop.products_available') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse($products as $product)
                        <a href="{{ route('product.show', $product->id) }}" class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                            <div class="aspect-square bg-gradient-to-br from-blue-100 to-blue-200 relative overflow-hidden">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-20 h-20 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                @endif
                                @if($product->discount > 0)
                                    <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        -{{ $product->discount }}%
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-black text-blue-600">
                                            {{ number_format($product->sell_price, 2) }} {{ $product->currency }}
                                        </div>
                                        @if($product->original_price && $product->original_price > $product->sell_price)
                                            <div class="text-sm text-gray-400 line-through">
                                                {{ number_format($product->original_price, 2) }} {{ $product->currency }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500 text-lg">{{ __('shop.no_products_in_category') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif

        @else
            <!-- All Categories View -->
            @php
                $allCategories = \App\Models\Category::whereNull('parent_id')
                    ->withCount('products')
                    ->orderBy('name')
                    ->get();
            @endphp

            <!-- Search & Filter Bar -->
            <div class="bg-white rounded-2xl p-6 mb-8 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex-1 w-full md:w-auto">
                        <div class="relative">
                            <input type="text" id="categorySearch" placeholder="{{ __('shop.search_categories') }}"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-gray-600">
                        <span class="font-bold text-gray-900">{{ $allCategories->count() }}</span> {{ __('shop.categories_total') }}
                    </div>
                </div>
            </div>

            <!-- Categories Grid -->
            <div id="categoriesGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($allCategories as $category)
                    <a href="{{ route('shop', ['category' => $category->id]) }}" class="category-card group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2" data-name="{{ strtolower($category->name) }}">
                        <div class="aspect-square bg-gradient-to-br from-blue-100 to-blue-200 relative overflow-hidden p-8 flex items-center justify-center">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-24 h-24 bg-blue-300 rounded-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                            @if($category->products_count > 0)
                                <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $category->products_count }}
                                </div>
                            @endif
                        </div>
                        <div class="p-6 bg-white">
                            <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-blue-600 transition-colors">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ $category->products_count }} {{ __('shop.products') }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-16">
                        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500 text-lg mb-4">{{ __('shop.no_categories') }}</p>
                        <p class="text-gray-400">{{ __('shop.sync_hint') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- Empty State for Search -->
            <div id="noResults" class="hidden text-center py-16">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">{{ __('shop.no_results') }}</p>
            </div>

        @endif
    </div>
</section>

<!-- Category Search Script -->
@if(!request()->has('category'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('categorySearch');
    const categoryCards = document.querySelectorAll('.category-card');
    const noResults = document.getElementById('noResults');
    const categoriesGrid = document.getElementById('categoriesGrid');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;

        categoryCards.forEach(card => {
            const categoryName = card.getAttribute('data-name');
            if (categoryName.includes(searchTerm)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            categoriesGrid.classList.add('hidden');
            noResults.classList.remove('hidden');
        } else {
            categoriesGrid.classList.remove('hidden');
            noResults.classList.add('hidden');
        }
    });
});
</script>
@endif

@endsection
