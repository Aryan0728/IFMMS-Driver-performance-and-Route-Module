<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'assigned_driver_id' => User::factory()->create(['role' => 'Driver'])->id,
            'vehicle_id' => Vehicle::factory()->create()->id,
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'estimated_end_time' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'delayed', 'canceled']),
            'optimized_waypoints' => json_encode([
                ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude],
                ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude],
            ]),
            'estimated_distance' => $this->faker->randomFloat(2, 10, 500),
            'estimated_duration' => $this->faker->numberBetween(30, 480), // minutes
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
