<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use App\Models\LogEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_entry_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $logEntry = LogEntry::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $logEntry->user);
        $this->assertEquals($user->id, $logEntry->user->id);
    }

    public function test_log_entry_belongs_to_goal(): void
    {
        $goal = Goal::factory()->create();
        $logEntry = LogEntry::factory()->create(['goal_id' => $goal->id]);

        $this->assertInstanceOf(Goal::class, $logEntry->goal);
        $this->assertEquals($goal->id, $logEntry->goal->id);
    }

    public function test_log_entry_casts_value_to_decimal(): void
    {
        $logEntry = LogEntry::factory()->create(['value' => 10.50]);

        $this->assertEquals('10.50', $logEntry->value);
    }

    public function test_log_entry_casts_created_at_to_datetime(): void
    {
        $logEntry = LogEntry::factory()->create(['created_at' => now()]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $logEntry->created_at);
    }

    public function test_log_entry_can_have_null_note(): void
    {
        $logEntry = LogEntry::factory()->create(['note' => null]);

        $this->assertNull($logEntry->note);
    }

    public function test_log_entry_can_have_note(): void
    {
        $logEntry = LogEntry::factory()->create(['note' => 'Test note']);

        $this->assertEquals('Test note', $logEntry->note);
    }
}
