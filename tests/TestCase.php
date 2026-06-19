<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    protected function actingAsUser(User $user): self
    {
        $token = JWTAuth::fromUser($user->load('role.permissions'));

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    protected function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function createAgent(): User
    {
        return User::factory()->agent()->create();
    }

    protected function createCustomer(): User
    {
        return User::factory()->customer()->create();
    }
}
