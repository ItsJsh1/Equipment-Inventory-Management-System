<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-all duration-300 ease-in-out"
       x-bind:class="{
           'w-64': $store.sidebar.open,
           'w-20': !$store.sidebar.open,
           'translate-x-0': $store.sidebar.open,
           '-translate-x-full lg:translate-x-0': !$store.sidebar.open
       }">
    
    <!-- Logo -->
    <div class="h-16 flex items-center justify-center border-b border-gray-200 dark:border-gray-700 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-black dark:bg-white rounded-lg flex items-center justify-center">
                <span class="text-white dark:text-black font-bold text-lg">E</span>
            </div>
            <span x-show="$store.sidebar.open" 
                  x-transition:enter="transition-opacity duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-bold text-xl text-gray-900 dark:text-white">EIMS</span>
        </a>
    </div>
    
    <!-- Navigation -->
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-4rem)]">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Dashboard</span>
        </a>
        
        <!-- Equipment Management -->
        <div x-data="{ open: {{ request()->routeIs('equipment.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('equipment.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Equipment</span>
                </div>
                <svg x-show="$store.sidebar.open" class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && $store.sidebar.open" x-collapse class="mt-1 ml-4 space-y-1">
                <a href="{{ route('equipment.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('equipment.index') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    All Equipment
                </a>
                @can('create_equipment')
                <a href="{{ route('equipment.create') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('equipment.create') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    Add Equipment
                </a>
                @endcan
            </div>
        </div>
        
        <!-- Transactions -->
        <div x-data="{ open: {{ request()->routeIs('transactions.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('transactions.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Transactions</span>
                </div>
                <svg x-show="$store.sidebar.open" class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && $store.sidebar.open" x-collapse class="mt-1 ml-4 space-y-1">
                <a href="{{ route('transactions.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('transactions.index') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    All Transactions
                </a>
                <a href="{{ route('transactions.incoming') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('transactions.incoming') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    Incoming
                </a>
                <a href="{{ route('transactions.outgoing') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('transactions.outgoing') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    Outgoing
                </a>
            </div>
        </div>
        
        <!-- Borrowings -->
        <div x-data="{ open: {{ request()->routeIs('borrowings.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('borrowings.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Borrowings</span>
                </div>
                <svg x-show="$store.sidebar.open" class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && $store.sidebar.open" x-collapse class="mt-1 ml-4 space-y-1">
                <a href="{{ route('borrowings.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('borrowings.index') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    All Borrowings
                </a>
                <a href="{{ route('borrowings.overdue') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('borrowings.overdue') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">
                    Overdue
                </a>
            </div>
        </div>
        
        <!-- Maintenance -->
        <a href="{{ route('maintenances.index') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('maintenances.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Maintenance</span>
        </a>
        
        <!-- Disposals -->
        <a href="{{ route('disposals.index') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('disposals.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Disposals</span>
        </a>
        
        <!-- Reports -->
        <a href="{{ route('reports.index') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Reports</span>
        </a>
        
        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
        
        <!-- Master Data -->
        <div x-data="{ open: {{ request()->routeIs('brands.*', 'categories.*', 'departments.*', 'locations.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('brands.*', 'categories.*', 'departments.*', 'locations.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Master Data</span>
                </div>
                <svg x-show="$store.sidebar.open" class="w-4 h-4 transition-transform" x-bind:class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && $store.sidebar.open" x-collapse class="mt-1 ml-4 space-y-1">
                <a href="{{ route('brands.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('brands.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">Brands</a>
                <a href="{{ route('categories.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('categories.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">Categories</a>
                <a href="{{ route('departments.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('departments.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">Departments</a>
                <a href="{{ route('locations.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('locations.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} text-gray-600 dark:text-gray-400">Locations</a>
            </div>
        </div>
        
        @can('view_users')
        <!-- User Management -->
        <a href="{{ route('users.index') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Users</span>
        </a>
        @endcan
        
        @can('view_audit_trail')
        <!-- Audit Trail -->
        <a href="{{ route('audit-trail.index') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('audit-trail.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Audit Trail</span>
        </a>
        @endcan
        
        @can('manage_settings')
        <!-- Settings -->
        <a href="{{ route('settings.index') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('settings.*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            <span x-show="$store.sidebar.open" x-transition class="whitespace-nowrap">Settings</span>
        </a>
        @endcan
    </nav>
</aside>
