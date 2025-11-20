<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user" content="{{ auth()->user()->id }}">
    @endauth

    <title>@yield('title', config('app.name', 'Gaming Store'))</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- Custom Gaming Style CSS -->
    <link rel="stylesheet" href="{{ asset('css/gaming-style.css') }}">

    <!-- Abady Store Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/abady-style.css') }}">

    <!-- Luxury Navigation CSS -->
    <link rel="stylesheet" href="{{ asset('css/navigation-luxury.css') }}">

    <!-- RTL Support CSS -->
    <link rel="stylesheet" href="{{ asset('css/rtl-support.css') }}">

    <!-- Cairo Font (for Arabic support) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bunny Fonts (Privacy-friendly Google Fonts alternative) -->
    <link href="https://fonts.bunny.net/css?family=urbanist:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i" rel="stylesheet" />

    <!-- Tailwind Config -->
    <script>
        // Wait for Tailwind to be available
        if (typeof tailwind !== 'undefined') {
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'primary-blue': '#49b8ef',
                            'primary-black': '#000000',
                            'primary-border': '#23262B',
                            'primary-border-secondary': '#3C3E42'
                        },
                        fontFamily: {
                            'urbanist': ['Urbanist', 'sans-serif']
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html, body {
            font-family: "Urbanist", sans-serif;
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        main {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #49b8ef;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3da2d4;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #49b8ef;
            color: #000;
            padding: 15px 20px;
            border-radius: 5px;
            min-width: 250px;
            z-index: 99999;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        /* Product Card Hover */
        .product-card:hover {
            border-color: #49b8ef;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(73, 184, 239, 0.2);
        }

        /* Loading Spinner */
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #49b8ef;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-white text-gray-900">

    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <section class="page-title-wrapper bg-cover bg-center py-[60px]" style="background-image: url('/assets/img/page-title-bg.png')">
            <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
                {{ $header }}
            </div>
        </section>
    @endisset

    <!-- Page Content -->
    <main>
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="w-full bg-[#2E2370] border-t border-[#F4C430] mt-[60px]" style="min-height: 100px;">
        <div class="max-w-[1170px] mx-auto px-5 py-8 text-[#F6F7FB] text-sm flex flex-col md:flex-row gap-4 md:gap-8">
            <div class="flex-1">© {{ date('Y') }} {{ config('company.name') }}</div>
            <nav class="flex flex-wrap gap-x-6 gap-y-2">
                <a href="{{ route('legal.privacy') }}" class="hover:underline">{{ __('Privacy Policy') }}</a>
                <a href="{{ route('legal.terms') }}" class="hover:underline">{{ __('Terms & Conditions') }}</a>
                <a href="{{ route('legal.refund') }}" class="hover:underline">{{ __('Refund Policy') }}</a>
                <a href="{{ route('legal.contact') }}" class="hover:underline">{{ __('Contact & Support') }}</a>
                @if(config('company.country') === 'DE')
                    <a href="{{ route('legal.imprint') }}" class="hover:underline">{{ __('Imprint') }}</a>
                @endif
            </nav>
        </div>
    </footer>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @stack('scripts')

    <script>
        // Toast notification system
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgColor = type === 'success' ? '#49b8ef' :
                           type === 'error' ? '#ef4444' :
                           '#fbbf24';

            toast.className = 'toast';
            toast.style.background = bgColor;
            toast.innerHTML = message;

            container.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
</body>
</html>
