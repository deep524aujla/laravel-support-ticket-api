<?php

namespace Tests\Unit;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function test_ticket_status_values(): void
    {
        $this->assertContains('open', TicketStatus::values());
        $this->assertContains('closed', TicketStatus::values());
    }

    public function test_ticket_priority_values(): void
    {
        $this->assertContains('urgent', TicketPriority::values());
        $this->assertContains('low', TicketPriority::values());
    }
}
