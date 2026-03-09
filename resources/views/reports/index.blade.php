@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h2>
        <p class="text-gray-500 dark:text-gray-400">Generate and export various reports</p>
    </div>
    
    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Equipment Report -->
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div class="flex-shrink-0 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Equipment Report</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Complete inventory listing with status and details</p>
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('reports.equipment') }}" class="btn-secondary text-sm">View Report</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="btn-secondary text-sm flex items-center gap-1">
                        Export
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                        <a href="{{ route('reports.export', ['type' => 'equipment', 'format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Excel</a>
                        <a href="{{ route('reports.export', ['type' => 'equipment', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">PDF</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Transaction Report -->
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Transaction Report</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Incoming and outgoing equipment transactions</p>
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('reports.transactions') }}" class="btn-secondary text-sm">View Report</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="btn-secondary text-sm flex items-center gap-1">
                        Export
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                        <a href="{{ route('reports.export', ['type' => 'transactions', 'format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Excel</a>
                        <a href="{{ route('reports.export', ['type' => 'transactions', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">PDF</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Borrowing Report -->
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Borrowing Report</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Equipment borrowing history and overdue items</p>
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('reports.borrowings') }}" class="btn-secondary text-sm">View Report</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="btn-secondary text-sm flex items-center gap-1">
                        Export
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                        <a href="{{ route('reports.export', ['type' => 'borrowings', 'format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Excel</a>
                        <a href="{{ route('reports.export', ['type' => 'borrowings', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">PDF</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Maintenance Report -->
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div class="flex-shrink-0 p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Maintenance Report</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Equipment maintenance records and schedules</p>
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('reports.maintenances') }}" class="btn-secondary text-sm">View Report</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="btn-secondary text-sm flex items-center gap-1">
                        Export
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                        <a href="{{ route('reports.export', ['type' => 'maintenances', 'format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Excel</a>
                        <a href="{{ route('reports.export', ['type' => 'maintenances', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">PDF</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Disposal Report -->
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Disposal Report</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Equipment disposal records and history</p>
            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('reports.disposals') }}" class="btn-secondary text-sm">View Report</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="btn-secondary text-sm flex items-center gap-1">
                        Export
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                        <a href="{{ route('reports.export', ['type' => 'disposals', 'format' => 'xlsx']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Excel</a>
                        <a href="{{ route('reports.export', ['type' => 'disposals', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
