<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTicketCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
    ) {}

    public function handle(): void
    {
        Log::info('Ticket created notification queued', [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'user_id' => $this->ticket->user_id,
        ]);
    }
}
