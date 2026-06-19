<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = $this->createCustomer();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['access_token', 'user']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = $this->createCustomer();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = $this->createCustomer();

        $response = $this->actingAsUser($user)->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->createCustomer();

        $response = $this->actingAsUser($user)->postJson('/api/v1/auth/logout');

        $response->assertOk();
    }

    public function test_inactive_user_cannot_access_api(): void
    {
        $user = User::factory()->customer()->inactive()->create();

        $response = $this->actingAsUser($user)->getJson('/api/v1/auth/me');

        $response->assertForbidden();
    }
}
