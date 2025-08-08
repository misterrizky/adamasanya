<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountVerifiedNotification extends Notification
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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Akun Anda Telah Diverifikasi!')
            ->greeting('Selamat! Akun Anda resmi terverifikasi')
            ->line('Proses verifikasi akun Anda telah berhasil diselesaikan oleh tim admin.')
            ->line('Anda sekarang mendapatkan akses penuh ke semua fitur:')
            ->line('- Fitur sewa')
            ->line('- Transaksi lengkap')
            ->line('- Komunitas eksklusif')
            ->action('Jelajahi Sekarang', route('home'))
            ->line('Terima kasih telah bergabung dengan kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'AKUN TERVERIFIKASI',
            'message' => 'Selamat! Akun Anda telah diverifikasi oleh admin',
            'severity' => 'success',
            'icon' => '🎉',
            'action' => [
                'text' => 'Buka Home',
                'url' => route('home')
            ]
        ];
    }
}
