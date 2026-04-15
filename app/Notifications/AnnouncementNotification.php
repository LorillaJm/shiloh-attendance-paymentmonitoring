<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Announcement $announcement
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->announcement->title . ' - Shiloh Learning Center')
            ->greeting('Hello!')
            ->line($this->announcement->message)
            ->action('View Dashboard', url('/login'))
            ->line('Thank you for being part of Shiloh Learning and Development Center!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'created_by' => $this->announcement->creator->name,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'created_by' => $this->announcement->creator->name,
            'created_at' => now()->toISOString(),
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
        ];
    }
}
