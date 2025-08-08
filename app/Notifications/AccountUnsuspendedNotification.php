<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountUnsuspendedNotification extends Notification
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
            ->subject('Akun Anda Telah Diaktifkan Kembali')
            ->line('Akun Anda telah di-unsuspend dan dapat digunakan kembali.')
            ->line('Silakan login untuk mengakses akun Anda.')
            ->action('Login Sekarang', route('login'))
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Akun Diaktifkan Kembali',
            'message' => 'Akun Anda telah di-unsuspend dan dapat digunakan normal kembali.',
            'severity' => 'success',
            'icon' => '✅',
            'action' => [
                'text' => 'Login Sekarang',
                'url' => route('login')
            ]
        ];
    }
}
