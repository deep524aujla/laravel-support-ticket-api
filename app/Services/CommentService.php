<?php

namespace App\Services;

use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
    ) {}

    public function listByTicket(int $ticketId): Collection
    {
        return $this->commentRepository->getByTicket($ticketId);
    }

    public function find(int $id): ?Comment
    {
        return $this->commentRepository->findById($id);
    }

    public function create(User $user, int $ticketId, array $data): Comment
    {
        return $this->commentRepository->create([
            'ticket_id' => $ticketId,
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);
    }

    public function update(Comment $comment, array $data): Comment
    {
        return $this->commentRepository->update($comment, $data);
    }

    public function delete(Comment $comment): bool
    {
        return $this->commentRepository->delete($comment);
    }
}
