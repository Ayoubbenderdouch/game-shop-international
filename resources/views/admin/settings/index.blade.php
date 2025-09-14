@extends('admin.layout')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">System Settings</h1>
        <p class="text-gray-400">Configure your store settings and preferences</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="testEmail()" class="px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all">
            <i class="fas fa-envelope mr-2"></i>Test Email
        </button>
        <button onclick="clearCache()" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all">
            <i class="fas fa-broom mr-2"></i>Clear Cache
        </button>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" id="settings-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Groups -->
        <div class="lg:col-span-2 space-y-6">
            @foreach($groupedSettings as $groupKey => $group)
            <div class="bg-dark-card rounded-xl border border-dark-border">
                <div class="p-6 border-b border-dark-border">
                    <div class="flex items-center space-x-3">
                        <i class="{{ $group['icon'] }} text-primary-blue"></i>
                        <h2 class="text-lg font-semibold text-white">{{ $group['title'] }}</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($group['settings'] as $settingKey)
                        @php
                            $fieldType = 'text';
                            $fieldLabel = ucwords(str_replace('_', ' ', $settingKey));

                            // Determine field type based on key
                            if (str_contains($settingKey, 'password') || str_contains($settingKey, 'secret') || str_contains($settingKey, 'key')) {
                                $fieldType = 'password';
                            } elseif (str_contains($settingKey, 'email')) {
                                $fieldType = 'email';
                            } elseif (str_contains($settingKey, 'url') || str_contains($settingKey, 'endpoint')) {
                                $fieldType = 'url';
                            } elseif (in_array($settingKey, ['tax_rate', 'shipping_fee', 'minimum_order_amount', 'free_shipping_threshold', 'products_per_page', 'order_cancellation_time'])) {
                                $fieldType = 'number';
                            } elseif (in_array($settingKey, ['store_status', 'allow_registration', 'require_email_verification', 'allow_guest_checkout', 'show_out_of_stock', 'enable_reviews', 'review_approval_required', 'auto_complete_order', 'api_enabled'])) {
                                $fieldType = 'select';
                            } elseif (str_contains($settingKey, 'description') || str_contains($settingKey, 'message')) {
                                $fieldType = 'textarea';
                            }
                        @endphp

                        <div class="{{ $fieldType === 'textarea' ? 'md:col-span-2' : '' }}">
                            <label class="block text-gray-400 text-sm mb-2">{{ $fieldLabel }}</label>

                            @if($fieldType === 'select')
                                @if($settingKey === 'store_status')
                                    <select name="{{ $settingKey }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                                        <option value="open" {{ ($settings[$settingKey] ?? '') == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="maintenance" {{ ($settings[$settingKey] ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="closed" {{ ($settings[$settingKey] ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                @elseif($settingKey === 'default_product_sorting')
                                    <select name="{{ $settingKey }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                                        <option value="newest" {{ ($settings[$settingKey] ?? '') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                        <option value="price_low" {{ ($settings[$settingKey] ?? '') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                        <option value="price_high" {{ ($settings[$settingKey] ?? '') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                        <option value="popular" {{ ($settings[$settingKey] ?? '') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                        <option value="rating" {{ ($settings[$settingKey] ?? '') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                                    </select>
                                @elseif($settingKey === 'order_status_after_payment')
                                    <select name="{{ $settingKey }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                                        <option value="pending" {{ ($settings[$settingKey] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ ($settings[$settingKey] ?? '') == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ ($settings[$settingKey] ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                @elseif($settingKey === 'smtp_encryption')
                                    <select name="{{ $settingKey }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                                        <option value="tls" {{ ($settings[$settingKey] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($settings[$settingKey] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ ($settings[$settingKey] ?? '') == '' ? 'selected' : '' }}>None</option>
                                    </select>
                                @else
                                    <select name="{{ $settingKey }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                                        <option value="1" {{ ($settings[$settingKey] ?? '') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($settings[$settingKey] ?? '') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                @endif
                            @elseif($fieldType === 'textarea')
                                <textarea name="{{ $settingKey }}" rows="3" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">{{ $settings[$settingKey] ?? '' }}</textarea>
                            @elseif($settingKey === 'payment_methods')
                                <div class="space-y-2">
                                    @php
                                        $paymentMethods = json_decode($settings[$settingKey] ?? '[]', true) ?: [];
                                    @endphp
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="credit_card" {{ in_array('credit_card', $paymentMethods) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-gray-300">Credit Card</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="paypal" {{ in_array('paypal', $paymentMethods) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-gray-300">PayPal</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="stripe" {{ in_array('stripe', $paymentMethods) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-gray-300">Stripe</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="cash" {{ in_array('cash', $paymentMethods) ? 'checked' : '' }} class="mr-2">
                                        <span class="text-gray-300">Cash on Delivery</span>
                                    </label>
                                </div>
                            @else
                                <input type="{{ $fieldType }}" name="{{ $settingKey }}" value="{{ $settings[$settingKey] ?? '' }}"
                                       class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none"
                                       {{ $fieldType === 'number' ? 'step="any"' : '' }}>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Save Button -->
            <div class="bg-dark-card rounded-xl border border-dark-border p-6">
                <button type="submit" class="w-full px-4 py-3 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
                <p class="text-gray-400 text-sm mt-3 text-center">
                    Last updated: {{ now()->format('M d, Y H:i') }}
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-dark-card rounded-xl border border-dark-border p-6">
                <h3 class="text-white font-semibold mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button type="button" onclick="testConnection()" class="w-full px-4 py-2 bg-dark-bg text-gray-300 rounded-lg hover:bg-gray-800 transition-all text-left">
                        <i class="fas fa-plug mr-2"></i>Test API Connection
                    </button>
                    <button type="button" onclick="exportSettings()" class="w-full px-4 py-2 bg-dark-bg text-gray-300 rounded-lg hover:bg-gray-800 transition-all text-left">
                        <i class="fas fa-download mr-2"></i>Export Settings
                    </button>
                    <button type="button" onclick="importSettings()" class="w-full px-4 py-2 bg-dark-bg text-gray-300 rounded-lg hover:bg-gray-800 transition-all text-left">
                        <i class="fas fa-upload mr-2"></i>Import Settings
                    </button>
                    <button type="button" onclick="resetDefaults()" class="w-full px-4 py-2 bg-dark-bg text-red-400 rounded-lg hover:bg-gray-800 transition-all text-left">
                        <i class="fas fa-undo mr-2"></i>Reset to Defaults
                    </button>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-dark-card rounded-xl border border-dark-border p-6">
                <h3 class="text-white font-semibold mb-4">System Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">PHP Version</span>
                        <span class="text-gray-300">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Laravel Version</span>
                        <span class="text-gray-300">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Server</span>
                        <span class="text-gray-300">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Database</span>
                        <span class="text-gray-300">{{ config('database.default') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Test Email Modal -->
<div id="test-email-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeTestEmailModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white">Test Email Settings</h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm mb-2">Send Test Email To</label>
                    <input type="email" id="test-email-address" placeholder="test@example.com"
                           class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                </div>
                <div class="flex space-x-3">
                    <button onclick="sendTestEmail()" class="flex-1 px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                        Send Test
                    </button>
                    <button onclick="closeTestEmailModal()" class="flex-1 px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function testEmail() {
    document.getElementById('test-email-modal').classList.remove('hidden');
}

function closeTestEmailModal() {
    document.getElementById('test-email-modal').classList.add('hidden');
}

function sendTestEmail() {
    const email = document.getElementById('test-email-address').value;
    if (!email) {
        alert('Please enter an email address');
        return;
    }

    fetch('{{ route("admin.settings.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ test_email: email })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            closeTestEmailModal();
        }
    });
}

function clearCache() {
    if (confirm('Are you sure you want to clear all cache?')) {
        window.location.href = '{{ route("admin.settings.clear-cache") }}';
    }
}

function testConnection() {
    alert('Testing API connection...');
}

function exportSettings() {
    alert('Exporting settings...');
}

function importSettings() {
    alert('Import settings feature coming soon');
}

function resetDefaults() {
    if (confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
        alert('Reset to defaults feature coming soon');
    }
}
</script>
@endpush
