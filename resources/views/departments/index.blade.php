@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Departments</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage organizational departments</p>
        </div>
        @can('manage_departments')
        <button type="button" x-data @click="$dispatch('open-create-modal')" class="btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Department
        </button>
        @endcan
    </div>
    
    <!-- Search -->
    <div class="card p-4">
        <form method="GET" action="{{ route('departments.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search departments..." class="input-field">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Search</button>
                <a href="{{ route('departments.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Users</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($departments as $department)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-white">{{ $department->code }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $department->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $department->users_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('manage_departments')
                                <button type="button" x-data @click="$dispatch('open-edit-modal', { id: {{ $department->id }}, code: '{{ $department->code }}', name: '{{ addslashes($department->name) }}' })" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Edit">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endcan
                                @can('manage_departments')
                                <form method="POST" action="{{ route('departments.destroy', $department) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Delete">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No departments found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $departments->links() }}</div>@endif
    </div>
</div>

<!-- Create Modal -->
<div x-data="{ open: false }" @open-create-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="open" x-transition class="fixed inset-0 bg-black/50" @click="open = false"></div>
        <div x-show="open" x-transition class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Department</h3>
            <form method="POST" action="{{ route('departments.store') }}">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code *</label><input type="text" name="code" class="input-field" maxlength="10" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label><input type="text" name="name" class="input-field" required></div>
                </div>
                <div class="flex justify-end gap-3 mt-6"><button type="button" @click="open = false" class="btn-secondary">Cancel</button><button type="submit" class="btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div x-data="{ open: false, item: { id: null, code: '', name: '' } }" @open-edit-modal.window="open = true; item = $event.detail" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="open" x-transition class="fixed inset-0 bg-black/50" @click="open = false"></div>
        <div x-show="open" x-transition class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Department</h3>
            <form :action="'/departments/' + item.id" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code *</label><input type="text" name="code" x-model="item.code" class="input-field" maxlength="10" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label><input type="text" name="name" x-model="item.name" class="input-field" required></div>
                </div>
                <div class="flex justify-end gap-3 mt-6"><button type="button" @click="open = false" class="btn-secondary">Cancel</button><button type="submit" class="btn-primary">Update</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
