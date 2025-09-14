<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user" content="{{ auth()->user()->id }}">
    @endauth

    <title>@yield('title', config('app.name', 'GameShop'))</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

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
                            'primary-blue': '#45F882',
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
        body {
            font-family: "Urbanist", sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #45F882;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3fda74;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #45F882;
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
            border-color: #45F882;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(69, 248, 130, 0.2);
        }

        /* Loading Spinner */
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #45F882;
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
<body class="bg-[#0b0e13] text-[#e5e7eb]">

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
    <footer class="w-full bg-black border-t border-[#23262B] mt-[60px]">
        <div class="max-w-[1170px] mx-auto px-5 lg:px-0 py-[60px]">
            <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-8">
                <!-- Logo & About -->
                <div>
                    <a href="{{ route('home') }}" class="inline-block mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-primary-blue rounded-lg flex items-center justify-center">
                                <span class="text-black font-black text-xl">G</span>
                            </div>
                            <span class="text-white font-bold text-xl">GameShop</span>
                        </div>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Your ultimate destination for digital game cards, gift cards, and premium gaming subscriptions.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-primary-blue transition-all">Home</a></li>
                        <li><a href="{{ route('shop') }}" class="text-gray-400 hover:text-primary-blue transition-all">Shop</a></li>
                        @auth
                        <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-primary-blue transition-all">Dashboard</a></li>
                        <li><a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-primary-blue transition-all">My Orders</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Categories</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-primary-blue transition-all">Game Cards</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-blue transition-all">Gift Cards</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-blue transition-all">Subscriptions</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-blue transition-all">Top Up</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-400">
                            <span>Email: support@gameshop.com</span>
                        </li>
                        <li class="text-gray-400">
                            <span>24/7 Customer Support</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-[#23262B] mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-sm text-gray-400">
                        © {{ date('Y') }} {{ config('app.name', 'GameShop') }}. All rights reserved.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-primary-blue transition-all">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-primary-blue transition-all">Terms of Service</a>
                    </div>
                </div>
            </div>
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

            const bgColor = type === 'success' ? '#45F882' :
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

        // Show Laravel session messages as toasts
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
</body>
</html>
