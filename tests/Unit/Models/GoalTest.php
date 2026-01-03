<?php

namespace Tests\Unit\Models;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Models\Goal;
use App\Models\LogEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_goal_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $goal->user);
        $this->assertEquals($user->id, $goal->user->id);
    }

    public function test_goal_has_many_log_entries(): void
    {
        $goal = Goal::factory()->create();
        LogEntry::factory()->count(3)->create(['goal_id' => $goal->id, 'user_id' => $goal->user_id]);

        $this->assertCount(3, $goal->logEntries);
        $this->assertInstanceOf(LogEntry::class, $goal->logEntries->first());
    }

    public function test_counter_goal_progress_calculation(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::Counter,
            'target_value' => 100,
            'initial_value' => 0,
            'current_value' => 50,
        ]);

        $this->assertEquals(50.0, $goal->progress);
    }

    public function test_counter_goal_progress_caps_at_100(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::Counter,
            'target_value' => 100,
            'initial_value' => 0,
            'current_value' => 150,
        ]);

        $this->assertEquals(100.0, $goal->progress);
    }

    public function test_counter_goal_progress_zero_when_target_is_zero(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::Counter,
            'target_value' => 0,
            'initial_value' => 0,
            'current_value' => 50,
        ]);

        $this->assertEquals(0.0, $goal->progress);
    }

    public function test_counter_goal_progress_with_initial_value(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::Counter,
            'target_value' => 100,
            'initial_value' => 20,
            'current_value' => 60,
        ]);

        // Progress: (60 - 20) / (100 - 20) = 40/80 = 50%
        $this->assertEquals(50.0, $goal->progress);
    }

    public function test_money_goal_progress_calculation(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::Money,
            'target_value' => 10000,
            'current_value' => 2500,
            'currency' => 'EUR',
        ]);

        $this->assertEquals(25.0, $goal->progress);
    }

    public function test_yes_no_goal_progress_zero_when_not_completed(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::YesNo,
            'is_completed' => false,
        ]);

        $this->assertEquals(0.0, $goal->progress);
    }

    public function test_yes_no_goal_progress_100_when_completed(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::YesNo,
            'is_completed' => true,
        ]);

        $this->assertEquals(100.0, $goal->progress);
    }

    public function test_percentage_goal_progress_returns_current_value(): void
    {
        $goal = Goal::factory()->create([
            'type' => GoalType::Percentage,
            'current_value' => 75,
        ]);

        $this->assertEquals(75.0, $goal->progress);
    }

    public function test_goal_casts_category_to_enum(): void
    {
        $goal = Goal::factory()->create(['category' => GoalCategory::BodyMind]);

        $this->assertInstanceOf(GoalCategory::class, $goal->category);
        $this->assertEquals(GoalCategory::BodyMind, $goal->category);
    }

    public function test_goal_casts_type_to_enum(): void
    {
        $goal = Goal::factory()->create(['type' => GoalType::Counter]);

        $this->assertInstanceOf(GoalType::class, $goal->type);
        $this->assertEquals(GoalType::Counter, $goal->type);
    }

    public function test_goal_casts_booleans_correctly(): void
    {
        $goal = Goal::factory()->create([
            'is_completed' => true,
            'is_archived' => false,
        ]);

        $this->assertIsBool($goal->is_completed);
        $this->assertIsBool($goal->is_archived);
        $this->assertTrue($goal->is_completed);
        $this->assertFalse($goal->is_archived);
    }

    public function test_goal_casts_completed_at_to_datetime(): void
    {
        $goal = Goal::factory()->create([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $goal->completed_at);
    }
}
