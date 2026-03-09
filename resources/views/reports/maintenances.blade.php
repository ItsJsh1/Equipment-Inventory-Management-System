@extends('layouts.app')

@section('title', 'Maintenance Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Maintenance Report</h2>
            <p class="text-gray-500 dark:text-gray-400">Equipment maintenance records and schedules</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Reports
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Records</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Cost</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['total_cost'], 2) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Scheduled</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['by_status']['scheduled'] ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $summary['by_status']['completed'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('reports.maintenances') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="type" class="input-field">
                    <option value="">All Types</option>
                    <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                    <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                    <option value="inspection" {{ request('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="{{ request('date_from') ? 'date' : 'text' }}" name="date_from" value="{{ request('date_from') }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="{{ request('date_to') ? 'date' : 'text' }}" name="date_to" value="{{ request('date_to') }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                <a href="{{ route('reports.maintenances') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Scheduled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Completed</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($maintenances as $maintenance)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $maintenance->maintenance_code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $maintenance->equipment?->model_name ?? 'Deleted Equipment' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                {{ ucfirst($maintenance->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $maintenance->scheduled_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $maintenance->completion_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right">₱{{ number_format($maintenance->cost ?? 0, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($maintenance->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($maintenance->status == 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No maintenance records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="flex justify-end gap-2">
        <a href="{{ route('reports.maintenances', array_merge(request()->all(), ['export' => true, 'format' => 'xlsx'])) }}" class="btn-secondary">Export Excel</a>
        <a href="{{ route('reports.maintenances', array_merge(request()->all(), ['export' => true, 'format' => 'pdf'])) }}" class="btn-primary">Export PDF</a>
    </div>
</div>
@endsection
