<?php

namespace App\Listeners;

use App\Events\TransactionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionUpdated $event)
    {
        $event->transaction->user->notify(new \App\Notifications\TransactionUpdatedNotification($event->transaction));
    }
}
