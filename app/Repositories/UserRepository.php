<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::with('role.permissions')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::with('role.permissions')->where('email', $email)->first();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('role')->orderByDesc('created_at');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create($data)->load('role');
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh('role');
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function getAgents(): Collection
    {
        return User::with('role')
            ->whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'agent']))
            ->where('is_active', true)
            ->get();
    }
}
