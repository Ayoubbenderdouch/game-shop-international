<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 relative overflow-hidden py-12">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,rgba(147,51,234,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,rgba(73,186,238,0.1),transparent_50%)]"></div>
            <div class="absolute top-1/3 left-1/3 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-purple-500/25">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </a>
                <h2 class="mt-6 text-3xl font-black text-white">
                    {{ __('auth.create_account') }}
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    {{ __('auth.join_us_today') }}
                </p>
            </div>

            <!-- Registration Form Card -->
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl shadow-black/50 p-8">
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('auth.full_name') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input id="name"
                                   name="name"
                                   type="text"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   autocomplete="name"
                                   class="block w-full pl-10 pr-3 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="John Doe">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400 text-sm" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('auth.email') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="username"
                                   class="block w-full pl-10 pr-3 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="you@example.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('auth.password') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   required
                                   autocomplete="new-password"
                                   class="block w-full pl-10 pr-3 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="togglePassword('password')" class="text-slate-400 hover:text-slate-300 focus:outline-none">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />

                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 h-1 bg-slate-700 rounded-full overflow-hidden">
                                    <div id="password-strength" class="h-full bg-gradient-to-r from-red-500 to-green-500 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <span id="strength-text" class="text-xs text-slate-400">Weak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('auth.confirm_password') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 8.118l-8 4-8-4V5.764a2 2 0 01-.951.236H4a2 2 0 01-2-2v2a2 2 0 002 2h12a2 2 0 002-2zM5 9V7a5 5 0 1110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input id="password_confirmation"
                                   name="password_confirmation"
                                   type="password"
                                   required
                                   autocomplete="new-password"
                                   class="block w-full pl-10 pr-3 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <input id="terms"
                               name="terms"
                               type="checkbox"
                               required
                               class="mt-1 w-4 h-4 bg-slate-800 border-slate-600 rounded text-purple-500 focus:ring-purple-500 focus:ring-offset-0 focus:ring-2 transition-colors">
                        <label for="terms" class="ml-2 text-sm text-slate-300">
                            I agree to the
                            <a href="#" class="text-purple-400 hover:text-purple-300 transition-colors">Terms and Conditions</a>
                            and
                            <a href="#" class="text-purple-400 hover:text-purple-300 transition-colors">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-purple-500 to-cyan-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-purple-500/25 transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                        {{ __('auth.create_account') }}
                    </button>

                    <!-- Divider -->
                    <!-- <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-slate-900/50 text-slate-400">Or sign up with</span>
                        </div>
                    </div> -->

                    <!-- Social Register Options -->
                    <!-- <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                                class="flex items-center justify-center px-4 py-2.5 bg-slate-800/50 border border-slate-700 rounded-xl hover:bg-slate-700/50 transition-colors">
                            <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                            </svg>
                            <span class="ml-2 text-sm font-medium text-slate-300">Google</span>
                        </button>

                        <button type="button"
                                class="flex items-center justify-center px-4 py-2.5 bg-slate-800/50 border border-slate-700 rounded-xl hover:bg-slate-700/50 transition-colors">
                            <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836a9.59 9.59 0 012.504.337c1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/>
                            </svg>
                            <span class="ml-2 text-sm font-medium text-slate-300">GitHub</span>
                        </button>
                    </div> -->
                </form>
            </div>

            <!-- Login Link -->
            <p class="mt-8 text-center text-sm text-slate-400">
                {{ __('auth.already_have_account') }}
                <a href="{{ route('login') }}"
                   class="font-medium text-purple-400 hover:text-purple-300 transition-colors">
                    {{ __('auth.sign_in') }}
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('strength-text');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            const percentage = (strength / 4) * 100;
            strengthBar.style.width = percentage + '%';

            if (strength === 0) {
                strengthText.textContent = 'Weak';
                strengthText.className = 'text-xs text-red-400';
            } else if (strength <= 2) {
                strengthText.textContent = 'Fair';
                strengthText.className = 'text-xs text-yellow-400';
            } else if (strength === 3) {
                strengthText.textContent = 'Good';
                strengthText.className = 'text-xs text-blue-400';
            } else {
                strengthText.textContent = 'Strong';
                strengthText.className = 'text-xs text-green-400';
            }
        });
    </script>
</x-guest-layout>
