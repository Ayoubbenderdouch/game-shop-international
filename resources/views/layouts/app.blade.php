<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary: #49baee;
            --primary-dark: #38a8dc;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #49baee #1a1a2e;
        }

        *::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        *::-webkit-scrollbar-track {
            background: #1a1a2e;
        }

        *::-webkit-scrollbar-thumb {
            background: #49baee;
            border-radius: 4px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0f1e;
            background-image:
                radial-gradient(ellipse at top right, rgba(73, 186, 238, 0.1), transparent 50%),
                radial-gradient(ellipse at bottom left, rgba(73, 186, 238, 0.05), transparent 50%);
            min-height: 100vh;
        }

        .font-gaming {
            font-family: 'Orbitron', monospace;
        }

        .neon-button {
            background: linear-gradient(135deg, #49baee, #38a8dc);
            color: #0f0f1e;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(73, 186, 238, 0.3);
        }

        .neon-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(73, 186, 238, 0.5);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }
    </style>
</head>
<body class="text-gray-100 antialiased">
    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden"></div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed top-0 left-0 h-full w-64 bg-gray-900 z-50 overflow-y-auto">
        <div class="p-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="font-gaming text-xl text-[#49baee]">{{ __('app.app.name') }}</h2>
                <button onclick="toggleMobileMenu()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="space-y-2">
                <a href="/" class="block px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->is('/') ? 'bg-gray-800 text-[#49baee]' : '' }}">
                    {{ __('app.app.nav.home') }}
                </a>
                <a href="/shop" class="block px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->is('shop*') ? 'bg-gray-800 text-[#49baee]' : '' }}">
                    {{ __('app.app.nav.shop') }}
                </a>

                @auth
                    <a href="/cart" class="block px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->is('cart*') ? 'bg-gray-800 text-[#49baee]' : '' }}">
                        {{ __('app.app.nav.cart') }}
                        @if(auth()->user()->cartItems()->count() > 0)
                            <span class="ml-2 px-2 py-1 bg-[#49baee] text-gray-900 text-xs rounded-full">
                                {{ auth()->user()->cartItems()->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="/orders" class="block px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->is('orders*') ? 'bg-gray-800 text-[#49baee]' : '' }}">
                        {{ __('app.app.nav.orders') }}
                    </a>
                    <a href="/profile" class="block px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->is('profile*') ? 'bg-gray-800 text-[#49baee]' : '' }}">
                        {{ __('app.app.nav.profile') }}
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="/admin" class="block px-4 py-2 rounded hover:bg-gray-800 transition text-yellow-400">
                            {{ __('app.app.nav.admin') }}
                        </a>
                    @endif

                    <form method="POST" action="/logout" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 transition text-red-400">
                            {{ __('app.app.nav.logout') }}
                        </button>
                    </form>
                @else
                    <a href="/login" class="block px-4 py-2 rounded hover:bg-gray-800 transition">
                        {{ __('app.app.nav.login') }}
                    </a>
                    <a href="/register" class="block px-4 py-2 rounded hover:bg-gray-800 transition">
                        {{ __('app.app.nav.register') }}
                    </a>
                @endauth
            </nav>
        </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-30 bg-gray-900/95 backdrop-blur-md border-b border-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Mobile Menu Button -->
                <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Logo -->
                <a href="/" class="font-gaming text-xl lg:text-2xl font-bold text-[#49baee]">
                    GAME<span class="text-white">SHOP</span>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="/" class="hover:text-[#49baee] transition {{ request()->is('/') ? 'text-[#49baee]' : '' }}">
                        {{ __('app.app.nav.home') }}
                    </a>
                    <a href="/shop" class="hover:text-[#49baee] transition {{ request()->is('shop*') ? 'text-[#49baee]' : '' }}">
                        {{ __('app.app.nav.shop') }}
                    </a>
                    <a href="/pubg-uc" class="hover:text-[#49baee] transition {{ request()->is('pubg-uc*') ? 'text-[#49baee]' : '' }}">
                        PUBG UC
                    </a>
                </nav>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Cart Icon (Mobile) -->
                        <a href="/cart" class="lg:hidden relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="absolute -top-2 -right-2 w-5 h-5 bg-[#49baee] text-gray-900 text-xs rounded-full flex items-center justify-center">
                                    {{ auth()->user()->cartItems()->count() }}
                                </span>
                            @endif
                        </a>

                        <!-- Desktop Menu -->
                        <div class="hidden lg:flex items-center space-x-4">
                            <a href="/cart" class="relative hover:text-[#49baee] transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                @if(auth()->user()->cartItems()->count() > 0)
                                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-[#49baee] text-gray-900 text-xs rounded-full flex items-center justify-center">
                                        {{ auth()->user()->cartItems()->count() }}
                                    </span>
                                @endif
                            </a>

                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 hover:text-[#49baee] transition">
                                    <span>{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl overflow-hidden">
                                    <a href="/profile" class="block px-4 py-2 hover:bg-gray-700 transition">
                                        {{ __('app.app.nav.profile') }}
                                    </a>
                                    <a href="/orders" class="block px-4 py-2 hover:bg-gray-700 transition">
                                        {{ __('app.app.nav.orders') }}
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="/admin" class="block px-4 py-2 hover:bg-gray-700 transition text-yellow-400">
                                            {{ __('app.app.nav.admin') }}
                                        </a>
                                    @endif
                                    <hr class="border-gray-700">
                                    <form method="POST" action="/logout">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-700 transition text-red-400">
                                            {{ __('app.app.nav.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="hidden lg:flex items-center space-x-4">
                            <a href="/login" class="hover:text-[#49baee] transition">{{ __('app.app.nav.login') }}</a>
                            <a href="/register" class="neon-button px-4 py-2 rounded-lg">{{ __('app.app.nav.register') }}</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 mx-4 mt-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 mx-4 mt-4 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-500/20 border border-blue-500 text-blue-400 px-4 py-3 mx-4 mt-4 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-gray-800">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-gaming text-xl text-[#49baee] mb-4">GAMESHOP</h3>
                    <p class="text-gray-400 text-sm">{{ __('app.app.tagline') }}</p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/shop" class="hover:text-white transition">Shop</a></li>
                        <li><a href="/pubg-uc" class="hover:text-white transition">PUBG UC</a></li>
                        <li><a href="/orders" class="hover:text-white transition">My Orders</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-[#49baee] transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-[#49baee] transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-[#49baee] transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('app.app.copyright') }}</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-menu-overlay');

            menu.classList.toggle('active');
            overlay.classList.toggle('hidden');

            overlay.onclick = function() {
                menu.classList.remove('active');
                overlay.classList.add('hidden');
            };
        }
    </script>
</body>
</html>
