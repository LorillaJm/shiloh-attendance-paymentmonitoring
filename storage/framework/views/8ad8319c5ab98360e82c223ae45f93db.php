<?php echo app('Illuminate\Foundation\Vite')('resources/js/push-notifications.js'); ?>
<script>
    // Auto-request push notification permission after login
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(async () => {
            if (window.pushManager && Notification.permission === "default") {
                // Show a subtle prompt to enable notifications
                const shouldAsk = localStorage.getItem("push_prompt_dismissed") !== "true";
                if (shouldAsk) {
                    const banner = document.createElement("div");
                    banner.id = "push-notification-banner";
                    banner.innerHTML = `
                        <div style="position:fixed;bottom:20px;right:20px;z-index:9999;background:linear-gradient(135deg,rgba(0,113,227,0.95),rgba(51,153,255,0.95));color:white;padding:16px 20px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);max-width:360px;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);animation:slideUp 0.4s ease-out">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div style="font-size:24px;">🔔</div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;margin-bottom:4px;">Enable Notifications</div>
                                    <div style="font-size:13px;opacity:0.9;margin-bottom:12px;">Get instant alerts for announcements and updates</div>
                                    <div style="display:flex;gap:8px;">
                                        <button onclick="enablePushNotifications()" style="background:white;color:#0071E3;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px;">Enable</button>
                                        <button onclick="dismissPushBanner()" style="background:rgba(255,255,255,0.2);color:white;border:none;padding:8px 16px;border-radius:8px;font-weight:500;cursor:pointer;font-size:13px;">Later</button>
                                    </div>
                                </div>
                                <button onclick="dismissPushBanner()" style="background:none;border:none;color:white;opacity:0.7;cursor:pointer;font-size:18px;padding:0;line-height:1;">&times;</button>
                            </div>
                        </div>
                        <style>@keyframes slideUp{from{transform:translateY(100px);opacity:0}to{transform:translateY(0);opacity:1}}</style>
                    `;
                    document.body.appendChild(banner);
                }
            }
        }, 3000);
    });

    window.enablePushNotifications = async function() {
        if (window.pushManager) {
            const success = await window.pushManager.subscribe();
            if (success) {
                document.getElementById("push-notification-banner")?.remove();
                // Show success toast
                const toast = document.createElement("div");
                toast.innerHTML = `<div style="position:fixed;bottom:20px;right:20px;z-index:9999;background:#10b981;color:white;padding:16px 24px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.15);font-weight:500;animation:slideUp 0.3s ease-out">✓ Notifications enabled!</div>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        }
    };

    window.dismissPushBanner = function() {
        document.getElementById("push-notification-banner")?.remove();
        localStorage.setItem("push_prompt_dismissed", "true");
    };
</script>
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/components/push-notification-scripts.blade.php ENDPATH**/ ?>