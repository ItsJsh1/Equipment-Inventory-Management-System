@extends('layouts.app')

@section('title', 'Disposals')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Disposals</h2>
            <p class="text-gray-500 dark:text-gray-400">Track equipment disposal records</p>
        </div>
        <div class="flex items-center gap-3">
            @can('export_disposals')
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                    <a href="{{ route('disposals.export', ['format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Export as Excel</a>
                    <a href="{{ route('disposals.export', ['format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Export as PDF</a>
                </div>
            </div>
            @endcan
            
            @can('create_disposals')
            <a href="{{ route('disposals.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Disposal
            </a>
            @endcan
        </div>
    </div>
    
    <!-- Equipment Status Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $equipmentForDisposalCount ?? 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Equipment For Disposal</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $equipmentDisposedCount ?? 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Equipment Disposed</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingDisposalsCount ?? 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending Disposal Requests</p>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('disposals.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="input-field">
            </div>
            <div>
                <select name="method" class="input-field">
                    <option value="">All Methods</option>
                    <option value="sold" {{ request('method') == 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="donated" {{ request('method') == 'donated' ? 'selected' : '' }}>Donated</option>
                    <option value="recycled" {{ request('method') == 'recycled' ? 'selected' : '' }}>Recycled</option>
                    <option value="scrapped" {{ request('method') == 'scrapped' ? 'selected' : '' }}>Scrapped</option>
                    <option value="returned" {{ request('method') == 'returned' ? 'selected' : '' }}>Returned</option>
                </select>
            </div>
            <div>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <input type="{{ request('date_from') ? 'date' : 'text' }}" name="date_from" value="{{ request('date_from') }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                <a href="{{ route('disposals.index') }}" class="btn-secondary">Reset</a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Disposal Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($disposals as $disposal)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $disposal->disposal_code }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $disposal->equipment->model_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $disposal->equipment->equipment_code }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $disposal->method_badge }}">
                                {{ ucfirst($disposal->method) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $disposal->disposal_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $disposal->status_badge }}">
                                {{ ucfirst($disposal->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('disposals.show', $disposal) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="View">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                @if($disposal->status == 'pending_approval')
                                    @can('approve_disposal')
                                    <form method="POST" action="{{ route('disposals.approve', $disposal) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Approve">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                                
                                @if($disposal->status == 'approved')
                                    @can('edit_disposals')
                                    <form method="POST" action="{{ route('disposals.complete', $disposal) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Complete">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                                
                                @if($disposal->status == 'completed' && $disposal->equipment)
                                    @can('delete_disposals')
                                    <form method="POST" action="{{ route('disposals.delete-equipment', $disposal) }}" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this equipment? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 hover:bg-red-100 dark:hover:bg-red-900/30 rounded text-xs font-medium text-red-600 dark:text-red-400" title="Delete Equipment Permanently">
                                            Delete Equipment
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                                
                                @can('edit_disposals')
                                @if($disposal->status == 'pending_approval')
                                <a href="{{ route('disposals.edit', $disposal) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Edit">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan
                                
                                @can('delete_disposals')
                                @if($disposal->status == 'pending_approval')
                                <form method="POST" action="{{ route('disposals.destroy', $disposal) }}" class="inline" onsubmit="return confirm('Are you sure?')">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <p>No disposal records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($disposals->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $disposals->links() }}
        </div>
        @endif
    </div>
    
    <!-- Equipment with Disposal Status -->
    @if(isset($disposalEquipment) && $disposalEquipment->count() > 0)
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Equipment For Disposal / Disposed</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Equipment marked for disposal or already disposed in the system</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($disposalEquipment as $equipment)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $equipment->equipment_code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $equipment->model_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->brand->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->location->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('equipment.show', $equipment) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
