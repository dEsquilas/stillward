<?php

namespace Tests\Feature;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_log_counter_progress(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Counter,
            'target_value' => 100,
            'current_value' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('log_entries', [
            'goal_id' => $goal->id,
            'user_id' => $this->user->id,
            'value' => 5,
        ]);
        $this->assertEquals(15, $goal->fresh()->current_value);
    }

    public function test_user_can_log_money_progress(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Money,
            'target_value' => 10000,
            'current_value' => 500,
            'currency' => 'EUR',
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 250.50,
        ]);

        $response->assertRedirect();
        $this->assertEquals(750.50, $goal->fresh()->current_value);
    }

    public function test_user_can_toggle_yes_no_goal(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::YesNo,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 1,
        ]);

        $response->assertRedirect();
        $goal->refresh();
        $this->assertTrue($goal->is_completed);
        $this->assertNotNull($goal->completed_at);
    }

    public function test_user_can_untoggle_yes_no_goal(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::YesNo,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 0,
        ]);

        $response->assertRedirect();
        $goal->refresh();
        $this->assertFalse($goal->is_completed);
        $this->assertNull($goal->completed_at);
    }

    public function test_user_can_log_percentage_progress(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Percentage,
            'current_value' => 0,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 75,
        ]);

        $response->assertRedirect();
        $this->assertEquals(75, $goal->fresh()->current_value);
    }

    public function test_percentage_is_capped_at_100(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Percentage,
            'current_value' => 0,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 150,
        ]);

        $response->assertRedirect();
        $this->assertEquals(100, $goal->fresh()->current_value);
    }

    public function test_percentage_is_floored_at_0(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Percentage,
            'current_value' => 50,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => -20,
        ]);

        $response->assertRedirect();
        $this->assertEquals(0, $goal->fresh()->current_value);
    }

    public function test_counter_goal_auto_completes_when_target_reached(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Counter,
            'target_value' => 100,
            'current_value' => 95,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 10,
        ]);

        $response->assertRedirect();
        $goal->refresh();
        $this->assertTrue($goal->is_completed);
        $this->assertNotNull($goal->completed_at);
    }

    public function test_user_can_add_note_to_log_entry(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Counter,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 1,
            'note' => 'Great progress today!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('log_entries', [
            'goal_id' => $goal->id,
            'note' => 'Great progress today!',
        ]);
    }

    public function test_user_cannot_log_progress_on_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = Goal::factory()->create([
            'user_id' => $otherUser->id,
            'type' => GoalType::Counter,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), [
            'value' => 5,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('log_entries', [
            'goal_id' => $goal->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_guest_cannot_log_progress(): void
    {
        $goal = Goal::factory()->create();

        $response = $this->post(route('goals.log', $goal), ['value' => 5]);

        $response->assertRedirect(route('login'));
    }

    public function test_log_entry_requires_value(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->post(route('goals.log', $goal), []);

        $response->assertSessionHasErrors('value');
    }
}
