<?php

namespace App\Services;

use App\Contracts\Repositories\AttachmentRepositoryInterface;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    public function __construct(
        private readonly AttachmentRepositoryInterface $attachmentRepository,
    ) {}

    public function listByTicket(int $ticketId): Collection
    {
        return $this->attachmentRepository->getByTicket($ticketId);
    }

    public function find(int $id): ?Attachment
    {
        return $this->attachmentRepository->findById($id);
    }

    public function upload(User $user, int $ticketId, UploadedFile $file): Attachment
    {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs("tickets/{$ticketId}", $filename, 'local');

        return $this->attachmentRepository->create([
            'ticket_id' => $ticketId,
            'user_id' => $user->id,
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize(),
        ]);
    }

    public function delete(Attachment $attachment): bool
    {
        Storage::disk('local')->delete($attachment->path);

        return $this->attachmentRepository->delete($attachment);
    }
}
