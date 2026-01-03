<?php

namespace App\Models;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'type',
        'title',
        'description',
        'target_value',
        'initial_value',
        'current_value',
        'unit',
        'currency',
        'is_completed',
        'completed_at',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'category' => GoalCategory::class,
            'type' => GoalType::class,
            'target_value' => 'decimal:2',
            'initial_value' => 'decimal:2',
            'current_value' => 'decimal:2',
            'is_completed' => 'boolean',
            'is_archived' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logEntries(): HasMany
    {
        return $this->hasMany(LogEntry::class);
    }

    public function getProgressAttribute(): float
    {
        return match ($this->type) {
            GoalType::Counter, GoalType::Money => $this->target_value > 0
                ? min(100, ($this->current_value / $this->target_value) * 100)
                : 0,
            GoalType::YesNo => $this->is_completed ? 100 : 0,
            GoalType::Percentage => (float) $this->current_value,
            GoalType::Number => $this->calculateNumberProgress(),
        };
    }

    private function calculateNumberProgress(): float
    {
        $initial = (float) $this->initial_value;
        $target = (float) $this->target_value;
        $current = (float) $this->current_value;

        $range = $target - $initial;
        if ($range == 0) {
            return $current == $target ? 100 : 0;
        }

        $progress = (($current - $initial) / $range) * 100;
        return min(100, max(0, $progress));
    }
}
