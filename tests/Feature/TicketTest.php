<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Tests\TestCase;

class TicketTest extends TestCase
{
    public function test_customer_can_create_ticket(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAsUser($customer)->postJson('/api/v1/tickets', [
            'title' => 'Login issue',
            'description' => 'Cannot login to my account',
            'priority' => 'high',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Login issue')
            ->assertJsonPath('data.status', 'open');
    }

    public function test_customer_can_view_own_tickets(): void
    {
        $customer = $this->createCustomer();
        Ticket::factory()->create(['user_id' => $customer->id]);
        Ticket::factory()->create();

        $response = $this->actingAsUser($customer)->getJson('/api/v1/tickets');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_customer_cannot_view_other_users_ticket(): void
    {
        $customer = $this->createCustomer();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAsUser($customer)->getJson("/api/v1/tickets/{$ticket->id}");

        $response->assertForbidden();
    }

    public function test_admin_can_view_all_tickets(): void
    {
        $admin = $this->createAdmin();
        Ticket::factory()->count(3)->create();

        $response = $this->actingAsUser($admin)->getJson('/api/v1/tickets');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_admin_can_assign_ticket_to_agent(): void
    {
        $admin = $this->createAdmin();
        $agent = $this->createAgent();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAsUser($admin)->putJson("/api/v1/tickets/{$ticket->id}", [
            'assigned_to' => $agent->id,
            'status' => 'in_progress',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.assignee.id', $agent->id);
    }

    public function test_agent_can_view_assigned_ticket(): void
    {
        $agent = $this->createAgent();
        $ticket = Ticket::factory()->assigned($agent)->create(['user_id' => $this->createCustomer()->id]);

        $response = $this->actingAsUser($agent)->getJson("/api/v1/tickets/{$ticket->id}");

        $response->assertOk();
    }
}
