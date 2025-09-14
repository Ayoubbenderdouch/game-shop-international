@extends('admin.layout')

@section('title', 'Categories Management')
@section('page-title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Categories Management</h1>
        <p class="text-gray-400">Organize your products into categories</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="syncCategories()" class="px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all">
            <i class="fas fa-sync mr-2"></i>Sync from API
        </button>
        <button onclick="openCategoryModal()" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
            <i class="fas fa-plus mr-2"></i>Add Category
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">All Categories</h2>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @foreach($categories ?? [] as $category)
                    <div class="flex items-center justify-between p-4 bg-dark-bg rounded-lg hover:bg-gray-800 transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                <i class="fas fa-folder text-white"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-white font-medium">{{ $category->name }}</h3>
                                <p class="text-gray-400 text-sm">{{ $category->products_count ?? 0 }} products</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', {{ $category->is_active ? 'true' : 'false' }}, {{ $category->sort_order }})" class="text-blue-400 hover:text-blue-300 p-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCategory({{ $category->id }})" class="text-red-400 hover:text-red-300 p-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    @if($category->children && count($category->children) > 0)
                        @foreach($category->children as $child)
                        <div class="ml-8 flex items-center justify-between p-4 bg-dark-bg/50 rounded-lg hover:bg-gray-800/50 transition-all">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-teal-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-folder-open text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-white font-medium">{{ $child->name }}</h3>
                                    <p class="text-gray-400 text-sm">{{ $child->products_count ?? 0 }} products</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $child->is_active ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                                    {{ $child->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <button onclick="editCategory({{ $child->id }}, '{{ $child->name }}', '{{ $child->description }}', {{ $child->is_active ? 'true' : 'false' }}, {{ $child->sort_order }})" class="text-blue-400 hover:text-blue-300 p-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCategory({{ $child->id }})" class="text-red-400 hover:text-red-300 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-dark-card rounded-xl border border-dark-border mb-6">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Category Statistics</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Categories</span>
                    <span class="text-white font-semibold">{{ $totalCategories ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Active Categories</span>
                    <span class="text-green-400 font-semibold">{{ $activeCategories ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Products with Category</span>
                    <span class="text-white font-semibold">{{ $productsWithCategory ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Last Sync</span>
                    <span class="text-gray-300 text-sm">{{ $lastSync ?? 'Never' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Top Categories</h2>
            </div>
            <div class="p-6 space-y-3">
                @foreach($topCategories ?? [] as $category)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-primary-blue/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-folder text-primary-blue text-sm"></i>
                        </div>
                        <span class="text-gray-300">{{ $category->name }}</span>
                    </div>
                    <span class="text-white font-semibold">{{ $category->products_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div id="category-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeCategoryModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white" id="modal-title">Add Category</h2>
            </div>
            <form id="category-form" class="p-6">
                @csrf
                <input type="hidden" id="category-id" name="category_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Category Name *</label>
                        <input type="text" id="category-name" name="name" required class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Parent Category</label>
                        <select id="parent-category" name="parent_id" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="">None (Top Level)</option>
                            @foreach($categories ?? [] as $category)
                                @if(!$category->parent_id)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Description</label>
                        <textarea id="category-description" name="description" rows="3" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Sort Order</label>
                        <input type="number" id="category-sort" name="sort_order" value="0" min="0" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="category-active" name="is_active" value="1" checked class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                            <span class="text-gray-300">Active</span>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                            Save Category
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
function openCategoryModal() {
    document.getElementById('modal-title').textContent = 'Add Category';
    document.getElementById('category-form').reset();
    document.getElementById('category-id').value = '';
    document.getElementById('category-modal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
}

function editCategory(id, name, description, isActive, sortOrder) {
    document.getElementById('modal-title').textContent = 'Edit Category';
    document.getElementById('category-id').value = id;
    document.getElementById('category-name').value = name;
    document.getElementById('category-description').value = description || '';
    document.getElementById('category-active').checked = isActive;
    document.getElementById('category-sort').value = sortOrder;
    document.getElementById('category-modal').classList.remove('hidden');
}

document.getElementById('category-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const categoryId = document.getElementById('category-id').value;

    const url = categoryId
        ? `/admin/categories/${categoryId}`
        : '{{ route("admin.categories.store") }}';

    if (categoryId) {
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

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category? All products in this category will need to be reassigned.')) {
        fetch(`/admin/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete category');
            }
        });
    }
}

function syncCategories() {
    if (confirm('This will sync all categories from the API. Continue?')) {
        const button = event.target;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';

        fetch('{{ route("admin.api-sync.categories") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-sync mr-2"></i>Sync from API';
                alert(data.message || 'Sync failed');
            }
        });
    }
}
</script>
@endpush

