@extends('layouts.app')

@section('title', $game['name'] . ' ' . $region['name'] . ' - ' . (app()->getLocale() == 'ar' ? 'شحن مباشر' : 'Direct Top-Up'))

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
        <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
        <span style="color: #999;">/</span>
        <a href="{{ route('mazaya.game-selection', $gameSlug) }}" style="color: #666; text-decoration: none;">{{ $game['name'] }}</a>
        <span style="color: #999;">/</span>
        <span style="color: var(--purple-main); font-weight: 600;">{{ app()->getLocale() == 'ar' ? $region['name_ar'] : $region['name'] }}</span>
    </nav>

    @if(count($products) > 0)
    <!-- Product Card -->
    <div class="product-detail-card">
        <div class="product-detail-grid">
            <!-- Product Image -->
            <div class="product-image-section">
                @php
                    $gameImages = [
                        'PUBG Mobile' => 'catgorie/PUBG realod.png',
                        'Free Fire' => 'catgorie/FREE FIRE realod.png',
                        'Mobile Legends' => 'catgorie/mobile-legends.jpg',
                        'Yalla Ludo' => 'catgorie/yala ludo realod.png',
                        'Genshin Impact' => 'catgorie/genshin impact realod.png',
                        'FC Mobile' => 'catgorie/fc26.png',
                    ];
                    $gameImage = $gameImages[$game['name']] ?? null;
                @endphp

                @if($gameImage && file_exists(public_path('images/' . $gameImage)))
                <img src="{{ asset('images/' . $gameImage) }}" alt="{{ $game['name'] }}" class="product-main-image">
                @else
                <div class="product-placeholder-image">
                    <div style="font-size: 8rem;">⚡</div>
                    <h3 style="color: var(--gold-main); font-size: 2rem; font-weight: 800; margin-top: 20px;">{{ $game['name'] }}</h3>
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <!-- Category Badge -->
                <div style="margin-bottom: 15px;">
                    <span class="category-badge">{{ $game['name'] }}</span>
                </div>

                <!-- Product Name -->
                <h1 class="product-title">{{ $game['name'] }} {{ app()->getLocale() == 'ar' ? $region['name_ar'] : $region['name'] }}</h1>

                <!-- Price Display -->
                <div class="product-price-section" id="price-display">
                    <div class="product-current-price">
                        $<span id="selected-price">{{ number_format($products[0]['price'], 2) }}</span>
                    </div>
                </div>

                <!-- Availability -->
                <div class="availability-badge">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'متوفر - توصيل فوري' : 'Available - Instant Delivery' }}
                </div>

                <!-- Amount Selection -->
                <div class="amount-selection-section">
                    <label class="amount-selection-label">
                        {{ app()->getLocale() == 'ar' ? 'اختر المبلغ:' : 'Select Amount:' }}
                    </label>
                    <div class="amounts-grid">
                        @foreach($products as $index => $product)
                        <div class="amount-card {{ $index == 0 ? 'active' : '' }}"
                             onclick="selectAmount({{ $product['id'] }}, '{{ addslashes($product['name']) }}', {{ $product['price'] }})">
                            <div class="amount-badge-main">
                                @php
                                    // Extract UC/Diamond amount from product name
                                    preg_match('/(\d+)/', $product['name'], $matches);
                                    $amount = $matches[0] ?? '';
                                @endphp
                                {{ $amount }} {{ strpos(strtolower($game['name']), 'pubg') !== false ? 'UC' : (strpos(strtolower($game['name']), 'free fire') !== false ? 'Diamonds' : (strpos(strtolower($game['name']), 'mobile legends') !== false ? 'Diamonds' : 'Coins')) }}
                            </div>
                            <div class="amount-price-main">
                                ${{ number_format($product['price'], 2) }}
                            </div>
                            <div class="amount-checkmark" style="display: {{ $index == 0 ? 'flex' : 'none' }};">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Player ID Form -->
                <form action="{{ route('mazaya.order') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="product_id" id="product_id" value="{{ $products[0]['id'] }}">

                    <!-- Player ID Input -->
                    <div style="margin-bottom: 20px;">
                        <label class="amount-selection-label">
                            {{ app()->getLocale() == 'ar' ? 'معرف اللاعب' : 'Player ID' }} <span style="color: red;">*</span>
                        </label>
                        <input type="text" name="player_id" id="player_id" required
                               style="width: 100%; padding: 15px; border: 2px solid rgba(46, 35, 112, 0.2); border-radius: 12px; font-size: 1rem; transition: all 0.3s ease;"
                               placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل معرف اللاعب الخاص بك' : 'Enter your Player ID' }}"
                               onfocus="this.style.borderColor='var(--purple-main)'"
                               onblur="this.style.borderColor='rgba(46, 35, 112, 0.2)'">
                    </div>

                    <!-- Player Name Input (Optional) -->
                    <div style="margin-bottom: 25px;">
                        <label class="amount-selection-label">
                            {{ app()->getLocale() == 'ar' ? 'اسم اللاعب (اختياري)' : 'Player Name (Optional)' }}
                        </label>
                        <input type="text" name="player_name" id="player_name"
                               style="width: 100%; padding: 15px; border: 2px solid rgba(46, 35, 112, 0.2); border-radius: 12px; font-size: 1rem; transition: all 0.3s ease;"
                               placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسم اللاعب (اختياري)' : 'Enter player name (optional)' }}"
                               onfocus="this.style.borderColor='var(--purple-main)'"
                               onblur="this.style.borderColor='rgba(46, 35, 112, 0.2)'">
                    </div>

                    <!-- Buy Now Button -->
                    <div class="add-to-cart-section">
                        <button type="submit" class="btn-add-to-cart" style="width: 100%;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ app()->getLocale() == 'ar' ? 'اشتر الآن' : 'Buy Now' }}
                        </button>
                    </div>
                </form>

                <!-- Description -->
                <div class="product-description-section" style="margin-top: 30px;">
                    <h3 class="section-title">{{ app()->getLocale() == 'ar' ? 'الوصف' : 'Description' }}</h3>
                    <p class="description-text">
                        {{ app()->getLocale() == 'ar' ? 'شحن مباشر عبر معرف اللاعب. سيتم إضافة الرصيد إلى حسابك فوراً بعد الدفع.' : 'Direct top-up via Player ID. Balance will be added to your account instantly after payment.' }}
                    </p>
                </div>
            </div>
        </div>
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
        <a href="{{ route('home') }}" class="btn-add-to-cart" style="display: inline-block; width: auto; padding: 15px 40px; text-decoration: none;">
            {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
        </a>
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
/* Enhanced Product Detail Card */
.product-detail-card {
    background: linear-gradient(145deg, #ffffff, #fafafa);
    border: 2px solid rgba(46, 35, 112, 0.08);
    border-radius: 30px;
    padding: 50px;
    box-shadow: 0 20px 60px rgba(46, 35, 112, 0.12);
    transition: all 0.3s ease;
}

.product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: 60px;
    align-items: start;
}

@media (max-width: 968px) {
    .product-detail-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .product-detail-card {
        padding: 30px;
    }
}

/* Enhanced Product Image */
.product-image-section {
    position: relative;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 15px 45px rgba(46, 35, 112, 0.2);
    transition: transform 0.3s ease;
}

.product-image-section:hover {
    transform: scale(1.02);
}

.product-main-image {
    width: 100%;
    border-radius: 25px;
    display: block;
}

.product-placeholder-image {
    width: 100%;
    aspect-ratio: 1;
    background: linear-gradient(135deg, var(--purple-main), var(--purple-dark));
    border-radius: 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Enhanced Category Badge */
.category-badge {
    display: inline-block;
    background: linear-gradient(135deg, var(--purple-main), var(--purple-dark));
    color: white;
    padding: 10px 25px;
    border-radius: 50px;
    font-size: 0.95rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 5px 15px rgba(46, 35, 112, 0.3);
}

/* Enhanced Product Title */
.product-title {
    font-size: 2.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--purple-main), var(--purple-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    line-height: 1.2;
}

@media (max-width: 768px) {
    .product-title {
        font-size: 2rem;
    }
}

/* Enhanced Price Section */
.product-price-section {
    padding: 25px;
    border-radius: 20px;
    background: linear-gradient(135deg, #fff9e6, #fffaeb);
    border: 2px solid var(--gold-main);
    box-shadow: 0 8px 25px rgba(244, 196, 48, 0.2);
}

.product-current-price {
    font-size: 3.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 10px rgba(244, 196, 48, 0.3);
}

/* Enhanced Availability Badge */
.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.95rem;
    box-shadow: 0 5px 15px rgba(21, 87, 36, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Enhanced Amount Selection */
.amount-selection-section {
    margin: 25px 0;
}

.amount-selection-label {
    display: block;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--purple-main);
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.amounts-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
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

/* Enhanced Amount Cards */
.amount-card {
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border: 3px solid rgba(46, 35, 112, 0.12);
    border-radius: 20px;
    padding: 25px 20px;
    text-align: center;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    cursor: pointer;
    overflow: hidden;
}

.amount-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-light));
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.amount-card:hover::before {
    opacity: 0.1;
}

.amount-card:hover {
    transform: translateY(-8px) scale(1.03);
    border-color: var(--gold-main);
    box-shadow: 0 15px 40px rgba(244, 196, 48, 0.35);
}

.amount-card.active {
    border-color: var(--gold-main);
    background: linear-gradient(145deg, #fffef5, #fffaeb);
    box-shadow: 0 15px 45px rgba(244, 196, 48, 0.5);
    transform: translateY(-5px);
}

.amount-card.active::before {
    opacity: 0.15;
}

.amount-badge-main {
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 12px 18px;
    border-radius: 50px;
    font-size: 1.15rem;
    font-weight: 900;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
    box-shadow: 0 5px 20px rgba(244, 196, 48, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.amount-price-main {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--purple-main);
    position: relative;
    z-index: 1;
}

.amount-checkmark {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(244, 196, 48, 0.6);
    animation: checkmarkPop 0.3s ease;
    z-index: 2;
}

@keyframes checkmarkPop {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Enhanced Input Fields */
input[type="text"] {
    width: 100%;
    padding: 18px 20px;
    border: 3px solid rgba(46, 35, 112, 0.15);
    border-radius: 15px;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff, #fafafa);
    font-weight: 600;
    color: var(--purple-dark);
}

input[type="text"]:focus {
    border-color: var(--purple-main);
    box-shadow: 0 8px 25px rgba(46, 35, 112, 0.2);
    background: white;
    outline: none;
    transform: translateY(-2px);
}

input[type="text"]::placeholder {
    color: #999;
    font-weight: 500;
}

/* Enhanced Buy Button */
.btn-add-to-cart {
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 20px 40px;
    border-radius: 50px;
    font-size: 1.3rem;
    font-weight: 900;
    text-align: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 30px rgba(244, 196, 48, 0.4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
}

.btn-add-to-cart::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-add-to-cart:hover::before {
    width: 300px;
    height: 300px;
}

.btn-add-to-cart:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 45px rgba(244, 196, 48, 0.6);
}

.btn-add-to-cart:active {
    transform: translateY(-2px) scale(1.02);
}

/* Enhanced Description Section */
.product-description-section {
    background: linear-gradient(145deg, #f8f9fa, #e9ecef);
    padding: 25px;
    border-radius: 20px;
    border-left: 5px solid var(--purple-main);
}

.section-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--purple-main);
    margin: 0 0 15px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.description-text {
    font-size: 1.05rem;
    color: #555;
    line-height: 1.7;
    margin: 0;
}

/* Animation for page load */
.product-detail-card {
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@push('scripts')
<script>
// Amount Selection
function selectAmount(productId, productName, productPrice) {
    // Update hidden input
    document.getElementById('product_id').value = productId;

    // Update price display
    document.getElementById('selected-price').textContent = productPrice.toFixed(2);

    // Update active state
    const cards = document.querySelectorAll('.amount-card');
    cards.forEach(card => {
        card.classList.remove('active');
        const checkmark = card.querySelector('.amount-checkmark');
        if (checkmark) {
            checkmark.style.display = 'none';
        }
    });

    // Add active class to clicked card
    event.currentTarget.classList.add('active');
    const activeCheckmark = event.currentTarget.querySelector('.amount-checkmark');
    if (activeCheckmark) {
        activeCheckmark.style.display = 'flex';
    }
}
</script>
@endpush
