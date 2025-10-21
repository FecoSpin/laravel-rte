<?php

namespace Database\Factories;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Escuela ' . fake()->company(),
            'cct' => fake()->unique()->regexify('[0-9]{8}[A-Z]{3}[0-9]{3}'),
            'direction' => fake()->address(),
            'zone_id' => Zone::factory(),
        ];
    }
}
