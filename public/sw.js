/* Garage Management service worker — static assets only; Laravel pages stay network-first. */
const CACHE_VERSION = 'gm-static-v1';
const STATIC_CACHE = `${CACHE_VERSION}-shell`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.json',
    '/css/admin.css',
    '/js/custom.js',
    '/js/pwa.js',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon-192x192-maskable.png',
    '/icons/icon-512x512-maskable.png',
    '/icons/apple-touch-icon.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        (async () => {
            const cache = await caches.open(STATIC_CACHE);
            await cache.addAll(PRECACHE_URLS);
            // Activate immediately only on first install; updates wait for reload.
            if (!self.registration.active) {
                await self.skipWaiting();
            }
        })()
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        (async () => {
            const keys = await caches.keys();
            await Promise.all(
                keys
                    .filter((key) => key !== STATIC_CACHE && key !== RUNTIME_CACHE)
                    .map((key) => caches.delete(key))
            );
            await self.clients.claim();
        })()
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Leave cross-origin (CDN) requests to the browser.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Never cache the service worker script itself.
    if (url.pathname === '/sw.js') {
        return;
    }

    // Laravel HTML navigations: network-only (online-first auth/CSRF/data).
    if (request.mode === 'navigate' || isHtmlRequest(request)) {
        event.respondWith(networkOnlyNavigation(request));
        return;
    }

    // Cache only versioned/static public assets — not dynamic app data.
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
    }
});

function isHtmlRequest(request) {
    const accept = request.headers.get('accept') || '';
    return accept.includes('text/html');
}

function isStaticAsset(pathname) {
    if (
        pathname === '/manifest.json' ||
        pathname === '/offline.html' ||
        pathname === '/favicon.ico'
    ) {
        return true;
    }

    return (
        pathname.startsWith('/css/') ||
        pathname.startsWith('/js/') ||
        pathname.startsWith('/icons/') ||
        pathname.startsWith('/build/')
    );
}

async function networkOnlyNavigation(request) {
    try {
        const response = await fetch(request);
        return response;
    } catch (error) {
        const cache = await caches.open(STATIC_CACHE);
        const offline = await cache.match('/offline.html');
        return offline || Response.error();
    }
}

async function cacheFirst(request) {
    const cache = await caches.open(RUNTIME_CACHE);
    const cached = await cache.match(request);

    if (cached) {
        return cached;
    }

    try {
        const response = await fetch(request);

        if (shouldCacheResponse(response)) {
            await cache.put(request, response.clone());
        }

        return response;
    } catch (error) {
        const staticCache = await caches.open(STATIC_CACHE);
        const fallback = await staticCache.match(request);
        if (fallback) {
            return fallback;
        }
        throw error;
    }
}

function shouldCacheResponse(response) {
    if (!response || response.status !== 200) {
        return false;
    }

    if (response.type !== 'basic' && response.type !== 'cors') {
        return false;
    }

    const cacheControl = response.headers.get('cache-control') || '';
    if (cacheControl.includes('no-store') || cacheControl.includes('private')) {
        return false;
    }

    if (response.headers.has('set-cookie')) {
        return false;
    }

    return true;
}
