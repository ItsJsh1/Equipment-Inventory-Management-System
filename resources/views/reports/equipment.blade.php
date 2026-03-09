@extends('layouts.app')

@section('title', 'Equipment Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment Report</h2>
            <p class="text-gray-500 dark:text-gray-400">Complete inventory listing with status and details</p>
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
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Equipment</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Value</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['total_value'], 2) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Available</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $summary['by_status']['available'] ?? 0 }}</p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Under Maintenance</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $summary['by_status']['maintenance'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('reports.equipment') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
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
                <a href="{{ route('reports.equipment') }}" class="btn-secondary">Reset</a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Condition</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($equipments as $equipment)
                    <tr class="table-row">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $equipment->equipment_code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $equipment->model_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->brand->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $equipment->category->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->status_badge ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->condition_badge ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($equipment->condition) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right">₱{{ number_format($equipment->acquisition_cost, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No equipment found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="flex justify-end gap-2">
        <a href="{{ route('reports.equipment', array_merge(request()->all(), ['export' => true, 'format' => 'xlsx'])) }}" class="btn-secondary">Export Excel</a>
        <a href="{{ route('reports.equipment', array_merge(request()->all(), ['export' => true, 'format' => 'pdf'])) }}" class="btn-primary">Export PDF</a>
    </div>
</div>
@endsection
