<?php

namespace App\Http\Controllers;

use App\Enums\GoalType;
use App\Http\Requests\StoreLogEntryRequest;
use App\Models\Goal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LogEntryController extends Controller
{
    public function store(StoreLogEntryRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $validated = $request->validated();

        // Create log entry
        $goal->logEntries()->create([
            'user_id' => Auth::id(),
            'value' => $validated['value'],
            'note' => $validated['note'] ?? null,
            'created_at' => now(),
        ]);

        // Update goal's current value based on type
        $this->updateGoalProgress($goal, (float) $validated['value']);

        return back()->with('success', 'Progress logged.');
    }

    private function updateGoalProgress(Goal $goal, float $value): void
    {
        match ($goal->type) {
            GoalType::Counter, GoalType::Money => $goal->update([
                'current_value' => $goal->current_value + $value,
            ]),
            GoalType::YesNo => $goal->update([
                'is_completed' => $value > 0,
                'completed_at' => $value > 0 ? now() : null,
            ]),
            GoalType::Percentage => $goal->update([
                'current_value' => max(0, min(100, $value)),
            ]),
        };

        // Check if goal is now completed
        if ($goal->fresh()->progress >= 100 && !$goal->is_completed) {
            $goal->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
    }
}
