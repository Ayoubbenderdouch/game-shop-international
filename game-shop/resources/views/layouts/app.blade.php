<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            950: '#0f172a'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
        }

        /* Modern button styles */
        .neon-button {
            background: linear-gradient(135deg, #49baee 0%, #38a8dc 100%);
            transition: all 0.3s;
        }
        .neon-button:hover {
            box-shadow: 0 0 20px rgba(73, 186, 238, 0.8);
            transform: translateY(-2px);
        }

        /* Modern card styles */
        .category-card {
            @apply bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-[#49baee]/30 transition-all duration-300;
        }

        /* Glassmorphism effect */
        .glass-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(73, 186, 238, 0.2);
        }

        /* Navbar blur effect */
        .navbar-blur {
            backdrop-filter: blur(12px);
            background: rgba(15, 23, 42, 0.8);
        }

        /* Cart badge animation */
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .cart-badge-bounce {
            animation: bounce 0.3s ease;
        }

        /* Mobile menu transition */
        .mobile-menu {
            transition: max-height 0.3s ease, opacity 0.3s ease;
        }

        /* Language Switcher Styles */
        .language-dropdown {
            position: relative;
        }

        .language-dropdown-content {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(73, 186, 238, 0.2);
            border-radius: 0.5rem;
            padding: 0.5rem;
            min-width: 150px;
            margin-top: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .language-dropdown:hover .language-dropdown-content,
        .language-dropdown:focus-within .language-dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Modern Navbar -->
    <nav class="navbar-blur border-b border-slate-800 sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#49baee] to-[#38a8dc] rounded-xl flex items-center justify-center group-hover:shadow-[0_0_20px_rgba(73,186,238,0.5)] transition-all duration-300">
                        <svg class="w-6 h-6 text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-black bg-gradient-to-r from-[#49baee] to-[#5cc5f5] bg-clip-text text-transparent">
                        {{ __('app.name') }}
                    </span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="nav-link relative text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium group">
                        {{ __('nav.home') }}
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49baee] group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="/shop" class="nav-link relative text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium group">
                        {{ __('nav.shop') }}
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49baee] group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="/pubg-uc" class="nav-link relative text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium group">
                        <span class="flex items-center gap-1">
                            {{ __('nav.pubg_uc') }}
                            <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded-full">{{ __('nav.hot') }}</span>
                        </span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49baee] group-hover:w-full transition-all duration-300"></span>
                    </a>

                    @auth
                        <a href="/cart" class="relative text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium group">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="absolute -top-2 -right-2 w-5 h-5 bg-[#49baee] text-slate-950 text-xs font-bold rounded-full flex items-center justify-center cart-badge">
                                0
                            </span>
                        </a>

                        <a href="/orders" class="nav-link relative text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium group">
                            {{ __('nav.orders') }}
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49baee] group-hover:w-full transition-all duration-300"></span>
                        </a>

                        @if(auth()->user()->is_admin)
                            <a href="/admin" class="nav-link relative text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium group">
                                <span class="flex items-center gap-1">
                                    {{ __('nav.admin') }}
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#49baee] group-hover:w-full transition-all duration-300"></span>
                            </a>
                        @endif

                        <form action="/logout" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition-colors duration-300 font-medium">
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="/login" class="px-4 py-2 text-slate-300 hover:text-[#49baee] transition-colors duration-300 font-medium">
                            {{ __('nav.login') }}
                        </a>
                        <a href="/register" class="px-6 py-2.5 bg-gradient-to-r from-[#49baee] to-[#5cc5f5] text-slate-950 font-bold rounded-lg hover:shadow-[0_0_20px_rgba(73,186,238,0.4)] transition-all duration-300">
                            {{ __('nav.register') }}
                        </a>
                    @endauth

                    <!-- Language Switcher -->
                    <div class="language-dropdown">
                        <button class="flex items-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span>{{ strtoupper(app()->getLocale()) }}</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="language-dropdown-content">
                            <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2 text-sm text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded transition-all {{ app()->getLocale() == 'en' ? 'text-[#49baee] bg-slate-800/30' : '' }}">
                                English
                            </a>
                            <!-- Add more languages here when available -->
                            <a href="#" class="block px-4 py-2 text-sm text-slate-500 cursor-not-allowed opacity-50">
                                More languages coming soon
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-slate-300 hover:text-[#49baee] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="mobile-menu md:hidden max-h-0 opacity-0 overflow-hidden">
                <div class="py-4 space-y-3 border-t border-slate-800">
                    <a href="/" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                        {{ __('nav.home') }}
                    </a>
                    <a href="/shop" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                        {{ __('nav.shop') }}
                    </a>
                    <a href="/pubg-uc" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                        {{ __('nav.pubg_uc') }} <span class="ml-2 px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded-full">{{ __('nav.hot') }}</span>
                    </a>

                    @auth
                        <a href="/cart" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                            {{ __('nav.cart') }}
                        </a>
                        <a href="/orders" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                            {{ __('nav.orders') }}
                        </a>
                        @if(auth()->user()->is_admin)
                            <a href="/admin" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                                {{ __('nav.admin_panel') }}
                            </a>
                        @endif
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="/login" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300">
                            {{ __('nav.login') }}
                        </a>
                        <a href="/register" class="block px-4 py-2 bg-gradient-to-r from-[#49baee] to-[#5cc5f5] text-slate-950 font-bold rounded-lg text-center">
                            {{ __('nav.register') }}
                        </a>
                    @endauth

                    <!-- Mobile Language Switcher -->
                    <div class="border-t border-slate-800 pt-3">
                        <p class="px-4 py-2 text-xs text-slate-500 uppercase tracking-wider">Language</p>
                        <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2 text-slate-300 hover:text-[#49baee] hover:bg-slate-800/50 rounded-lg transition-all duration-300 {{ app()->getLocale() == 'en' ? 'text-[#49baee] bg-slate-800/30' : '' }}">
                            English
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="glass-card bg-green-500/10 border border-green-500/30 text-green-400 px-6 py-4 rounded-xl mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-green-400 hover:text-green-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="glass-card bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-red-400 hover:text-red-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="bg-slate-950 border-t border-slate-800 mt-20">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-[#49baee] to-[#38a8dc] rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">{{ __('app.name') }}</span>
                    </div>
                    <p class="text-slate-400 text-sm">{{ __('footer.company_description') }}</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-white mb-4">{{ __('footer.quick_links') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="/shop" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('nav.shop') }}</a></li>
                        <li><a href="/pubg-uc" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('nav.pubg_uc') }}</a></li>
                        <li><a href="/orders" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('footer.my_orders') }}</a></li>
                        <li><a href="/cart" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('nav.cart') }}</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h3 class="font-bold text-white mb-4">{{ __('footer.categories') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="/shop?category=game-cards" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('categories.game_cards') }}</a></li>
                        <li><a href="/shop?category=gift-cards" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('categories.gift_cards') }}</a></li>
                        <li><a href="/shop?category=subscriptions" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('categories.subscriptions') }}</a></li>
                        <li><a href="/shop?category=game-topups" class="text-slate-400 hover:text-[#49baee] transition-colors text-sm">{{ __('categories.game_topups') }}</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-bold text-white mb-4">{{ __('footer.connect') }}</h3>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-[#49baee] transition-colors group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-950" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.531-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-[#49baee] transition-colors group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-950" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-[#49baee] transition-colors group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-950" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 text-center">
                <p class="text-slate-500 text-sm">{{ __('app.copyright', ['year' => date('Y')]) }}</p>
                <p class="text-slate-600 text-xs mt-2">{!! __('app.tagline') !!}</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            if (mobileMenu.style.maxHeight) {
                mobileMenu.style.maxHeight = null;
                mobileMenu.style.opacity = '0';
            } else {
                mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                mobileMenu.style.opacity = '1';
            }
        });

        // Add active state to current page
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('text-[#49baee]');
                const underline = link.querySelector('span:last-child');
                if (underline) {
                    underline.style.width = '100%';
                }
            }
        });

        // Cart badge animation
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
            // Update cart count via AJAX
            fetch('/api/cart/count')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        cartBadge.textContent = data.count;
                        cartBadge.classList.add('cart-badge-bounce');
                        setTimeout(() => {
                            cartBadge.classList.remove('cart-badge-bounce');
                        }, 300);
                    }
                })
                .catch(error => console.log('Cart count not available'));
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-section');
            if (parallax) {
                parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>
</body>
</html>

