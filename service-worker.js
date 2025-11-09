// Service Worker - Pico y PL
// Permite que la app funcione offline y se cachee

const CACHE_NAME = 'pico-y-pl-v1';
const URLS_TO_CACHE = [
  '/',
  '/index.html',
  '/manifest.json',
  'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap'
];

// Instalación del Service Worker
self.addEventListener('install', (event) => {
  console.log('Service Worker instalado');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Cache abierto');
        return cache.addAll(URLS_TO_CACHE);
      })
      .catch((error) => {
        console.log('Error en cache:', error);
      })
  );
  self.skipWaiting();
});

// Activación del Service Worker
self.addEventListener('activate', (event) => {
  console.log('Service Worker activado');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Eliminando cache anterior:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Estrategia de fetching: Network First, Fall back to Cache
self.addEventListener('fetch', (event) => {
  // Solo cachear peticiones GET
  if (event.request.method !== 'GET') {
    return;
  }

  // No cachear Google APIs o servicios externos
  if (event.request.url.includes('ipapi.co')) {
    event.respondWith(
      fetch(event.request)
        .catch(() => {
          // Si falla la conexión, usar cache o respuesta genérica
          return caches.match(event.request)
            .then((response) => {
              return response || new Response('Sin conexión a internet', {
                status: 503,
                statusText: 'Sin conexión',
                headers: new Headers({
                  'Content-Type': 'text/plain'
                })
              });
            });
        })
    );
    return;
  }

  // Para otros recursos, usar estrategia Network First
  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Si la respuesta es exitosa, cachearlo
        if (!response || response.status !== 200 || response.type === 'error') {
          return response;
        }

        // Clonar la respuesta
        const responseToCache = response.clone();

        caches.open(CACHE_NAME)
          .then((cache) => {
            cache.put(event.request, responseToCache);
          });

        return response;
      })
      .catch(() => {
        // Si falla la red, intentar obtener del cache
        return caches.match(event.request)
          .then((response) => {
            if (response) {
              return response;
            }

            // Si no está en cache, devolver página offline (si existe)
            if (event.request.destination === 'document') {
              return caches.match('/index.html');
            }

            return new Response('No disponible offline', {
              status: 503,
              statusText: 'Sin conexión',
              headers: new Headers({
                'Content-Type': 'text/plain'
              })
            });
          });
      })
  );
});

// Sincronización en background (cuando regresa la conexión)
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-pico-placa') {
    event.waitUntil(
      fetch('/').then(() => {
        console.log('Sincronización completada');
      })
    );
  }
});

// Push notifications (futuro)
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'Nueva notificación',
    icon: '/icon-192x192.png',
    badge: '/badge-72x72.png'
  };

  event.waitUntil(
    self.registration.showNotification('Pico y PL', options)
  );
});

console.log('Service Worker cargado correctamente');
