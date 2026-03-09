@props([
    'title' => '',
    'subtitle' => '',
    'badge' => null,
    'badgeClass' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
])

<div {{ $attributes->merge(['class' => 'mobile-card p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50']) }}>
    {{-- Header with title, subtitle and optional badge --}}
    <div class="flex items-start justify-between gap-3 mb-3">
        <div class="flex-1 min-w-0">
            @if($title)
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $title }}</p>
            @endif
            @if($subtitle)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
        @if($badge)
            <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">
                {{ $badge }}
            </span>
        @endif
    </div>
    
    {{-- Content slot for additional fields --}}
    @if(isset($content))
        <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
            {{ $content }}
        </div>
    @endif
    
    {{-- Actions slot --}}
    @if(isset($actions))
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
            {{ $actions }}
        </div>
    @endif
</div>
