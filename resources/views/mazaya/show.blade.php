@extends('layouts.app')

@section('title', $product['name'] . ' - Direct Top-Up')

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
        <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
        <span style="color: #999;">/</span>
        <a href="{{ route('mazaya.index') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'الشحن المباشر' : 'Direct Top-Up' }}</a>
        <span style="color: #999;">/</span>
        <span style="color: var(--purple-main); font-weight: 600;">{{ $product['name'] }}</span>
    </nav>

    <!-- Product Card -->
    <div class="product-detail-card">
        <div class="product-detail-grid">
            <!-- Product Image -->
            <div class="product-image-section">
                @if(isset($product['img']) && $product['img'])
                <img src="{{ $product['img'] }}" alt="{{ $product['name'] }}" class="product-main-image">
                @else
                <div class="product-placeholder-image">
                    <div style="font-size: 8rem;">🎮</div>
                </div>
                @endif
            </div>

            <!-- Product Info & Order Form -->
            <div class="product-info-section">
                <!-- Product Name -->
                <h1 class="product-title">{{ $product['name'] }}</h1>

                <!-- Description -->
                @if(isset($product['description']) && $product['description'])
                <p style="font-size: 1rem; color: #666; margin: 15px 0;">
                    {{ $product['description'] }}
                </p>
                @endif

                <!-- Type Badge -->
                <div style="margin: 20px 0;">
                    <span class="category-badge">
                        {{ $product['type'] == 'id' ? (app()->getLocale() == 'ar' ? '⚡ شحن مباشر' : '⚡ Direct Top-Up') : '🎫 Code' }}
                    </span>
                </div>

                <!-- Availability -->
                @if($product['is_available'])
                <div class="availability-badge">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'متوفر - توصيل فوري' : 'Available - Instant Delivery' }}
                </div>
                @else
                <div style="display: inline-flex; align-items: center; gap: 10px; background: #fee; color: #c00; padding: 12px 20px; border-radius: 50px; font-weight: 600;">
                    ❌ {{ app()->getLocale() == 'ar' ? 'غير متوفر' : 'Not Available' }}
                </div>
                @endif

                <hr style="margin: 30px 0; border: none; border-top: 2px solid rgba(46, 35, 112, 0.1);">

                <!-- Order Form -->
                @if($product['is_available'])
                <form action="{{ route('mazaya.order') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                    <!-- Player ID Input -->
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-size: 1.2rem; font-weight: 700; color: var(--purple-main); margin-bottom: 10px;">
                            {{ app()->getLocale() == 'ar' ? 'معرف اللاعب (Player ID) *' : 'Player ID *' }}
                        </label>
                        <input type="text"
                               name="player_id"
                               required
                               placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل معرف اللاعب...' : 'Enter Player ID...' }}"
                               style="width: 100%; padding: 15px 20px; border: 2px solid rgba(46, 35, 112, 0.2); border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s;">
                    </div>

                    <!-- Player Name Input (Optional) -->
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-size: 1.1rem; font-weight: 600; color: var(--purple-main); margin-bottom: 10px;">
                            {{ app()->getLocale() == 'ar' ? 'اسم اللاعب (اختياري)' : 'Player Name (Optional)' }}
                        </label>
                        <input type="text"
                               name="player_name"
                               placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسم اللاعب...' : 'Enter Player Name...' }}"
                               style="width: 100%; padding: 15px 20px; border: 2px solid rgba(46, 35, 112, 0.2); border-radius: 15px; font-size: 1rem;">
                    </div>

                    <!-- Quantity Input -->
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-size: 1.1rem; font-weight: 600; color: var(--purple-main); margin-bottom: 10px;">
                            {{ app()->getLocale() == 'ar' ? 'الكمية' : 'Quantity' }}
                        </label>
                        <div class="quantity-selector">
                            <button type="button" onclick="decreaseQuantity()" class="qty-btn">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" class="qty-input" readonly>
                            <button type="button" onclick="increaseQuantity()" class="qty-btn">+</button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-order-now">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 10px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ app()->getLocale() == 'ar' ? 'اطلب الآن - شحن فوري!' : 'Order Now - Instant Delivery!' }}
                    </button>
                </form>
                @endif

            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
input:focus {
    outline: none;
    border-color: var(--gold-main) !important;
    box-shadow: 0 0 0 3px rgba(244, 196, 48, 0.1);
}

.btn-order-now {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gold-main), var(--gold-dark));
    color: var(--purple-dark);
    padding: 18px 40px;
    border-radius: 50px;
    font-size: 1.3rem;
    font-weight: 800;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 30px rgba(244, 196, 48, 0.4);
    margin-top: 10px;
}

.btn-order-now:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(244, 196, 48, 0.6);
    background: linear-gradient(135deg, var(--gold-light), var(--gold-main));
}

.btn-order-now:active {
    transform: translateY(-1px);
}
</style>
@endpush

@push('scripts')
<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue < 10) {
        input.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

// Form validation
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const playerId = document.querySelector('input[name="player_id"]').value.trim();

    if (!playerId) {
        e.preventDefault();
        alert('{{ app()->getLocale() == 'ar' ? 'يرجى إدخال معرف اللاعب!' : 'Please enter Player ID!' }}');
        return false;
    }

    // Confirm order
    const confirmMsg = '{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من إرسال الطلب؟ سيتم الشحن فوراً!' : 'Are you sure you want to place this order? It will be processed instantly!' }}';
    if (!confirm(confirmMsg)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush
