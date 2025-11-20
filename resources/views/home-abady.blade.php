@extends('layouts.app')

@section('title', 'Home - Gaming Store')

@section('content')

<div class="container-abady">

    <!-- Direct Top-Up Games Banner -->
    <img src="{{ asset('images/banner/direct-topup-banner.jpg') }}" alt="Direct Top-Up" style="width: 100%; max-width: 100%; height: auto; border-radius: 20px; margin: 2px 0; box-shadow: 0 10px 50px rgba(0,0,0,0.3); display: block;">

    <!-- Products Grid - Direct Top-Up -->
    <div class="products-grid-3col" style="display: grid !important; grid-template-columns: repeat(3, 1fr) !important; gap: 0px;">
        @php
            $mazayaGames = [
                ['name' => 'PUBG Mobile', 'image' => 'PUBG realod.png', 'slug' => 'pubg-mobile-direct', 'categoryId' => 4],
                ['name' => 'Free Fire', 'image' => 'FREE FIRE realod.png', 'slug' => 'free-fire-direct', 'categoryId' => 5],
                ['name' => 'Mobile Legends', 'image' => 'mobile-legends-direct.png', 'slug' => 'mobile-legends-direct', 'categoryId' => 172],
                ['name' => 'Yalla Ludo', 'image' => 'yala ludo realod.png', 'slug' => 'yalla-ludo-direct', 'categoryId' => 185],
                ['name' => 'Genshin Impact', 'image' => 'genshin impact realod.png', 'slug' => 'genshin-impact-direct', 'categoryId' => 171],
                ['name' => 'FC Mobile', 'image' => 'fc-mobile-direct.png', 'slug' => 'fc-mobile-direct', 'categoryId' => 12],
            ];
        @endphp

        @foreach($mazayaGames as $game)
        <a href="{{ route('mazaya.game-selection', $game['slug']) }}" class="product-card-abady scroll-animate">
            <div class="product-image-container">
                @if(file_exists(public_path('images/catgorie/' . $game['image'])))
                <img src="{{ asset('images/catgorie/' . $game['image']) }}" alt="{{ $game['name'] }}">
                @else
                <div style="width: 100%; height: 600px; background: linear-gradient(135deg, var(--purple-main), var(--purple-dark)); border-radius: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="font-size: 8rem; margin-bottom: 20px;">⚡</div>
                    <h3 style="color: var(--gold-main); font-size: 2rem; font-weight: 800;">{{ $game['name'] }}</h3>
                    <p style="color: white; font-size: 1.2rem; margin-top: 10px;">Direct Top-Up</p>
                </div>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    <!-- Digital Cards Banner -->
    <img src="{{ asset('images/banner/digitalbanner.jpg') }}" alt="Digital Banner" style="width: 100%; max-width: 100%; height: auto; border-radius: 20px; margin: 40px 0 2px 0; box-shadow: 0 10px 50px rgba(0,0,0,0.3); display: block;">

    <!-- Products Grid - Digital Cards -->
    <div class="products-grid-3col" style="display: grid !important; grid-template-columns: repeat(3, 1fr) !important; gap: 0px;">
        @php
            $digitalCards = [
                ['name' => 'Google Play', 'image' => 'google play realod.png', 'slug' => 'google-play'],
                ['name' => 'iTunes', 'image' => 'itunes realod.png', 'slug' => 'itunes'],
                ['name' => 'Steam', 'image' => 'steam realod.png', 'slug' => 'steam'],
                ['name' => 'PlayStation', 'image' => 'play realod.png', 'slug' => 'playstation'],
                ['name' => 'Xbox', 'image' => 'xbox realod.png', 'slug' => 'xbox'],
                ['name' => 'Razer Gold', 'image' => 'razer gpld realod.png', 'slug' => 'razer-gold'],
            ];
        @endphp

        @foreach($digitalCards as $card)
        <a href="{{ route('product.category', $card['slug']) }}" class="product-card-abady scroll-animate">
            <div class="product-image-container">
                <img src="{{ asset('images/catgorie/' . $card['image']) }}" alt="{{ $card['name'] }}">
            </div>
        </a>
        @endforeach
    </div>

    <!-- CTA Section -->

</div>

@endsection

@push('scripts')
<script>
// Scroll Animation Observer
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for scroll animations
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

    // Observe all elements with scroll-animate class
    const animatedElements = document.querySelectorAll('.scroll-animate');
    animatedElements.forEach(element => {
        observer.observe(element);
    });
});
</script>
@endpush
