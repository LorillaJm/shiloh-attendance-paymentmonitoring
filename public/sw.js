/**
 * Service Worker for Push Notifications
 * Shiloh Learning Center - Real-time Notifications
 */

const CACHE_NAME = 'shiloh-v1';
const NOTIFICATION_SOUND = '/sounds/notification.mp3';

// Install event
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('[SW] Service Worker activated');
    event.waitUntil(clients.claim());
});

// Push notification received
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');
    
    let data = {
        title: 'Shiloh Learning Center',
        body: 'You have a new notification',
        icon: '/images/logo.png',
        badge: '/images/badge.png',
        url: '/admin',
        tag: 'default'
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = {
                title: payload.title || data.title,
                body: payload.body || data.body,
                icon: payload.icon || data.icon,
                badge: payload.badge || data.badge,
                url: payload.data?.url || data.url,
                tag: payload.tag || data.tag,
                image: payload.image,
                actions: payload.actions || [
                    { action: 'view', title: 'View' },
                    { action: 'dismiss', title: 'Dismiss' }
                ],
                vibrate: payload.vibrate || [200, 100, 200],
                requireInteraction: payload.requireInteraction || false,
                renotify: payload.renotify || true,
                data: payload.data || {}
            };
        } catch (e) {
            console.error('[SW] Error parsing push data:', e);
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon,
        badge: data.badge,
        image: data.image,
        tag: data.tag,
        actions: data.actions,
        vibrate: data.vibrate,
        requireInteraction: data.requireInteraction,
        renotify: data.renotify,
        data: {
            url: data.url,
            ...data.data
        },
        silent: false
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
            .then(() => {
                // Play notification sound by sending message to clients
                return clients.matchAll({ type: 'window', includeUncontrolled: true })
                    .then((clientList) => {
                        clientList.forEach((client) => {
                            client.postMessage({
                                type: 'PLAY_NOTIFICATION_SOUND',
                                sound: NOTIFICATION_SOUND
                            });
                        });
                    });
            })
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event.action);
    
    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    const urlToOpen = event.notification.data?.url || '/admin';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.focus();
                        client.navigate(urlToOpen);
                        return;
                    }
                }
                // Open new window if none exists
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Notification close handler
self.addEventListener('notificationclose', (event) => {
    console.log('[SW] Notification closed');
});

// Message handler (for manual triggers)
self.addEventListener('message', (event) => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
