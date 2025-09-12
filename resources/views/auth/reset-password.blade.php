<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 relative overflow-hidden">
        <!-- Animated Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,rgba(34,197,94,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,rgba(16,185,129,0.1),transparent_50%)]"></div>

            <!-- Animated grid -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3Cpattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse"%3E%3Cpath d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(34,197,94,0.05)" stroke-width="1"/%3E%3C/pattern%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grid)"/%3E%3C/svg%3E')]"></div>

            <!-- Floating orbs -->
            <div class="absolute top-1/4 left-1/3 w-96 h-96 bg-green-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse delay-700"></div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl blur-lg opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-br from-green-400 via-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-green-500/25 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <h2 class="mt-6 text-3xl font-black text-white">
                    Create New Password
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Set a strong password to secure your account
                </p>
            </div>

            <!-- Main Card -->
            <div class="relative">
                <!-- Card glow effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 via-emerald-500/20 to-teal-500/20 rounded-2xl blur-xl"></div>

                <!-- Card content -->
                <div class="relative bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl shadow-black/50 p-8 overflow-hidden">
                    <!-- Success indicator -->
                    <div class="absolute top-4 right-4">
                        <div class="flex items-center space-x-1 bg-green-500/20 px-3 py-1 rounded-full">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs text-green-400 font-medium">Link Verified</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address (Read-only) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('Email Address') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                </div>
                                <input id="email"
                                       name="email"
                                       type="email"
                                       value="{{ old('email', $request->email) }}"
                                       required
                                       readonly
                                       class="block w-full pl-10 pr-3 py-3 bg-slate-800/30 border border-green-500/30 rounded-xl text-slate-300
                                              cursor-not-allowed"
                                       autocomplete="username">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('New Password') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-500 group-focus-within:text-green-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input id="password"
                                       name="password"
                                       type="password"
                                       required
                                       autocomplete="new-password"
                                       class="block w-full pl-10 pr-10 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                                              hover:border-slate-600 transition-all duration-200"
                                       placeholder="Enter new password">

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
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 opacity-0 group-focus-within:opacity-20 blur transition-opacity pointer-events-none"></div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />

                            <!-- Password Strength Indicator -->
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400">Password Strength</span>
                                    <span id="strength-text" class="text-xs font-medium text-slate-400">Enter password</span>
                                </div>
                                <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                                    <div id="password-strength"
                                         class="h-full bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 rounded-full transition-all duration-300"
                                         style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('Confirm New Password') }}
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-500 group-focus-within:text-green-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 8.118l-8 4-8-4V5.764a2 2 0 01-.951.236H4a2 2 0 01-2-2v2a2 2 0 002 2h12a2 2 0 002-2zM5 9V7a5 5 0 1110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input id="password_confirmation"
                                       name="password_confirmation"
                                       type="password"
                                       required
                                       autocomplete="new-password"
                                       class="block w-full pl-10 pr-10 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                                              hover:border-slate-600 transition-all duration-200"
                                       placeholder="Confirm new password">

                                <!-- Match indicator -->
                                <div id="password-match" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>

                                <!-- Input focus glow -->
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 opacity-0 group-focus-within:opacity-20 blur transition-opacity pointer-events-none"></div>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
                        </div>

                        <!-- Password Requirements -->
                        <div class="bg-slate-800/30 rounded-xl p-4 border border-slate-700/50">
                            <h4 class="text-xs font-semibold text-slate-300 mb-3 uppercase tracking-wider">Password Requirements</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex items-center space-x-2">
                                    <div id="req-length" class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-slate-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400">At least 8 characters</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div id="req-uppercase" class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-slate-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400">One uppercase letter</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div id="req-number" class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-slate-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400">One number</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div id="req-special" class="w-4 h-4 rounded-full bg-slate-700 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-slate-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-slate-400">One special character</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="relative w-full py-3 px-4 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 text-white font-semibold rounded-xl
                                       shadow-lg hover:shadow-green-500/25 transform hover:scale-[1.02] transition-all duration-200
                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-slate-900
                                       group overflow-hidden">

                            <!-- Button shine effect -->
                            <div class="absolute inset-0 -top-2 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>

                            <span class="relative flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('Reset Password & Sign In') }}
                            </span>
                        </button>

                        <!-- Alternative Actions -->
                        <div class="text-center pt-4 border-t border-slate-800">
                            <p class="text-xs text-slate-400">
                                Remember your password?
                                <a href="{{ route('login') }}" class="text-green-400 hover:text-green-300 font-medium transition-colors">
                                    Sign in instead
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 text-center">
                <div class="inline-flex items-center space-x-2 text-xs text-slate-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Your new password will be encrypted and securely stored</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        // Password strength and requirements checker
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('strength-text');

            // Check requirements
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[^a-zA-Z0-9]/.test(password);

            // Update requirement indicators
            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-special', hasSpecial);

            // Calculate strength
            let strength = 0;
            if (hasLength) strength++;
            if (hasUppercase) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;

            const percentage = (strength / 4) * 100;
            strengthBar.style.width = percentage + '%';

            // Update strength text
            if (strength === 0) {
                strengthText.textContent = 'Too weak';
                strengthText.className = 'text-xs font-medium text-red-400';
            } else if (strength <= 2) {
                strengthText.textContent = 'Weak';
                strengthText.className = 'text-xs font-medium text-yellow-400';
            } else if (strength === 3) {
                strengthText.textContent = 'Good';
                strengthText.className = 'text-xs font-medium text-blue-400';
            } else {
                strengthText.textContent = 'Strong';
                strengthText.className = 'text-xs font-medium text-green-400';
            }

            checkPasswordMatch();
        });

        // Check password confirmation match
        document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const matchIndicator = document.getElementById('password-match');

            if (confirmation && password === confirmation) {
                matchIndicator.classList.remove('hidden');
            } else {
                matchIndicator.classList.add('hidden');
            }
        }

        function updateRequirement(id, met) {
            const element = document.getElementById(id);
            const icon = element.querySelector('svg');

            if (met) {
                element.classList.remove('bg-slate-700');
                element.classList.add('bg-green-500');
                icon.classList.remove('hidden', 'text-slate-500');
                icon.classList.add('text-white');
            } else {
                element.classList.add('bg-slate-700');
                element.classList.remove('bg-green-500');
                icon.classList.add('hidden', 'text-slate-500');
                icon.classList.remove('text-white');
            }
        }
    </script>
</x-guest-layout>
