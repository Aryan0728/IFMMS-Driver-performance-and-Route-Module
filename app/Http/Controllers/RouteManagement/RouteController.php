<?php
// app/Http/Controllers/RouteManagement/RouteController.php

namespace App\Http\Controllers\RouteManagement;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        // Get all drivers for the filter dropdown
        $drivers = User::where('role', 'Driver')->get();
        
        $query = Route::with(['driver', 'vehicle']);
        
        // Apply search
        if ($search = request('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        // Apply filters
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        
        if ($driverId = request('driver_id')) {
            $query->where('assigned_driver_id', $driverId);
        }
        
        if ($dateFrom = request('date_from')) {
            $query->whereDate('start_time', '>=', $dateFrom);
        }
        
        if ($dateTo = request('date_to')) {
            $query->whereDate('start_time', '<=', $dateTo);
        }
        
        $routes = $query->orderBy('start_time', 'desc')->paginate(10);
        
        return view('route-management.index', [
            'routes' => $routes,
            'drivers' => $drivers
        ]);
    }
    
    public function create()
    {
        return view('route-management.create', [
            'drivers' => User::where('role', 'Driver')->get(),
            'vehicles' => Vehicle::where('status', 'available')->get()
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assigned_driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_time' => 'required|date',
            'estimated_end_time' => 'required|date|after:start_time',
            'optimized_waypoints' => 'required|json',
            'estimated_distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'status' => 'sometimes|in:planned,in_progress,completed,delayed,canceled',
            'notes' => 'nullable|string'
        ]);
        
        try {
            // Parse waypoints to ensure valid JSON
            $waypoints = json_decode($validated['optimized_waypoints']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['optimized_waypoints' => 'Invalid JSON format for waypoints'])->withInput();
            }
            
            $route = Route::create([
                'name' => $validated['name'],
                'assigned_driver_id' => $validated['assigned_driver_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'start_time' => $validated['start_time'],
                'estimated_end_time' => $validated['estimated_end_time'],
                'optimized_waypoints' => $waypoints,
                'estimated_distance' => $validated['estimated_distance'],
                'estimated_duration' => $validated['estimated_duration'],
                'status' => $validated['status'] ?? 'planned',
                'notes' => $validated['notes'] ?? null
            ]);
            
            return redirect()->route('route-management.show', $route)
                ->with('success', 'Route created successfully!');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create route: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function show(Route $route)
    {
        $route->load(['driver', 'vehicle', 'logs' => function($query) {
            $query->orderBy('recorded_at', 'asc');
        }]);
        
        return view('route-management.show', ['route' => $route]);
    }

    public function edit(Route $route)
    {
        return view('route-management.edit', [
            'route' => $route,
            'drivers' => User::where('role', 'Driver')->get(),
            'vehicles' => Vehicle::all()
        ]);
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assigned_driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_time' => 'required|date',
            'estimated_end_time' => 'required|date|after:start_time',
            'optimized_waypoints' => 'required|json',
            'estimated_distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'status' => 'sometimes|in:planned,in_progress,completed,delayed,canceled',
            'notes' => 'nullable|string'
        ]);

        try {
            // Parse waypoints to ensure valid JSON
            $waypoints = json_decode($validated['optimized_waypoints']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['optimized_waypoints' => 'Invalid JSON format for waypoints'])->withInput();
            }

            $route->update([
                'name' => $validated['name'],
                'assigned_driver_id' => $validated['assigned_driver_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'start_time' => $validated['start_time'],
                'estimated_end_time' => $validated['estimated_end_time'],
                'optimized_waypoints' => $waypoints,
                'estimated_distance' => $validated['estimated_distance'],
                'estimated_duration' => $validated['estimated_duration'],
                'status' => $validated['status'] ?? 'planned',
                'notes' => $validated['notes'] ?? null
            ]);

            return redirect()->route('route-management.index')
                ->with('success', 'Route updated successfully!');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update route: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('route-management.index')
            ->with('success', 'Route deleted successfully!');
    }

    public function vehicles()
    {
        $vehicles = Vehicle::with('user')->get()->map(function ($vehicle) {
            // Simulate current position in Fiji for GPS
            $fijiLocations = [
                ['lat' => -18.1401, 'lng' => 178.4186], // Suva
                ['lat' => -17.8019, 'lng' => 177.4164], // Nadi
                ['lat' => -17.6167, 'lng' => 177.4500], // Lautoka
            ];
            $pos = $fijiLocations[array_rand($fijiLocations)];
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->make . ' ' . $vehicle->model . ' (' . $vehicle->license_plate . ')',
                'lat' => $pos['lat'],
                'lng' => $pos['lng'],
                'driver' => $vehicle->user ? $vehicle->user->name : 'Unassigned'
            ];
        });

        return response()->json($vehicles);
    }

    public function activeRoutes()
    {
        $routes = Route::with(['driver', 'vehicle'])
            ->whereIn('status', ['planned', 'in_progress'])
            ->get()
            ->map(function ($route) {
                return [
                    'id' => $route->id,
                    'name' => $route->name,
                    'status' => $route->status,
                    'optimized_waypoints' => $route->optimized_waypoints,
                    'driver' => $route->driver ? $route->driver->name : 'Unassigned',
                    'vehicle' => $route->vehicle ? $route->vehicle->make . ' ' . $route->vehicle->model : 'Unassigned'
                ];
            });

        return response()->json($routes);
    }
}