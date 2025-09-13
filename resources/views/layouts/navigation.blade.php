<nav x-data="{ open: false, userMenuOpen: false }" class="bg-slate-900/80 backdrop-blur-lg border-b border-cyan-500/20 fixed w-full top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Left Side Navigation -->
            <div class="flex items-center space-x-8">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-purple-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-black text-xl">G</span>
                    </div>
                    <span class="text-white font-bold text-xl">GameStore</span>
                </a>

                <!-- Main Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>

                    <x-nav-link :href="route('shop')" :active="request()->routeIs('shop')">
                        {{ __('Shop') }}
                    </x-nav-link>

                    @auth
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                    <x-nav-link :href="route('admin.dashboard')"
                                :active="request()->routeIs('admin.*')"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                       {{ request()->routeIs('admin.*')
                                          ? 'bg-purple-500/20 text-purple-400 shadow-lg shadow-purple-500/25'
                                          : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        {{ __('Admin Panel') }}
                    </x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="flex items-center space-x-4">
                @auth
                <!-- Favorites -->
                <a href="{{ route('favorites.index') }}"
                   class="relative p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="favorites-count" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center hidden">
                        0
                    </span>
                </a>

                <!-- Cart -->
                <a href="{{ route('cart.index') }}"
                   class="relative p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span id="cart-count" class="absolute -top-1 -right-1 w-5 h-5 bg-cyan-500 text-white text-xs font-bold rounded-full flex items-center justify-center hidden">
                        0
                    </span>
                </a>

                <!-- Notifications -->
                <button class="relative p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </button>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center space-x-3 px-3 py-2 bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-all duration-200 border border-slate-700 hover:border-cyan-500/50">
                        <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-purple-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </div>
                        <span class="hidden md:block text-slate-300">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         @click.away="userMenuOpen = false"
                         class="absolute right-0 mt-2 w-48 bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">

                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                            {{ __('Profile') }}
                        </a>

                        <a href="{{ route('orders.index') }}"
                           class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                            {{ __('My Orders') }}
                        </a>

                        <a href="{{ route('favorites.index') }}"
                           class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                            {{ __('My Favorites') }}
                        </a>

                        <div class="border-t border-slate-700"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <!-- Guest Links -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors">
                        {{ __('Log in') }}
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-cyan-500 to-purple-500 rounded-lg hover:shadow-lg hover:shadow-cyan-500/25 transition-all duration-200 hover:scale-105">
                        {{ __('Get Started') }}
                    </a>
                    @endif
                </div>
                @endauth

                <!-- Mobile Menu Toggle -->
                <button @click="open = !open"
                        class="md:hidden p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="md:hidden bg-slate-900/95 backdrop-blur-lg border-t border-cyan-500/20">

        <div class="px-4 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Home') }}
            </a>

            <a href="{{ route('shop') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('shop') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Shop') }}
            </a>

            @auth
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Dashboard') }}
            </a>

            <a href="{{ route('cart.index') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('cart.*') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Cart') }}
                <span class="inline-block ml-2 px-2 py-1 bg-cyan-500 text-white text-xs rounded-full cart-count-mobile">0</span>
            </a>

            <a href="{{ route('favorites.index') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('favorites.*') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Favorites') }}
            </a>

            <a href="{{ route('orders.index') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('orders.*') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('My Orders') }}
            </a>

            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('admin.*') ? 'bg-purple-500/20 text-purple-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Admin Panel') }}
            </a>
            @endif
            @endauth
        </div>
    </div>
</nav>

@push('scripts')
<script>
// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    @auth
    updateCartCount();
    updateFavoritesCount();
    @endauth
});

// Function to update cart count
function updateCartCount() {
    fetch('/api/cart/count', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        const cartCountElement = document.getElementById('cart-count');
        const mobileCartCount = document.querySelector('.cart-count-mobile');

        if (data.count > 0) {
            cartCountElement.textContent = data.count;
            cartCountElement.classList.remove('hidden');
            if (mobileCartCount) {
                mobileCartCount.textContent = data.count;
            }
        } else {
            cartCountElement.classList.add('hidden');
            if (mobileCartCount) {
                mobileCartCount.textContent = '0';
            }
        }
    })
    .catch(error => console.error('Error updating cart count:', error));
}

function updateFavoritesCount() {
    // This would require a similar API endpoint for favorites count
    // For now, we'll leave it as a placeholder
}
</script>
@endpush
