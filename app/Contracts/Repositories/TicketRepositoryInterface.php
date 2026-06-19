<?php

namespace App\Contracts\Repositories;

use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TicketRepositoryInterface
{
    public function findById(int $id): ?Ticket;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Ticket;

    public function update(Ticket $ticket, array $data): Ticket;

    public function delete(Ticket $ticket): bool;

    public function countByStatus(): array;

    public function countByPriority(): array;

    public function countTotal(): int;
}
