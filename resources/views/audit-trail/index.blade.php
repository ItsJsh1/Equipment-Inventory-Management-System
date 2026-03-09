@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Trail</h2>
        <p class="text-gray-500 dark:text-gray-400">Track all system activities and changes</p>
    </div>
    
    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" action="{{ route('audit-trail.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search activities..." class="input-field">
            </div>
            <div>
                <select name="event" class="input-field">
                    <option value="">All Events</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
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
                <a href="{{ route('audit-trail.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Activity List -->
    <div class="card overflow-hidden">
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($activities as $activity)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @if($activity->event == 'created')
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        @elseif($activity->event == 'updated')
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        @elseif($activity->event == 'deleted')
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $activity->description }}
                            </p>
                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-4">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span>
                                By: {{ $activity->causer?->name ?? 'System' }}
                            </span>
                            <span>•</span>
                            <span>
                                {{ class_basename($activity->subject_type ?? '') }}
                            </span>
                            <span>•</span>
                            <span class="px-1.5 py-0.5 rounded text-xs font-medium
                                @if($activity->event == 'created') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($activity->event == 'updated') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($activity->event == 'deleted') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ ucfirst($activity->event ?? 'unknown') }}
                            </span>
                        </div>
                        @if($activity->properties && count($activity->properties) > 0)
                        <div class="mt-2">
                            <a href="{{ route('audit-trail.show', $activity) }}" class="text-xs text-black dark:text-white hover:underline">
                                View Details →
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p>No activity logs found</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
