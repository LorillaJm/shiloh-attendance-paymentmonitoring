<?php

namespace App\Observers;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementPushNotification;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Log;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     */
    public function created(Announcement $announcement): void
    {
        try {
            if (!$announcement->is_published) {
                return;
            }

            $this->sendPushNotifications($announcement);
        } catch (\Throwable $e) {
            Log::error('AnnouncementObserver created error: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Announcement "updated" event.
     */
    public function updated(Announcement $announcement): void
    {
        try {
            // Send push if announcement was just published
            if ($announcement->wasChanged('is_published') && $announcement->is_published) {
                $this->sendPushNotifications($announcement);
            }
        } catch (\Throwable $e) {
            Log::error('AnnouncementObserver updated error: ' . $e->getMessage());
        }
    }

    /**
     * Send push notifications to target users.
     */
    protected function sendPushNotifications(Announcement $announcement): void
    {
        try {
            // Skip if webpush is not properly configured
            if (!class_exists(\NotificationChannels\WebPush\WebPushChannel::class)) {
                Log::info('WebPush not available, skipping push notifications');
                return;
            }

            $users = $this->getTargetUsers($announcement);
            
            if ($users->isEmpty()) {
                Log::info('No target users for announcement push notification');
                return;
            }

            $notifiedCount = 0;
            foreach ($users as $user) {
                try {
                    // Check if user has push subscriptions
                    if (method_exists($user, 'pushSubscriptions') && $user->pushSubscriptions()->exists()) {
                        $user->notify(new AnnouncementPushNotification(
                            $announcement->title ?? 'New Announcement',
                            strip_tags($announcement->message ?? ''),
                            '/admin/announcements'
                        ));
                        $notifiedCount++;
                    }
                } catch (\Throwable $userError) {
                    Log::warning("Failed to notify user {$user->id}: " . $userError->getMessage());
                    continue;
                }
            }

            Log::info("Push notifications sent for announcement: {$announcement->title}", [
                'announcement_id' => $announcement->id,
                'users_notified' => $notifiedCount
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to send push notifications: {$e->getMessage()}", [
                'announcement_id' => $announcement->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get target users based on announcement audience.
     */
    protected function getTargetUsers(Announcement $announcement)
    {
        try {
            $query = User::query();

            switch ($announcement->target_audience) {
                case 'parents':
                    $query->where('role', UserRole::PARENT);
                    break;
                case 'admins':
                    $query->whereIn('role', [UserRole::ADMIN, UserRole::SUPERADMIN]);
                    break;
                case 'specific_user':
                    if ($announcement->target_user_id) {
                        $query->where('id', $announcement->target_user_id);
                    }
                    break;
                case 'all':
                default:
                    // All users
                    break;
            }

            return $query->get();
        } catch (\Throwable $e) {
            Log::error('getTargetUsers error: ' . $e->getMessage());
            return collect();
        }
    }
}
