<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Route;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $driver;
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->driver = Driver::factory()->create([
            'name' => 'Test Driver'
        ]);

        // Create test vehicle
        $vehicle = Vehicle::factory()->create([
            'make' => 'Test Make',
            'model' => 'Test Model',
            'license_plate' => 'TEST123'
        ]);

        // Create test route
        $this->route = Route::factory()->create([
            'name' => 'Test Route',
            'driver_id' => $this->driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'planned',
            'start_time' => now(),
            'estimated_duration' => 120,
            'estimated_distance' => 50
        ]);
    }

    /** @test */
    public function admin_can_view_routes_list()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('route-management.index'));

        $response->assertStatus(200)
            ->assertViewIs('route-management.index')
            ->assertViewHas('routes')
            ->assertSee('Test Route');
    }

    /** @test */
    public function admin_can_create_new_route()
    {
        $routeData = [
            'name' => 'New Test Route',
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->route->vehicle_id,
            'status' => 'planned',
            'start_time' => now()->format('Y-m-d H:i:s'),
            'estimated_duration' => 60,
            'estimated_distance' => 25,
            'notes' => 'Test notes'
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('route-management.store'), $routeData);

        $response->assertRedirect(route('route-management.index'));
        $this->assertDatabaseHas('routes', ['name' => 'New Test Route']);
    }

    /** @test */
    public function admin_can_update_route()
    {
        $updateData = [
            'name' => 'Updated Route Name',
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->route->vehicle_id,
            'status' => 'in_progress',
            'start_time' => now()->format('Y-m-d H:i:s'),
            'estimated_duration' => 90,
            'estimated_distance' => 35,
            'notes' => 'Updated notes'
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('route-management.update', $this->route), $updateData);

        $response->assertRedirect(route('route-management.show', $this->route));
        $this->assertDatabaseHas('routes', ['name' => 'Updated Route Name']);
    }

    /** @test */
    public function admin_can_delete_route()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('route-management.destroy', $this->route));

        $response->assertRedirect(route('route-management.index'));
        $this->assertDatabaseMissing('routes', ['id' => $this->route->id]);
    }

    /** @test */
    public function driver_can_only_view_assigned_routes()
    {
        $driverUser = User::factory()->create([
            'role' => 'driver',
            'driver_id' => $this->driver->id
        ]);

        $response = $this->actingAs($driverUser)
            ->get(route('route-management.index'));

        $response->assertStatus(200)
            ->assertViewIs('route-management.index')
            ->assertSee('Test Route');

        // Driver should not see create button
        $response->assertDontSee('Create New Route');
    }

    /** @test */
    public function non_admin_cannot_modify_routes()
    {
        $driverUser = User::factory()->create([
            'role' => 'driver',
            'driver_id' => $this->driver->id
        ]);

        // Try to create
        $response = $this->actingAs($driverUser)
            ->post(route('route-management.store'), []);
        $response->assertStatus(403);

        // Try to update
        $response = $this->actingAs($driverUser)
            ->put(route('route-management.update', $this->route), []);
        $response->assertStatus(403);

        // Try to delete
        $response = $this->actingAs($driverUser)
            ->delete(route('route-management.destroy', $this->route));
        $response->assertStatus(403);
    }

    /** @test */
    public function routes_can_be_filtered_by_status()
    {
        Route::factory()->create(['status' => 'completed']);
        Route::factory()->create(['status' => 'delayed']);

        $response = $this->actingAs($this->admin)
            ->get(route('route-management.index', ['status' => 'planned']));

        $response->assertStatus(200)
            ->assertViewIs('route-management.index')
            ->assertSee('Test Route')
            ->assertDontSee('completed')
            ->assertDontSee('delayed');
    }

    /** @test */
    public function routes_can_be_filtered_by_driver()
    {
        $otherDriver = Driver::factory()->create();
        Route::factory()->create([
            'driver_id' => $otherDriver->id,
            'name' => 'Other Driver Route'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('route-management.index', ['driver_id' => $this->driver->id]));

        $response->assertStatus(200)
            ->assertViewIs('route-management.index')
            ->assertSee('Test Route')
            ->assertDontSee('Other Driver Route');
    }
}