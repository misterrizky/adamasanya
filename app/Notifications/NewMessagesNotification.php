<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Messages\MailMessage;

class NewMessagesNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $message;
    protected $thread;

    public function __construct($message, $thread)
    {
        $this->message = $message;
        $this->thread = $thread;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
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
            'message_id' => $this->message->id,
            'thread_id' => $this->thread->id,
            'sender' => $this->message->user->name,
            'body' => $this->message->body,
            'url' => route('messages.show', $this->thread->id),
        ];
    }
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Pesan Baru dari ' . $this->message->user->name)
            ->icon('/media/icons/icon-192x192.png')
            ->body($this->message->body)
            ->action('Lihat Pesan', url('/home?thread_id=' . $this->thread->id))
            ->data(['url' => url('/home?thread_id=' . $this->thread->id), 'thread_id' => $this->thread->id]);
    }
}
