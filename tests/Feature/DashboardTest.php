<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_admin_can_view_dashboard(): void
    {
        $admin = $this->createAdmin();
        Ticket::factory()->count(2)->create();

        $response = $this->actingAsUser($admin)->getJson('/api/v1/dashboard');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['total_tickets', 'by_status', 'by_priority', 'agents']]);
    }

    public function test_customer_can_view_dashboard(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAsUser($customer)->getJson('/api/v1/dashboard');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['my_tickets', 'by_status']]);
    }
}
