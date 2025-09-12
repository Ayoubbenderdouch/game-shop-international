<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Gradient animations */
            @keyframes gradient-shift {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            .bg-gradient-animate {
                background-size: 200% 200%;
                animation: gradient-shift 10s ease infinite;
            }

            /* Glow effects */
            .glow-cyan {
                box-shadow: 0 0 30px rgba(6, 182, 212, 0.3);
            }

            .glow-purple {
                box-shadow: 0 0 30px rgba(147, 51, 234, 0.3);
            }

            /* Improved transitions */
            * {
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
        </style>

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
        <!-- Background effects -->
        <div class="fixed inset-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(73,186,238,0.08),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom,rgba(147,51,234,0.06),transparent_50%)]"></div>
        </div>

        <div class="relative min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-slate-900/50 backdrop-blur-sm border-b border-slate-800">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                            {{ $header }}
                        </h1>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10">
                @yield('content')
                {{ $slot ?? '' }}
            </main>

            <!-- Footer -->
            <footer class="relative mt-auto py-6 px-4 sm:px-6 lg:px-8 border-t border-slate-800 bg-slate-900/50 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="text-sm text-slate-400">
                            © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                        </div>
                        <div class="flex space-x-6">
                            <a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">Privacy</a>
                            <a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">Terms</a>
                            <a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">Support</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Toast Notifications Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        @stack('scripts')

        <script>
            // Toast notification system
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                const bgColor = type === 'success' ? 'from-green-500 to-emerald-500' :
                               type === 'error' ? 'from-red-500 to-pink-500' :
                               type === 'warning' ? 'from-yellow-500 to-orange-500' :
                               'from-blue-500 to-cyan-500';

                toast.className = `min-w-[300px] px-6 py-4 bg-gradient-to-r ${bgColor} text-white rounded-xl shadow-2xl transform transition-all duration-300 translate-x-96`;
                toast.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            ${type === 'success' ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                              type === 'error' ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' :
                              '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'}
                        </svg>
                        <span class="font-medium">${message}</span>
                    </div>
                `;

                container.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-96');
                    toast.classList.add('translate-x-0');
                }, 10);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-96', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            };
        </script>
    </body>
</html>
