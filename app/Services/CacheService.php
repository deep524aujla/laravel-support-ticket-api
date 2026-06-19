<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function dashboardKey(User $user): string
    {
        return "dashboard.stats.{$user->id}.{$user->role?->slug}";
    }

    public function forgetDashboard(?User $user = null): void
    {
        if ($user) {
            Cache::forget($this->dashboardKey($user));

            return;
        }

        Cache::flush();
    }
}
