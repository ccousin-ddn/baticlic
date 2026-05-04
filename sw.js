var cacheName = 'SC-cache-v1';
var urlsToCache = [
	'',
	'css/bootstrap.min.css',
	'css/style_gen.css',
	'css/font-awesome.min.css',
	'js/jquery.min.js',
	'js/bootstrap.bundle.min.js',
	'js/main.js'
];

self.addEventListener('install', event => {
	console.log('Installing…');
	// OPTION: self.skipWaiting() instead of event.waitUntil()
	event.waitUntil(
		caches.open(cacheName)
			.then(cache => {
				// Precaching was successful so service worker is installed.
				console.log('Opened cache');
				return cache.addAll(urlsToCache);
			}, error => {
				// Precaching failed so service worker is not installed. 
				console.error(`Service Worker installation failed: ${error}`);
			})
	);
});

self.addEventListener('activate', event => {
	event.waitUntil(
		caches.keys().then((keyList) => {
			return Promise.all(keyList.map((key) => {
				// Same cacheName that we defined before.
				if (key !== cacheName) {
					console.log('[ServiceWorker] Removing old cache', key);
					return caches.delete(key);
				}
			}));
		})
	);
});

/* after a service worker is installed and the user navigates to a different page or 
refreshes,the service worker will begin to receive fetch events */
self.addEventListener('fetch', (event) => {
	event.respondWith(caches.open(cacheName).then((cache) => {
		return cache.match(event.request).then((response) => {
			console.log("cache request: " + event.request.url);
			var fetchPromise = fetch(event.request).then((networkResponse) => {
				// if we got a response from the cache, update the cache
				console.log("fetch completed: " + event.request.url, networkResponse);
				if (networkResponse) {
					console.debug("updated cached page: " + event.request.url, networkResponse);
					//cache.put(event.request, networkResponse.clone());
				}
				return networkResponse;
			}, function (event) {
				// rejected promise - just ignore it, we're offline!   
				console.log("Error in fetch()", event);
				event.waitUntil(
					// our 'cache' here is named *cache* in the caches.open()
					caches.open(cacheName).then((cache) => {
						return cache.addAll(urlsToCache);
					}) 
				); 
			});
			// respond from the cache, or the network
			return response || fetchPromise;
		}); 
	}));
});

// always updating i.e latest version available
self.addEventListener('install', (event) => {
	self.skipWaiting();
	console.log("Latest version installed!");
});
