@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedIds: [],
    selectAll: false,
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedIds = {{ json_encode($equipments->pluck('id')->toArray()) }};
        } else {
            this.selectedIds = [];
        }
    },
    isSelected(id) {
        return this.selectedIds.includes(id);
    },
    toggleSelect(id) {
        if (this.isSelected(id)) {
            this.selectedIds = this.selectedIds.filter(i => i !== id);
        } else {
            this.selectedIds.push(id);
        }
        this.selectAll = this.selectedIds.length === {{ $equipments->count() }};
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage your equipment inventory</p>
        </div>
        <div class="flex items-center gap-3">
            @can('export_equipment')
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                    <a href="{{ route('equipment.export', ['format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Export as Excel</a>
                    <a href="{{ route('equipment.export', ['format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Export as PDF</a>
                </div>
            </div>
            @endcan
            
            @can('create_equipment')
            <a href="{{ route('equipment.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Equipment
            </a>
            @endcan
        </div>
    </div>

    <!-- Bulk Action Bar -->
    @can('create_disposals')
    <div x-show="selectedIds.length > 0" x-transition class="card p-4 bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                    <span x-text="selectedIds.length"></span> equipment selected
                </span>
                <button @click="selectedIds = []; selectAll = false" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    Clear selection
                </button>
            </div>
            <form method="GET" action="{{ route('disposals.bulk-create') }}" class="flex items-center gap-2">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="equipment_ids[]" :value="id">
                </template>
                <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Bulk Dispose
                </button>
            </form>
        </div>
    </div>
    @endcan
    
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('equipment.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search equipment..." class="input-field">
            </div>
            <div>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="for_disposal" {{ request('status') == 'for_disposal' ? 'selected' : '' }}>For Disposal</option>
                </select>
            </div>
            <div>
                <select name="category_id" class="input-field">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="brand_id" class="input-field">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                <a href="{{ route('equipment.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="card overflow-hidden">
        {{-- Desktop Table View --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        @can('create_disposals')
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                        </th>
                        @endcan
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Condition</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($equipments as $equipment)
                    <tr class="table-row" :class="{ 'bg-blue-50 dark:bg-blue-900/20': isSelected({{ $equipment->id }}) }">
                        @can('create_disposals')
                        <td class="px-6 py-4">
                            <input type="checkbox" :checked="isSelected({{ $equipment->id }})" @change="toggleSelect({{ $equipment->id }})" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                        </td>
                        @endcan
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $equipment->equipment_code }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipment->model_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $equipment->brand->name }} | {{ $equipment->serial_number }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->location?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->condition_badge }}">
                                {{ ucfirst($equipment->condition) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('equipment.show', $equipment) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="View">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @can('edit_equipment')
                                <a href="{{ route('equipment.edit', $equipment) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Edit">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('create_disposals')
                                <a href="{{ route('disposals.create', ['equipment_id' => $equipment->id]) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-xs font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" title="Dispose Equipment">
                                    Dispose
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <p>No equipment found</p>
                            @can('create_equipment')
                            <a href="{{ route('equipment.create') }}" class="inline-block mt-4 text-black dark:text-white hover:underline">Add your first equipment</a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @can('create_disposals')
            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-3">
                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Select All</span>
            </div>
            @endcan
            @forelse($equipments as $equipment)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50" :class="{ 'bg-blue-50 dark:bg-blue-900/20': isSelected({{ $equipment->id }}) }">
                <div class="flex items-start justify-between gap-3 mb-2">
                    @can('create_disposals')
                    <input type="checkbox" :checked="isSelected({{ $equipment->id }})" @change="toggleSelect({{ $equipment->id }})" class="mt-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                    @endcan
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $equipment->model_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $equipment->equipment_code }}</p>
                    </div>
                    <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full {{ $equipment->status_badge }}">
                        {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Brand:</span>
                        <span class="text-gray-900 dark:text-white ml-1">{{ $equipment->brand->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Category:</span>
                        <span class="text-gray-900 dark:text-white ml-1">{{ $equipment->category->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Serial:</span>
                        <span class="text-gray-900 dark:text-white ml-1">{{ $equipment->serial_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Condition:</span>
                        <span class="px-1.5 py-0.5 rounded-full {{ $equipment->condition_badge }}">{{ ucfirst($equipment->condition) }}</span>
                    </div>
                    @if($equipment->location)
                    <div class="col-span-2">
                        <span class="text-gray-500 dark:text-gray-400">Location:</span>
                        <span class="text-gray-900 dark:text-white ml-1">{{ $equipment->location->name }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('equipment.show', $equipment) }}" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        View
                    </a>
                    @can('edit_equipment')
                    <a href="{{ route('equipment.edit', $equipment) }}" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        Edit
                    </a>
                    @endcan
                    @can('create_disposals')
                    <a href="{{ route('disposals.create', ['equipment_id' => $equipment->id]) }}" class="text-xs font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                        Dispose
                    </a>
                    @endcan
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                <p>No equipment found</p>
                @can('create_equipment')
                <a href="{{ route('equipment.create') }}" class="inline-block mt-4 text-black dark:text-white hover:underline">Add your first equipment</a>
                @endcan
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($equipments->hasPages())
        <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $equipments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
