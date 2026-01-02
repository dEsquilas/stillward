<?php

namespace App\Enums;

enum GoalCategory: string
{
    case BodyMind = 'body_mind';
    case MoneyWork = 'money_work';
    case Growth = 'growth';
    case Life = 'life';

    public function label(): string
    {
        return match ($this) {
            self::BodyMind => 'Body & Mind',
            self::MoneyWork => 'Money & Work',
            self::Growth => 'Growth',
            self::Life => 'Life',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BodyMind => '#10B981',
            self::MoneyWork => '#F59E0B',
            self::Growth => '#8B5CF6',
            self::Life => '#EC4899',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BodyMind => 'heart',
            self::MoneyWork => 'currency-dollar',
            self::Growth => 'academic-cap',
            self::Life => 'sparkles',
        };
    }
}
