<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;

class AttachmentPolicy
{
    public function viewAny(User $user, Ticket $ticket): bool
    {
        return $this->canAccessTicket($user, $ticket);
    }

    public function create(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission('attachments.create') && $this->canAccessTicket($user, $ticket);
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        if (! $user->hasPermission('attachments.delete')) {
            return false;
        }

        return $user->isAdmin() || $user->id === $attachment->user_id;
    }

    private function canAccessTicket(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAgent()) {
            return $ticket->assigned_to === $user->id;
        }

        return $ticket->user_id === $user->id;
    }
}
