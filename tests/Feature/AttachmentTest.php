<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    public function test_customer_can_upload_attachment_to_own_ticket(): void
    {
        Storage::fake('local');

        $customer = $this->createCustomer();
        $ticket = Ticket::factory()->create(['user_id' => $customer->id]);
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $response = $this->actingAsUser($customer)->postJson("/api/v1/tickets/{$ticket->id}/attachments", [
            'file' => $file,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.original_name', 'report.pdf');
    }
}
