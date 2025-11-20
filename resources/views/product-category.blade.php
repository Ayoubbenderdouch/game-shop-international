@extends('layouts.app')

@section('title', $category . ' - X-Reload')

@section('content')

<div class="container-abady" style="padding: 40px 20px;">

    <!-- Category Header -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--purple-main); margin-bottom: 10px;">
            {{ $category }}
        </h1>
        <p style="color: #666; font-size: 1.1rem;">
            {{ app()->getLocale() == 'ar' ? 'اختر الكمية المناسبة لك' : 'Choose the amount that suits you' }}
        </p>
    </div>

    <!-- Price Options Grid -->
    <div class="price-options-grid">
        @foreach($options as $option)
        <div class="price-option-card">
            <div class="option-image">
                <img src="{{ asset('images/GiftCard/' . $option['image']) }}" alt="{{ $option['name'] }}">
            </div>
            <div class="option-details">
                <h3 class="option-name">{{ $option['name'] }}</h3>
                <div class="option-price">€{{ $option['price'] }}</div>
                <button class="buy-button" onclick="addToCart('{{ $option['name'] }}', '{{ $option['price'] }}')">
                    <span>{{ app()->getLocale() == 'ar' ? 'اشتري الآن' : 'Buy Now' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection

@push('styles')
<style>
.price-options-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

.price-option-card {
    background: white;
    border: 2px solid rgba(244, 196, 48, 0.3);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s;
    box-shadow: 0 5px 20px rgba(46, 35, 112, 0.1);
}

.price-option-card:hover {
    transform: translateY(-10px);
    border-color: var(--gold-main);
    box-shadow: 0 15px 40px rgba(244, 196, 48, 0.3);
}

.option-image {
    width: 100%;
    height: 300px;
    overflow: hidden;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.option-details {
    padding: 25px;
    text-align: center;
}

.option-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--purple-main);
    margin-bottom: 15px;
}

.option-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--gold-main);
    margin-bottom: 20px;
    text-shadow: 1px 1px 3px rgba(244, 196, 48, 0.3);
}

.buy-button {
    width: 100%;
    background: linear-gradient(135deg, var(--purple-main) 0%, var(--purple-dark) 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 5px 20px rgba(46, 35, 112, 0.3);
}

.buy-button:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 30px rgba(46, 35, 112, 0.5);
}

@media (max-width: 1024px) {
    .price-options-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .price-options-grid {
        grid-template-columns: 1fr;
    }

    .option-image {
        height: 250px;
    }
}
</style>
@endpush

@push('scripts')
<script>
function addToCart(name, price) {
    alert('{{ app()->getLocale() == "ar" ? "تمت الإضافة إلى السلة" : "Added to cart" }}: ' + name + ' (€' + price + ')');
    // TODO: Implement real cart functionality
}
</script>
@endpush
