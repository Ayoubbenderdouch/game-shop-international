@extends('admin.layout')

@section('title', 'Products Management')
@section('page-title', 'Products')

@section('content')
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Products Management</h1>
        <p class="text-gray-400">Manage your product catalog and pricing</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <button onclick="openBulkPricingModal()" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
            <i class="fas fa-tags mr-2"></i>Bulk Pricing
        </button>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
            <i class="fas fa-plus mr-2"></i>Add Product
        </a>
        <button onclick="syncProducts()" class="px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all">
            <i class="fas fa-sync mr-2"></i>Sync from API
        </button>
    </div>
</div>

<div class="bg-dark-card rounded-xl border border-dark-border mb-6">
    <div class="p-6 border-b border-dark-border">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="search" placeholder="Search products..." class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
            </div>
            <div>
                <select id="category-filter" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="status-filter" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
            </div>
            <div>
                <select id="sort-by" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    <option value="name">Sort by Name</option>
                    <option value="price">Sort by Price</option>
                    <option value="margin">Sort by Margin</option>
                    <option value="sales">Sort by Sales</option>
                    <option value="created">Sort by Date</option>
                </select>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <input type="checkbox" id="select-all" class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue">
                <label for="select-all" class="text-gray-400 text-sm">Select All</label>
                <span id="selected-count" class="text-gray-400 text-sm hidden">(<span class="count">0</span> selected)</span>
            </div>
            <div id="bulk-actions" class="hidden space-x-2">
                <button onclick="bulkUpdateStatus('active')" class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Activate</button>
                <button onclick="bulkUpdateStatus('inactive')" class="px-3 py-1 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700">Deactivate</button>
                <button onclick="bulkDelete()" class="px-3 py-1 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Delete</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 text-sm border-b border-dark-border">
                        <th class="pb-3 w-10"></th>
                        <th class="pb-3">Product</th>
                        <th class="pb-3">Category</th>
                        <th class="pb-3">Cost Price</th>
                        <th class="pb-3">Selling Price</th>
                        <th class="pb-3">Margin</th>
                        <th class="pb-3">Stock</th>
                        <th class="pb-3">Sales</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($products ?? [] as $product)
                    <tr class="border-b border-dark-border/50 hover:bg-dark-bg/50 transition-all">
                        <td class="py-4">
                            <input type="checkbox" class="product-checkbox w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded" value="{{ $product->id }}">
                        </td>
                        <td class="py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-dark-bg rounded-lg overflow-hidden flex-shrink-0">
                                    @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-xl">🎮</div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ Str::limit($product->name, 40) }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $product->api_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <span class="px-2 py-1 bg-dark-bg rounded text-xs">{{ $product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td class="py-4">
                            <span class="font-mono">${{ number_format($product->cost_price, 2) }}</span>
                        </td>
                        <td class="py-4">
                            <span class="font-mono font-semibold text-white">${{ number_format($product->selling_price, 2) }}</span>
                        </td>
                        <td class="py-4">
                            <div>
                                <span class="text-primary-blue font-semibold">
                                    @if($product->margin_type === 'percentage')
                                        {{ number_format($product->margin_percentage, 1) }}%
                                    @else
                                        ${{ number_format($product->margin_amount, 2) }}
                                    @endif
                                </span>
                                <p class="text-xs text-gray-500">${{ number_format($product->selling_price - $product->cost_price, 2) }}</p>
                            </div>
                        </td>
                        <td class="py-4">
                            @if($product->stock_quantity !== null)
                                <span class="@if($product->stock_quantity < 10) text-red-400 @else text-green-400 @endif">
                                    {{ $product->stock_quantity }}
                                </span>
                            @else
                                <span class="text-gray-500">∞</span>
                            @endif
                        </td>
                        <td class="py-4">
                            <span class="text-yellow-400">{{ $product->sales_count }}</span>
                        </td>
                        <td class="py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($product->is_active && $product->is_available) bg-green-900/50 text-green-400
                                @elseif($product->is_active) bg-yellow-900/50 text-yellow-400
                                @else bg-red-900/50 text-red-400
                                @endif">
                                @if($product->is_active && $product->is_available)
                                    Active
                                @elseif($product->is_active)
                                    Unavailable
                                @else
                                    Inactive
                                @endif
                            </span>
                        </td>
                        <td class="py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-400 hover:text-blue-300">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="quickEditPrice({{ $product->id }}, {{ $product->cost_price }}, {{ $product->selling_price }})" class="text-green-400 hover:text-green-300">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                                <button onclick="deleteProduct({{ $product->id }})" class="text-red-400 hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(isset($products) && $products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<div id="bulk-pricing-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeBulkPricingModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white">Bulk Pricing Update</h2>
                <p class="text-gray-400 text-sm mt-1">Apply pricing rules to multiple products at once</p>
            </div>
            <form id="bulk-pricing-form" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Apply To</label>
                        <select name="apply_to" id="apply-to" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="selected">Selected Products</option>
                            <option value="all">All Products</option>
                            <option value="category">By Category</option>
                        </select>
                    </div>

                    <div id="category-select" class="hidden">
                        <label class="block text-gray-400 text-sm mb-2">Select Category</label>
                        <select name="category_id" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Pricing Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-3 bg-dark-bg border border-dark-border rounded-lg cursor-pointer hover:border-primary-blue">
                                <input type="radio" name="margin_type" value="percentage" checked class="mr-3 text-primary-blue">
                                <div>
                                    <p class="text-white font-medium">Percentage Margin</p>
                                    <p class="text-gray-500 text-xs">Add % to cost price</p>
                                </div>
                            </label>
                            <label class="flex items-center p-3 bg-dark-bg border border-dark-border rounded-lg cursor-pointer hover:border-primary-blue">
                                <input type="radio" name="margin_type" value="fixed" class="mr-3 text-primary-blue">
                                <div>
                                    <p class="text-white font-medium">Fixed Margin</p>
                                    <p class="text-gray-500 text-xs">Add fixed amount</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Margin Value</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="margin_value" step="0.01" min="0" class="flex-1 px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none" placeholder="Enter value">
                            <span id="margin-suffix" class="text-gray-400">%</span>
                        </div>
                    </div>

                    <div class="bg-dark-bg rounded-lg p-4">
                        <p class="text-gray-400 text-sm mb-2">Preview</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Example: Cost Price $10.00</span>
                                <span class="text-white" id="preview-price">→ Selling Price $11.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeBulkPricingModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                            Apply Pricing
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="quick-edit-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeQuickEditModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white">Quick Price Edit</h2>
            </div>
            <form id="quick-edit-form" class="p-6">
                @csrf
                @method('PATCH')
                <input type="hidden" id="quick-edit-id" name="product_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Cost Price</label>
                        <input type="number" id="quick-cost-price" step="0.01" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Selling Price</label>
                        <input type="number" id="quick-selling-price" name="selling_price" step="0.01" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>
                    <div class="bg-dark-bg rounded-lg p-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Margin</span>
                            <span class="text-primary-blue font-semibold" id="quick-margin">$0.00 (0%)</span>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeQuickEditModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                            Update Price
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedProducts = [];

document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
    const count = selectedProducts.length;
    const countElement = document.getElementById('selected-count');
    const bulkActions = document.getElementById('bulk-actions');

    if (count > 0) {
        countElement.classList.remove('hidden');
        countElement.querySelector('.count').textContent = count;
        bulkActions.classList.remove('hidden');
    } else {
        countElement.classList.add('hidden');
        bulkActions.classList.add('hidden');
    }
}

function openBulkPricingModal() {
    document.getElementById('bulk-pricing-modal').classList.remove('hidden');
}

function closeBulkPricingModal() {
    document.getElementById('bulk-pricing-modal').classList.add('hidden');
}

function quickEditPrice(id, costPrice, sellingPrice) {
    document.getElementById('quick-edit-id').value = id;
    document.getElementById('quick-cost-price').value = costPrice.toFixed(2);
    document.getElementById('quick-selling-price').value = sellingPrice.toFixed(2);
    updateQuickMargin();
    document.getElementById('quick-edit-modal').classList.remove('hidden');
}

function closeQuickEditModal() {
    document.getElementById('quick-edit-modal').classList.add('hidden');
}

function updateQuickMargin() {
    const cost = parseFloat(document.getElementById('quick-cost-price').value) || 0;
    const selling = parseFloat(document.getElementById('quick-selling-price').value) || 0;
    const margin = selling - cost;
    const marginPercent = cost > 0 ? ((margin / cost) * 100).toFixed(1) : 0;
    document.getElementById('quick-margin').textContent = `$${margin.toFixed(2)} (${marginPercent}%)`;
}

document.getElementById('quick-selling-price').addEventListener('input', updateQuickMargin);

document.getElementById('apply-to').addEventListener('change', function() {
    document.getElementById('category-select').classList.toggle('hidden', this.value !== 'category');
});

document.querySelectorAll('input[name="margin_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('margin-suffix').textContent = this.value === 'percentage' ? '%' : '$';
        updatePreview();
    });
});

document.querySelector('input[name="margin_value"]').addEventListener('input', updatePreview);

function updatePreview() {
    const type = document.querySelector('input[name="margin_type"]:checked').value;
    const value = parseFloat(document.querySelector('input[name="margin_value"]').value) || 0;
    const costPrice = 10;
    let sellingPrice;

    if (type === 'percentage') {
        sellingPrice = costPrice * (1 + value / 100);
    } else {
        sellingPrice = costPrice + value;
    }

    document.getElementById('preview-price').textContent = `→ Selling Price $${sellingPrice.toFixed(2)}`;
}

document.getElementById('bulk-pricing-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    if (formData.get('apply_to') === 'selected') {
        selectedProducts.forEach(id => {
            formData.append('product_ids[]', id);
        });
    }

    fetch('{{ route("admin.products.apply-margin") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
});

document.getElementById('quick-edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('quick-edit-id').value;
    const formData = new FormData(this);

    fetch(`/admin/products/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
});

function bulkUpdateStatus(status) {
    if (confirm(`Are you sure you want to ${status} selected products?`)) {
        const formData = new FormData();
        formData.append('action', status === 'active' ? 'activate' : 'deactivate');
        selectedProducts.forEach(id => {
            formData.append('product_ids[]', id);
        });

        fetch('{{ route("admin.products.bulk-update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function bulkDelete() {
    if (confirm('Are you sure you want to delete selected products? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        selectedProducts.forEach(id => {
            formData.append('product_ids[]', id);
        });

        fetch('{{ route("admin.products.bulk-update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch(`/admin/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function syncProducts() {
    if (confirm('This will sync all products from the API. Continue?')) {
        const button = event.target;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';

        fetch('{{ route("admin.api-sync.products") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

document.getElementById('search').addEventListener('input', debounce(function() {
    filterProducts();
}, 500));

document.getElementById('category-filter').addEventListener('change', filterProducts);
document.getElementById('status-filter').addEventListener('change', filterProducts);
document.getElementById('sort-by').addEventListener('change', filterProducts);

function filterProducts() {
    const params = new URLSearchParams();

    const search = document.getElementById('search').value;
    if (search) params.append('search', search);

    const category = document.getElementById('category-filter').value;
    if (category) params.append('category', category);

    const status = document.getElementById('status-filter').value;
    if (status) params.append('status', status);

    const sort = document.getElementById('sort-by').value;
    if (sort) params.append('sort', sort);

    window.location.href = '{{ route("admin.products.index") }}?' + params.toString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
@endsection
