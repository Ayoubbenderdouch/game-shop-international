<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 relative overflow-hidden">
        <!-- Animated Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(59,130,246,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(147,51,234,0.1),transparent_50%)]"></div>

            <!-- Animated mail icons floating -->
            <div class="absolute inset-0">
                <div class="absolute top-1/4 left-1/4 text-blue-500/10 animate-pulse">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                </div>
                <div class="absolute bottom-1/3 right-1/4 text-purple-500/10 animate-pulse delay-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                </div>
            </div>

            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="40" height="40" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3Cpattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"%3E%3Cpath d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(59,130,246,0.05)" stroke-width="1"/%3E%3C/pattern%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grid)"/%3E%3C/svg%3E')]"></div>
        </div>

        <div class="relative z-10 w-full max-w-lg px-6">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-500 rounded-2xl blur-lg opacity-50 group-hover:opacity-75 transition-opacity animate-pulse"></div>
                        <div class="relative w-20 h-20 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/25 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <h2 class="mt-6 text-3xl font-black text-white">
                    Verify Your Email
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    One more step to activate your account
                </p>
            </div>

            <!-- Main Card -->
            <div class="relative">
                <!-- Card glow effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-indigo-500/20 to-purple-500/20 rounded-2xl blur-xl"></div>

                <!-- Card content -->
                <div class="relative bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl shadow-black/50 p-8 overflow-hidden">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full blur-2xl"></div>

                    <!-- Success Status Message -->
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-6 bg-green-500/10 border border-green-500/30 rounded-xl p-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-green-400">
                                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Main Message with Animation -->
                    <div class="mb-8">
                        <div class="relative bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-xl p-6 border border-blue-500/20">
                            <!-- Animated envelope -->
                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2">
                                <div class="relative">
                                    <div class="absolute inset-0 bg-blue-500 rounded-full blur-xl opacity-50 animate-pulse"></div>
                                    <div class="relative w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 text-center">
                                <h3 class="text-lg font-semibold text-white mb-3">Check Your Inbox!</h3>
                                <p class="text-sm text-slate-300 leading-relaxed">
                                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Status Indicators -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto bg-blue-500/20 rounded-xl flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400">Email Sent</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto bg-slate-700 rounded-xl flex items-center justify-center mb-2 relative">
                                <div class="absolute inset-0 bg-purple-500/20 rounded-xl animate-pulse"></div>
                                <svg class="w-6 h-6 text-purple-400 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400">Awaiting Click</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto bg-slate-700 rounded-xl flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400">Verified</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit"
                                    class="relative w-full py-3 px-4 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 text-white font-semibold rounded-xl
                                           shadow-lg hover:shadow-blue-500/25 transform hover:scale-[1.02] transition-all duration-200
                                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900
                                           group overflow-hidden">

                                <!-- Button shine effect -->
                                <div class="absolute inset-0 -top-2 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>

                                <span class="relative flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"></path>
                                    </svg>
                                    {{ __('Resend Verification Email') }}
                                </span>
                            </button>
                        </form>

                        <!-- Tips Section -->
                        <div class="bg-slate-800/30 rounded-xl p-4 border border-slate-700/50">
                            <h4 class="text-sm font-semibold text-white mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Didn't receive the email?
                            </h4>
                            <ul class="space-y-2 text-xs text-slate-400">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-cyan-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Check your spam or junk folder
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-cyan-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Add noreply@example.com to your contacts
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-cyan-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Wait a few minutes and check again
                                </li>
                            </ul>
                        </div>

                        <!-- Logout Option -->
                        <div class="text-center pt-4 border-t border-slate-800">
                            <p class="text-sm text-slate-400 mb-3">Wrong email address?</p>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="text-sm text-red-400 hover:text-red-300 font-medium transition-colors">
                                    {{ __('Sign out and try again') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400">
                    Still having issues?
                    <a href="#" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                        Contact Support
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
