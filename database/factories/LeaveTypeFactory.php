<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => $this->faker->words(2, true),
            'code'              => $this->faker->unique()->lexify('????'),
            'is_paid'           => true,
            'requires_document' => false,
            'max_days_per_year' => null,
            'is_active'         => true,
        ];
    }
}
