@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage system users and their roles</p>
        </div>
        @can('create_users')
        <a href="{{ route('users.create') }}" class="btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </a>
        @endcan
    </div>
    
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="input-field">
            </div>
            <div>
                <select name="role" class="input-field">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="card p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Users</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $activeUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="card p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Admins</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $adminCount ?? 0 }} / {{ $maxAdmins ?? 2 }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card overflow-hidden">
        {{-- Desktop Table View --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="table-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" 
                                     class="flex-shrink-0 w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Joined {{ $user->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($role->name == 'super_admin') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($role->name == 'admin') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $user->department?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('users.show', $user) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="View">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                @can('edit_users')
                                @if(!$user->hasRole('super_admin') || auth()->user()->hasRole('super_admin'))
                                <a href="{{ route('users.edit', $user) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Edit">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan
                                
                                @can('edit_users')
                                @if($user->id != auth()->id() && !$user->hasRole('super_admin'))
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if($user->is_active)
                                        <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        @else
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @endif
                                    </button>
                                </form>
                                @endif
                                @endcan
                                
                                @can('delete_users')
                                @if($user->id != auth()->id() && !$user->hasRole('super_admin'))
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Delete">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p>No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($users as $user)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="flex items-start gap-3">
                    <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" 
                         class="flex-shrink-0 w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                            </div>
                            @if($user->is_active)
                            <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                            @else
                            <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                            @endif
                        </div>
                        
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            @foreach($user->roles as $role)
                            <span class="px-2 py-0.5 rounded-full 
                                @if($role->name == 'super_admin') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($role->name == 'admin') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            </span>
                            @endforeach
                            @if($user->department)
                            <span class="text-gray-500 dark:text-gray-400">• {{ $user->department->name }}</span>
                            @endif
                        </div>
                        
                        <div class="mt-3 flex items-center gap-3">
                            <a href="{{ route('users.show', $user) }}" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                View
                            </a>
                            @can('edit_users')
                            @if(!$user->hasRole('super_admin') || auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('users.edit', $user) }}" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Edit
                            </a>
                            @endif
                            @endcan
                            @can('edit_users')
                            @if($user->id != auth()->id() && !$user->hasRole('super_admin'))
                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-medium {{ $user->is_active ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p>No users found</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
