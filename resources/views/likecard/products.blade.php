@extends('layouts.app')

@section('title', $category . ' - Gaming Store')

@section('content')

<div class="container-abady" style="padding-top: 20px;">

    <!-- Category Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--purple-main); margin-bottom: 10px;">
            {{ $category }}
        </h1>
        <p style="font-size: 1.1rem; color: #666;">
            {{ app()->getLocale() == 'ar' ? 'اختر الباقة التي تناسبك' : 'Choose the package that suits you' }}
        </p>
    </div>

    @if($products->count() > 0)
    <!-- Products Grid -->
    <div class="products-grid-likecard">
        @foreach($products as $product)
        <div class="product-card-likecard scroll-animate">
            <div class="product-card-header">
                <h3 class="likecard-product-name">{{ $product->name }}</h3>
            </div>

            <div class="product-card-body">
                @if($product->image)
                <div class="product-image">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" onerror="this.style.display='none'">
                </div>
                @endif

                <div class="likecard-price">
                    <span class="currency">{{ $product->currency == 'SAR' ? 'SAR' : '$' }}</span>
                    <span class="amount">{{ number_format($product->selling_price, 2) }}</span>
                </div>

                @if($product->description && $product->description !== $product->name)
                <p class="product-description">{{ $product->description }}</p>
                @endif
            </div>

            <div class="product-card-footer">
                <a href="{{ route('product.show', $product->slug) }}" class="btn-buy-likecard">
                    {{ __('app.home.buy_now') }}
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Coming Soon -->
    <div style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 5rem; margin-bottom: 20px;">⏳</div>
        <h2 style="font-size: 2rem; font-weight: 800; color: #2E2370; margin-bottom: 15px;">
            {{ app()->getLocale() == 'ar' ? 'قريباً' : 'Coming Soon' }}
        </h2>
        <p style="font-size: 1.1rem; color: #666; margin-bottom: 10px;">
            {{ app()->getLocale() == 'ar' ? 'هذا المنتج غير متوفر حالياً' : 'This product is not available yet' }}
        </p>
        <p style="font-size: 1rem; color: #999; margin-bottom: 40px;">
            {{ app()->getLocale() == 'ar' ? 'سيكون متاحاً قريباً جداً!' : 'It will be available very soon!' }}
        </p>
        <a href="{{ route('home') }}" class="btn-buy-likecard" style="display: inline-block; width: auto; padding: 15px 40px;">
            {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
        </a>
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
/* Products Grid - LikeCard */
.products-grid-likecard {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    padding: 20px 0;
    width: 100%;
    max-width: 100%;
}

@media (max-width: 768px) {
    .products-grid-likecard {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .products-grid-likecard {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

/* Product Card - LikeCard */
.product-card-likecard {
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 2px solid rgba(46, 35, 112, 0.1);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(46, 35, 112, 0.1);
}

.product-card-likecard:hover {
    transform: translateY(-5px);
    border-color: var(--gold-main);
    box-shadow: 0 10px 30px rgba(244, 196, 48, 0.3);
}

.product-card-header {
    background: linear-gradient(135deg, #2E2370, #1F1851);
    padding: 20px;
    text-align: center;
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.likecard-product-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #FFFFFF !important;
    margin: 0;
    line-height: 1.4;
    text-align: center;
    display: block !important;
}

.product-card-body {
    padding: 30px 20px;
    text-align: center;
}

.product-image {
    margin-bottom: 15px;
}

.product-image img {
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
    border-radius: 10px;
}

.likecard-price {
    font-size: 3rem;
    font-weight: 800;
    color: #F4C430 !important;
    margin-bottom: 15px;
    line-height: 1;
    background: transparent !important;
    padding: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

.likecard-price .currency {
    font-size: 2rem;
    vertical-align: top;
    color: #F4C430 !important;
}

.likecard-price .amount {
    color: #F4C430 !important;
}

.product-description {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
    line-height: 1.5;
}

.product-card-footer {
    padding: 0 20px 20px 20px;
}

.btn-buy-likecard {
    display: block;
    width: 100%;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(244, 196, 48, 0.3);
}

.btn-buy-likecard:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(244, 196, 48, 0.5);
    background: linear-gradient(135deg, var(--gold-light), var(--gold-main));
}
</style>
@endpush

@push('scripts')
<script>
// Scroll Animation Observer
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const animatedElements = document.querySelectorAll('.scroll-animate');
    animatedElements.forEach(element => {
        observer.observe(element);
    });
});
</script>
@endpush
