<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 relative overflow-hidden">
        <!-- Animated Background Effects -->
        <div class="absolute inset-0">
            <!-- Gradient overlays -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,rgba(59,130,246,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,rgba(168,85,247,0.15),transparent_50%)]"></div>

            <!-- Animated orbs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse delay-700"></div>

            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3Cpattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse"%3E%3Cpath d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/%3E%3C/pattern%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grid)"/%3E%3C/svg%3E')] opacity-50"></div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-500 rounded-2xl blur-lg opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/25 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <h2 class="mt-6 text-3xl font-black text-white">
                    Password Recovery
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Don't worry, we'll help you reset your password
                </p>
            </div>

            <!-- Main Card -->
            <div class="relative">
                <!-- Card glow effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-pink-500/20 rounded-2xl blur-xl"></div>

                <!-- Card content -->
                <div class="relative bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl shadow-black/50 p-8 overflow-hidden">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-purple-500/10 to-pink-500/10 rounded-full blur-2xl"></div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4 text-green-400 text-sm bg-green-500/10 px-3 py-2 rounded-lg" :status="session('status')" />

                    <!-- Description -->
                    <div class="mb-6 relative">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-slate-300 leading-relaxed">
                                {{ __('Enter your email address and we\'ll send you a link to reset your password. The link will expire in 60 minutes for security reasons.') }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6 relative">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('Email Address') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-500 group-focus-within:text-blue-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                </div>
                                <input id="email"
                                       name="email"
                                       type="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="username"
                                       class="block w-full pl-10 pr-3 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                              hover:border-slate-600 transition-all duration-200"
                                       placeholder="you@example.com">

                                <!-- Input focus glow -->
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500 to-purple-500 opacity-0 group-focus-within:opacity-20 blur transition-opacity pointer-events-none"></div>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                        </div>

                        <!-- Security Features -->
                        <div class="bg-slate-800/30 rounded-xl p-4 border border-slate-700/50">
                            <h4 class="text-sm font-semibold text-white mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Security Features
                            </h4>
                            <ul class="space-y-2 text-xs text-slate-400">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-cyan-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Secure password reset link sent to your email</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-cyan-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Link expires in 60 minutes for your protection</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-cyan-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Email verification required for password reset</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="relative w-full py-3 px-4 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 text-white font-semibold rounded-xl
                                       shadow-lg hover:shadow-blue-500/25 transform hover:scale-[1.02] transition-all duration-200
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900
                                       group overflow-hidden">

                            <!-- Button shine effect -->
                            <div class="absolute inset-0 -top-2 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>

                            <span class="relative flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                {{ __('Send Password Reset Link') }}
                            </span>
                        </button>

                        <!-- Alternative Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                            <a href="{{ route('login') }}"
                               class="flex items-center text-sm text-slate-400 hover:text-cyan-400 transition-colors group">
                                <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back to login
                            </a>

                            <a href="{{ route('register') }}"
                               class="text-sm text-slate-400 hover:text-purple-400 transition-colors">
                                Create account
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400">
                    Having trouble?
                    <a href="#" class="font-medium text-cyan-400 hover:text-cyan-300 transition-colors">
                        Contact Support
                    </a>
                </p>
            </div>

            <!-- Trust Badges -->
            <div class="mt-8 flex justify-center space-x-6">
                <div class="flex items-center space-x-2 text-slate-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-xs">SSL Secured</span>
                </div>
                <div class="flex items-center space-x-2 text-slate-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"></path>
                    </svg>
                    <span class="text-xs">Encrypted</span>
                </div>
                <div class="flex items-center space-x-2 text-slate-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-xs">GDPR Compliant</span>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
