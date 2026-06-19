<?php

namespace App\Services;

use App\Contracts\Repositories\TicketRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const CACHE_KEY = 'dashboard.stats';

    private const CACHE_TTL = 300;

    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly CacheService $cacheService,
    ) {}

    public function getStats(User $user): array
    {
        $cacheKey = $this->cacheService->dashboardKey($user);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            if ($user->isAdmin()) {
                return $this->adminStats();
            }

            if ($user->isAgent()) {
                return $this->agentStats($user);
            }

            return $this->customerStats($user);
        });
    }

    private function adminStats(): array
    {
        return [
            'total_tickets' => $this->ticketRepository->countTotal(),
            'by_status' => $this->ticketRepository->countByStatus(),
            'by_priority' => $this->ticketRepository->countByPriority(),
            'agents' => $this->userRepository->getAgents()->count(),
        ];
    }

    private function agentStats(User $user): array
    {
        $filters = ['assigned_to' => $user->id];

        return [
            'assigned_tickets' => $this->ticketRepository->paginate($filters, 1)->total(),
            'by_status' => $this->ticketRepository->countByStatus(),
        ];
    }

    private function customerStats(User $user): array
    {
        $filters = ['user_id' => $user->id];

        return [
            'my_tickets' => $this->ticketRepository->paginate($filters, 1)->total(),
            'by_status' => $this->ticketRepository->countByStatus(),
        ];
    }
}
