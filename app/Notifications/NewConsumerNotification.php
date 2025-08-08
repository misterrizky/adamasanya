<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use App\Models\User;

class NewConsumerNotification extends Notification
{
    use Queueable;

    protected $consumer;
    protected $branch;

    public function __construct(User $consumer, $branch)
    {
        $this->consumer = $consumer;
        $this->branch = $branch;
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Konsumen Baru Mendaftar')
            ->icon('/media/icons/icon-192x192.png')
            ->body("Konsumen {$this->consumer->name} baru saja mendaftar ke cabang {$this->branch->name}, mohon segera di verifikasi ya!")
            ->action('Lihat Konsumen', url('/admin/user/consumer'))
            ->data(['url' => url('/admin/user/consumer')]);
    }
}