@extends('admin.layout')

@section('title', 'Users Management')
@section('page-title', 'Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Users Management</h1>
        <p class="text-gray-400">Manage customer accounts and permissions</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="exportUsers()" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all">
            <i class="fas fa-download mr-2"></i>Export
        </button>
        <button onclick="openUserModal()" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
            <i class="fas fa-plus mr-2"></i>Add User
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Users</span>
            <i class="fas fa-users text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalUsers ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">All registered users</p>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Active Users</span>
            <i class="fas fa-user-check text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $activeUsers ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">Last 30 days</p>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">New Users</span>
            <i class="fas fa-user-plus text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $newUsers ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">This month</p>
    </div>

    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Admins</span>
            <i class="fas fa-user-shield text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $adminUsers ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">System administrators</p>
    </div>
</div>

<div class="bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" id="search" placeholder="Search by name, email..." class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
            </div>
            <div>
                <select id="role-filter" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div>
                <select id="status-filter" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="verified">Verified</option>
                    <option value="unverified">Unverified</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-400 text-sm border-b border-dark-border">
                    <th class="px-6 pb-3">User</th>
                    <th class="px-6 pb-3">Email</th>
                    <th class="px-6 pb-3">Role</th>
                    <th class="px-6 pb-3">Orders</th>
                    <th class="px-6 pb-3">Total Spent</th>
                    <th class="px-6 pb-3">Status</th>
                    <th class="px-6 pb-3">Joined</th>
                    <th class="px-6 pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-300">
                @foreach($users ?? [] as $user)
                <tr class="border-b border-dark-border/50 hover:bg-dark-bg/50 transition-all">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $user->name }}</p>
                                <p class="text-gray-500 text-xs">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-gray-300">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                            <span class="text-green-400 text-xs"><i class="fas fa-check-circle mr-1"></i>Verified</span>
                            @else
                            <span class="text-yellow-400 text-xs"><i class="fas fa-exclamation-circle mr-1"></i>Unverified</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->is_admin ? 'bg-purple-900/50 text-purple-400' : 'bg-blue-900/50 text-blue-400' }}">
                            {{ $user->is_admin ? 'Admin' : 'Customer' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-yellow-400">{{ $user->orders_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-white">${{ number_format($user->total_spent ?? 0, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm">{{ $user->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button onclick="viewUser({{ $user->id }})" class="text-blue-400 hover:text-blue-300 p-2">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editUser({{ $user->id }})" class="text-green-400 hover:text-green-300 p-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($user->id !== Auth::id())
                            <button onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})" class="text-yellow-400 hover:text-yellow-300 p-2">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                            </button>
                            <button onclick="deleteUser({{ $user->id }})" class="text-red-400 hover:text-red-300 p-2">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(isset($users) && $users->hasPages())
    <div class="p-6 border-t border-dark-border">
        {{ $users->links() }}
    </div>
    @endif
</div>

<div id="user-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeUserModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white" id="modal-title">Add User</h2>
            </div>
            <form id="user-form" class="p-6">
                @csrf
                <input type="hidden" id="user-id" name="user_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Name *</label>
                        <input type="text" id="user-name" name="name" required class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Email *</label>
                        <input type="email" id="user-email" name="email" required class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    </div>

                    <div id="password-field">
                        <label class="block text-gray-400 text-sm mb-2">Password *</label>
                        <input type="password" name="password" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                        <p class="text-gray-500 text-xs mt-1">Leave empty to keep current password</p>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Role</label>
                        <select id="user-role" name="role" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="user-active" name="is_active" value="1" checked class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                            <span class="text-gray-300">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="user-verified" name="email_verified" value="1" class="w-4 h-4 text-primary-blue bg-dark-bg border-dark-border rounded focus:ring-primary-blue mr-2">
                            <span class="text-gray-300">Email Verified</span>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeUserModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                            Save User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="user-details-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeDetailsModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white">User Details</h2>
            </div>
            <div class="p-6">
                <div id="user-details-content">
                    <!-- User details will be loaded here -->
                </div>
                <div class="pt-4 flex justify-end">
                    <button onclick="closeDetailsModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUserModal() {
    document.getElementById('modal-title').textContent = 'Add User';
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('password-field').querySelector('input').required = true;
    document.getElementById('user-modal').classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('user-modal').classList.add('hidden');
}

function editUser(id) {
    fetch(`/admin/users/${id}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('modal-title').textContent = 'Edit User';
            document.getElementById('user-id').value = user.id;
            document.getElementById('user-name').value = user.name;
            document.getElementById('user-email').value = user.email;
            document.getElementById('user-role').value = user.is_admin ? 'admin' : 'customer';
            document.getElementById('user-active').checked = user.is_active;
            document.getElementById('user-verified').checked = user.email_verified_at != null;
            document.getElementById('password-field').querySelector('input').required = false;
            document.getElementById('user-modal').classList.remove('hidden');
        });
}

function viewUser(id) {
    fetch(`/admin/users/${id}`)
        .then(response => response.json())
        .then(user => {
            const content = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Name</p>
                        <p class="text-white">${user.name}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Email</p>
                        <p class="text-white">${user.email}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Role</p>
                        <p class="text-white">${user.is_admin ? 'Admin' : 'Customer'}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Status</p>
                        <p class="text-white">${user.is_active ? 'Active' : 'Inactive'}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Total Orders</p>
                        <p class="text-white">${user.orders_count || 0}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Total Spent</p>
                        <p class="text-white">$${(user.total_spent || 0).toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Joined</p>
                        <p class="text-white">${new Date(user.created_at).toLocaleDateString()}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Email Verified</p>
                        <p class="text-white">${user.email_verified_at ? 'Yes' : 'No'}</p>
                    </div>
                </div>
            `;
            document.getElementById('user-details-content').innerHTML = content;
            document.getElementById('user-details-modal').classList.remove('hidden');
        });
}

function closeDetailsModal() {
    document.getElementById('user-details-modal').classList.add('hidden');
}

document.getElementById('user-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const userId = document.getElementById('user-id').value;

    const url = userId
        ? `/admin/users/${userId}`
        : '{{ route("admin.users.store") }}';

    if (userId) {
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

function toggleUserStatus(id, activate) {
    const action = activate ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        fetch(`/admin/users/${id}`, {
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
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`/admin/users/${id}`, {
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

function exportUsers() {
    const params = new URLSearchParams();
    const search = document.getElementById('search').value;
    const role = document.getElementById('role-filter').value;
    const status = document.getElementById('status-filter').value;

    if (search) params.append('search', search);
    if (role) params.append('role', role);
    if (status) params.append('status', status);

    window.location.href = `/admin/users/export?${params.toString()}`;
}

document.getElementById('search').addEventListener('input', debounce(filterUsers, 500));
document.getElementById('role-filter').addEventListener('change', filterUsers);
document.getElementById('status-filter').addEventListener('change', filterUsers);

function filterUsers() {
    const params = new URLSearchParams();
    const search = document.getElementById('search').value;
    const role = document.getElementById('role-filter').value;
    const status = document.getElementById('status-filter').value;

    if (search) params.append('search', search);
    if (role) params.append('role', role);
    if (status) params.append('status', status);

    window.location.href = '{{ route("admin.users.index") }}?' + params.toString();
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
