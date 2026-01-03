<?php

namespace App\Http\Requests;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(GoalCategory::class)],
            'type' => ['required', Rule::enum(GoalType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_value' => ['nullable', 'numeric'],
            'initial_value' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
            'increment' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
