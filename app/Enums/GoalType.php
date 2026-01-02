<?php

namespace App\Enums;

enum GoalType: string
{
    case Counter = 'counter';
    case YesNo = 'yes_no';
    case Percentage = 'percentage';
    case Money = 'money';

    public function label(): string
    {
        return match ($this) {
            self::Counter => 'Counter',
            self::YesNo => 'Yes/No',
            self::Percentage => 'Percentage',
            self::Money => 'Money',
        };
    }
}
