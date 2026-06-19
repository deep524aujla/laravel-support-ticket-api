<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_admin_can_list_users(): void
    {
        $admin = $this->createAdmin();
        $this->createCustomer();

        $response = $this->actingAsUser($admin)->getJson('/api/v1/users');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function test_customer_cannot_list_users(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAsUser($customer)->getJson('/api/v1/users');

        $response->assertForbidden();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createAdmin();
        $agentRole = \App\Models\Role::where('slug', 'agent')->first();

        $response = $this->actingAsUser($admin)->postJson('/api/v1/users', [
            'name' => 'New Agent',
            'email' => 'newagent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $agentRole->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'newagent@example.com');
    }
}
