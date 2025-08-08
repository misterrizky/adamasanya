var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/style.bundle.css',
    '/plugins/global/plugins.bundle.js',
    '/js/scripts.bundle.js',
    '/js/onboarding.js',
    '/js/function.js',
    '/js/metronic-navigated.js',
    '/plugins/custom/fslightbox/fslightbox.bundle.js',
    '/plugins/custom/typedjs/typedjs.bundle.js',
    '/plugins/custom/fullcalendar/fullcalendar.bundle.js',
    '/js/widgets.bundle.js',
    '/media/icons/icon-72x72.png',
    '/media/icons/icon-96x96.png',
    '/media/icons/icon-128x128.png',
    '/media/icons/icon-144x144.png',
    '/media/icons/icon-152x152.png',
    '/media/icons/icon-192x192.png',
    '/media/icons/icon-384x384.png',
    '/media/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
self.addEventListener('push', function(event) {
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/media/icons/icon-192x192.png',
        badge: '/media/icons/icon-96x96.png',
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/home'
        }
    };
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data.url || '/home';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientsArr => {
            let found = false;
            for (const client of clientsArr) {
                if (client.url.includes('/home')) {
                    found = true;
                    client.focus();
                    break;
                }
            }

            if (!found) {
                clients.openWindow(url);
            }
        })
    );
});