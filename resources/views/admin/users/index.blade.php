@extends('admin.layout')

@section('title', 'Users Management')
@section('page-title', 'Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Users Management</h1>
        <p class="text-gray-400">Manage registered users and their permissions</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="exportUsers()" class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all">
            <i class="fas fa-download mr-2"></i>Export
        </button>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
            <i class="fas fa-plus mr-2"></i>Add User
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Users</span>
            <i class="fas fa-users text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalUsers ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Verified</span>
            <i class="fas fa-check-circle text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $verifiedUsers ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Admins</span>
            <i class="fas fa-user-shield text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $adminUsers ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Active (30d)</span>
            <i class="fas fa-chart-line text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $activeUsers ?? 0 }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-dark-card rounded-xl border border-dark-border p-4 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
        </div>

        <div>
            <select name="role" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                <option value="all">All Roles</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div>
            <select name="verified" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                <option value="all">All Status</option>
                <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Verified</option>
                <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Unverified</option>
            </select>
        </div>

        <div>
            <button type="submit" class="w-full px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <h2 class="text-lg font-semibold text-white">All Users</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark-bg border-b border-dark-border">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Reviews</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse($users ?? [] as $user)
                <tr class="hover:bg-dark-bg transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-blue to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-white font-medium">{{ $user->name }}</p>
                                <p class="text-gray-400 text-sm">ID: #{{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-gray-300">{{ $user->email }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-900/50 text-purple-400' : 'bg-blue-900/50 text-blue-400' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-white">{{ $user->orders_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-white">{{ $user->reviews_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-900/50 text-green-400">
                                Verified
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900/50 text-red-400">
                                Unverified
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-400 hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-green-400 hover:text-green-300">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <button onclick="deleteUser({{ $user->id }})" class="text-red-400 hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($users) && $users->hasPages())
    <div class="p-4 border-t border-dark-border">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Recent Users Widget -->
<div class="mt-6 bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <h2 class="text-lg font-semibold text-white">Recent Users</h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($recentUsers ?? [] as $user)
            <div class="flex items-center justify-between p-4 bg-dark-bg rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-blue to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $user->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full {{ $user->email_verified_at ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">
                        {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                    </span>
                    <p class="text-gray-400 text-xs mt-1">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function exportUsers() {
    window.location.href = '{{ route("admin.reports.export") }}?type=users';
}
</script>
@endpush
