@extends('layouts.app')

@section('title', $gameName . ' - ' . (app()->getLocale() == 'ar' ? 'اختر المنطقة' : 'Select Region'))

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
        <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
        <span style="color: #999;">/</span>
        <span style="color: var(--purple-main); font-weight: 600;">{{ $gameName }}</span>
    </nav>

    <!-- Category Header -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.8rem; font-weight: 800; color: var(--purple-main); margin-bottom: 15px;">
            {{ $gameName }}
        </h1>
        <p style="font-size: 1.2rem; color: #666;">
            {{ app()->getLocale() == 'ar' ? 'اختر المنطقة' : 'Select Region' }}
        </p>
    </div>

    <!-- Regions Grid -->
    <div class="products-grid-3col">
        @foreach($regions as $region)
        <a href="{{ route('mazaya.region-selection', [$gameSlug, $region['slug']]) }}" class="product-card-abady scroll-animate" style="text-decoration: none;">
            <div class="product-image-container">
                @if(file_exists(public_path('images/catgorie/' . $region['image'])))
                <img src="{{ asset('images/catgorie/' . $region['image']) }}" alt="{{ $region['name'] }}">
                @else
                <div style="width: 100%; height: 600px; background: linear-gradient(135deg, var(--purple-main), var(--purple-dark)); border-radius: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="font-size: 8rem; margin-bottom: 20px;">🌍</div>
                    <h3 style="color: var(--gold-main); font-size: 2rem; font-weight: 800;">{{ app()->getLocale() == 'ar' ? $region['name_ar'] : $region['name'] }}</h3>
                    <p style="color: white; font-size: 1.2rem; margin-top: 10px;">{{ $gameName }}</p>
                </div>
                @endif

                <!-- Region Label Overlay -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(46, 35, 112, 0.95), transparent); padding: 30px 20px 20px 20px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <h3 style="color: var(--gold-main); font-size: 1.5rem; font-weight: 800; margin: 0; text-align: center;">
                        {{ app()->getLocale() == 'ar' ? $region['name_ar'] : $region['name'] }}
                    </h3>
                </div>
            </div>
        </a>
        @endforeach
    </div>

</div>

@endsection

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
