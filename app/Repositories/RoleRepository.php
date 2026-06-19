<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function findBySlug(string $slug): ?Role
    {
        return Role::with('permissions')->where('slug', $slug)->first();
    }

    public function all(): Collection
    {
        return Role::with('permissions')->orderBy('name')->get();
    }
}
