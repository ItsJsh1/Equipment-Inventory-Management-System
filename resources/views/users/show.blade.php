@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" 
                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @can('manage users')
            <a href="{{ route('users.edit', $user) }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>
    
    <!-- Status Badge -->
    <div class="card p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
                @if($user->roles->first())
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    @if($user->roles->first()->name === 'super_admin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                    @elseif($user->roles->first()->name === 'admin') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $user->roles->first()->name)) }}
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Joined {{ $user->created_at->format('M d, Y') }}
            </p>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- User Info -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Full Name</p>
                    <p class="text-gray-900 dark:text-white">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Email Address</p>
                    <p class="text-gray-900 dark:text-white">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Email Verified</p>
                    <p class="text-gray-900 dark:text-white">
                        @if($user->email_verified_at)
                        <span class="text-green-600 dark:text-green-400">Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                        @else
                        <span class="text-yellow-600 dark:text-yellow-400">Not verified</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Last Updated</p>
                    <p class="text-gray-900 dark:text-white">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Permissions -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Permissions</h3>
            @if($user->roles->first())
            @php $permissions = $user->roles->first()->permissions @endphp
            @if($permissions->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($permissions as $permission)
                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">
                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                </span>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400">No specific permissions</p>
            @endif
            @else
            <p class="text-gray-500 dark:text-gray-400">No role assigned</p>
            @endif
        </div>
    </div>
    
    <!-- Activity Stats -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity Summary</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->processedTransactions()->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Transactions</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->processedBorrowings()->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Borrowings</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->scheduledMaintenances()->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Maintenances</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Audit Entries</p>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
        @php
        $activities = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
        @endphp
        @if($activities->count() > 0)
        <div class="space-y-3">
            @foreach($activities as $activity)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                        @if($activity->event === 'created') bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400
                        @elseif($activity->event === 'updated') bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400
                        @elseif($activity->event === 'deleted') bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400
                        @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400
                        @endif">
                        @if($activity->event === 'created')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        @elseif($activity->event === 'updated')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        @elseif($activity->event === 'deleted')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @endif
                    </span>
                    <div>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ class_basename($activity->subject_type ?? 'Unknown') }}</p>
                    </div>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
        @endif
    </div>
</div>
@endsection
