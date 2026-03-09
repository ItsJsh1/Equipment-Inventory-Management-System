<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="{ 'dark': $store.darkMode.on }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'EIMS') }} - @yield('title', 'Dashboard')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1f2937" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EIMS">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/icons/icon-152x152.png">
    
    <!-- Splash screens for iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- PWA Update notification styles -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* iOS safe area support */
        @supports (padding: env(safe-area-inset-bottom)) {
            .safe-bottom {
                padding-bottom: env(safe-area-inset-bottom);
            }
            .safe-top {
                padding-top: env(safe-area-inset-top);
            }
        }
        
        /* Prevent pull-to-refresh on PWA */
        body.pwa-standalone {
            overscroll-behavior-y: contain;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-size-adjust-none"
      x-data="{ isPWA: window.matchMedia('(display-mode: standalone)').matches }"
      :class="{ 'pwa-standalone': isPWA }">
    
    <!-- Network Status Indicator -->
    @include('components.network-status')
    
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             x-bind:class="{ 'lg:ml-64': $store.sidebar.open, 'lg:ml-20': !$store.sidebar.open }">
            
            <!-- Top Navigation -->
            @include('layouts.partials.header')
            
            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg dark:bg-green-900 dark:border-green-800 dark:text-green-200" 
                         x-data="{ show: true }" 
                         x-show="show" 
                         x-transition
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center justify-between">
                            <span>{{ session('success') }}</span>
                            <button @click="show = false" class="text-green-700 dark:text-green-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg dark:bg-red-900 dark:border-red-800 dark:text-red-200"
                         x-data="{ show: true }"
                         x-show="show"
                         x-transition>
                        <div class="flex items-center justify-between">
                            <span>{{ session('error') }}</span>
                            <button @click="show = false" class="text-red-700 dark:text-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 px-6 safe-bottom">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} EIMS - Equipment Inventory Management System. All rights reserved.
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="$store.sidebar.open" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$store.sidebar.toggle()"
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
    </div>
    
    <!-- PWA Install Prompt -->
    @include('components.pwa-install-prompt')
    
    <!-- Service Worker Registration with Update Detection -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => {
                        console.log('EIMS: Service Worker registered');
                        
                        // Check for updates
                        reg.addEventListener('updatefound', () => {
                            const newWorker = reg.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New content available, show refresh prompt
                                    if (confirm('New version available! Reload to update?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(err => console.log('EIMS: Service Worker registration failed', err));
                    
                // Handle controller change (when new SW takes over)
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    console.log('EIMS: New Service Worker activated');
                });
            });
        }
        
        // Online/Offline status indicator
        window.addEventListener('online', () => {
            document.body.classList.remove('offline');
            console.log('EIMS: Back online');
        });
        
        window.addEventListener('offline', () => {
            document.body.classList.add('offline');
            console.log('EIMS: Gone offline');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
