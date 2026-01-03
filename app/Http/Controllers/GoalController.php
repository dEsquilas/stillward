<?php

namespace App\Http\Controllers;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GoalController extends Controller
{
    public function index(): Response
    {
        $goals = Auth::user()->goals()
            ->where('is_archived', false)
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $archivedCount = Auth::user()->goals()->where('is_archived', true)->count();

        return Inertia::render('Goals/Index', [
            'goals' => $goals,
            'archivedCount' => $archivedCount,
            'categories' => collect(GoalCategory::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'color' => $c->color(),
                'icon' => $c->icon(),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Goals/Create', [
            'categories' => collect(GoalCategory::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'color' => $c->color(),
            ]),
            'types' => collect(GoalType::cases())->map(fn ($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        Auth::user()->goals()->create($request->validated());

        return redirect()->route('goals.index')->with('success', 'Goal created.');
    }

    public function show(Goal $goal): Response
    {
        $this->authorize('view', $goal);

        $goal->load(['logEntries' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(20)]);

        return Inertia::render('Goals/Show', [
            'goal' => $goal,
            'categories' => collect(GoalCategory::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'color' => $c->color(),
            ]),
        ]);
    }

    public function edit(Goal $goal): Response
    {
        $this->authorize('update', $goal);

        return Inertia::render('Goals/Edit', [
            'goal' => $goal,
            'categories' => collect(GoalCategory::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'color' => $c->color(),
            ]),
            'types' => collect(GoalType::cases())->map(fn ($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return redirect()->route('goals.index')->with('success', 'Goal updated.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Goal deleted.');
    }

    public function archive(Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $goal->update(['is_archived' => true]);

        return redirect()->route('goals.index')->with('success', 'Goal archived.');
    }

    public function restore(Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $goal->update(['is_archived' => false]);

        return redirect()->route('goals.index')->with('success', 'Goal restored.');
    }

    public function archived(): Response
    {
        $goals = Auth::user()->goals()
            ->where('is_archived', true)
            ->orderBy('category')
            ->get()
            ->groupBy('category');

        return Inertia::render('Goals/Archived', [
            'goals' => $goals,
            'categories' => collect(GoalCategory::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'color' => $c->color(),
                'icon' => $c->icon(),
            ]),
        ]);
    }
}
