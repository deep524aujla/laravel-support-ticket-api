<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTicketStatusChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $previousStatus,
    ) {}

    public function handle(CacheService $cacheService): void
    {
        Log::info('Ticket status changed', [
            'ticket_id' => $this->ticket->id,
            'from' => $this->previousStatus,
            'to' => $this->ticket->status->value,
        ]);

        $cacheService->forgetDashboard();
    }
}
