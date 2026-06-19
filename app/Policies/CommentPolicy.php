<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('comments.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('comments.create');
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->hasPermission('comments.update') && ($user->isAdmin() || $user->id === $comment->user_id);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasPermission('comments.delete') && ($user->isAdmin() || $user->id === $comment->user_id);
    }
}
