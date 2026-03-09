@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $transaction->transaction_code }}</h2>
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
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_badge }}">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>
            <p class="text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('F d, Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            
            @if($transaction->status == 'pending')
                @can('approve_transaction')
                <form method="POST" action="{{ route('transactions.approve', $transaction) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve
                    </button>
                </form>
                @endcan
                
                @can('edit_transactions')
                <a href="{{ route('transactions.edit', $transaction) }}" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                @endcan
            @endif
            
            @if($transaction->status == 'approved')
                @can('edit_transactions')
                <form method="POST" action="{{ route('transactions.complete', $transaction) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Complete
                    </button>
                </form>
                @endcan
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Equipment Information -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Information</h3>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $transaction->equipment->model_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->equipment->equipment_code }}</p>
                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ $transaction->equipment->brand->name }}</span>
                            <span>•</span>
                            <span>{{ $transaction->equipment->category->name }}</span>
                        </div>
                        <a href="{{ route('equipment.show', $transaction->equipment) }}" class="inline-block mt-2 text-sm text-black dark:text-white hover:underline">View Equipment →</a>
                    </div>
                </div>
            </div>
            
            <!-- Person Information -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $transaction->type == 'incoming' ? 'Source / Supplier' : 'Recipient' }} Information
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->person_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department/Organization</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->person_department ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->person_contact ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->person_email ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            
            <!-- Purpose & Notes -->
            @if($transaction->purpose || $transaction->notes)
            <div class="card p-6">
                @if($transaction->purpose)
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Purpose</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $transaction->purpose }}</p>
                </div>
                @endif
                
                @if($transaction->notes)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notes</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Transaction Details -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Transaction Code</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $transaction->transaction_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Type</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type_badge }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_badge }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Transaction Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->transaction_date->format('M d, Y') }}</dd>
                    </div>
                    @if($transaction->reference_number)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Reference Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->reference_number }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            
            <!-- Record Information -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Record Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Processed By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->processedBy?->name ?? '-' }}</dd>
                    </div>
                    @if($transaction->approved_by)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Approved By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->approvedBy?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Approved At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->approved_at?->format('M d, Y H:i') ?? '-' }}</dd>
                    </div>
                    @endif
                    @if($transaction->completed_at)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Completed At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->completed_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
