const CACHE_NAME = 'sms-cache-v3';

// URLs to cache
const urlsToCache = [
  './assets/css/style.css',
  './assets/js/script.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css'
];

// URLs that should never be cached (auth/dynamic pages)
const noCacheUrls = [
  '/auth/',
  '/login',
  '/logout',
  'login_process.php',
  'logout.php'
];

// Install service worker
self.addEventListener('install', event => {
  console.log('[SW] Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[SW] Caching static assets');
        return cache.addAll(urlsToCache);
      })
      .then(() => self.skipWaiting())
  );
});

// Activate service worker
self.addEventListener('activate', event => {
  console.log('[SW] Activating...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event handler
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  
  // Skip service worker entirely for auth pages and POST requests
  const shouldNotCache = noCacheUrls.some(path => url.pathname.includes(path)) || 
                         event.request.method !== 'GET';
  
  if (shouldNotCache) {
    // Let auth pages bypass service worker completely
    return;
  }
  
  // For static assets - try cache first, then network
  event.respondWith(
    caches.match(event.request)
      .then(cachedResponse => {
        if (cachedResponse) {
          return cachedResponse;
        }
        
        return fetch(event.request, {
          credentials: 'same-origin',
          redirect: 'follow'
        }).then(response => {
          // Don't cache redirects, errors, or opaque responses
          if (!response || 
              response.status !== 200 || 
              response.type === 'opaque' ||
              response.redirected) {
            return response;
          }
          
          // Clone and cache the response
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
          });
          
          return response;
        });
      })
      .catch(error => {
        console.log('[SW] Fetch failed:', error);
        return new Response('Offline', {
          status: 503,
          statusText: 'Service Unavailable'
        });
      })
  );
});
