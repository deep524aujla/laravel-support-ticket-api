<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Ticket;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
    ) {}

    public function index(Ticket $ticket): AnonymousResourceCollection
    {
        $this->authorize('view', $ticket);
        $this->authorize('viewAny', Comment::class);

        return CommentResource::collection($this->commentService->listByTicket($ticket->id));
    }

    public function store(StoreCommentRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('view', $ticket);

        $comment = $this->commentService->create($request->user(), $ticket->id, $request->validated());

        return (new CommentResource($comment))->response()->setStatusCode(201);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $updated = $this->commentService->update($comment, $request->validated());

        return new CommentResource($updated);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->delete($comment);

        return response()->json(['message' => 'Comment deleted successfully.']);
    }
}
