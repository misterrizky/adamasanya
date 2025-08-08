<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class OnboardingCompletedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Onboarding Selesai')
            ->icon('/media/icons/icon-192x192.png')
            ->body('Mohon menunggu verifikasi 1x24 jam')
            ->action('Lihat Beranda', url('/home'))
            ->data(['url' => url('/home')]);
    }
}