// The cache name from the config
var CACHE_NAME = '{{ strtolower(APP_NAME) }}-v{{ APP_VERSION }}';

// List with all the files to cache
var filesToCache = [
    '/offline',
    '/bulma.min.css',
    '/script.min.js',
    '/images/icon-16.png',
    '/images/icon-32.png',
    '/images/icon-192.png'
];

// The service worker install event
self.addEventListener('install', function (event) {
    // Open a new cache and put all files to cache in it
    event.waitUntil(caches.open(CACHE_NAME).then(function (cache) {
        return cache.addAll(filesToCache);
    }));
});

// The service worker active event
self.addEventListener('activate', function (event) {
    // Delete all old caches
    event.waitUntil(caches.keys().then(function (cacheNames) {
        return Promise.all(cacheNames.filter(function (cacheName) {
            return cacheName != CACHE_NAME
        }).map(function (cacheName) {
            return caches.delete(cacheName);
        }));
    }));
});

// The service worker fetch event
self.addEventListener('fetch', (event) => {
    // First try to find the file in the cache
    event.respondWith(caches.match(event.request).then(function (response) {
        // Else try to fetch it from the internet
        return response || fetch(event.request).catch(function () {
            // When offline give the offline file back
            return caches.match('/offline');
        });
    }));
});
