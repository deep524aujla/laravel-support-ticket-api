<?php

use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api', 'active'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('roles', [DashboardController::class, 'roles']);

        Route::apiResource('users', UserController::class);

        Route::apiResource('tickets', TicketController::class);

        Route::get('tickets/{ticket}/comments', [CommentController::class, 'index']);
        Route::post('tickets/{ticket}/comments', [CommentController::class, 'store']);
        Route::put('comments/{comment}', [CommentController::class, 'update']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

        Route::get('tickets/{ticket}/attachments', [AttachmentController::class, 'index']);
        Route::post('tickets/{ticket}/attachments', [AttachmentController::class, 'store']);
        Route::get('tickets/{ticket}/attachments/{attachment}/download', [AttachmentController::class, 'download']);
        Route::delete('tickets/{ticket}/attachments/{attachment}', [AttachmentController::class, 'destroy']);
    });
});
