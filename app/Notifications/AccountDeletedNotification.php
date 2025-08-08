<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletedNotification extends Notification
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
            ->subject('📌 Akun Anda Telah Dihapus')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Kami ingin mengkonfirmasi bahwa akun Anda telah berhasil dihapus dari sistem kami.')
            ->line('Detail akun yang dihapus:')
            ->line('- Nama: ' . $notifiable->name)
            ->line('- Email: ' . $notifiable->email)
            ->line('- Tanggal Penghapusan: ' . now()->format('d F Y H:i'))
            ->line('Konsekuensi:')
            ->line('- Semua data pribadi telah dihapus permanen')
            ->line('- Tidak dapat mengakses sistem dengan akun ini lagi')
            ->line('Jika ini bukan tindakan Anda, segera hubungi tim support kami.')
            ->action('Hubungi Support', url('/contact'))
            ->line('Terima kasih telah menjadi bagian dari komunitas kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'AKUN DIHAPUS',
            'message' => 'Kami ingin mengkonfirmasi bahwa akun Anda telah berhasil dihapus dari sistem kami.',
            'severity' => 'critical',
            'icon' => '📌',
            'action' => [
                'text' => 'Hubungi Support',
                'url' => route('contact')
            ]
        ];
    }
}
