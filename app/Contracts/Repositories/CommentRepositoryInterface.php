<?php

namespace App\Contracts\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    public function findById(int $id): ?Comment;

    public function getByTicket(int $ticketId): Collection;

    public function create(array $data): Comment;

    public function update(Comment $comment, array $data): Comment;

    public function delete(Comment $comment): bool;
}
