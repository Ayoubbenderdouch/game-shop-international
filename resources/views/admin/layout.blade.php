<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - GameShop</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#45F882',
                        'dark-bg': '#0b0e13',
                        'dark-card': '#1a1d23',
                        'dark-border': '#23262B',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('styles')
</head>
<body class="bg-dark-bg text-gray-200">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="w-64 bg-black border-r border-dark-border flex-shrink-0 hidden lg:block">
            <div class="h-full flex flex-col">
                <div class="p-6 border-b border-dark-border">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-primary-blue rounded-lg flex items-center justify-center">
                            <span class="text-black font-black text-xl">G</span>
                        </div>
                        <span class="text-white font-bold text-xl">Admin Panel</span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto p-4">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-dashboard w-5"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.products.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-gamepad w-5"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-folder w-5"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pricing-rules.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.pricing-rules.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-tags w-5"></i>
                                <span>Pricing Rules</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-shopping-cart w-5"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.users.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-users w-5"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-chart-line w-5"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.api-sync.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.api-sync.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-sync w-5"></i>
                                <span>API Sync</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-card transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-dark-card text-primary-blue' : 'text-gray-400' }}">
                                <i class="fas fa-cog w-5"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="p-4 border-t border-dark-border">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-2 text-gray-400 hover:text-white transition-all">
                        <i class="fas fa-arrow-left w-5"></i>
                        <span>Back to Site</span>
                    </a>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-black border-b border-dark-border px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="mobile-menu-toggle" class="lg:hidden text-white">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-white">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button class="relative text-gray-400 hover:text-white transition-all">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-primary-blue rounded-full"></span>
                        </button>

                        <div class="relative">
                            <button id="user-dropdown" class="flex items-center space-x-2 text-gray-400 hover:text-white transition-all">
                                <div class="w-8 h-8 bg-primary-blue rounded-full flex items-center justify-center">
                                    <span class="text-black font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-dark-card border border-dark-border rounded-lg shadow-lg z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-dark-bg transition-all">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-400 hover:text-white hover:bg-dark-bg transition-all">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-dark-bg p-6">
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-900/50 border border-green-600 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 p-4 bg-red-900/50 border border-red-600 rounded-lg text-red-400">
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div id="mobile-sidebar" class="lg:hidden fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" id="mobile-sidebar-overlay"></div>
        <aside class="absolute left-0 top-0 h-full w-64 bg-black border-r border-dark-border">
            <div class="h-full flex flex-col">
                <div class="p-6 border-b border-dark-border flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-primary-blue rounded-lg flex items-center justify-center">
                            <span class="text-black font-black text-xl">G</span>
                        </div>
                        <span class="text-white font-bold text-xl">Admin</span>
                    </a>
                    <button id="mobile-menu-close" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto p-4">
                    <ul class="space-y-1">
                        <li><a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-dark-card transition-all"><i class="fas fa-dashboard w-5"></i><span>Dashboard</span></a></li>
                        <li><a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-dark-card transition-all"><i class="fas fa-gamepad w-5"></i><span>Products</span></a></li>
                        <li><a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-dark-card transition-all"><i class="fas fa-folder w-5"></i><span>Categories</span></a></li>
                        <li><a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-dark-card transition-all"><i class="fas fa-shopping-cart w-5"></i><span>Orders</span></a></li>
                        <li><a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-dark-card transition-all"><i class="fas fa-users w-5"></i><span>Users</span></a></li>
                        <li><a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-dark-card transition-all"><i class="fas fa-cog w-5"></i><span>Settings</span></a></li>
                    </ul>
                </nav>
            </div>
        </aside>
    </div>

    <script>
        document.getElementById('user-dropdown').addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.remove('hidden');
        });

        document.getElementById('mobile-menu-close').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('hidden');
        });

        document.getElementById('mobile-sidebar-overlay').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!document.getElementById('user-dropdown').contains(e.target)) {
                document.getElementById('user-menu').classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
