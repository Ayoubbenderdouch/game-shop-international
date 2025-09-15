<header class="bg-black sticky top-0 z-50">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <div class="h-[80px] flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}">
                    <!-- Gaming Controller Logo SVG -->
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" style="height: 70px; width: 100px;">
                </a>
            </div>

            <!-- Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-white hover:text-[#49b8ef] transition-all duration-300 {{ request()->routeIs('home') ? 'text-[#49b8ef]' : '' }}">Home</a>
                <a href="{{ route('shop') }}" class="text-white hover:text-[#49b8ef] transition-all duration-300 {{ request()->routeIs('shop') ? 'text-[#49b8ef]' : '' }}">Shop</a>

                @auth
                <a href="{{ route('dashboard') }}" class="text-white hover:text-[#49b8ef] transition-all duration-300 {{ request()->routeIs('dashboard') ? 'text-[#49b8ef]' : '' }}">Dashboard</a>

                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-[#49b8ef] transition-all duration-300 {{ request()->routeIs('admin.*') ? 'text-[#49b8ef]' : '' }}">Admin</a>
                @endif
                @endauth
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center space-x-6">
                @auth
                <!-- Search -->
                <button class="text-white hover:text-[#49b8ef] transition-all duration-300">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>

                <!-- Wishlist -->
                <a href="{{ route('favorites.index') }}" class="relative text-white hover:text-[#49b8ef] transition-all duration-300">
                    <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 3.70259L11.6851 3.00005C13.816 0.814763 17.2709 0.814761 19.4018 3.00005C21.4755 5.12665 21.5392 8.55385 19.5461 10.76L13.8197 17.0982C12.2984 18.782 9.70154 18.782 8.18026 17.0982L2.45393 10.76C0.460783 8.55388 0.5245 5.12667 2.5982 3.00007C4.72912 0.814774 8.18404 0.814776 10.315 3.00007L11 3.70259Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span id="favorites-count" class="absolute -top-2 -right-2 bg-[#49b8ef] text-black text-xs w-5 h-5 rounded-full flex items-center justify-center hidden">0</span>
                </a>

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative text-white hover:text-[#49b8ef] transition-all duration-300">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 4H18C20.2091 4 22 5.79086 22 8V13C22 15.2091 20.2091 17 18 17H10C7.79086 17 6 15.2091 6 13V4ZM6 4C6 2.89543 5.10457 2 4 2H2" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-[#49b8ef] text-black text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span>
                </a>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-white hover:text-[#49b8ef] transition-all duration-300">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 10C12.7614 10 15 7.76142 15 5C15 2.23858 12.7614 0 10 0C7.23858 0 5 2.23858 5 5C5 7.76142 7.23858 10 10 10Z" fill="currentColor"/>
                            <path d="M10 12C4.477 12 0 16.477 0 22H20C20 16.477 15.523 12 10 12Z" fill="currentColor"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 bg-black border border-[#23262B] rounded-lg shadow-xl">

                        <div class="p-3 border-b border-[#23262B]">
                            <p class="text-white font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-gray-400 text-sm">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-300 hover:bg-[#23262B] hover:text-white transition-colors">
                            Profile
                        </a>
                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-[#23262B] hover:text-white transition-colors">
                            My Orders
                        </a>
                        <a href="{{ route('favorites.index') }}" class="block px-4 py-2 text-gray-300 hover:bg-[#23262B] hover:text-white transition-colors">
                            My Favorites
                        </a>

                        <div class="border-t border-[#23262B]"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-gray-300 hover:bg-[#23262B] hover:text-white transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <!-- Guest Links -->
                <a href="{{ route('login') }}" class="text-white hover:text-[#49b8ef] transition-all duration-300">Login</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-[#49b8ef] text-black font-semibold rounded-lg hover:bg-[#3da2d4] transition-all duration-300">
                    Get Started
                </a>
                @endauth

                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden text-white" id="mobile-menu-toggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12H21" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 6H21" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 18H21" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="lg:hidden hidden bg-black border-t border-[#23262B]">
        <div class="px-5 py-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Home</a>
            <a href="{{ route('shop') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Shop</a>

            @auth
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Dashboard</a>
            <a href="{{ route('cart.index') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Cart</a>
            <a href="{{ route('favorites.index') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Favorites</a>
            <a href="{{ route('orders.index') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">My Orders</a>

            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Admin Panel</a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">
                    Logout
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Login</a>
            <a href="{{ route('register') }}" class="block px-3 py-2 text-white hover:bg-[#23262B] rounded-lg transition-colors">Register</a>
            @endauth
        </div>
    </div>
</header>

<!-- Include Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@2.8.2" defer></script>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});

// Update cart and favorites count
@auth
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    updateFavoritesCount();
});

function updateCartCount() {
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            if (cartCount && data.count > 0) {
                cartCount.textContent = data.count;
                cartCount.classList.remove('hidden');
            }
        })
        .catch(error => console.error('Error fetching cart count:', error));
}

function updateFavoritesCount() {
    fetch('/api/favorites/count')
        .then(response => response.json())
        .then(data => {
            const favCount = document.getElementById('favorites-count');
            if (favCount && data.count > 0) {
                favCount.textContent = data.count;
                favCount.classList.remove('hidden');
            }
        })
        .catch(error => console.error('Error fetching favorites count:', error));
}
@endauth
</script>
