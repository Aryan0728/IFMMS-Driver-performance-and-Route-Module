<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition()
    {
        $estimatedDuration = $this->faker->numberBetween(30, 240);
        $estimatedDistance = $this->faker->randomFloat(2, 10, 200);
        
        return [
            'name' => $this->faker->unique()->sentence(3),
            'driver_id' => Driver::factory(),
            'vehicle_id' => Vehicle::factory(),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'delayed', 'canceled']),
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_time' => $this->faker->optional()->dateTimeBetween('+1 week', '+2 weeks'),
            'estimated_duration' => $estimatedDuration,
            'actual_duration' => $this->faker->optional()->numberBetween($estimatedDuration - 30, $estimatedDuration + 60),
            'estimated_distance' => $estimatedDistance,
            'actual_distance' => $this->faker->optional()->randomFloat(2, $estimatedDistance - 10, $estimatedDistance + 20),
            'notes' => $this->faker->optional()->paragraph,
            'start_location' => $this->faker->address,
            'end_location' => $this->faker->address,
            'optimized_waypoints' => $this->faker->optional()->randomElements([$this->faker->address, $this->faker->address], 2)
        ];
    }

    /**
     * Indicate that the route is planned.
     */
    public function planned()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'planned',
                'actual_duration' => null,
                'actual_distance' => null,
                'end_time' => null
            ];
        });
    }

    /**
     * Indicate that the route is in progress.
     */
    public function inProgress()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_progress',
                'start_time' => now()->subHours(2),
                'actual_duration' => null,
                'actual_distance' => null,
                'end_time' => null
            ];
        });
    }

    /**
     * Indicate that the route is completed.
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            $startTime = now()->subHours(4);
            $endTime = $startTime->copy()->addHours(3);
            
            return [
                'status' => 'completed',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'actual_duration' => 180,
                'actual_distance' => $attributes['estimated_distance'] + $this->faker->randomFloat(2, -5, 10)
            ];
        });
    }
}