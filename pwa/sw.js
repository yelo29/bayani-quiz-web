const CACHE_NAME = 'bayani-world-v5';
const urlsToCache = [
  '/',
  '/index.php',
  '/maglaro.php',
  '/quiz.php',
  '/leaderboard.php',
  '/profile.php',
  '/tindahan.php',
  '/inventaryo.php',
  '/pwa/offline.php',
  '/assets/css/custom.css',
  '/assets/js/quiz.js',
  '/assets/js/confetti.js',
  '/assets/js/share.js'
];

// Install event - cache core pages
self.addEventListener('install', event => {
  console.log('Bayani World: Service Worker installing');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Bayani World: Caching core pages');
        return cache.addAll(urlsToCache.map(url => new Request(url, {cache: 'reload'})));
      })
      .then(() => {
        console.log('Bayani World: All core pages cached');
        return self.skipWaiting();
      })
      .catch(err => {
        console.error('Bayani World: Cache install failed:', err);
      })
  );
});

// Fetch event - always show offline page when network fails for navigation
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // For navigation requests (page loads)
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // If network is available, cache the response
          if (response && response.status === 200) {
            const responseToCache = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });
            return response;
          }
          // If response is not OK, try cache
          return caches.match(event.request);
        })
        .catch(() => {
          // Network failed, try cache first
          return caches.match(event.request)
            .then(cachedResponse => {
              if (cachedResponse) {
                console.log('Bayani World: Serving from cache:', url.pathname);
                return cachedResponse;
              }
              // If not in cache, show offline page
              console.log('Bayani World: Not in cache, showing offline page');
              return caches.match('/pwa/offline.php');
            });
        })
    );
  } else {
    // For non-navigation requests (assets, API, etc.)
    event.respondWith(
      caches.match(event.request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }

          return fetch(event.request)
            .then(response => {
              if (!response || response.status !== 200) {
                return response;
              }

              const responseToCache = response.clone();
              caches.open(CACHE_NAME)
                .then(cache => {
                  cache.put(event.request, responseToCache);
                });

              return response;
            })
            .catch(() => {
              return new Response('Offline', { status: 503 });
            });
        })
    );
  }
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  console.log('Bayani World: Service Worker activating');
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== CACHE_NAME) {
              console.log('Bayani World: Deleting old cache', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('Bayani World: Service Worker activated');
        return self.clients.claim();
      })
  );
});
