<?php

namespace App\Repositories;

use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    public function findById(int $id): ?Comment
    {
        return Comment::with(['user', 'ticket'])->find($id);
    }

    public function getByTicket(int $ticketId): Collection
    {
        return Comment::with('user')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at')
            ->get();
    }

    public function create(array $data): Comment
    {
        return Comment::create($data)->load('user');
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);

        return $comment->fresh('user');
    }

    public function delete(Comment $comment): bool
    {
        return (bool) $comment->delete();
    }
}
