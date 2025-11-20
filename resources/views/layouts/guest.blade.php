<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gaming Store') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script>
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
    </script>

    <style>
        body {
            font-family: "Urbanist", sans-serif;
        }

    /* Prevent dropdowns from flashing before Alpine initializes */
    [x-cloak] { display: none !important; }

        /* Custom animations */
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
        }

        /* Custom scrollbar */
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

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#0b0e13] text-[#e5e7eb] min-h-screen">
    <!-- Language & Currency Switcher (Top Right Corner) -->
    <div class="fixed top-6 right-6 z-50 flex items-center gap-3 pointer-events-auto">
        <!-- Language Switcher -->
        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
            <button @click="open = !open" :aria-expanded="open.toString()" class="flex items-center gap-2 px-3 py-2 bg-black/50 border border-gray-700 rounded-lg hover:border-[#49b8ef] transition-all backdrop-blur-sm shadow-md hover:shadow-primary-blue/20">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 20C15.5228 20 20 15.5228 20 10C20 4.47715 15.5228 0 10 0C4.47715 0 0 4.47715 0 10C0 15.5228 4.47715 20 10 20Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M1 10H19M10 1C11.6569 3.5 12.5 6.5 12.5 10C12.5 13.5 11.6569 16.5 10 19C8.34315 16.5 7.5 13.5 7.5 10C7.5 6.5 8.34315 3.5 10 1Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span class="text-sm font-semibold uppercase">{{ app()->getLocale() }}</span>
            </button>

            <div x-cloak x-show="open" @click.outside="open = false" x-transition.opacity.scale.origin.top.right class="absolute right-0 mt-2 w-48 bg-black/95 border border-gray-700 rounded-lg shadow-2xl z-[9999] backdrop-blur-md overflow-hidden">
                <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 hover:bg-gray-800 transition-colors {{ app()->getLocale() == 'en' ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                    🇬🇧 English
                </a>
                <a href="{{ route('language.switch', 'de') }}" class="block px-4 py-2 hover:bg-gray-800 transition-colors {{ app()->getLocale() == 'de' ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                    🇩🇪 Deutsch
                </a>
                <a href="{{ route('language.switch', 'fr') }}" class="block px-4 py-2 hover:bg-gray-800 transition-colors {{ app()->getLocale() == 'fr' ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                    🇫🇷 Français
                </a>
                <a href="{{ route('language.switch', 'es') }}" class="block px-4 py-2 hover:bg-gray-800 transition-colors {{ app()->getLocale() == 'es' ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                    🇪🇸 Español
                </a>
                <a href="{{ route('language.switch', 'it') }}" class="block px-4 py-2 hover:bg-gray-800 transition-colors {{ app()->getLocale() == 'it' ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                    🇮🇹 Italiano
                </a>
                <a href="{{ route('language.switch', 'ar') }}" class="block px-4 py-2 hover:bg-gray-800 transition-colors {{ app()->getLocale() == 'ar' ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                    🇸🇦 العربية
                </a>
            </div>
        </div>

        <!-- Currency Switcher -->
        @php
            $currentCurrency = session('currency', 'USD');
            $availableCurrencies = \App\Models\CurrencyRate::where('is_active', true)->get();
        @endphp
        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
            <button @click="open = !open" :aria-expanded="open.toString()" class="flex items-center gap-2 px-3 py-2 bg-black/50 border border-gray-700 rounded-lg hover:border-[#49b8ef] transition-all backdrop-blur-sm shadow-md hover:shadow-primary-blue/20">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13.41 18.09V20H10.74V18.07C9.03 17.71 7.58 16.61 7.58 14.66H9.47C9.47 15.69 10.22 16.51 12.08 16.51C13.94 16.51 14.39 15.59 14.39 15.06C14.39 14.32 13.94 13.62 11.63 13.15C9.04 12.62 7.58 11.51 7.58 9.62C7.58 7.83 8.93 6.71 10.74 6.32V4H13.41V6.35C15.11 6.79 16.09 8.04 16.09 9.62H14.2C14.2 8.59 13.56 7.89 12.08 7.89C10.6 7.89 9.47 8.54 9.47 9.62C9.47 10.47 10.22 10.99 12.08 11.35C13.94 11.71 16.28 12.45 16.28 15.06C16.28 16.85 14.93 17.82 13.41 18.09Z" fill="currentColor"/>
                </svg>
                <span class="text-sm font-bold">{{ $currentCurrency }}</span>
            </button>

            <div x-cloak x-show="open" @click.outside="open = false" x-transition.opacity.scale.origin.top.right class="absolute right-0 mt-2 w-52 bg-black/95 border border-gray-700 rounded-lg shadow-2xl max-h-80 overflow-y-auto z-[9999] backdrop-blur-md">
                @foreach($availableCurrencies as $currency)
                    <form method="POST" action="{{ route('currency.switch') }}">
                        @csrf
                        <input type="hidden" name="currency" value="{{ $currency->currency }}">
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-800 transition-colors flex items-center gap-2 {{ $currentCurrency == $currency->currency ? 'bg-gray-800 text-[#49b8ef]' : '' }}">
                            <span class="text-lg">{{ $currency->currency_symbol }}</span>
                            <span class="font-semibold">{{ $currency->currency }}</span>
                            <span class="text-xs text-gray-500 ml-auto">{{ $currency->currency_name }}</span>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0b0e13] via-[#0b0e13] to-black opacity-90"></div>
            <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-[#49b8ef]/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>

            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="40" height="40" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3Cpattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"%3E%3Cpath d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(73,184,239,0.05)" stroke-width="1"/%3E%3C/pattern%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grid)"/%3E%3C/svg%3E')]"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 w-full">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
