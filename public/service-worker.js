/* global workbox */
importScripts('https://storage.googleapis.com/workbox-cdn/releases/6.5.4/workbox-sw.js');

if (self.workbox) {
    const { core, precaching, routing, strategies, expiration, cacheableResponse } =
        self.workbox;

    core.setCacheNameDetails({
        prefix: 'electrons',
        suffix: 'v1',
    });

    core.skipWaiting();
    core.clientsClaim();

    const manifest = self.__WB_MANIFEST || [];
    precaching.precacheAndRoute(manifest, {
        ignoreURLParametersMatching: [/.*/],
    });
    precaching.cleanupOutdatedCaches();

    routing.registerRoute(
        ({ request }) => ['style', 'script', 'worker'].includes(request.destination),
        new strategies.StaleWhileRevalidate({
            cacheName: 'electrons-assets',
            plugins: [
                new cacheableResponse.CacheableResponsePlugin({
                    statuses: [0, 200],
                }),
                new expiration.ExpirationPlugin({
                    maxEntries: 80,
                    maxAgeSeconds: 30 * 24 * 60 * 60, // 30 days
                }),
            ],
        })
    );

    routing.registerRoute(
        ({ request }) => request.destination === 'image',
        new strategies.CacheFirst({
            cacheName: 'electrons-images',
            plugins: [
                new cacheableResponse.CacheableResponsePlugin({
                    statuses: [0, 200],
                }),
                new expiration.ExpirationPlugin({
                    maxEntries: 100,
                    maxAgeSeconds: 30 * 24 * 60 * 60, // 30 days
                }),
            ],
        })
    );

    routing.registerRoute(
        ({ request }) => request.destination === 'font',
        new strategies.StaleWhileRevalidate({
            cacheName: 'electrons-fonts',
            plugins: [
                new cacheableResponse.CacheableResponsePlugin({
                    statuses: [0, 200],
                }),
                new expiration.ExpirationPlugin({
                    maxEntries: 30,
                    maxAgeSeconds: 365 * 24 * 60 * 60, // 1 year
                }),
            ],
        })
    );

    routing.registerRoute(
        ({ url }) =>
            ['https://fonts.googleapis.com', 'https://fonts.gstatic.com'].includes(
                url.origin
            ),
        new strategies.StaleWhileRevalidate({
            cacheName: 'electrons-google-fonts',
            plugins: [
                new cacheableResponse.CacheableResponsePlugin({
                    statuses: [0, 200],
                }),
                new expiration.ExpirationPlugin({
                    maxEntries: 30,
                    maxAgeSeconds: 365 * 24 * 60 * 60, // 1 year
                }),
            ],
        })
    );

    routing.registerRoute(
        ({ url }) =>
            ['https://cdn.jsdelivr.net', 'https://cdnjs.cloudflare.com'].includes(
                url.origin
            ),
        new strategies.StaleWhileRevalidate({
            cacheName: 'electrons-cdn',
            plugins: [
                new cacheableResponse.CacheableResponsePlugin({
                    statuses: [0, 200],
                }),
                new expiration.ExpirationPlugin({
                    maxEntries: 40,
                    maxAgeSeconds: 30 * 24 * 60 * 60, // 30 days
                }),
            ],
        })
    );

    self.addEventListener('message', (event) => {
        if (event.data && event.data.type === 'SKIP_WAITING') {
            core.skipWaiting();
        }
    });
} else {
    self.addEventListener('install', () => self.skipWaiting());
    self.addEventListener('activate', (event) => {
        event.waitUntil(self.clients.claim());
    });
}
