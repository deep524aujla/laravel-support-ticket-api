<?php

namespace App\Services;

use App\Contracts\Repositories\TicketRepositoryInterface;
use App\Enums\TicketStatus;
use App\Jobs\ProcessTicketStatusChange;
use App\Jobs\SendTicketCreatedNotification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly CacheService $cacheService,
    ) {}

    public function list(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if ($user->isCustomer()) {
            $filters['user_id'] = $user->id;
        } elseif ($user->isAgent()) {
            $filters['assigned_to'] = $user->id;
        }

        return $this->ticketRepository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Ticket
    {
        return $this->ticketRepository->findById($id);
    }

    public function create(User $user, array $data): Ticket
    {
        $data['user_id'] = $user->id;
        $data['status'] = TicketStatus::Open->value;

        $ticket = $this->ticketRepository->create($data);

        SendTicketCreatedNotification::dispatch($ticket);
        $this->cacheService->forgetDashboard();

        return $ticket;
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $previousStatus = $ticket->status;

        if (isset($data['status']) && in_array($data['status'], [TicketStatus::Resolved->value, TicketStatus::Closed->value], true)) {
            $data['resolved_at'] = now();
        }

        $ticket = $this->ticketRepository->update($ticket, $data);

        if ($previousStatus !== $ticket->status) {
            ProcessTicketStatusChange::dispatch($ticket, $previousStatus->value);
        }

        $this->cacheService->forgetDashboard();

        return $ticket;
    }

    public function delete(Ticket $ticket): bool
    {
        $deleted = $this->ticketRepository->delete($ticket);
        $this->cacheService->forgetDashboard();

        return $deleted;
    }
}
