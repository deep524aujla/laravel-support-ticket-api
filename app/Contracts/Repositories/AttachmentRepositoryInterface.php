<?php

namespace App\Contracts\Repositories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Collection;

interface AttachmentRepositoryInterface
{
    public function findById(int $id): ?Attachment;

    public function getByTicket(int $ticketId): Collection;

    public function create(array $data): Attachment;

    public function delete(Attachment $attachment): bool;
}
