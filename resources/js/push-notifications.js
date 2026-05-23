/**
 * Push Notification Manager
 * Handles Service Worker registration and push subscription
 */

class PushNotificationManager {
    constructor() {
        this.swRegistration = null;
        this.isSubscribed = false;
        this.vapidPublicKey = null;
        this.notificationSound = null;
        
        this.init();
    }

    async init() {
        // Check if push notifications are supported
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.warn('[Push] Push notifications not supported');
            return;
        }

        // Preload notification sound
        this.notificationSound = new Audio('/sounds/notification.mp3');
        this.notificationSound.volume = 0.5;

        // Listen for sound play messages from service worker
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'PLAY_NOTIFICATION_SOUND') {
                this.playNotificationSound();
            }
        });

        try {
            // Get VAPID public key
            const response = await fetch('/push/vapid-public-key');
            const data = await response.json();
            this.vapidPublicKey = data.publicKey;

            // Register service worker
            this.swRegistration = await navigator.serviceWorker.register('/sw.js');
            console.log('[Push] Service Worker registered');

            // Check current subscription status
            const subscription = await this.swRegistration.pushManager.getSubscription();
            this.isSubscribed = subscription !== null;

            if (this.isSubscribed) {
                console.log('[Push] Already subscribed');
            } else {
                // Auto-subscribe if permission already granted
                if (Notification.permission === 'granted') {
                    await this.subscribe();
                }
            }
        } catch (error) {
            console.error('[Push] Initialization error:', error);
        }
    }

    async requestPermission() {
        if (!('Notification' in window)) {
            console.warn('[Push] Notifications not supported');
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission === 'denied') {
            console.warn('[Push] Notifications blocked by user');
            return false;
        }

        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }

    async subscribe() {
        try {
            const permissionGranted = await this.requestPermission();
            if (!permissionGranted) {
                console.warn('[Push] Permission not granted');
                return false;
            }

            if (!this.vapidPublicKey) {
                console.error('[Push] VAPID public key not available');
                return false;
            }

            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            // Send subscription to server
            const response = await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(subscription)
            });

            if (response.ok) {
                this.isSubscribed = true;
                console.log('[Push] Subscribed successfully');
                return true;
            } else {
                console.error('[Push] Server subscription failed');
                return false;
            }
        } catch (error) {
            console.error('[Push] Subscribe error:', error);
            return false;
        }
    }

    async unsubscribe() {
        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            if (subscription) {
                await subscription.unsubscribe();

                // Notify server
                await fetch('/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ endpoint: subscription.endpoint })
                });

                this.isSubscribed = false;
                console.log('[Push] Unsubscribed successfully');
                return true;
            }
        } catch (error) {
            console.error('[Push] Unsubscribe error:', error);
            return false;
        }
    }

    playNotificationSound() {
        if (this.notificationSound) {
            this.notificationSound.currentTime = 0;
            this.notificationSound.play().catch(e => {
                console.warn('[Push] Could not play sound:', e);
            });
        }
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    getSubscriptionStatus() {
        return {
            supported: 'serviceWorker' in navigator && 'PushManager' in window,
            permission: Notification.permission,
            subscribed: this.isSubscribed
        };
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.pushManager = new PushNotificationManager();
});

// Export for use in other scripts
window.PushNotificationManager = PushNotificationManager;
