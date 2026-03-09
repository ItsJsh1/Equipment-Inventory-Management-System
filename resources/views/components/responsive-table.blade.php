@props([
    'headers' => [],
    'mobileCardView' => true,
    'stickyHeader' => false
])

<div class="card overflow-hidden">
    {{-- Desktop Table View --}}
    <div class="overflow-x-auto {{ $mobileCardView ? 'hidden md:block' : '' }}">
        <table class="w-full min-w-[640px]">
            <thead class="table-header {{ $stickyHeader ? 'sticky top-0 z-10' : '' }}">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    
    {{-- Mobile Card View --}}
    @if($mobileCardView && isset($mobileView))
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            {{ $mobileView }}
        </div>
    @endif
    
    {{-- Empty state --}}
    @if(isset($empty))
        {{ $empty }}
    @endif
</div>
