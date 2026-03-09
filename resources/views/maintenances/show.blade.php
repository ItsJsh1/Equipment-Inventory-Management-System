@extends('layouts.app')

@section('title', 'Maintenance Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Maintenance Details</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $maintenance->maintenance_code }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($maintenance->status === 'scheduled')
            @can('manage maintenances')
            <form method="POST" action="{{ route('maintenances.start', $maintenance) }}" class="inline">
                @csrf
                <button type="submit" class="btn-primary" onclick="return confirm('Start this maintenance?')">
                    Start Maintenance
                </button>
            </form>
            @endcan
            @endif
            @if($maintenance->status === 'in_progress')
            @can('manage maintenances')
            <form method="POST" action="{{ route('maintenances.complete', $maintenance) }}" class="inline" x-data x-ref="completeForm">
                @csrf
                <input type="hidden" name="completed_date" value="{{ date('Y-m-d') }}">
                <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700" onclick="return confirm('Complete this maintenance?')">
                    Mark Complete
                </button>
            </form>
            @endcan
            @endif
            <a href="{{ route('maintenances.index') }}" class="btn-secondary flex items-center gap-2">
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
                    @if($maintenance->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($maintenance->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($maintenance->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                    {{ ucfirst($maintenance->type) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Created {{ $maintenance->created_at->format('M d, Y h:i A') }}
            </p>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Equipment Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Information</h3>
            @if($maintenance->equipment)
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Equipment Code</p>
                    <a href="{{ route('equipment.show', $maintenance->equipment) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ $maintenance->equipment->equipment_code }}
                    </a>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Model / Brand</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->equipment->model_name }} / {{ $maintenance->equipment->brand->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Serial Number</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->equipment->serial_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current Status</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                        @if($maintenance->equipment->status === 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($maintenance->equipment->status === 'under_maintenance') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $maintenance->equipment->status)) }}
                    </span>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Equipment has been deleted</p>
            </div>
            @endif
        </div>
        
        <!-- Maintenance Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Scheduled By</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->scheduledBy->name ?? 'System' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Scheduled Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->scheduled_date?->format('M d, Y') ?? '-' }}</p>
                </div>
                @if($maintenance->started_date)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Started Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->started_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($maintenance->completed_date)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Completed Date</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->completed_date->format('M d, Y') }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Technician</p>
                    <p class="text-gray-900 dark:text-white">{{ $maintenance->technician_name ?? 'Not assigned' }}</p>
                </div>
                @if($maintenance->cost)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cost</p>
                    <p class="text-gray-900 dark:text-white font-semibold">₱{{ number_format($maintenance->cost, 2) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Description -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $maintenance->description }}</p>
    </div>
    
    <!-- Notes -->
    @if($maintenance->notes)
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $maintenance->notes }}</p>
    </div>
    @endif
</div>
@endsection
