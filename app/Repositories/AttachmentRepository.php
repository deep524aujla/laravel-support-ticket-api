<?php

namespace App\Repositories;

use App\Contracts\Repositories\AttachmentRepositoryInterface;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Collection;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function findById(int $id): ?Attachment
    {
        return Attachment::with(['user', 'ticket'])->find($id);
    }

    public function getByTicket(int $ticketId): Collection
    {
        return Attachment::with('user')
            ->where('ticket_id', $ticketId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $data): Attachment
    {
        return Attachment::create($data)->load('user');
    }

    public function delete(Attachment $attachment): bool
    {
        return (bool) $attachment->delete();
    }
}
