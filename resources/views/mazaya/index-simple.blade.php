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

    <!-- Gaming Banner -->
    <div class="category-banner" style="margin-bottom: 30px;">
        <h2 class="category-title">{{ app()->getLocale() == 'ar' ? 'الألعاب المدعومة' : 'Supported Games' }}</h2>
        <span class="category-icon">🎮</span>
    </div>

    <!-- Games Grid - 6 Games Only -->
    <div class="products-grid">
        <!-- PUBG Mobile -->
        <a href="{{ route('mazaya.game-selection', 'pubg-mobile-direct') }}" class="product-card-mazaya scroll-animate">
            <div class="product-image-container-mazaya">
                <img src="{{ asset('images/catgorie/PUBG realod.png') }}" alt="PUBG Mobile"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 100%; height: 200px; background: linear-gradient(135deg, #2E2370, #4A3B8C); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;\'>🎮</div>'">
            </div>
            <div class="product-info-mazaya">
                <h4 style="font-size: 1.4rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                    PUBG Mobile
                </h4>
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                    {{ app()->getLocale() == 'ar' ? 'شحن UC فوري' : 'Instant UC Top-Up' }}
                </p>
                <div style="text-align: center;">
                    <span class="order-now-badge">
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                    </span>
                </div>
            </div>
        </a>

        <!-- Free Fire -->
        <a href="{{ route('mazaya.game-selection', 'free-fire-direct') }}" class="product-card-mazaya scroll-animate">
            <div class="product-image-container-mazaya">
                <img src="{{ asset('images/catgorie/FREE FIRE realod.png') }}" alt="Free Fire"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 100%; height: 200px; background: linear-gradient(135deg, #FF6B6B, #FF8E53); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;\'>🔥</div>'">
            </div>
            <div class="product-info-mazaya">
                <h4 style="font-size: 1.4rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                    Free Fire
                </h4>
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                    {{ app()->getLocale() == 'ar' ? 'شحن الماس فوري' : 'Instant Diamonds Top-Up' }}
                </p>
                <div style="text-align: center;">
                    <span class="order-now-badge">
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                    </span>
                </div>
            </div>
        </a>

        <!-- Mobile Legends -->
        <a href="{{ route('mazaya.game-selection', 'mobile-legends-direct') }}" class="product-card-mazaya scroll-animate">
            <div class="product-image-container-mazaya">
                <img src="{{ asset('images/catgorie/mobile legends.png') }}" alt="Mobile Legends"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 100%; height: 200px; background: linear-gradient(135deg, #667EEA, #764BA2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;\'>⚔️</div>'">
            </div>
            <div class="product-info-mazaya">
                <h4 style="font-size: 1.4rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                    Mobile Legends
                </h4>
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                    {{ app()->getLocale() == 'ar' ? 'شحن الماس فوري' : 'Instant Diamonds Top-Up' }}
                </p>
                <div style="text-align: center;">
                    <span class="order-now-badge">
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                    </span>
                </div>
            </div>
        </a>

        <!-- Yalla Ludo -->
        <a href="{{ route('mazaya.game-selection', 'yalla-ludo-direct') }}" class="product-card-mazaya scroll-animate">
            <div class="product-image-container-mazaya">
                <img src="{{ asset('images/catgorie/yala ludo realod.png') }}" alt="Yalla Ludo"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 100%; height: 200px; background: linear-gradient(135deg, #F093FB, #F5576C); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;\'>🎲</div>'">
            </div>
            <div class="product-info-mazaya">
                <h4 style="font-size: 1.4rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                    Yalla Ludo
                </h4>
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                    {{ app()->getLocale() == 'ar' ? 'شحن الماس فوري' : 'Instant Diamonds Top-Up' }}
                </p>
                <div style="text-align: center;">
                    <span class="order-now-badge">
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                    </span>
                </div>
            </div>
        </a>

        <!-- Genshin Impact -->
        <a href="{{ route('mazaya.game-selection', 'genshin-impact-direct') }}" class="product-card-mazaya scroll-animate">
            <div class="product-image-container-mazaya">
                <img src="{{ asset('images/catgorie/genshin impact realod.png') }}" alt="Genshin Impact"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 100%; height: 200px; background: linear-gradient(135deg, #FA709A, #FEE140); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;\'>⭐</div>'">
            </div>
            <div class="product-info-mazaya">
                <h4 style="font-size: 1.4rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                    Genshin Impact
                </h4>
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                    {{ app()->getLocale() == 'ar' ? 'شحن البريمو فوري' : 'Instant Primogems Top-Up' }}
                </p>
                <div style="text-align: center;">
                    <span class="order-now-badge">
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                    </span>
                </div>
            </div>
        </a>

        <!-- FC Mobile -->
        <a href="{{ route('mazaya.game-selection', 'fc-mobile-direct') }}" class="product-card-mazaya scroll-animate">
            <div class="product-image-container-mazaya">
                <img src="{{ asset('images/catgorie/fc26.png') }}" alt="FC Mobile"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px;"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 100%; height: 200px; background: linear-gradient(135deg, #4CAF50, #8BC34A); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem;\'>⚽</div>'">
            </div>
            <div class="product-info-mazaya">
                <h4 style="font-size: 1.4rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                    FC Mobile
                </h4>
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                    {{ app()->getLocale() == 'ar' ? 'شحن نقاط فوري' : 'Instant Points Top-Up' }}
                </p>
                <div style="text-align: center;">
                    <span class="order-now-badge">
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن' : 'Order Now' }} →
                    </span>
                </div>
            </div>
        </a>
    </div>

</div>

@endsection

@push('styles')
<style>
/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 30px;
}

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

.order-now-badge {
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 8px 15px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    display: inline-block;
}

/* Category Banner */
.category-banner {
    background: linear-gradient(135deg, var(--purple-main), var(--purple-dark));
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.category-title {
    color: white;
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
}

.category-icon {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 3rem;
    opacity: 0.3;
}

/* Responsive */
@media (max-width: 1024px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* CSS Variables */
:root {
    --purple-main: #2E2370;
    --purple-dark: #1A1543;
    --gold-main: #F4C430;
    --gold-dark: #D4A520;
}
</style>
@endpush