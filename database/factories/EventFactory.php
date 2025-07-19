<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'type' => $this->faker->randomElement(['INFO', 'WARNING', 'ALERT']),
            'description' => $this->faker->sentence(),
            'value' => $this->faker->numberBetween(1, 100),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the event is of type INFO.
     */
    public function info(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'INFO',
        ]);
    }

    /**
     * Indicate that the event is of type WARNING.
     */
    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'WARNING',
        ]);
    }

    /**
     * Indicate that the event is of type ALERT.
     */
    public function alert(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'ALERT',
        ]);
    }
}
