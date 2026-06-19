<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Users', 'slug' => 'users.view'],
            ['name' => 'Create Users', 'slug' => 'users.create'],
            ['name' => 'Update Users', 'slug' => 'users.update'],
            ['name' => 'Delete Users', 'slug' => 'users.delete'],
            ['name' => 'View Tickets', 'slug' => 'tickets.view'],
            ['name' => 'Create Tickets', 'slug' => 'tickets.create'],
            ['name' => 'Update Tickets', 'slug' => 'tickets.update'],
            ['name' => 'Delete Tickets', 'slug' => 'tickets.delete'],
            ['name' => 'View Comments', 'slug' => 'comments.view'],
            ['name' => 'Create Comments', 'slug' => 'comments.create'],
            ['name' => 'Update Comments', 'slug' => 'comments.update'],
            ['name' => 'Delete Comments', 'slug' => 'comments.delete'],
            ['name' => 'Create Attachments', 'slug' => 'attachments.create'],
            ['name' => 'Delete Attachments', 'slug' => 'attachments.delete'],
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $roles = [
            'admin' => [
                'name' => 'Administrator',
                'description' => 'Full system access',
                'permissions' => array_column($permissions, 'slug'),
            ],
            'agent' => [
                'name' => 'Support Agent',
                'description' => 'Handles assigned support tickets',
                'permissions' => [
                    'tickets.view', 'tickets.update',
                    'comments.view', 'comments.create', 'comments.update', 'comments.delete',
                    'attachments.create', 'attachments.delete',
                    'dashboard.view',
                ],
            ],
            'customer' => [
                'name' => 'Customer',
                'description' => 'Creates and manages own tickets',
                'permissions' => [
                    'tickets.view', 'tickets.create', 'tickets.update', 'tickets.delete',
                    'comments.view', 'comments.create', 'comments.update', 'comments.delete',
                    'attachments.create', 'attachments.delete',
                    'dashboard.view',
                ],
            ],
        ];

        foreach ($roles as $slug => $data) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]
            );

            $permissionIds = Permission::whereIn('slug', $data['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
