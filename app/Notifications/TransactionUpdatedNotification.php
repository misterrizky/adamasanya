<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model; // Untuk Rent atau Sale

class TransactionUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     *
     * @param Model $transaction
     */
    public function __construct(Model $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Tambah 'webpush' jika setup
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->transaction instanceof \App\Models\Transaction\Rent ? 'Rental' : 'Pembelian';
        $status = $this->transaction->status['text']; // Dari accessor di model

        return (new MailMessage)
            ->subject("Update Status Transaksi {$type} Anda: {$this->transaction->code}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Transaksi {$type} dengan kode **{$this->transaction->code}** telah diupdate.")
            ->line("Status terbaru: **{$status}**")
            ->line("Total: Rp " . number_format($this->transaction->total_price))
            ->action('Lihat Detail Transaksi', route('consumer.transaction.view', ['code' => $this->transaction->code]))
            ->line('Terima kasih telah menggunakan platform kami. Jika ada pertanyaan, hubungi support.')
            ->salutation('Salam hangat, Tim ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification (for database channel).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'transaction_code' => $this->transaction->code,
            'transaction_type' => $this->transaction instanceof \App\Models\Transaction\Rent ? 'rent' : 'sale',
            'status' => $this->transaction->status['text'],
            'message' => "Transaksi Anda ({$this->transaction->code}) telah diupdate menjadi: {$this->transaction->status['text']}",
        ];
    }

    // Optional: Tambah toWebPush jika pakai webpush
    // public function toWebPush($notifiable, $notification)
    // {
    //     return (new WebPushMessage)
    //         ->title('Update Transaksi')
    //         ->body("Status transaksi {$this->transaction->code} berubah menjadi {$this->transaction->status['text']}. Cek sekarang!")
    //         ->action('Lihat Detail', route('consumer.transaction.view', ['code' => $this->transaction->code]));
    // }
}