<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DriverMetric;
use App\Models\RouteLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DriverPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $driver;

    public function setUp(): void
    {
        parent::setUp();
        // Seed database or create necessary data
        $this->driver = User::factory()->create([
            'role' => 'Driver',
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
        ]);
    }

    public function testDriverMetricsEditFormLoads()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $metric = DriverMetric::factory()->create([
            'driver_id' => $this->driver->id,
            'vehicle_id' => $vehicle->id,
            'record_date' => Carbon::today(),
        ]);

        $response = $this->actingAs($this->driver)
            ->get(route('driver-performance.edit', [$this->driver, $metric]));

        $response->assertStatus(200);
        $response->assertSee('Edit Driver Metric');
        $response->assertSee((string)$metric->miles_driven);
    }

    public function testDriverMetricsUpdate()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $metric = DriverMetric::factory()->create([
            'driver_id' => $this->driver->id,
            'vehicle_id' => $vehicle->id,
            'record_date' => Carbon::today(),
            'miles_driven' => 100,
            'fuel_consumed' => 10,
            'deliveries_completed' => 5,
            'on_time_percentage' => 90,
            'hard_brakes' => 1,
            'rapid_accelerations' => 1,
            'speeding_incidents' => 0,
            'score' => 85,
        ]);

        $updateData = [
            'miles_driven' => 120,
            'fuel_consumed' => 12,
            'deliveries_completed' => 6,
            'on_time_percentage' => 92,
            'hard_brakes' => 0,
            'rapid_accelerations' => 0,
            'speeding_incidents' => 1,
            'score' => 88,
        ];

        $response = $this->actingAs($this->driver)
            ->put(route('driver-performance.update', [$this->driver, $metric]), $updateData);

        $response->assertRedirect(route('driver-performance.show', $this->driver));
        $this->assertDatabaseHas('driver_metrics', array_merge(['id' => $metric->id], $updateData));
    }

    public function testBehaviorAnalysisDetectsHardBraking()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();
        $this->driver->vehicle_id = $vehicle->id;
        $this->driver->save();

        $route = \App\Models\Route::factory()->create([
            'assigned_driver_id' => $this->driver->id,
            'vehicle_id' => $vehicle->id,
        ]);

        // Create route logs with hard braking event
        $date = Carbon::yesterday()->startOfDay();
        RouteLog::factory()->create([
            'route_id' => $route->id,
            'recorded_at' => $date->copy()->addSeconds(0),
            'speed' => 50,
            'latitude' => 0,
            'longitude' => 0,
            'odometer' => 100,
            'fuel_level' => 50,
            'location_name' => 'Test Location 1',
        ]);
        RouteLog::factory()->create([
            'route_id' => $route->id,
            'recorded_at' => $date->copy()->addSeconds(1),
            'speed' => 35, // drop > 10 mph in 1 second = hard brake
            'latitude' => 0,
            'longitude' => 0,
            'odometer' => 101,
            'fuel_level' => 49,
            'location_name' => 'Test Location 2',
        ]);

        $response = $this->actingAs($this->driver)
            ->post(route('driver-performance.analyze-behavior', $this->driver));

        $response->assertRedirect(route('driver-performance.show', $this->driver));
        $this->assertDatabaseHas('driver_metrics', [
            'driver_id' => $this->driver->id,
            'record_date' => $date->toDateString(),
            'hard_brakes' => 1,
        ]);
    }

    public function testTrainingRecommendationsGenerated()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $metric = DriverMetric::factory()->create([
            'driver_id' => $this->driver->id,
            'vehicle_id' => $vehicle->id,
            'record_date' => Carbon::today(),
            'score' => 65,
            'hard_brakes' => 6,
            'rapid_accelerations' => 7,
            'speeding_incidents' => 4,
            'on_time_percentage' => 75,
            'miles_driven' => 100,
            'fuel_consumed' => 10,
        ]);

        $response = $this->actingAs($this->driver)
            ->get(route('driver-performance.show', $this->driver));

        $response->assertSee('Consider refresher training on safe driving practices.');
        $response->assertSee('Training on smooth braking techniques is recommended.');
        $response->assertSee('Training on gradual acceleration is recommended.');
        $response->assertSee('Speed management training is strongly recommended.');
        $response->assertSee('Time management and route planning training suggested.');
        $response->assertSee('Fuel efficiency training recommended to improve MPG.');
    }
}
