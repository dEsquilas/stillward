<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\LogEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WalkingKmLogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'desquilas@gmail.com')->firstOrFail();
        $goal = Goal::where('user_id', $user->id)->where('title', 'Km andando')->firstOrFail();

        $dailyKm = [
            1.92, 8.73, 2.21, 0.48, 3.09, 0.63, 2.42, 2.54, 1.30, 9.28,
            1.56, 2.48, 2.58, 3.50, 4.41, 1.92, 2.80, 7.65, 0.76, 3.31,
            3.08, 2.08, 0.21, 0.97, 2.16, 2.73, 2.60, 2.60, 3.29, 0.85,
            10.93, 2.62, 2.73, 3.87, 4.14, 0.49, 1.99, 1.88, 1.52,
        ];

        $startDate = Carbon::create(2025, 1, 1);
        $total = 0;

        foreach ($dailyKm as $index => $km) {
            $total += $km;

            LogEntry::create([
                'user_id' => $user->id,
                'goal_id' => $goal->id,
                'value' => $km,
                'created_at' => $startDate->copy()->addDays($index)->setTime(20, 0),
            ]);
        }

        $goal->update(['current_value' => $total]);
    }
}
