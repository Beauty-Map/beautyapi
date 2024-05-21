<?php

namespace App\Listeners;

use App\Events\UploadTicketFileEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UploadTicketFileListener
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
    public function handle(UploadTicketFileEvent $event): void
    {
        $ticket = $event->ticket;
    }
}
