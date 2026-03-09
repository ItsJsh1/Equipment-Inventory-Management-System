@extends('layouts.app')

@section('title', 'Borrowing Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Borrowing Details</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $borrowing->borrowing_code }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($borrowing->status === 'borrowed')
            @can('manage borrowings')
            <button type="button" class="btn-primary bg-green-600 hover:bg-green-700 flex items-center gap-2"
                x-data
                @click="$dispatch('open-return-modal', { id: {{ $borrowing->id }}, code: '{{ $borrowing->borrowing_code }}', equipment: '{{ $borrowing->equipment->model_name }}' })">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Return Equipment
            </button>
            @endcan
            @endif
            <a href="{{ route('borrowings.index') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>
    
    <!-- Status Banner -->
    <div class="card p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    @if($borrowing->status === 'borrowed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($borrowing->status === 'returned') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ ucfirst($borrowing->status) }}
                </span>
                @if($borrowing->status === 'borrowed' && $borrowing->due_date && $borrowing->due_date->isPast())
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    Overdue by {{ $borrowing->due_date->diffInDays(now()) }} days
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Borrowed {{ $borrowing->borrow_date?->format('M d, Y') ?? '-' }}
            </p>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Equipment Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Equipment Code</p>
                    <a href="{{ route('equipment.show', $borrowing->equipment) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ $borrowing->equipment->equipment_code }}
                    </a>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Model / Brand</p>
                    <p class="text-gray-900 dark:text-white">{{ $borrowing->equipment->model_name }} / {{ $borrowing->equipment->brand->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Serial Number</p>
                    <p class="text-gray-900 dark:text-white">{{ $borrowing->equipment->serial_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current Status</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                        @if($borrowing->equipment->status === 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($borrowing->equipment->status === 'borrowed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $borrowing->equipment->status)) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Borrower Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Borrower Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Borrower Name</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $borrowing->borrower_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Department</p>
                    <p class="text-gray-900 dark:text-white">{{ $borrowing->borrower_department }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Contact Number</p>
                    <p class="text-gray-900 dark:text-white">{{ $borrowing->borrower_contact ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Processed By</p>
                    <p class="text-gray-900 dark:text-white">{{ $borrowing->processedBy->name ?? 'System' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Timeline -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Timeline</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Borrow Date</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $borrowing->borrow_date?->format('M d, Y') ?? '-' }}</p>
            </div>
            <div class="text-center p-4 rounded-lg {{ $borrowing->status === 'borrowed' && $borrowing->due_date && $borrowing->due_date->isPast() ? 'bg-red-50 dark:bg-red-900/30' : 'bg-gray-50 dark:bg-gray-700' }}">
                <p class="text-xs {{ $borrowing->status === 'borrowed' && $borrowing->due_date && $borrowing->due_date->isPast() ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }} mb-1">Due Date</p>
                <p class="text-lg font-semibold {{ $borrowing->status === 'borrowed' && $borrowing->due_date && $borrowing->due_date->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $borrowing->due_date?->format('M d, Y') ?? '-' }}
                </p>
            </div>
            <div class="text-center p-4 rounded-lg {{ $borrowing->status === 'returned' ? 'bg-green-50 dark:bg-green-900/30' : 'bg-gray-50 dark:bg-gray-700' }}">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Return Date</p>
                <p class="text-lg font-semibold {{ $borrowing->status === 'returned' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $borrowing->return_date ? $borrowing->return_date->format('M d, Y') : 'Pending' }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Purpose -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purpose</h3>
        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $borrowing->purpose }}</p>
    </div>
    
    <!-- Notes -->
    @if($borrowing->notes)
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $borrowing->notes }}</p>
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition Notes</label>
                    <textarea name="notes" rows="2" class="input-field" placeholder="Note any damage or issues..."></textarea>
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
