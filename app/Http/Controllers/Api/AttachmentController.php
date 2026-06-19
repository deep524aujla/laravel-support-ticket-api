<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
    ) {}

    public function index(Ticket $ticket): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Attachment::class, $ticket]);

        return AttachmentResource::collection($this->attachmentService->listByTicket($ticket->id));
    }

    public function store(StoreAttachmentRequest $request, Ticket $ticket): JsonResponse
    {
        $attachment = $this->attachmentService->upload(
            $request->user(),
            $ticket->id,
            $request->file('file')
        );

        return (new AttachmentResource($attachment))->response()->setStatusCode(201);
    }

    public function download(Ticket $ticket, Attachment $attachment): StreamedResponse
    {
        $this->authorize('viewAny', [Attachment::class, $ticket]);

        abort_unless($attachment->ticket_id === $ticket->id, 404);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }

    public function destroy(Ticket $ticket, Attachment $attachment): JsonResponse
    {
        abort_unless($attachment->ticket_id === $ticket->id, 404);

        $this->authorize('delete', $attachment);

        $this->attachmentService->delete($attachment);

        return response()->json(['message' => 'Attachment deleted successfully.']);
    }
}
