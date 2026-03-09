const CACHE_NAME = 'eims-cache-v2';
const STATIC_CACHE = 'eims-static-v2';
const DYNAMIC_CACHE = 'eims-dynamic-v2';
const OFFLINE_URL = '/offline.html';

// Assets to cache on install (critical for offline)
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json'
];

// Static assets to cache (images, fonts, etc.)
const STATIC_PATTERNS = [
    /\/icons\/.*/,
    /\/images\/.*/,
    /\.woff2?$/,
    /\.ttf$/
];

// Dynamic pages that can be cached
const CACHEABLE_PATTERNS = [
    /\/dashboard/,
    /\/equipment/,
    /\/users/,
    /\/borrowings/,
    /\/transactions/,
    /\/maintenance/,
    /\/disposals/
];

// Install event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            console.log('EIMS: Caching static assets');
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    const cacheWhitelist = [STATIC_CACHE, DYNAMIC_CACHE];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => !cacheWhitelist.includes(name))
                    .map((name) => {
                        console.log('EIMS: Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        })
    );
    self.clients.claim();
});

// Helper: Check if URL matches any pattern
function matchesPattern(url, patterns) {
    return patterns.some(pattern => pattern.test(url));
}

// Helper: Determine caching strategy based on request
function getCacheStrategy(request) {
    const url = request.url;
    
    // Static assets: Cache first
    if (matchesPattern(url, STATIC_PATTERNS)) {
        return 'cache-first';
    }
    
    // API requests: Network only
    if (url.includes('/api/') || url.includes('/livewire/')) {
        return 'network-only';
    }
    
    // HTML pages: Network first with fallback
    if (request.headers.get('accept')?.includes('text/html')) {
        return 'network-first';
    }
    
    // CSS/JS: Stale-while-revalidate
    if (url.endsWith('.css') || url.endsWith('.js')) {
        return 'stale-while-revalidate';
    }
    
    // Default: Network first
    return 'network-first';
}

// Cache-first strategy
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return new Response('Asset not available offline', { status: 503 });
    }
}

// Network-first strategy
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) return cached;
        
        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match(OFFLINE_URL);
        }
        
        return new Response('Network error', { status: 503 });
    }
}

// Stale-while-revalidate strategy
async function staleWhileRevalidate(request) {
    const cache = await caches.open(DYNAMIC_CACHE);
    const cached = await cache.match(request);
    
    const networkPromise = fetch(request).then(response => {
        if (response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    }).catch(() => null);
    
    return cached || networkPromise || new Response('Asset not available', { status: 503 });
}

// Fetch event handler
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // Skip certain requests
    if (event.request.url.includes('/sanctum/') || 
        event.request.url.includes('hot-update') ||
        event.request.headers.get('content-type')?.includes('multipart/form-data')) {
        return;
    }

    const strategy = getCacheStrategy(event.request);
    
    switch (strategy) {
        case 'cache-first':
            event.respondWith(cacheFirst(event.request));
            break;
        case 'network-only':
            // Let browser handle it
            return;
        case 'stale-while-revalidate':
            event.respondWith(staleWhileRevalidate(event.request));
            break;
        case 'network-first':
        default:
            event.respondWith(networkFirst(event.request));
            break;
    }
});

// Handle background sync for form submissions (future enhancement)
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-forms') {
        console.log('EIMS: Background sync triggered');
    }
});

// Handle push notifications (future enhancement)
self.addEventListener('push', (event) => {
    const options = {
        body: event.data?.text() || 'New notification from EIMS',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        }
    };

    event.waitUntil(
        self.registration.showNotification('EIMS', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/')
    );
});
