<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class AnnouncementPushNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected ?string $url;
    protected ?string $icon;

    public function __construct(string $title, string $body, ?string $url = null, ?string $icon = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url ?? '/admin';
        $this->icon = $icon ?? '/images/logo.png';
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon($this->icon)
            ->body($this->body)
            ->action('View', 'view_action')
            ->badge('/images/badge.png')
            ->dir('ltr')
            ->image('/images/notification-banner.png')
            ->lang('en')
            ->renotify()
            ->requireInteraction()
            ->tag('announcement')
            ->vibrate([200, 100, 200])
            ->data([
                'url' => $this->url,
                'id' => $notification->id ?? null,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
        ];
    }
}
