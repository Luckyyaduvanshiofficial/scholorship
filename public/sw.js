/**
 * Tamboli Samaj Portal — Service Worker
 * Caches static assets and provides offline fallback.
 */

const CACHE_NAME = 'tamboli-portal-v1';
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/assets/css/bootstrap.min.css',
  '/assets/css/style.css',
  '/assets/js/bootstrap.bundle.min.js',
  '/assets/js/app.js',
  '/assets/images/logo/logo-placeholder.svg',
  '/assets/images/icons/icon-192x192.png',
  '/assets/images/icons/icon-512x512.png',
  '/favicon.png'
];

// Install: cache core static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

// Fetch: cache-first for static assets, network-first for pages
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests and cross-origin requests
  if (request.method !== 'GET' || url.origin !== self.location.origin) {
    return;
  }

  const isStatic = STATIC_ASSETS.some((path) => url.pathname === path || url.pathname.startsWith('/assets/'));

  if (isStatic) {
    event.respondWith(
      caches.match(request).then((cached) =>
        cached || fetch(request).then((response) => {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
          return response;
        })
      )
    );
    return;
  }

  // HTML pages: network-first with offline fallback
  event.respondWith(
    fetch(request)
      .then((response) => {
        const clone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
        return response;
      })
      .catch(() =>
        caches.match(request).then((cached) =>
          cached || caches.match('/offline.html')
        )
      )
  );
});
