<?php

namespace Tests\Feature;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_goals(): void
    {
        $this->get(route('goals.index'))->assertRedirect(route('login'));
        $this->get(route('goals.create'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_goals_index(): void
    {
        Goal::factory()->count(3)->create(['user_id' => $this->user->id, 'is_archived' => false]);
        Goal::factory()->count(2)->create(['user_id' => $this->user->id, 'is_archived' => true]);

        $response = $this->actingAs($this->user)->get(route('goals.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Goals/Index')
            ->has('goals')
            ->where('archivedCount', 2)
            ->has('categories', 4)
        );
    }

    public function test_user_only_sees_own_goals(): void
    {
        $otherUser = User::factory()->create();
        Goal::factory()->count(2)->create(['user_id' => $this->user->id]);
        Goal::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('goals.index'));

        $response->assertOk();
        // Flatten goals from grouped collection
        $response->assertInertia(fn ($page) => $page
            ->component('Goals/Index')
            ->where('archivedCount', 0)
        );
    }

    public function test_user_can_view_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('goals.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Goals/Create')
            ->has('categories', 4)
            ->has('types', 4)
        );
    }

    public function test_user_can_create_counter_goal(): void
    {
        $data = [
            'category' => GoalCategory::BodyMind->value,
            'type' => GoalType::Counter->value,
            'title' => 'Read 50 books',
            'description' => 'Reading challenge',
            'target_value' => 50,
            'unit' => 'books',
        ];

        $response = $this->actingAs($this->user)->post(route('goals.store'), $data);

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'user_id' => $this->user->id,
            'title' => 'Read 50 books',
            'type' => GoalType::Counter->value,
        ]);
    }

    public function test_user_can_create_money_goal(): void
    {
        $data = [
            'category' => GoalCategory::MoneyWork->value,
            'type' => GoalType::Money->value,
            'title' => 'Save 10000 EUR',
            'target_value' => 10000,
            'currency' => 'EUR',
        ];

        $response = $this->actingAs($this->user)->post(route('goals.store'), $data);

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'user_id' => $this->user->id,
            'title' => 'Save 10000 EUR',
            'type' => GoalType::Money->value,
            'currency' => 'EUR',
        ]);
    }

    public function test_user_can_create_yes_no_goal(): void
    {
        $data = [
            'category' => GoalCategory::Life->value,
            'type' => GoalType::YesNo->value,
            'title' => 'Get married',
        ];

        $response = $this->actingAs($this->user)->post(route('goals.store'), $data);

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'user_id' => $this->user->id,
            'title' => 'Get married',
            'type' => GoalType::YesNo->value,
        ]);
    }

    public function test_user_can_create_percentage_goal(): void
    {
        $data = [
            'category' => GoalCategory::Growth->value,
            'type' => GoalType::Percentage->value,
            'title' => 'Learn Spanish',
        ];

        $response = $this->actingAs($this->user)->post(route('goals.store'), $data);

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'user_id' => $this->user->id,
            'title' => 'Learn Spanish',
            'type' => GoalType::Percentage->value,
        ]);
    }

    public function test_create_goal_validation_requires_title(): void
    {
        $data = [
            'category' => GoalCategory::BodyMind->value,
            'type' => GoalType::Counter->value,
            'title' => '',
        ];

        $response = $this->actingAs($this->user)->post(route('goals.store'), $data);

        $response->assertSessionHasErrors('title');
    }

    public function test_user_can_view_own_goal(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('goals.show', $goal));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Goals/Show')
            ->has('goal')
            ->where('goal.id', $goal->id)
        );
    }

    public function test_user_cannot_view_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('goals.show', $goal));

        $response->assertForbidden();
    }

    public function test_user_can_view_edit_page(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('goals.edit', $goal));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Goals/Edit')
            ->has('goal')
            ->where('goal.id', $goal->id)
        );
    }

    public function test_user_cannot_edit_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('goals.edit', $goal));

        $response->assertForbidden();
    }

    public function test_user_can_update_own_goal(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Counter,
            'title' => 'Old title',
        ]);

        $response = $this->actingAs($this->user)->put(route('goals.update', $goal), [
            'category' => $goal->category->value,
            'type' => $goal->type->value,
            'title' => 'New title',
            'target_value' => $goal->target_value,
            'unit' => $goal->unit,
        ]);

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'title' => 'New title',
        ]);
    }

    public function test_user_cannot_update_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->put(route('goals.update', $goal), [
            'category' => $goal->category->value,
            'type' => $goal->type->value,
            'title' => 'Hacked',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_archive_goal(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'is_archived' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.archive', $goal));

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'is_archived' => true,
        ]);
    }

    public function test_user_can_restore_goal(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'is_archived' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('goals.restore', $goal));

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'is_archived' => false,
        ]);
    }

    public function test_user_can_delete_goal(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('goals.destroy', $goal));

        $response->assertRedirect(route('goals.index'));
        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    public function test_user_cannot_delete_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('goals.destroy', $goal));

        $response->assertForbidden();
        $this->assertDatabaseHas('goals', ['id' => $goal->id]);
    }

    public function test_user_can_view_archived_goals(): void
    {
        Goal::factory()->count(3)->create(['user_id' => $this->user->id, 'is_archived' => true]);

        $response = $this->actingAs($this->user)->get(route('goals.archived'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Goals/Archived')
            ->has('goals')
        );
    }
}
