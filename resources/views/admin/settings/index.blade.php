@extends('admin.layout')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white mb-2">System Settings</h1>
    <p class="text-gray-400">Configure your store settings and preferences</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <form id="settings-form" class="space-y-6">
            @csrf

            <div class="bg-dark-card rounded-xl border border-dark-border">
                <div class="p-6 border-b border-dark-border">
                    <h2 class="text-lg font-semibold text-white">General Settings</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Store Name</label>
                        <input type="text" name="store_name" value="{{ $settings->store_name ?? 'GameShop' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Store Email</label>
                        <input type="email" name="store_email" value="{{ $settings->store_email ?? 'support@gameshop.com' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Store Phone</label>
                        <input type="tel" name="store_phone" value="{{ $settings->store_phone ?? '' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Store Address</label>
                        <textarea name="store_address" rows="3" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">{{ $settings->store_address ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Default Currency</label>
                        <select name="default_currency" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="USD" {{ ($settings->default_currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ ($settings->default_currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ ($settings->default_currency ?? '') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Timezone</label>
                        <select name="timezone" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles">Pacific Time</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-dark-card rounded-xl border border-dark-border">
                <div class="p-6 border-b border-dark-border">
                    <h2 class="text-lg font-semibold text-white">API Configuration</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">API Key</label>
                        <div class="relative">
                            <input type="password" name="api_key" value="{{ $settings->api_key ?? '' }}" id="api-key" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none pr-10">
                            <button type="button" onclick="toggleApiKey()" class="absolute right-3 top-2.5 text-gray-400 hover:text-white">
                                <i class="fas fa-eye" id="api-key-toggle"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">API Secret</label>
                        <div class="relative">
                            <input type="password" name="api_secret" value="{{ $settings->api_secret ?? '' }}" id="api-secret" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none pr-10">
                            <button type="button" onclick="toggleApiSecret()" class="absolute right-3 top-2.5 text-gray-400 hover:text-white">
                                <i class="fas fa-eye" id="api-secret-toggle"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">API Endpoint</label>
                        <input type="url" name="api_endpoint" value="{{ $settings->api_endpoint ?? 'https://api.likecard.com/api/v2' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-dark-card rounded-xl border border-dark-border">
                <div class="p-6 border-b border-dark-border">
                    <h2 class="text-lg font-semibold text-white">Pricing Settings</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Default Margin Type</label>
                        <select name="default_margin_type" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="percentage" {{ ($settings->default_margin_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ ($settings->default_margin_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Default Margin Value</label>
                        <input type="number" name="default_margin_value" value="{{ $settings->default_margin_value ?? 10 }}" step="0.01" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Minimum Margin (%)</label>
                        <input type="number" name="min_margin" value="{{ $settings->min_margin ?? 5 }}" step="0.01" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Maximum Margin (%)</label>
                        <input type="number" name="max_margin" value="{{ $settings->max_margin ?? 50 }}" step="0.01" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="round_prices" value="1" {{ ($settings->round_prices ?? true) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                            <span class="text-gray-300">Round prices to nearest .99</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-dark-card rounded-xl border border-dark-border">
                <div class="p-6 border-b border-dark-border">
                    <h2 class="text-lg font-semibold text-white">Payment Settings</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Stripe Public Key</label>
                        <input type="text" name="stripe_public_key" value="{{ $settings->stripe_public_key ?? '' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Stripe Secret Key</label>
                        <input type="password" name="stripe_secret_key" value="{{ $settings->stripe_secret_key ?? '' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">PayPal Client ID</label>
                        <input type="text" name="paypal_client_id" value="{{ $settings->paypal_client_id ?? '' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">PayPal Secret</label>
                        <input type="password" name="paypal_secret" value="{{ $settings->paypal_secret ?? '' }}" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="paypal_sandbox" value="1" {{ ($settings->paypal_sandbox ?? false) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                            <span class="text-gray-300">PayPal Sandbox Mode</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-dark-card rounded-xl border border-dark-border">
                <div class="p-6 border-b border-dark-border">
                    <h2 class="text-lg font-semibold text-white">Features</h2>
                </div>
                <div class="p-6 space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="enable_registration" value="1" {{ ($settings->enable_registration ?? true) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                        <span class="text-gray-300">Enable User Registration</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="enable_guest_checkout" value="1" {{ ($settings->enable_guest_checkout ?? false) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                        <span class="text-gray-300">Enable Guest Checkout</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="enable_reviews" value="1" {{ ($settings->enable_reviews ?? true) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                        <span class="text-gray-300">Enable Product Reviews</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="enable_wishlist" value="1" {{ ($settings->enable_wishlist ?? true) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                        <span class="text-gray-300">Enable Wishlist</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings->maintenance_mode ?? false) ? 'checked' : '' }} class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                        <span class="text-gray-300">Maintenance Mode</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
            </div>
            <div class="p-6 space-y-3">
                <button onclick="clearCache()" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all text-left">
                    <i class="fas fa-broom mr-2"></i>Clear Cache
                </button>

                <button onclick="optimizeDatabase()" class="w-full px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all text-left">
                    <i class="fas fa-database mr-2"></i>Optimize Database
                </button>

                <button onclick="exportSettings()" class="w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all text-left">
                    <i class="fas fa-download mr-2"></i>Export Settings
                </button>

                <button onclick="importSettings()" class="w-full px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-all text-left">
                    <i class="fas fa-upload mr-2"></i>Import Settings
                </button>
            </div>
        </div>

        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">System Info</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Laravel Version</span>
                    <span class="text-white">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">PHP Version</span>
                    <span class="text-white">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Database</span>
                    <span class="text-white">{{ config('database.default') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Environment</span>
                    <span class="text-white">{{ app()->environment() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Debug Mode</span>
                    <span class="text-white">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                <div>
                    <p class="text-yellow-400 font-medium mb-1">Important</p>
                    <p class="text-yellow-400/80 text-sm">Changes to API settings may affect product syncing. Ensure credentials are correct before saving.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('settings-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const button = e.target.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        } else {
            alert('Failed to save settings: ' + data.message);
        }
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-save mr-2"></i>Save Settings';
    });
});

function toggleApiKey() {
    const input = document.getElementById('api-key');
    const icon = document.getElementById('api-key-toggle');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function toggleApiSecret() {
    const input = document.getElementById('api-secret');
    const icon = document.getElementById('api-secret-toggle');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function clearCache() {
    if (confirm('Are you sure you want to clear all cache?')) {
        fetch('/admin/settings/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Cache cleared successfully!');
        });
    }
}

function optimizeDatabase() {
    if (confirm('This will optimize database tables. Continue?')) {
        fetch('/admin/settings/optimize-database', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Database optimized successfully!');
        });
    }
}

function exportSettings() {
    window.location.href = '/admin/settings/export';
}

function importSettings() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('settings_file', file);

            fetch('/admin/settings/import', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Settings imported successfully!');
                    window.location.reload();
                } else {
                    alert('Import failed: ' + data.message);
                }
            });
        }
    };
    input.click();
}
</script>
@endpush

