@extends('layouts.app')

@section('title', 'Activity Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Details</h2>
            <p class="text-gray-500 dark:text-gray-400">View detailed information about this activity</p>
        </div>
        <a href="{{ route('audit-trail.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Activity Info Card -->
    <div class="card p-6">
        <div class="flex items-start gap-4 mb-6">
            <div class="flex-shrink-0">
                @if($activity->event == 'created')
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                @elseif($activity->event == 'updated')
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                @elseif($activity->event == 'deleted')
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                @else
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $activity->description }}</h3>
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $activity->causer?->name ?? 'System' }}
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $activity->created_at->format('M d, Y h:i A') }}
                    </span>
                    <span>•</span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        @if($activity->event == 'created') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($activity->event == 'updated') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($activity->event == 'deleted') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                        @endif">
                        {{ ucfirst($activity->event ?? 'unknown') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Subject Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Subject Type</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ class_basename($activity->subject_type ?? 'Unknown') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Subject ID</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->subject_id ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Log Name</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->log_name ?? 'default' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Time Ago</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
    
    <!-- Properties -->
    @if($activity->properties && count($activity->properties) > 0)
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Changes</h3>
        
        @if(isset($activity->properties['old']) && isset($activity->properties['attributes']))
        <!-- Updated Event - Show Old vs New -->
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Old Values
                    </h4>
                    <dl class="space-y-2">
                        @foreach($activity->properties['old'] as $key => $value)
                        <div>
                            <dt class="text-xs text-red-600 dark:text-red-400">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                            <dd class="text-sm text-red-900 dark:text-red-200 font-mono break-all">
                                @if(is_array($value))
                                    {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                @elseif(is_null($value))
                                    <span class="italic text-gray-400">null</span>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <h4 class="text-sm font-medium text-green-800 dark:text-green-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Values
                    </h4>
                    <dl class="space-y-2">
                        @foreach($activity->properties['attributes'] as $key => $value)
                        <div>
                            <dt class="text-xs text-green-600 dark:text-green-400">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                            <dd class="text-sm text-green-900 dark:text-green-200 font-mono break-all">
                                @if(is_array($value))
                                    {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                @elseif(is_null($value))
                                    <span class="italic text-gray-400">null</span>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
        @elseif(isset($activity->properties['attributes']))
        <!-- Created Event - Show Attributes -->
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <h4 class="text-sm font-medium text-green-800 dark:text-green-400 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Created Values
            </h4>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($activity->properties['attributes'] as $key => $value)
                <div>
                    <dt class="text-xs text-green-600 dark:text-green-400">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                    <dd class="text-sm text-green-900 dark:text-green-200 font-mono break-all">
                        @if(is_array($value))
                            {{ json_encode($value, JSON_PRETTY_PRINT) }}
                        @elseif(is_null($value))
                            <span class="italic text-gray-400">null</span>
                        @else
                            {{ $value }}
                        @endif
                    </dd>
                </div>
                @endforeach
            </dl>
        </div>
        @else
        <!-- Generic Properties Display -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <pre class="text-sm text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Raw Data (Collapsible) -->
    <div class="card p-6" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Raw Activity Data</h3>
            <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-collapse class="mt-4">
            <pre class="p-4 bg-gray-100 dark:bg-gray-900 rounded-lg text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($activity->toArray(), JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
