@extends('layouts.app')

@section('title', isset($type) ? ucfirst($type) . ' Transactions' : 'Transactions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                @if(isset($type))
                    {{ ucfirst($type) }} Transactions
                @else
                    All Transactions
                @endif
            </h2>
            <p class="text-gray-500 dark:text-gray-400">
                @if(isset($type) && $type == 'incoming')
                    Equipment received into inventory
                @elseif(isset($type) && $type == 'outgoing')
                    Equipment released from inventory
                @else
                    Manage incoming and outgoing equipment transactions
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @can('export_transactions')
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                    <a href="{{ route('transactions.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Export as Excel</a>
                    <a href="{{ route('transactions.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Export as PDF</a>
                </div>
            </div>
            @endcan
            
            @can('create_transactions')
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Transaction
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                    <a href="{{ route('transactions.create', ['type' => 'incoming']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            Incoming
                        </span>
                    </a>
                    <a href="{{ route('transactions.create', ['type' => 'outgoing']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            Outgoing
                        </span>
                    </a>
                </div>
            </div>
            @endcan
        </div>
    </div>
    
    <!-- Quick Filter Tabs -->
    <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('transactions.index') }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ !isset($type) ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            All
        </a>
        <a href="{{ route('transactions.incoming') }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ isset($type) && $type == 'incoming' ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            Incoming
        </a>
        <a href="{{ route('transactions.outgoing') }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ isset($type) && $type == 'outgoing' ? 'border-black dark:border-white text-black dark:text-white' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            Outgoing
        </a>
    </div>
    
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ request()->url() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="input-field">
            </div>
            <div>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <input type="{{ request('date_from') ? 'date' : 'text' }}" name="date_from" value="{{ request('date_from') }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
            </div>
            <div>
                <input type="{{ request('date_to') ? 'date' : 'text' }}" name="date_to" value="{{ request('date_to') }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $transaction)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->transaction_code }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type_badge }}">
                                @if($transaction->type == 'incoming')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                                @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                </svg>
                                @endif
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->equipment?->model_name ?? 'Deleted Equipment' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->equipment?->equipment_code ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $transaction->person_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->person_department ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_badge }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('transactions.show', $transaction) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="View">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                @if($transaction->status == 'pending')
                                    @can('approve_transaction')
                                    <form method="POST" action="{{ route('transactions.approve', $transaction) }}" class="inline">
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
                                
                                @if($transaction->status == 'approved')
                                    @can('edit_transactions')
                                    <form method="POST" action="{{ route('transactions.complete', $transaction) }}" class="inline">
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
                                
                                @can('edit_transactions')
                                @if($transaction->status == 'pending')
                                <a href="{{ route('transactions.edit', $transaction) }}" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Edit">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan
                                
                                @can('delete_transactions')
                                @if($transaction->status == 'pending')
                                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this transaction?')">
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
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <p>No transactions found</p>
                            @can('create_transactions')
                            <a href="{{ route('transactions.create') }}" class="inline-block mt-4 text-black dark:text-white hover:underline">Create your first transaction</a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
