<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRestoredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $adminName;

    public function __construct($adminName)
    {
        $this->adminName = $adminName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Akun Anda Telah Dipulihkan')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami senang memberitahukan bahwa akun Anda telah dipulihkan oleh admin.')
            ->line('Detail:')
            ->line('- Dipulihkan oleh: ' . $this->adminName)
            ->line('- Tanggal: ' . now()->format('d F Y H:i'))
            ->line('Anda sekarang dapat:')
            ->line('- Login seperti biasa')
            ->line('- Mengakses semua fitur')
            ->action('Login Sekarang', route('login'))
            ->line('Jika Anda mengalami kesulitan, silakan hubungi tim support kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'AKUN DIPULIHKAN',
            'message' => 'Akun Anda telah aktif kembali. Selamat datang kembali!',
            'admin' => $this->adminName,
            'icon' => '✅',
            'action' => [
                'text' => 'Login Sekarang',
                'url' => route('login')
            ]
        ];
    }
}
