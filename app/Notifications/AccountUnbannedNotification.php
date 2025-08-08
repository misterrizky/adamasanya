<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountUnbannedNotification extends Notification
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
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Akun Anda Telah Diaktifkan Kembali')
            ->greeting('Selamat! Akses Anda telah dipulihkan')
            ->line('Kami senang memberitahukan bahwa pembatasan pada akun Anda telah dicabut.')
            ->line('Anda sekarang dapat:')
            ->line('- Login seperti biasa')
            ->line('- Mengakses semua fitur')
            ->line('- Berinteraksi dengan komunitas')
            ->action('Login Sekarang', route('login'))
            ->line('Harap patuhi Syarat & Ketentuan kami untuk menghindari pembatasan di masa depan.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'AKUN DIAKTIFKAN KEMBALI',
            'message' => 'Pembatasan akun Anda telah dicabut. Selamat datang kembali!',
            'severity' => 'success',
            'icon' => '✅',
            'action' => [
                'text' => 'Login Sekarang',
                'url' => route('login')
            ]
        ];
    }
}
