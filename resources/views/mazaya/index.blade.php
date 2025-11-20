@extends('layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'الشحن المباشر' : 'Direct Top-Up') . ' - Gaming Store')

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Page Header -->
    <div style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 3rem; font-weight: 800; color: var(--purple-main); margin-bottom: 15px;">
            {{ app()->getLocale() == 'ar' ? 'الشحن المباشر' : 'Direct Top-Up' }} ⚡
        </h1>
        <p style="font-size: 1.2rem; color: #666;">
            {{ app()->getLocale() == 'ar' ? 'اشحن حسابك مباشرة بدون كود - توصيل فوري!' : 'Top-up your account directly without codes - Instant Delivery!' }}
        </p>
    </div>

    @if(isset($error))
    <div style="background: #fee; border: 2px solid #fcc; border-radius: 15px; padding: 20px; margin-bottom: 30px; text-align: center;">
        <p style="color: #c00; font-weight: 600;">{{ $error }}</p>
    </div>
    @endif

    <!-- Gaming Banner -->
    <div class="category-banner" style="margin-bottom: 30px;">
        <h2 class="category-title">{{ app()->getLocale() == 'ar' ? 'الألعاب المدعومة' : 'Supported Games' }}</h2>
        <span class="category-icon">🎮</span>
    </div>

    <!-- Products Grid by Game -->
    @if(isset($gameProducts) && count($gameProducts) > 0)
        @foreach($gameProducts as $gameName => $products)
        <div style="margin-bottom: 50px;">
            <!-- Game Name Header -->
            <div style="background: linear-gradient(135deg, var(--gold-main), var(--gold-dark)); padding: 15px 25px; border-radius: 15px; margin-bottom: 20px;">
                <h3 style="font-size: 1.8rem; font-weight: 700; color: var(--purple-dark); margin: 0;">
                    {{ $gameName }}
                </h3>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                @foreach($products as $product)
                <div class="product-card-mazaya scroll-animate">
                    <!-- Product Image -->
                    <div class="product-image-container-mazaya">
                        @if(isset($product['img']) && $product['img'])
                        <img src="{{ $product['img'] }}" alt="{{ $product['name'] }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;">
                        @else
                        <div style="width: 100%; height: 200px; background: linear-gradient(135deg, var(--purple-main), var(--purple-dark)); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                            🎮
                        </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="product-info-mazaya">
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px; min-height: 50px;">
                            {{ $product['name'] }}
                        </h4>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.9rem; color: #666;">
                                {{ $product['type'] == 'id' ? (app()->getLocale() == 'ar' ? 'شحن مباشر' : 'Direct Top-Up') : 'Code' }}
                            </span>

                            <span style="background: linear-gradient(135deg, var(--gold-main), var(--gold-dark)); color: var(--purple-dark); padding: 8px 15px; border-radius: 50px; font-weight: 700; font-size: 1rem;">
                                {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @else
    <!-- No Products Available -->
    <div style="text-align: center; padding: 80px 20px;">
        <div style="font-size: 6rem; margin-bottom: 25px;">🎮</div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--purple-main); margin-bottom: 15px;">
            {{ app()->getLocale() == 'ar' ? 'لا توجد منتجات متاحة حاليًا' : 'No products available at the moment' }}
        </h3>
        <p style="font-size: 1.2rem; color: #666;">
            {{ app()->getLocale() == 'ar' ? 'يرجى المحاولة مرة أخرى لاحقًا' : 'Please try again later' }}
        </p>
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
/* Mazaya Product Cards */
.product-card-mazaya {
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 2px solid rgba(46, 35, 112, 0.15);
    border-radius: 20px;
    padding: 20px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
    overflow: hidden;
}

.product-card-mazaya:hover {
    transform: translateY(-8px);
    border-color: var(--gold-main);
    box-shadow: 0 15px 40px rgba(244, 196, 48, 0.4);
}

.product-image-container-mazaya {
    margin-bottom: 15px;
    overflow: hidden;
    border-radius: 15px;
}

.product-info-mazaya {
    padding-top: 10px;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
