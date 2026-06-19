<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        $filename = fake()->uuid().'.txt';

        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'original_name' => 'document.txt',
            'filename' => $filename,
            'path' => 'tickets/1/'.$filename,
            'mime_type' => 'text/plain',
            'size' => fake()->numberBetween(100, 5000),
        ];
    }
}
