@extends('admin.layout')

@section('title', 'Pricing Rules')
@section('page-title', 'Pricing Rules')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Pricing Rules</h1>
        <p class="text-gray-400">Manage automated pricing rules and margins</p>
    </div>
    <button onclick="openRuleModal()" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
        <i class="fas fa-plus mr-2"></i>Create Rule
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Active Rules</span>
            <i class="fas fa-check-circle text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $activeRules ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Category Rules</span>
            <i class="fas fa-folder text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $categoryRules ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Product Rules</span>
            <i class="fas fa-gamepad text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $productRules ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Global Rules</span>
            <i class="fas fa-globe text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $globalRules ?? 0 }}</p>
    </div>
</div>

<div class="bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Pricing Rules</h2>
            <div class="flex space-x-2">
                <select id="filter-type" class="px-3 py-1 bg-dark-bg border border-dark-border rounded-lg text-sm text-gray-300">
                    <option value="">All Types</option>
                    <option value="all">Global</option>
                    <option value="category">Category</option>
                    <option value="product">Product</option>
                </select>
                <select id="filter-status" class="px-3 py-1 bg-dark-bg border border-dark-border rounded-lg text-sm text-gray-300">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="space-y-4">
            @foreach($pricingRules ?? [] as $rule)
            <div class="bg-dark-bg rounded-lg p-4 hover:bg-gray-800 transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-white font-medium">{{ $rule->name }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full {{ $rule->is_active ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-900/50 text-blue-400">
                                Priority: {{ $rule->priority }}
                            </span>
                        </div>

                        <p class="text-gray-400 text-sm mb-3">{{ $rule->description }}</p>

                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-tag text-gray-500"></i>
                                <span class="text-gray-300">
                                    @if($rule->apply_to === 'all')
                                        All Products
                                    @elseif($rule->apply_to === 'category')
                                        Category: {{ $rule->category->name ?? 'N/A' }}
                                    @else
                                        Product: {{ $rule->product->name ?? 'N/A' }}
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <i class="fas fa-percentage text-gray-500"></i>
                                <span class="text-primary-blue font-semibold">
                                    @if($rule->type === 'percentage')
                                        +{{ $rule->value }}%
                                    @else
                                        +${{ number_format($rule->value, 2) }}
                                    @endif
                                </span>
                            </div>

                            @if($rule->starts_at || $rule->ends_at)
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar text-gray-500"></i>
                                <span class="text-gray-300">
                                    @if($rule->starts_at && $rule->ends_at)
                                        {{ $rule->starts_at->format('M d') }} - {{ $rule->ends_at->format('M d, Y') }}
                                    @elseif($rule->starts_at)
                                        Starts {{ $rule->starts_at->format('M d, Y') }}
                                    @else
                                        Ends {{ $rule->ends_at->format('M d, Y') }}
                                    @endif
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 ml-4">
                        <button onclick="editRule({{ $rule->id }})" class="text-blue-400 hover:text-blue-300 p-2">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="toggleRule({{ $rule->id }}, {{ $rule->is_active ? 'false' : 'true' }})" class="text-yellow-400 hover:text-yellow-300 p-2">
                            <i class="fas fa-power-off"></i>
                        </button>
                        <button onclick="deleteRule({{ $rule->id }})" class="text-red-400 hover:text-red-300 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if((!isset($pricingRules) || $pricingRules->count() === 0))
        <div class="text-center py-12">
            <i class="fas fa-tags text-gray-600 text-4xl mb-4"></i>
            <p class="text-gray-400">No pricing rules found</p>
            <button onclick="openRuleModal()" class="mt-4 text-primary-blue hover:text-green-400">Create your first rule</button>
        </div>
        @endif
    </div>
</div>

<div id="rule-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeRuleModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white" id="modal-title">Create Pricing Rule</h2>
            </div>
            <form id="rule-form" class="p-6">
                @csrf
                <input type="hidden" id="rule-id" name="rule_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-2">Rule Name *</label>
                        <input type="text" id="rule-name" name="name" required class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-2">Description</label>
                        <textarea id="rule-description" name="description" rows="2" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Apply To *</label>
                        <select id="apply-to" name="apply_to" required class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="all">All Products</option>
                            <option value="category">Specific Category</option>
                            <option value="product">Specific Product</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Priority</label>
                        <input type="number" id="rule-priority" name="priority" value="0" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                        <p class="text-gray-500 text-xs mt-1">Higher priority rules apply first</p>
                    </div>

                    <div id="category-select" class="hidden md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-2">Select Category</label>
                        <select name="category_id" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="product-select" class="hidden md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-2">Select Product</label>
                        <input type="text" name="product_search" placeholder="Search product..." class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                        <input type="hidden" name="product_id">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Margin Type *</label>
                        <select id="margin-type" name="type" required class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Value *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400" id="value-prefix">%</span>
                            <input type="number" id="rule-value" name="value" step="0.01" min="0" required class="w-full pl-8 pr-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Start Date (Optional)</label>
                        <input type="datetime-local" name="starts_at" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">End Date (Optional)</label>
                        <input type="datetime-local" name="ends_at" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="rule-active" name="is_active" value="1" checked class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                            <span class="text-gray-300">Active</span>
                        </label>
                    </div>

                    <div class="md:col-span-2 pt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeRuleModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                            Save Rule
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
function openRuleModal() {
    document.getElementById('modal-title').textContent = 'Create Pricing Rule';
    document.getElementById('rule-form').reset();
    document.getElementById('rule-id').value = '';
    document.getElementById('rule-modal').classList.remove('hidden');
}

function closeRuleModal() {
    document.getElementById('rule-modal').classList.add('hidden');
}

function editRule(id) {
    fetch(`/admin/pricing-rules/${id}`)
        .then(response => response.json())
        .then(rule => {
            document.getElementById('modal-title').textContent = 'Edit Pricing Rule';
            document.getElementById('rule-id').value = rule.id;
            document.getElementById('rule-name').value = rule.name;
            document.getElementById('rule-description').value = rule.description || '';
            document.getElementById('apply-to').value = rule.apply_to;
            document.getElementById('margin-type').value = rule.type;
            document.getElementById('rule-value').value = rule.value;
            document.getElementById('rule-priority').value = rule.priority;
            document.getElementById('rule-active').checked = rule.is_active;

            if (rule.apply_to === 'category') {
                document.getElementById('category-select').classList.remove('hidden');
                document.querySelector('[name="category_id"]').value = rule.category_id;
            } else if (rule.apply_to === 'product') {
                document.getElementById('product-select').classList.remove('hidden');
                document.querySelector('[name="product_id"]').value = rule.product_id;
            }

            document.getElementById('rule-modal').classList.remove('hidden');
        });
}

document.getElementById('apply-to').addEventListener('change', function() {
    document.getElementById('category-select').classList.toggle('hidden', this.value !== 'category');
    document.getElementById('product-select').classList.toggle('hidden', this.value !== 'product');
});

document.getElementById('margin-type').addEventListener('change', function() {
    document.getElementById('value-prefix').textContent = this.value === 'percentage' ? '%' : '$';
});

document.getElementById('rule-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const ruleId = document.getElementById('rule-id').value;

    const url = ruleId
        ? `/admin/pricing-rules/${ruleId}`
        : '{{ route("admin.pricing-rules.store") }}';

    if (ruleId) {
        formData.append('_method', 'PUT');
    }

    fetch(url, {
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

function toggleRule(id, activate) {
    fetch(`/admin/pricing-rules/${id}`, {
        method: 'POST',
        body: JSON.stringify({ is_active: activate, _method: 'PATCH' }),
        headers: {
            'Content-Type': 'application/json',
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

function deleteRule(id) {
    if (confirm('Are you sure you want to delete this pricing rule?')) {
        fetch(`/admin/pricing-rules/${id}`, {
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

document.getElementById('filter-type').addEventListener('change', filterRules);
document.getElementById('filter-status').addEventListener('change', filterRules);

function filterRules() {
    const params = new URLSearchParams();
    const type = document.getElementById('filter-type').value;
    const status = document.getElementById('filter-status').value;

    if (type) params.append('type', type);
    if (status) params.append('status', status);

    window.location.href = '{{ route("admin.pricing-rules.index") }}?' + params.toString();
}
</script>
@endpush
@endsection
