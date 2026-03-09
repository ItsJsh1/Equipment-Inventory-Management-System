@extends('layouts.app')

@section('title', 'Equipment Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $equipment->model_name }}</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $equipment->equipment_code }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('equipment.index') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            @can('edit_equipment')
            <a href="{{ route('equipment.edit', $equipment) }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endcan
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipment Code</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->equipment_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Model Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->model_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Serial Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->serial_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Brand</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->brand->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->location?->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            
            <!-- Status & Condition -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status & Condition</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Condition</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->condition_badge }}">
                                {{ ucfirst($equipment->condition) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Acquisition Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->acquisition_date?->format('M d, Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Acquisition Cost</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->acquisition_cost ? '₱' . number_format($equipment->acquisition_cost, 2) : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Warranty Expiry</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->warranty_expiry?->format('M d, Y') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            
            <!-- Specifications -->
            @if($equipment->specifications)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Specifications</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $equipment->specifications }}</p>
            </div>
            @endif
            
            <!-- Notes -->
            @if($equipment->notes)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $equipment->notes }}</p>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if($equipment->status == 'available')
                        @can('create_borrowings')
                        <a href="{{ route('borrowings.create', ['equipment_id' => $equipment->id]) }}" class="w-full btn-secondary flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Lend Equipment
                        </a>
                        @endcan
                        @can('create_maintenance')
                        <a href="{{ route('maintenances.create', ['equipment_id' => $equipment->id]) }}" class="w-full btn-secondary flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Schedule Maintenance
                        </a>
                        @endcan
                    @endif
                    @if(in_array($equipment->status, ['available', 'in_use']))
                        @can('create_disposals')
                        <a href="{{ route('disposals.create', ['equipment_id' => $equipment->id]) }}" class="w-full btn-secondary flex items-center justify-center gap-2 text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Mark for Disposal
                        </a>
                        @endcan
                    @endif
                </div>
            </div>
            
            <!-- Metadata -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Record Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
    
    <!-- History Tabs -->
    <div class="card overflow-hidden" x-data="{ activeTab: 'transactions' }">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'transactions'" 
                        :class="activeTab === 'transactions' ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Transactions
                </button>
                <button @click="activeTab = 'borrowings'" 
                        :class="activeTab === 'borrowings' ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Borrowings
                </button>
                <button @click="activeTab = 'maintenances'" 
                        :class="activeTab === 'maintenances' ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Maintenances
                </button>
            </nav>
        </div>
        
        <!-- Transactions Tab -->
        <div x-show="activeTab === 'transactions'" class="p-6">
            @if($equipment->transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($equipment->transactions->take(5) as $transaction)
                        <tr class="table-row">
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('transactions.show', $transaction) }}" class="text-black dark:text-white hover:underline">{{ $transaction->transaction_code }}</a>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type_badge }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_badge }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No transactions found</p>
            @endif
        </div>
        
        <!-- Borrowings Tab -->
        <div x-show="activeTab === 'borrowings'" style="display: none;" class="p-6">
            @if($equipment->borrowings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Borrower</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Borrowed</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Returned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($equipment->borrowings->take(5) as $borrowing)
                        <tr class="table-row">
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('borrowings.show', $borrowing) }}" class="text-black dark:text-white hover:underline">{{ $borrowing->borrowing_code }}</a>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $borrowing->borrower_name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->borrowed_at?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->returned_at?->format('M d, Y') ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No borrowings found</p>
            @endif
        </div>
        
        <!-- Maintenances Tab -->
        <div x-show="activeTab === 'maintenances'" style="display: none;" class="p-6">
            @if($equipment->maintenances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Scheduled</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($equipment->maintenances->take(5) as $maintenance)
                        <tr class="table-row">
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('maintenances.show', $maintenance) }}" class="text-black dark:text-white hover:underline">{{ $maintenance->maintenance_code }}</a>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ ucfirst($maintenance->type) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $maintenance->scheduled_date->format('M d, Y') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $maintenance->status_badge }}">
                                    {{ ucfirst($maintenance->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No maintenance records found</p>
            @endif
        </div>
    </div>
</div>
@endsection
