<?php

namespace App\Notifications;

use App\Models\Promo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewPromoNotification extends Notification
{
    use Queueable;
    public $promo;
    /**
     * Create a new notification instance.
     */
    public function __construct(Promo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "New promotion: {$this->promo->name} ({$this->promo->discount_type == 'percentage' ? $this->promo->discount_value . '%' : 'Rp ' . number_format($this->promo->discount_value)})",
            'promo_id' => $this->promo->id,
        ];
    }
    public function toBroadcast(object $notifiable): array
    {
        return new BroadcastMessage([
            'message' => "New promotion: {$this->promo->name} ({$this->promo->discount_type == 'percentage' ? $this->promo->discount_value . '%' : 'Rp ' . number_format($this->promo->discount_value)})",
            'promo_id' => $this->promo->id,
        ]);
    }
}
