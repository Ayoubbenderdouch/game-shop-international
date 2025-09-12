<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 relative overflow-hidden">
        <!-- Animated Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(239,68,68,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(245,158,11,0.1),transparent_50%)]"></div>

            <!-- Animated security grid -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="40" height="40" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3Cpattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"%3E%3Cpath d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(239,68,68,0.1)" stroke-width="1"/%3E%3C/pattern%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grid)"/%3E%3C/svg%3E')] opacity-30"></div>

            <!-- Floating elements -->
            <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/3 left-1/4 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-red-400 to-orange-500 rounded-2xl blur-lg opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-br from-red-500 via-orange-500 to-yellow-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-red-500/25 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <h2 class="mt-6 text-3xl font-black text-white">
                    Security Checkpoint
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Confirm your identity to continue
                </p>
            </div>

            <!-- Main Card -->
            <div class="relative">
                <!-- Card glow effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 via-orange-500/20 to-yellow-500/20 rounded-2xl blur-xl"></div>

                <!-- Card content -->
                <div class="relative bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl shadow-black/50 p-8 overflow-hidden">
                    <!-- Security badge -->
                    <div class="absolute top-4 right-4">
                        <div class="flex items-center space-x-1 bg-red-500/20 px-3 py-1 rounded-full">
                            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-xs text-red-400 font-medium">Secure Area</span>
                        </div>
                    </div>

                    <!-- Warning message with icon -->
                    <div class="mb-6 bg-gradient-to-r from-orange-500/10 to-red-500/10 rounded-xl p-4 border border-orange-500/20">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center animate-pulse">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-white mb-1">Protected Area</h3>
                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ __('This is a secure area of the application. Please confirm your password before continuing to access sensitive information.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                        @csrf

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('Enter Your Password') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-500 group-focus-within:text-orange-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input id="password"
                                       name="password"
                                       type="password"
                                       required
                                       autocomplete="current-password"
                                       class="block w-full pl-10 pr-10 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent
                                              hover:border-slate-600 transition-all duration-200"
                                       placeholder="••••••••">

                                <!-- Show password toggle -->
                                <button type="button"
                                        onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                <!-- Input focus glow -->
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 opacity-0 group-focus-within:opacity-20 blur transition-opacity pointer-events-none"></div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
                        </div>

                        <!-- Security Info -->
                        <div class="bg-slate-800/30 rounded-xl p-4 border border-slate-700/50">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Your connection is encrypted and secure</p>
                                    <p class="text-xs text-green-400 font-medium mt-0.5">256-bit SSL Encryption Active</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between space-x-4">
                            <a href="{{ url()->previous() }}"
                               class="px-6 py-3 bg-slate-800 text-slate-300 font-medium rounded-xl hover:bg-slate-700 transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </a>

                            <button type="submit"
                                    class="relative flex-1 py-3 px-6 bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 text-white font-semibold rounded-xl
                                           shadow-lg hover:shadow-red-500/25 transform hover:scale-[1.02] transition-all duration-200
                                           focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-slate-900
                                           group overflow-hidden">

                                <!-- Button shine effect -->
                                <div class="absolute inset-0 -top-2 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>

                                <span class="relative flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Confirm & Continue') }}
                                </span>
                            </button>
                        </div>

                        <!-- Additional Options -->
                        <div class="text-center pt-4 border-t border-slate-800">
                            <p class="text-xs text-slate-400">
                                Forgot your password?
                                <a href="{{ route('password.request') }}" class="text-orange-400 hover:text-orange-300 font-medium transition-colors">
                                    Reset it here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Session timeout warning -->
            <div class="mt-6 text-center">
                <div class="inline-flex items-center space-x-2 text-xs text-slate-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Session expires after 15 minutes of inactivity</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-guest-layout>
