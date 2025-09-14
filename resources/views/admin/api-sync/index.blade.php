@extends('admin.layout')

@section('title', 'API Sync')
@section('page-title', 'API Synchronization')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white mb-2">API Synchronization</h1>
    <p class="text-gray-400">Sync products and categories from external API</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">API Status</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-dark-bg rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <div>
                            <p class="text-white font-medium">API Connection</p>
                            <p class="text-gray-400 text-sm">Connected to LikeCard API</p>
                        </div>
                    </div>
                    <button onclick="testConnection()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-all">
                        Test Connection
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-dark-bg rounded-lg">
                        <p class="text-gray-400 text-sm mb-1">API Balance</p>
                        <p class="text-2xl font-bold text-white" id="api-balance">${{ $apiBalance ?? '0.00' }}</p>
                        <button onclick="checkBalance()" class="mt-2 text-primary-blue text-sm hover:text-green-400">
                            <i class="fas fa-sync mr-1"></i>Refresh
                        </button>
                    </div>

                    <div class="p-4 bg-dark-bg rounded-lg">
                        <p class="text-gray-400 text-sm mb-1">Last Sync</p>
                        <p class="text-white font-medium">{{ $lastSync ?? 'Never' }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $lastSyncTime ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Sync Operations</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="p-4 bg-dark-bg rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-white font-medium">Categories</h3>
                            <p class="text-gray-400 text-sm">Sync product categories from API</p>
                        </div>
                        <div class="text-right">
                            <p class="text-primary-blue font-semibold">{{ $totalCategories ?? 0 }}</p>
                            <p class="text-gray-500 text-xs">Total</p>
                        </div>
                    </div>
                    <button onclick="syncCategories()" class="w-full px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all" id="sync-categories-btn">
                        <i class="fas fa-sync mr-2"></i>Sync Categories
                    </button>
                </div>

                <div class="p-4 bg-dark-bg rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-white font-medium">Products</h3>
                            <p class="text-gray-400 text-sm">Sync all products from API</p>
                        </div>
                        <div class="text-right">
                            <p class="text-primary-blue font-semibold">{{ $totalProducts ?? 0 }}</p>
                            <p class="text-gray-500 text-xs">Total</p>
                        </div>
                    </div>
                    <button onclick="syncProducts()" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all" id="sync-products-btn">
                        <i class="fas fa-sync mr-2"></i>Sync Products
                    </button>
                </div>

                <div class="p-4 bg-dark-bg rounded-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-white font-medium">Full Sync</h3>
                            <p class="text-gray-400 text-sm">Sync categories and products</p>
                        </div>
                        <div class="text-right">
                            <p class="text-yellow-400 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>May take time</p>
                        </div>
                    </div>
                    <button onclick="fullSync()" class="w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all" id="full-sync-btn">
                        <i class="fas fa-sync-alt mr-2"></i>Full Sync
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Sync History</h2>
            </div>
            <div class="p-6">
                <div class="space-y-3" id="sync-history">
                    @foreach($syncHistory ?? [] as $history)
                    <div class="flex items-center justify-between p-3 bg-dark-bg rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 rounded-full {{ $history->status === 'success' ? 'bg-green-400' : 'bg-red-400' }}"></div>
                            <div>
                                <p class="text-white text-sm">{{ $history->type }}</p>
                                <p class="text-gray-500 text-xs">{{ $history->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-300 text-sm">{{ $history->items_synced }} items</p>
                            <p class="text-gray-500 text-xs">{{ $history->duration }}s</p>
                        </div>
                    </div>
                    @endforeach

                    @if(empty($syncHistory) || count($syncHistory) === 0)
                    <p class="text-center text-gray-500 py-4">No sync history available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Sync Settings</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="flex items-center justify-between">
                        <span class="text-gray-300">Auto Sync</span>
                        <input type="checkbox" id="auto-sync" class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue">
                    </label>
                    <p class="text-gray-500 text-xs mt-1">Automatically sync every 24 hours</p>
                </div>

                <div>
                    <label class="flex items-center justify-between">
                        <span class="text-gray-300">Update Prices</span>
                        <input type="checkbox" id="update-prices" checked class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue">
                    </label>
                    <p class="text-gray-500 text-xs mt-1">Update product prices during sync</p>
                </div>

                <div>
                    <label class="flex items-center justify-between">
                        <span class="text-gray-300">Update Images</span>
                        <input type="checkbox" id="update-images" checked class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue">
                    </label>
                    <p class="text-gray-500 text-xs mt-1">Update product images if changed</p>
                </div>

                <div>
                    <label class="flex items-center justify-between">
                        <span class="text-gray-300">Remove Deleted</span>
                        <input type="checkbox" id="remove-deleted" class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue">
                    </label>
                    <p class="text-gray-500 text-xs mt-1">Remove products not in API</p>
                </div>

                <button onclick="saveSettings()" class="w-full px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all mt-4">
                    Save Settings
                </button>
            </div>
        </div>

        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Sync Statistics</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Categories Synced</span>
                    <span class="text-white font-semibold">{{ $categoriesSynced ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Products Synced</span>
                    <span class="text-white font-semibold">{{ $productsSynced ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">New Products</span>
                    <span class="text-green-400 font-semibold">{{ $newProducts ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Updated Products</span>
                    <span class="text-blue-400 font-semibold">{{ $updatedProducts ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Failed Syncs</span>
                    <span class="text-red-400 font-semibold">{{ $failedSyncs ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                <div>
                    <p class="text-yellow-400 font-medium mb-1">Important Notes</p>
                    <ul class="text-yellow-400/80 text-sm space-y-1">
                        <li>• Syncing may take several minutes</li>
                        <li>• Prices will be updated with margins</li>
                        <li>• Do not close this page during sync</li>
                        <li>• Check API balance before syncing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="sync-progress-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-dark-card rounded-xl border border-dark-border p-6">
            <h3 class="text-xl font-bold text-white mb-4">Syncing...</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Progress</span>
                        <span class="text-white" id="sync-progress-text">0%</span>
                    </div>
                    <div class="w-full bg-dark-bg rounded-full h-2">
                        <div class="bg-primary-blue h-2 rounded-full transition-all duration-300" id="sync-progress-bar" style="width: 0%"></div>
                    </div>
                </div>
                <p class="text-gray-400 text-sm" id="sync-status">Initializing...</p>
                <div id="sync-details" class="text-xs text-gray-500"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let syncInterval;

function testConnection() {
    fetch('/admin/api-sync/test-connection', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('API connection successful!');
        } else {
            alert('API connection failed: ' + data.message);
        }
    });
}

function checkBalance() {
    const button = event.target;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Checking...';

    fetch('{{ route("admin.api-sync.balance") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('api-balance').textContent = '$' + data.balance.toFixed(2);
        }
        button.innerHTML = '<i class="fas fa-sync mr-1"></i>Refresh';
    });
}

function syncCategories() {
    const button = document.getElementById('sync-categories-btn');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';
    showSyncProgress('Categories');

    fetch('{{ route("admin.api-sync.categories") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSyncProgress(100, 'Categories synced successfully!');
            setTimeout(() => {
                closeSyncProgress();
                window.location.reload();
            }, 2000);
        } else {
            closeSyncProgress();
            alert('Sync failed: ' + data.message);
        }
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sync mr-2"></i>Sync Categories';
    });
}

function syncProducts() {
    const button = document.getElementById('sync-products-btn');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';
    showSyncProgress('Products');

    let progress = 0;
    syncInterval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress > 90) progress = 90;
        updateSyncProgress(progress, 'Syncing products...');
    }, 500);

    fetch('{{ route("admin.api-sync.products") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(syncInterval);
        if (data.success) {
            updateSyncProgress(100, 'Products synced successfully!');
            setTimeout(() => {
                closeSyncProgress();
                window.location.reload();
            }, 2000);
        } else {
            closeSyncProgress();
            alert('Sync failed: ' + data.message);
        }
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sync mr-2"></i>Sync Products';
    });
}

function fullSync() {
    if (!confirm('This will sync all categories and products. This may take several minutes. Continue?')) {
        return;
    }

    const button = document.getElementById('full-sync-btn');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';
    showSyncProgress('Full Sync');

    updateSyncProgress(10, 'Syncing categories...');

    fetch('{{ route("admin.api-sync.categories") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSyncProgress(30, 'Categories synced. Syncing products...');

            return fetch('{{ route("admin.api-sync.products") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        } else {
            throw new Error('Category sync failed');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSyncProgress(100, 'Full sync completed successfully!');
            setTimeout(() => {
                closeSyncProgress();
                window.location.reload();
            }, 2000);
        } else {
            throw new Error('Product sync failed');
        }
    })
    .catch(error => {
        closeSyncProgress();
        alert('Sync failed: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Full Sync';
    });
}

function showSyncProgress(type) {
    document.getElementById('sync-progress-modal').classList.remove('hidden');
    document.getElementById('sync-status').textContent = `Starting ${type}...`;
    updateSyncProgress(0, 'Initializing...');
}

function updateSyncProgress(percent, status) {
    document.getElementById('sync-progress-bar').style.width = percent + '%';
    document.getElementById('sync-progress-text').textContent = Math.round(percent) + '%';
    document.getElementById('sync-status').textContent = status;
}

function closeSyncProgress() {
    document.getElementById('sync-progress-modal').classList.add('hidden');
    if (syncInterval) {
        clearInterval(syncInterval);
    }
}

function saveSettings() {
    const settings = {
        auto_sync: document.getElementById('auto-sync').checked,
        update_prices: document.getElementById('update-prices').checked,
        update_images: document.getElementById('update-images').checked,
        remove_deleted: document.getElementById('remove-deleted').checked
    };

    fetch('/admin/api-sync/settings', {
        method: 'POST',
        body: JSON.stringify(settings),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        }
    });
}
</script>
@endpush
@endsection
