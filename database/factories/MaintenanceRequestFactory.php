<?php

namespace Database\Factories;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceRequest>
 */
class MaintenanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'in_progress', 'completed', 'rejected']);
        $priority = fake()->randomElement(['low', 'medium', 'high', 'urgent']);
        
        return [
            'folio' => 'MNT-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => $priority,
            'status' => $status,
            'zone_id' => Zone::factory(),
            'requested_by' => User::factory(),
            'assigned_to' => $status !== 'pending' ? User::factory() : null,
            'location' => fake()->address(),
            'images' => fake()->boolean(30) ? [fake()->imageUrl(), fake()->imageUrl()] : null,
            'evidence_images' => $status === 'completed' ? [fake()->imageUrl()] : null,
            'resolution_notes' => in_array($status, ['completed', 'rejected']) ? fake()->paragraph() : null,
            'assigned_at' => $status !== 'pending' ? fake()->dateTimeBetween('-15 days', '-5 days') : null,
            'completed_at' => $status === 'completed' ? fake()->dateTimeBetween('-5 days', 'now') : null,
        ];
    }

    /**
     * Indicate that the request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'assigned_to' => null,
            'assigned_at' => null,
            'completed_at' => null,
            'resolution_notes' => null,
            'evidence_images' => null,
        ]);
    }

    /**
     * Indicate that the request is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'assigned_to' => User::factory(),
            'assigned_at' => fake()->dateTimeBetween('-10 days', '-1 day'),
            'completed_at' => null,
            'resolution_notes' => null,
            'evidence_images' => null,
        ]);
    }

    /**
     * Indicate that the request is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'assigned_to' => User::factory(),
            'assigned_at' => fake()->dateTimeBetween('-15 days', '-5 days'),
            'completed_at' => fake()->dateTimeBetween('-5 days', 'now'),
            'resolution_notes' => fake()->paragraph(),
            'evidence_images' => [fake()->imageUrl()],
        ]);
    }

    /**
     * Indicate that the request is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => fake()->randomElement(['high', 'urgent']),
        ]);
    }
}
