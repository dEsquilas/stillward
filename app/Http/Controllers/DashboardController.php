<?php

namespace App\Http\Controllers;

use App\Enums\GoalCategory;
use App\Models\LogEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $goals = $user->goals()->where('is_archived', false)->get();

        // Year progress (% of year elapsed)
        $now = Carbon::now();
        $startOfYear = Carbon::create($now->year, 1, 1);
        $endOfYear = Carbon::create($now->year, 12, 31);
        $yearProgress = round(($now->dayOfYear / $endOfYear->dayOfYear) * 100);

        // Overall stats
        $totalGoals = $goals->count();
        $completedGoals = $goals->where('is_completed', true)->count();

        // Calculate average progress across all goals
        $avgProgress = $totalGoals > 0
            ? round($goals->avg(fn ($g) => $this->calculateProgress($g)))
            : 0;

        // Progress by category
        $categoryStats = collect(GoalCategory::cases())->map(function ($category) use ($goals) {
            $categoryGoals = $goals->where('category', $category);
            $count = $categoryGoals->count();
            $completed = $categoryGoals->where('is_completed', true)->count();
            $avgProgress = $count > 0
                ? round($categoryGoals->avg(fn ($g) => $this->calculateProgress($g)))
                : 0;

            return [
                'value' => $category->value,
                'label' => $category->label(),
                'color' => $category->color(),
                'count' => $count,
                'completed' => $completed,
                'progress' => $avgProgress,
            ];
        })->filter(fn ($cat) => $cat['count'] > 0)->values();

        // Recent activity (last 10 log entries)
        $recentActivity = LogEntry::where('user_id', $user->id)
            ->with('goal:id,title,category,type')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($entry) => [
                'id' => $entry->id,
                'value' => $entry->value,
                'note' => $entry->note,
                'created_at' => $entry->created_at->toISOString(),
                'goal' => $entry->goal ? [
                    'id' => $entry->goal->id,
                    'title' => $entry->goal->title,
                    'category' => $entry->goal->category->value,
                ] : null,
            ]);

        // Weekly progress data for chart (last 8 weeks)
        $weeklyProgress = $this->getWeeklyProgressData($user, $goals);

        return Inertia::render('Dashboard', [
            'stats' => [
                'yearProgress' => $yearProgress,
                'totalGoals' => $totalGoals,
                'completedGoals' => $completedGoals,
                'avgProgress' => $avgProgress,
            ],
            'categoryStats' => $categoryStats,
            'recentActivity' => $recentActivity,
            'weeklyProgress' => $weeklyProgress,
            'categories' => collect(GoalCategory::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'color' => $c->color(),
            ]),
        ]);
    }

    private function calculateProgress($goal): float
    {
        return match ($goal->type->value) {
            'counter', 'money' => $goal->target_value > 0
                ? min(100, ($goal->current_value / $goal->target_value) * 100)
                : 0,
            'yes_no' => $goal->is_completed ? 100 : 0,
            'percentage' => (float) $goal->current_value,
            'number' => $this->calculateNumberProgress($goal),
            default => 0,
        };
    }

    private function calculateNumberProgress($goal): float
    {
        $initial = (float) $goal->initial_value;
        $target = (float) $goal->target_value;
        $current = (float) $goal->current_value;

        $range = $target - $initial;
        if ($range == 0) {
            return $current == $target ? 100 : 0;
        }

        $progress = (($current - $initial) / $range) * 100;
        return min(100, max(0, $progress));
    }

    private function getWeeklyProgressData($user, $goals): array
    {
        $weeks = [];
        $now = Carbon::now();

        for ($i = 7; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            // Count logs in this week
            $logsCount = LogEntry::where('user_id', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();

            $weeks[] = [
                'week' => $weekStart->format('M d'),
                'logs' => $logsCount,
            ];
        }

        return $weeks;
    }
}
