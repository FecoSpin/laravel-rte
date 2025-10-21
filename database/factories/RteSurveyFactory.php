<?php

namespace Database\Factories;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RteSurvey>
 */
class RteSurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'submitted', 'approved', 'rejected']);
        $stage = fake()->randomElement(['initial', 'first_report', 'second_report', 'final_report']);
        
        return [
            'title' => 'RTE - ' . fake()->words(3, true),
            'description' => fake()->paragraph(),
            'zone_id' => Zone::factory(),
            'user_id' => User::factory(),
            'folio' => 'RTE-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'status' => $status,
            'stage' => $stage,
            'form_data' => [
                'infrastructure' => fake()->paragraph(),
                'equipment' => fake()->paragraph(),
                'safety' => fake()->paragraph(),
                'observations' => fake()->paragraph(),
            ],
            'submitted_at' => $status !== 'draft' ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'approved_at' => $status === 'approved' ? fake()->dateTimeBetween('-15 days', 'now') : null,
            'approved_by' => $status === 'approved' ? User::factory() : null,
            'rejection_reason' => $status === 'rejected' ? fake()->sentence() : null,
        ];
    }

    /**
     * Indicate that the survey is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the survey is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'submitted_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the survey is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'submitted_at' => fake()->dateTimeBetween('-15 days', '-7 days'),
            'approved_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'approved_by' => User::factory(),
            'rejection_reason' => null,
        ]);
    }
}
