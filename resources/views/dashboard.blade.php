@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="card p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            Welcome back, {{ auth()->user()->firstname }}!
        </h2>
        <p class="text-gray-500 dark:text-gray-400 mt-1">
            Here's an overview of your equipment inventory management system.
        </p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Equipment -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Equipment</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($equipmentStats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span class="text-green-600 dark:text-green-400">{{ $equipmentStats['available'] }} Available</span>
                <span class="text-blue-600 dark:text-blue-400">{{ $equipmentStats['in_use'] }} In Use</span>
            </div>
        </div>
        
        <!-- Active Borrowings -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Borrowings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($borrowingStats['active']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            @if($borrowingStats['overdue'] > 0)
            <div class="mt-4">
                <span class="text-sm text-red-600 dark:text-red-400 font-medium">
                    ⚠️ {{ $borrowingStats['overdue'] }} Overdue
                </span>
            </div>
            @endif
        </div>
        
        <!-- Pending Transactions -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Transactions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($transactionStats['pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span class="text-green-600 dark:text-green-400">{{ $transactionStats['total_incoming'] }} Incoming</span>
                <span class="text-blue-600 dark:text-blue-400">{{ $transactionStats['total_outgoing'] }} Outgoing</span>
            </div>
        </div>
        
        <!-- Maintenance -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Maintenance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($maintenanceStats['in_progress']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $maintenanceStats['scheduled'] }} Scheduled</span>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Equipment by Status Chart -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment by Status</h3>
            <div class="h-64">
                <canvas id="equipmentStatusChart"></canvas>
            </div>
        </div>
        
        <!-- Monthly Transactions Chart -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Transactions</h3>
            <div class="h-64">
                <canvas id="monthlyTransactionsChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="card">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-black dark:text-white hover:underline">View All</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $transaction->transaction_code }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type_badge }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_badge }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No recent transactions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Overdue Borrowings -->
        <div class="card">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Overdue Borrowings</h3>
                    <a href="{{ route('borrowings.overdue') }}" class="text-sm text-black dark:text-white hover:underline">View All</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Borrower</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($overdueBorrowings as $borrowing)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $borrowing->borrowing_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->borrower_full_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $borrowing->expected_return_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $borrowing->expected_return_date->diffInDays(now()) }} days
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No overdue borrowings</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Equipment Status Chart
    const equipmentCtx = document.getElementById('equipmentStatusChart').getContext('2d');
    new Chart(equipmentCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map(fn($s) => ucfirst(str_replace('_', ' ', $s)), array_keys($equipmentByStatus))) !!},
            datasets: [{
                data: {!! json_encode(array_values($equipmentByStatus)) !!},
                backgroundColor: [
                    '#10B981', // green - available
                    '#3B82F6', // blue - in_use
                    '#F59E0B', // yellow - borrowed
                    '#F97316', // orange - maintenance
                    '#EF4444', // red - for_disposal
                    '#6B7280', // gray - disposed
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    
    // Monthly Transactions Chart
    const transactionsCtx = document.getElementById('monthlyTransactionsChart').getContext('2d');
    const monthlyData = @json($monthlyTransactions);
    new Chart(transactionsCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(monthlyData),
            datasets: [
                {
                    label: 'Incoming',
                    data: Object.values(monthlyData).map(d => d.incoming),
                    backgroundColor: '#10B981',
                },
                {
                    label: 'Outgoing',
                    data: Object.values(monthlyData).map(d => d.outgoing),
                    backgroundColor: '#3B82F6',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
