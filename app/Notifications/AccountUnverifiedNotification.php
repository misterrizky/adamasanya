<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountUnverifiedNotification extends Notification
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
            ->subject('⚠️ Status Verifikasi Akun Anda Dicabut')
            ->greeting('Halo '.$notifiable->name)
            ->line('Kami ingin memberitahukan bahwa status verifikasi akun Anda telah dicabut oleh tim admin.')
            ->line('Alasan:')
            ->line('- Terdapat ketidaksesuaian data verifikasi')
            ->line('- Pelanggaran kebijakan verifikasi')
            ->line('Dampaknya:')
            ->line('- Akses ke fitur sewa dibatasi')
            ->line('- Beberapa transaksi mungkin terpengaruh')
            ->action('Lihat Kebijakan Verifikasi', url('/verification-policy'))
            ->line('Jika Anda merasa ini kesalahan, silakan ajukan permohonan verifikasi ulang.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'VERIFIKASI DICABUT',
            'message' => 'Status verifikasi akun Anda telah dicabut. Beberapa fitur mungkin tidak tersedia.',
            'severity' => 'warning',
            'icon' => '⚠️',
            // 'action' => [
            //     'text' => 'Ajukan Verifikasi Ulang',
            //     'url' => url('/re-verification')
            // ]
        ];
    }
}
