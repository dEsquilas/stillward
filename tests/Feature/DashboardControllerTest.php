<?php

namespace Tests\Feature;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Models\Goal;
use App\Models\LogEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats')
            ->has('categoryStats')
            ->has('recentActivity')
            ->has('weeklyProgress')
            ->has('categories')
        );
    }

    public function test_dashboard_shows_correct_stats(): void
    {
        // Create 5 goals: 2 completed, 3 in progress
        Goal::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
            'is_archived' => false,
        ]);
        Goal::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
            'is_archived' => false,
        ]);
        // Archived goals should not count
        Goal::factory()->create([
            'user_id' => $this->user->id,
            'is_archived' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('stats.totalGoals', 5)
            ->where('stats.completedGoals', 2)
        );
    }

    public function test_dashboard_only_shows_users_own_data(): void
    {
        $otherUser = User::factory()->create();
        Goal::factory()->count(3)->create(['user_id' => $this->user->id, 'is_archived' => false]);
        Goal::factory()->count(5)->create(['user_id' => $otherUser->id, 'is_archived' => false]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('stats.totalGoals', 3)
        );
    }

    public function test_dashboard_shows_category_stats(): void
    {
        Goal::factory()->create([
            'user_id' => $this->user->id,
            'category' => GoalCategory::BodyMind,
            'is_archived' => false,
        ]);
        Goal::factory()->create([
            'user_id' => $this->user->id,
            'category' => GoalCategory::Growth,
            'is_archived' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('categoryStats', 2)
        );
    }

    public function test_dashboard_shows_recent_activity(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Counter,
        ]);
        LogEntry::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'goal_id' => $goal->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('recentActivity', 5)
        );
    }

    public function test_dashboard_limits_recent_activity_to_10(): void
    {
        $goal = Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Counter,
        ]);
        LogEntry::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'goal_id' => $goal->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('recentActivity', 10)
        );
    }

    public function test_dashboard_shows_weekly_progress(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('weeklyProgress', 8)
        );
    }

    public function test_dashboard_calculates_year_progress(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('stats.yearProgress')
            ->where('stats.yearProgress', fn ($value) => $value >= 0 && $value <= 100)
        );
    }

    public function test_dashboard_calculates_average_progress(): void
    {
        // Create goals with known progress
        Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::Percentage,
            'current_value' => 50,
            'is_archived' => false,
        ]);
        Goal::factory()->create([
            'user_id' => $this->user->id,
            'type' => GoalType::YesNo,
            'is_completed' => true,
            'is_archived' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        // Average of 50% and 100% = 75%
        $response->assertInertia(fn ($page) => $page
            ->where('stats.avgProgress', 75)
        );
    }
}
