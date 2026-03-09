@extends('layouts.app')

@section('title', 'Disposal Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Disposal Details</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $disposal->disposal_code }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($disposal->status === 'pending_approval')
            @can('approve disposals')
            <form method="POST" action="{{ route('disposals.approve', $disposal) }}" class="inline">
                @csrf
                <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700" onclick="return confirm('Approve this disposal request?')">
                    Approve
                </button>
            </form>
            @endcan
            @endif
            @if($disposal->status === 'approved')
            @can('manage disposals')
            <form method="POST" action="{{ route('disposals.complete', $disposal) }}" class="inline">
                @csrf
                <button type="submit" class="btn-primary" onclick="return confirm('Complete this disposal?')">
                    Complete Disposal
                </button>
            </form>
            @endcan
            @endif
            <a href="{{ route('disposals.index') }}" class="btn-secondary flex items-center gap-2">
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
                    @if($disposal->status === 'pending_approval') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($disposal->status === 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($disposal->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $disposal->status)) }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                    {{ ucfirst(str_replace('_', ' ', $disposal->method)) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Requested {{ $disposal->created_at->format('M d, Y h:i A') }}
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
                    <a href="{{ route('equipment.show', $disposal->equipment) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ $disposal->equipment->equipment_code }}
                    </a>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Model / Brand</p>
                    <p class="text-gray-900 dark:text-white">{{ $disposal->equipment->model_name }} / {{ $disposal->equipment->brand->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Serial Number</p>
                    <p class="text-gray-900 dark:text-white">{{ $disposal->equipment->serial_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Acquisition Cost</p>
                    <p class="text-gray-900 dark:text-white">₱{{ number_format($disposal->equipment->acquisition_cost ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current Status</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                        @if($disposal->equipment->status === 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($disposal->equipment->status === 'disposed') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $disposal->equipment->status)) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Disposal Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Disposal Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Requested By</p>
                    <p class="text-gray-900 dark:text-white">{{ $disposal->requestedBy->name ?? 'System' }}</p>
                </div>
                @if($disposal->approved_by)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Approved By</p>
                    <p class="text-gray-900 dark:text-white">{{ $disposal->approvedBy->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Approved Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $disposal->approved_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($disposal->disposal_date)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Disposal Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $disposal->disposal_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($disposal->disposal_value)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Disposal Value</p>
                    <p class="text-gray-900 dark:text-white font-semibold">₱{{ number_format($disposal->disposal_value, 2) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Reason -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reason for Disposal</h3>
        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $disposal->reason }}</p>
    </div>
    
    <!-- Notes -->
    @if($disposal->notes)
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $disposal->notes }}</p>
    </div>
    @endif
</div>
@endsection
