<nav x-data="{ open: false, userMenuOpen: false }" class="relative bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 backdrop-blur-lg border-b border-cyan-500/20 shadow-lg shadow-cyan-500/5">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/25 group-hover:shadow-cyan-500/40 transition-all duration-300 group-hover:scale-110">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 hidden sm:block">
                            {{ config('app.name', 'Laravel') }}
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1 ml-10">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                               {{ request()->routeIs('dashboard')
                                  ? 'bg-cyan-500/20 text-cyan-400 shadow-lg shadow-cyan-500/25'
                                  : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                    @if(Auth::user()->isAdmin())
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')"
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
                        <span class="hidden sm:block text-sm font-medium text-slate-300">
                            {{ Auth::user()->name }}
                        </span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                             :class="{'rotate-180': userMenuOpen}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen"
                         @click.away="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-56 bg-slate-800 backdrop-blur-xl border border-slate-700 rounded-xl shadow-2xl shadow-black/50 overflow-hidden z-50">

                        <div class="px-4 py-3 border-b border-slate-700">
                            <p class="text-xs text-slate-400">Signed in as</p>
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-400 transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('Profile') }}
                            </a>


                        </div>

                        <div class="border-t border-slate-700 py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center w-full px-4 py-2 text-sm text-slate-300 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <!-- Guest Links -->
                <div class="hidden sm:flex items-center space-x-3">
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 rounded-lg transition-all duration-200">
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
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Dashboard') }}
            </a>

            @auth
            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('admin.*') ? 'bg-purple-500/20 text-purple-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }} transition-colors">
                {{ __('Admin Panel') }}
            </a>
            @endif
            @endauth
        </div>

        @auth
        <div class="border-t border-slate-800 px-4 py-3">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-semibold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800/50 transition-colors">
                    {{ __('Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-3 py-2 rounded-lg text-sm text-slate-300 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="border-t border-slate-800 px-4 py-3 space-y-2">
            <a href="{{ route('login') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-colors">
                {{ __('Log in') }}
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="block px-3 py-2 rounded-lg text-base font-medium text-white bg-gradient-to-r from-cyan-500 to-purple-500 hover:shadow-lg hover:shadow-cyan-500/25 transition-colors">
                {{ __('Get Started') }}
            </a>
            @endif
        </div>
        @endauth
    </div>
</nav>
