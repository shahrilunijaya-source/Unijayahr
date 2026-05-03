<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'date'        => today(),
            'clock_in_at' => null,
            'is_late'     => false,
        ];
    }
}
