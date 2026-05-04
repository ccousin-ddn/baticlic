importScripts('https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js');

if (workbox) {

	workbox.loadModule('workbox-strategies');

	self.addEventListener('fetch', (event) => {
		if (event.request.url.endsWith('.js')) {
			// Referencing workbox.strategies will now work as expected.
			const cacheFirst = new workbox.strategies.CacheFirst();
			event.respondWith(cacheFirst.handle({request: event.request}));
		}
		if (event.request.url.endsWith('.css')) {
			// Referencing workbox.strategies will now work as expected.
			const cacheFirst = new workbox.strategies.CacheFirst();
			event.respondWith(cacheFirst.handle({request: event.request}));
		}
		if (event.request.url.endsWith('.png')) {
			// Referencing workbox.strategies will now work as expected.
			const cacheFirst = new workbox.strategies.CacheFirst();
			event.respondWith(cacheFirst.handle({request: event.request}));
		}
		
		if (event.request.url.endsWith('.jpg')) {
			// Referencing workbox.strategies will now work as expected.
			const cacheFirst = new workbox.strategies.CacheFirst();
			event.respondWith(cacheFirst.handle({request: event.request}));
		}
		
	});

} else {
	console.log(`Boo! Workbox didn't load`);
}