<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Messages\MailMessage;

class AccountBannedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚫 Akun Anda Telah Di-Ban Permanen')
            ->greeting('Maaf, akses Anda telah dibatasi')
            ->line('Akun Anda telah di-ban permanen karena pelanggaran berat terhadap ketentuan komunitas kami.')
            ->line('Detail:')
            ->line('- Tanggal Ban: '.now()->format('d F Y H:i'))
            ->line('- Alasan: Pelanggaran berulang terhadap Syarat & Ketentuan')
            ->line('Keputusan ini bersifat final dan tidak dapat diajukan banding.')
            ->line('Anda tidak dapat lagi mengakses sistem kami dengan akun ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'AKUN DIBAN PERMANEN',
            'message' => 'Akun Anda telah di-ban permanen karena pelanggaran berat. Tidak ada proses banding.',
            'severity' => 'critical',
            'icon' => '🚫'
        ];
    }
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('AKUN DIBAN PERMANEN!')
            ->icon('/approved-icon.png')
            ->body('Akun Anda telah di-ban permanen karena pelanggaran berat. Tidak ada proses banding.')
            // ->action('View account', 'view_account')
            ->options(['TTL' => 1000]);
            // ->data(['id' => $notification->id])
            // ->badge()
            // ->dir()
            // ->image()
            // ->lang()
            // ->renotify()
            // ->requireInteraction()
            // ->tag()
            // ->vibrate()
    }
}
