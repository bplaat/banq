var CACHE_NAME = 'banq-v1';
var urlsToCache = [
    '/offline',
    '/bulma.min.css',
    '/script.js',
    '/images/icon-16.png',
    '/images/icon-32.png',
    '/images/icon-192.png'
];

self.addEventListener('install', function (event) {
    event.waitUntil(caches.open(CACHE_NAME).then(function (cache) {
        return cache.addAll(urlsToCache);
    }));
});

self.addEventListener('fetch', function (event) {
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request.url, { redirect: 'follow' }).catch(function () {
            return caches.match('/offline');
        }));
    } else {
        event.respondWith(caches.match(event.request).then(function (response) {
            return response || fetch(event.request, { redirect: 'follow' });
        }));
    }
});
