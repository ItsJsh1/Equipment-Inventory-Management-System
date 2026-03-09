@extends('layouts.app')

@section('title', isset($overdue) ? 'Overdue Borrowings' : 'Borrowings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($overdue) ? 'Overdue Borrowings' : 'Borrowings' }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400">
                {{ isset($overdue) ? 'Equipment that has not been returned on time' : 'Track borrowed equipment' }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            @can('manage borrowings')
            <a href="{{ route('borrowings.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Lend Equipment
            </a>
            @endcan
        </div>
    </div>
    
    <!-- Quick Filter Tabs -->
    <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('borrowings.index') }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ !isset($overdue) ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            All Borrowings
        </a>
        <a href="{{ route('borrowings.overdue') }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ isset($overdue) ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            Overdue
        </a>
    </div>
    
    <!-- Equipment Status Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $borrowedEquipmentCount ?? 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Equipment Currently Borrowed</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activeBorrowingsCount ?? 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active Borrowing Records</p>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ request()->url() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search borrower or equipment..." class="input-field">
            </div>
            <div>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div>
                <input type="{{ request('date_from') ? 'date' : 'text' }}" name="date_from" value="{{ request('date_from') }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                <a href="{{ request()->url() }}" class="btn-secondary">Reset</a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Borrower</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Borrowed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($borrowings as $borrowing)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $borrowing->borrowing_code }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrowing->equipment->model_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $borrowing->equipment->equipment_code ?? '' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $borrowing->borrower_firstname }} {{ $borrowing->borrower_lastname }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $borrowing->department->name ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->borrow_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $isOverdue = $borrowing->status === 'borrowed' && $borrowing->expected_return_date->isPast();
                            @endphp
                            <span class="text-sm {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $borrowing->expected_return_date->format('M d, Y') }}
                                @if($isOverdue)
                                <span class="block text-xs">{{ $borrowing->expected_return_date->diffForHumans() }}</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($borrowing->status === 'returned')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Returned
                            </span>
                            @elseif($isOverdue)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                Overdue
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                Borrowed
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('borrowings.show', $borrowing) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="View">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                @if($borrowing->status === 'borrowed')
                                @can('manage borrowings')
                                <button type="button"
                                        x-data
                                        @click="$dispatch('open-return-modal', { id: {{ $borrowing->id }}, code: '{{ $borrowing->borrowing_code }}', equipment: '{{ $borrowing->equipment->model_name ?? 'Equipment' }}' })"
                                        class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                                        title="Process Return">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <p>No borrowing records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($borrowings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $borrowings->links() }}
        </div>
        @endif
    </div>
    
    <!-- Equipment with Borrowed Status -->
    @if(isset($borrowedEquipment) && $borrowedEquipment->count() > 0)
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Equipment with Borrowed Status</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Equipment currently marked as borrowed in the system</p>
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
                    @foreach($borrowedEquipment as $equipment)
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

<!-- Return Modal -->
<div x-data="{ open: false, borrowingId: null, code: '', equipment: '' }"
     @open-return-modal.window="open = true; borrowingId = $event.detail.id; code = $event.detail.code; equipment = $event.detail.equipment"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Return Equipment</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Returning <span class="font-medium" x-text="equipment"></span> (<span x-text="code"></span>)
            </p>
            <form method="POST" :action="'/borrowings/' + borrowingId + '/return'">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Return Date</label>
                    <input type="text" name="return_date" value="" class="input-field" placeholder="MM/DD/YYYY" required onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition on Return</label>
                    <select name="condition_on_return" class="input-field">
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea name="remarks" rows="2" class="input-field" placeholder="Any notes about the returned equipment..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
