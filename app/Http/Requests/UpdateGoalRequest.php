<?php

namespace App\Http\Requests;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['sometimes', Rule::enum(GoalCategory::class)],
            'type' => ['sometimes', Rule::enum(GoalType::class)],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_value' => ['nullable', 'numeric'],
            'initial_value' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
