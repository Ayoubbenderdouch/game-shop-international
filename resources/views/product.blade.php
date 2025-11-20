@extends('layouts.app')

@section('title', ($product->name ?? 'Product') . ' - GameShop')

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
        <a href="{{ route('home') }}" style="color: #666; text-decoration: none; transition: color 0.3s;">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
        <span style="color: #999;">/</span>
        @if($product->category)
        <a href="{{ route('shop') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'المتجر' : 'Shop' }}</a>
        <span style="color: #999;">/</span>
        <span style="color: var(--purple-main); font-weight: 600;">{{ $product->category->name }}</span>
        @endif
    </nav>

    <!-- Product Card -->
    <div class="product-detail-card">
        <div class="product-detail-grid">
            <!-- Product Image -->
            <div class="product-image-section">
                @php
                    $giftCardImages = [
                        'KSA Google play' => 'GiftCard/google play ksa.jpg',
                        'UAE Google play' => 'GiftCard/goole play uae.jpg',
                        'USA Google play' => 'GiftCard/google play usa.jpg',
                        'Apple Gift Card - USA' => 'GiftCard/itunes usa.jpg',
                        'Apple Gift Card  - KSA' => 'GiftCard/itunes ksa.jpg',
                        'Apple Gift Card - UAE' => 'GiftCard/itunes uau.jpg',
                    ];
                    $categoryName = $product->category->name ?? '';
                    $productImage = $giftCardImages[$categoryName] ?? null;
                @endphp

                @if($productImage && file_exists(public_path('images/' . $productImage)))
                <img src="{{ asset('images/' . $productImage) }}" alt="{{ $product->name }}" class="product-main-image">
                @elseif($product->image)
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-main-image">
                @else
                <div class="product-placeholder-image">
                    <div style="font-size: 5rem;">🎮</div>
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <!-- Category Badge -->
                @if($product->category)
                <div style="margin-bottom: 15px;">
                    <span class="category-badge">{{ $product->category->name }}</span>
                </div>
                @endif

                <!-- Product Name -->
                <h1 class="product-title">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="product-price-section">
                    <div class="product-current-price">
                        <x-price :price="$product->selling_price" />
                    </div>
                    @php
                        $currency = app(\App\Services\CurrencyService::class)->getUserCurrency();
                    @endphp
                    @if($currency !== 'USD')
                        <p style="font-size: 0.85rem; color: #999; margin-top: 5px;">
                            {{ __('Original Price') }}: ${{ number_format($product->selling_price, 2) }} USD
                        </p>
                    @endif
                    @if($product->vat_percentage > 0)
                    <p style="font-size: 0.85rem; color: #999; margin-top: 5px;">* {{ __('Includes') }} {{ $product->vat_percentage }}% {{ __('VAT') }}</p>
                    @endif
                </div>

                <!-- Availability -->
                <div class="availability-badge">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'متوفر - توصيل فوري' : 'Available - Instant Delivery' }}
                </div>

                <!-- Amount Selection -->
                @if(isset($amountVariations) && $amountVariations->count() > 1)
                <div class="amount-selection-section">
                    <label class="amount-selection-label">
                        {{ app()->getLocale() == 'ar' ? 'اختر المبلغ:' : 'Select Amount:' }}
                    </label>
                    <div class="amounts-grid">
                        @foreach($amountVariations as $variation)
                        <a href="{{ route('product.show', $variation->slug) }}"
                           class="amount-card {{ $variation->id == $product->id ? 'active' : '' }}">
                            <div class="amount-badge-main">
                                @php
                                    $variationCurrency = $variation->currency == 'SAR' ? 'SAR' : ($variation->currency == 'AED' ? 'AED' : '$');
                                    preg_match('/(\d+)/', $variation->name, $matches);
                                    $amount = $matches[0] ?? number_format($variation->selling_price, 0);
                                @endphp
                                {{ $variationCurrency }} {{ $amount }}
                            </div>
                            <div class="amount-price-main">
                                <x-price :price="$variation->selling_price" />
                            </div>
                            @if($variation->id == $product->id)
                            <div class="amount-checkmark">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                            </div>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Buy Now Button -->
                @if($product->is_available)
                <div class="add-to-cart-section">
                    <form action="{{ route('cart.add') }}" method="POST" style="display: flex; gap: 15px; align-items: center;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="quantity-selector">
                            <button type="button" onclick="decreaseQuantity()" class="qty-btn">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" class="qty-input" readonly>
                            <button type="button" onclick="increaseQuantity()" class="qty-btn">+</button>
                        </div>

                        <button type="submit" class="btn-add-to-cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'اشتر الآن' : 'Buy Now' }}
                        </button>
                    </form>
                </div>
                @endif

                <!-- Description -->
                @if($product->description && $product->description != $product->name)
                <div class="product-description-section">
                    <h3 class="section-title">{{ app()->getLocale() == 'ar' ? 'الوصف' : 'Description' }}</h3>
                    <p class="description-text">{{ $product->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
/* Product Detail Card */
.product-detail-card {
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 2px solid rgba(46, 35, 112, 0.1);
    border-radius: 25px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(46, 35, 112, 0.1);
}

.product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 50px;
    align-items: start;
}

@media (max-width: 968px) {
    .product-detail-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .product-detail-card {
        padding: 25px;
    }
}

/* Product Image */
.product-image-section {
    position: relative;
}

.product-main-image {
    width: 100%;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(46, 35, 112, 0.15);
}

.product-placeholder-image {
    width: 100%;
    aspect-ratio: 1;
    background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Product Info */
.product-info-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.category-badge {
    display: inline-block;
    background: linear-gradient(135deg, #2E2370, #1F1851);
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
}

.product-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--purple-main);
    margin: 0;
    line-height: 1.3;
}

@media (max-width: 768px) {
    .product-title {
        font-size: 1.8rem;
    }
}

.product-price-section {
    padding: 20px 0;
    border-top: 2px solid rgba(46, 35, 112, 0.1);
    border-bottom: 2px solid rgba(46, 35, 112, 0.1);
}

.product-current-price {
    font-size: 3rem;
    font-weight: 800;
    color: var(--gold-main);
}

.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    padding: 12px 20px;
    border-radius: 50px;
    font-weight: 600;
    width: fit-content;
}

/* Amount Selection */
.amount-selection-section {
    margin: 10px 0;
}

.amount-selection-label {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--purple-main);
    margin-bottom: 15px;
}

.amounts-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

@media (max-width: 1024px) {
    .amounts-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .amounts-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .amounts-grid {
        grid-template-columns: 1fr;
    }
}

.amount-card {
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 2px solid rgba(46, 35, 112, 0.15);
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.amount-card:hover {
    transform: translateY(-5px);
    border-color: var(--gold-main);
    box-shadow: 0 8px 25px rgba(244, 196, 48, 0.3);
}

.amount-card.active {
    border-color: var(--gold-main);
    background: linear-gradient(145deg, #fffef5, #fffaeb);
    box-shadow: 0 8px 25px rgba(244, 196, 48, 0.4);
}

.amount-badge-main {
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 10px 15px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: 10px;
}

.amount-price-main {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--purple-main);
}

.amount-checkmark {
    position: absolute;
    top: 8px;
    right: 8px;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(244, 196, 48, 0.5);
}

/* Add to Cart Section */
.add-to-cart-section {
    margin: 20px 0;
}

.quantity-selector {
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid rgba(46, 35, 112, 0.2);
    border-radius: 50px;
    overflow: hidden;
}

.qty-btn {
    width: 45px;
    height: 45px;
    background: transparent;
    border: none;
    color: var(--purple-main);
    font-size: 1.5rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.qty-btn:hover {
    background: var(--gold-main);
    color: var(--purple-dark);
}

.qty-input {
    width: 60px;
    text-align: center;
    border: none;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--purple-main);
    background: transparent;
}

.btn-add-to-cart {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 15px 35px;
    border-radius: 50px;
    font-size: 1.15rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(244, 196, 48, 0.3);
}

.btn-add-to-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(244, 196, 48, 0.5);
    background: linear-gradient(135deg, var(--gold-light), var(--gold-main));
}

/* Description */
.product-description-section {
    padding: 20px 0;
    border-top: 2px solid rgba(46, 35, 112, 0.1);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--purple-main);
    margin-bottom: 15px;
}

.description-text {
    font-size: 1rem;
    color: #666;
    line-height: 1.8;
}
</style>
@endpush

@push('scripts')
<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    input.value = currentValue + 1;
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}
</script>
@endpush
