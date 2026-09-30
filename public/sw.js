/* =========================================================
   PPI Check — Service Worker sederhana
   - Pre-cache app shell (CSS/JS/font aset statis)
   - Network-first untuk halaman, fallback ke cache, lalu /offline
   - Cache-first untuk aset statis
   ========================================================= */

const VERSION = 'ppi-check-v1';
const SHELL_CACHE = `${VERSION}-shell`;
const PAGE_CACHE = `${VERSION}-pages`;
const ASSET_CACHE = `${VERSION}-assets`;

const SHELL_ASSETS = [
    '/assets/css/bootstrap.min.css',
    '/assets/css/bootstrap-icons.min.css',
    '/assets/js/bootstrap.bundle.min.js',
    '/assets/js/alpine.min.js',
    '/assets/js/chart.umd.js',
    '/images/logo.png',
    '/images/favicon.png',
    '/offline',
];

// ---------- Install ----------
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(SHELL_CACHE)
            .then((cache) => cache.addAll(SHELL_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ---------- Activate ----------
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((k) => !k.startsWith(VERSION)).map((k) => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ---------- Fetch ----------
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Hanya tangani GET pada origin yang sama
    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    // Aset statis: cache-first
    if (request.destination === 'style' || request.destination === 'script' ||
        request.destination === 'font' || request.destination === 'image' ||
        request.url.includes('/assets/') || request.url.includes('/build/') ||
        request.url.includes('/images/') || request.url.includes('/icons/')) {
        event.respondWith(
            caches.open(ASSET_CACHE).then(async (cache) => {
                const hit = await cache.match(request);
                if (hit) return hit;
                try {
                    const res = await fetch(request);
                    if (res.ok) cache.put(request, res.clone());
                    return res;
                } catch {
                    return hit || Response.error();
                }
            })
        );
        return;
    }

    // Navigasi halaman: network-first → cache → /offline
    if (request.mode === 'navigate') {
        event.respondWith(
            (async () => {
                try {
                    const res = await fetch(request);
                    const cache = await caches.open(PAGE_CACHE);
                    cache.put(request, res.clone());
                    return res;
                } catch {
                    const cached = await caches.match(request);
                    if (cached) return cached;
                    const offline = await caches.match('/offline');
                    return offline || new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
                }
            })()
        );
    }
});
