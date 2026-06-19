<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Http\Resources\RoleResource;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function index(Request $request): DashboardResource
    {
        abort_unless($request->user()->hasPermission('dashboard.view'), 403);

        $stats = $this->dashboardService->getStats($request->user());

        return new DashboardResource($stats);
    }

    public function roles(): AnonymousResourceCollection
    {
        return RoleResource::collection($this->roleRepository->all());
    }
}
