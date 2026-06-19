<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function test_customer_can_add_comment_to_own_ticket(): void
    {
        $customer = $this->createCustomer();
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $response = $this->actingAsUser($customer)->postJson("/api/v1/tickets/{$ticket->id}/comments", [
            'body' => 'Any update on this?',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.body', 'Any update on this?');
    }

    public function test_customer_can_list_comments_on_own_ticket(): void
    {
        $customer = $this->createCustomer();
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);

        $this->actingAsUser($customer)->postJson("/api/v1/tickets/{$ticket->id}/comments", [
            'body' => 'First comment',
        ]);

        $response = $this->actingAsUser($customer)->getJson("/api/v1/tickets/{$ticket->id}/comments");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
