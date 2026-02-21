<?php

namespace Database\Seeders;

use App\Enums\GoalCategory;
use App\Enums\GoalType;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'desquilas@gmail.com'],
            ['name' => 'Dani', 'password' => bcrypt('password')],
        );

        $this->seedGoals($user);
    }

    private function seedGoals(User $user): void
    {
        $goals = [
            // Body & Mind
            ['category' => GoalCategory::BodyMind, 'type' => GoalType::Counter, 'title' => 'Entrenamientos', 'target_value' => 150, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'entrenamientos', 'increment' => 1],
            ['category' => GoalCategory::BodyMind, 'type' => GoalType::Counter, 'title' => 'Km andando', 'target_value' => 300, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'km', 'increment' => 3],
            ['category' => GoalCategory::BodyMind, 'type' => GoalType::Number, 'title' => 'Peso', 'target_value' => 100, 'initial_value' => 128.7, 'current_value' => 128.7, 'unit' => 'kg'],
            ['category' => GoalCategory::BodyMind, 'type' => GoalType::Counter, 'title' => 'Meditación', 'target_value' => 50, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'sesiones', 'increment' => 1],
            ['category' => GoalCategory::BodyMind, 'type' => GoalType::YesNo, 'title' => 'Trasplante de pelo'],

            // Money & Work
            ['category' => GoalCategory::MoneyWork, 'type' => GoalType::Money, 'title' => 'Ahorro invertido', 'target_value' => 15000, 'initial_value' => 0, 'current_value' => 0, 'currency' => 'EUR'],
            ['category' => GoalCategory::MoneyWork, 'type' => GoalType::YesNo, 'title' => 'Comprar piso'],
            ['category' => GoalCategory::MoneyWork, 'type' => GoalType::Number, 'title' => 'Salario', 'target_value' => 100000, 'initial_value' => 54500, 'current_value' => 54500, 'unit' => '€'],

            // Growth
            ['category' => GoalCategory::Growth, 'type' => GoalType::Counter, 'title' => 'Tecnologías/aptitudes nuevas', 'target_value' => 20, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'tecnologías', 'increment' => 1],
            ['category' => GoalCategory::Growth, 'type' => GoalType::Counter, 'title' => 'Proyectos completados', 'target_value' => 3, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'proyectos', 'increment' => 1],
            ['category' => GoalCategory::Growth, 'type' => GoalType::YesNo, 'title' => 'Proyecto con ingresos'],
            ['category' => GoalCategory::Growth, 'type' => GoalType::Counter, 'title' => 'Horas de inglés', 'target_value' => 100, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'horas', 'increment' => 1],

            // Life
            ['category' => GoalCategory::Life, 'type' => GoalType::Counter, 'title' => 'Viajes', 'target_value' => 10, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'viajes', 'increment' => 1],
            ['category' => GoalCategory::Life, 'type' => GoalType::Counter, 'title' => 'Km en moto', 'target_value' => 57068, 'initial_value' => 47068, 'current_value' => 47068, 'unit' => 'km', 'increment' => 50],
            ['category' => GoalCategory::Life, 'type' => GoalType::Counter, 'title' => 'Km en coche', 'target_value' => 96149, 'initial_value' => 86149, 'current_value' => 86149, 'unit' => 'km', 'increment' => 50],
            ['category' => GoalCategory::Life, 'type' => GoalType::Counter, 'title' => 'Quedadas con amigos', 'target_value' => 30, 'initial_value' => 0, 'current_value' => 0, 'unit' => 'quedadas', 'increment' => 1],
        ];

        foreach ($goals as $goal) {
            Goal::create([
                'user_id' => $user->id,
                ...$goal,
            ]);
        }
    }
}
