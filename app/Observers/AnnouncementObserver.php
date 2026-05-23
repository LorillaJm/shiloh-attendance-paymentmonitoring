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
        if (!$announcement->is_published) {
            return;
        }

        $this->sendPushNotifications($announcement);
    }

    /**
     * Handle the Announcement "updated" event.
     */
    public function updated(Announcement $announcement): void
    {
        // Send push if announcement was just published
        if ($announcement->wasChanged('is_published') && $announcement->is_published) {
            $this->sendPushNotifications($announcement);
        }
    }

    /**
     * Send push notifications to target users.
     */
    protected function sendPushNotifications(Announcement $announcement): void
    {
        try {
            $users = $this->getTargetUsers($announcement);
            
            foreach ($users as $user) {
                // Check if user has push subscriptions
                if ($user->pushSubscriptions()->exists()) {
                    $user->notify(new AnnouncementPushNotification(
                        $announcement->title,
                        strip_tags($announcement->message),
                        '/admin/announcements'
                    ));
                }
            }

            Log::info("Push notifications sent for announcement: {$announcement->title}", [
                'announcement_id' => $announcement->id,
                'users_notified' => $users->count()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send push notifications for announcement: {$e->getMessage()}", [
                'announcement_id' => $announcement->id
            ]);
        }
    }

    /**
     * Get target users based on announcement audience.
     */
    protected function getTargetUsers(Announcement $announcement)
    {
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
    }
}
