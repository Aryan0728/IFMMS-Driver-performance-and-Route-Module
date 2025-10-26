<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteAssignment;
use App\Models\DriverMetric;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RouteAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $assignments = RouteAssignment::with(['route', 'driver', 'vehicle'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->driver_id, fn($q) => $q->where('driver_id', $request->driver_id))
            ->paginate(10);
        $drivers = User::whereHas('roles', fn($q) => $q->where('name', 'Driver'))->get();
        return view('route-assignments.index', compact('assignments', 'drivers'));
    }

    public function create()
    {
        $routes = Route::whereIn('status', ['planned'])->get();
        $drivers = User::whereHas('roles', fn($q) => $q->where('name', 'Driver'))->get();
        $vehicles = Vehicle::all();
        return view('route-assignments.create', compact('routes', 'drivers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'assignment_date' => 'required|date',
            'scheduled_start_time' => 'required|date_format:H:i',
            'scheduled_end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $route = Route::findOrFail($validated['route_id']);
        $route->update([
            'assigned_driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'status' => 'planned',
        ]);

        $assignment = RouteAssignment::create([
            'route_id' => $validated['route_id'],
            'driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'assignment_date' => $validated['assignment_date'],
            'scheduled_start_time' => $validated['assignment_date'] . ' ' . $validated['scheduled_start_time'],
            'scheduled_end_time' => $validated['assignment_date'] . ' ' . $validated['scheduled_end_time'],
            'status' => 'planned',
            'assigned_by' => auth()->id(),
            'notes' => $validated['notes'],
        ]);

        // Update DriverMetric
        $metric = DriverMetric::firstOrCreate(
            ['driver_id' => $validated['driver_id'], 'record_date' => $validated['assignment_date']],
            [
                'vehicle_id' => $validated['vehicle_id'],
                'total_distance' => 0,
                'total_driving_time' => 0,
                'fuel_efficiency' => 0,
                'average_speed' => 0,
                'routes_completed' => 0,
                'routes_assigned' => 0,
                'on_time_percentage' => 0,
                'safety_incidents' => 0,
                'traffic_violations' => 0,
                'customer_rating' => 0,
                'deliveries_completed' => 0,
                'deliveries_failed' => 0,
                'overtime_hours' => 0,
                'idle_time' => 0,
            ]
        );
        $metric->increment('routes_assigned');
        $metric->save();

        return redirect()->route('route-assignments.index')->with('success', 'Route assigned.');
    }

    public function show($assignment)
    {
        $assignment = RouteAssignment::with(['route', 'driver', 'vehicle', 'checkpointVisits'])->findOrFail($assignment);
        return view('route-assignments.show', compact('assignment'));
    }

    public function edit($assignment)
    {
        $assignment = RouteAssignment::findOrFail($assignment);
        $routes = Route::whereIn('status', ['planned'])->get();
        $drivers = User::whereHas('roles', fn($q) => $q->where('name', 'Driver'))->get();
        $vehicles = Vehicle::all();
        return view('route-assignments.edit', compact('assignment', 'routes', 'drivers', 'vehicles'));
    }

    public function update(Request $request, $assignment)
    {
        $assignment = RouteAssignment::findOrFail($assignment);
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'assignment_date' => 'required|date',
            'scheduled_start_time' => 'required|date_format:H:i',
            'scheduled_end_time' => 'required|date_format:H:i',
            'status' => 'required|in:planned,in_progress,completed,delayed,canceled',
            'notes' => 'nullable|string',
        ]);

        $route = Route::findOrFail($validated['route_id']);
        $route->update([
            'assigned_driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'status' => $validated['status'],
        ]);

        $assignment->update($validated);
        return redirect()->route('route-assignments.show', $assignment)->with('success', 'Assignment updated.');
    }

    public function destroy($assignment)
    {
        RouteAssignment::findOrFail($assignment)->delete();
        return redirect()->route('route-assignments.index')->with('success', 'Assignment deleted.');
    }

    public function start(Request $request, $assignment)
    {
        $assignment = RouteAssignment::findOrFail($assignment);
        $assignment->update([
            'status' => 'in_progress',
            'actual_start_time' => now(),
        ]);
        $assignment->route->update(['status' => 'in_progress']);
        return redirect()->route('route-assignments.show', $assignment)->with('success', 'Route started.');
    }

    public function complete(Request $request, $assignment)
    {
        $assignment = RouteAssignment::findOrFail($assignment);
        $validated = $request->validate([
            'actual_distance' => 'nullable|numeric|min:0',
            'actual_duration' => 'nullable|numeric|min:0',
            'fuel_consumed' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $assignment->update([
            'status' => 'completed',
            'actual_end_time' => now(),
            'actual_distance' => $validated['actual_distance'] ?? $assignment->actual_distance,
            'actual_duration' => $validated['actual_duration'] ?? $assignment->actual_duration,
            'fuel_consumed' => $validated['fuel_consumed'] ?? $assignment->fuel_consumed,
            'notes' => $validated['notes'] ?? $assignment->notes,
        ]);

        $route = $assignment->route;
        $route->update([
            'status' => 'completed',
            'actual_distance' => $assignment->actual_distance,
            'actual_duration' => $assignment->actual_duration,
        ]);

        // Update DriverMetric
        $metric = DriverMetric::firstOrCreate(
            ['driver_id' => $assignment->driver_id, 'record_date' => $assignment->assignment_date],
            [
                'vehicle_id' => $assignment->vehicle_id,
                'total_distance' => 0,
                'total_driving_time' => 0,
                'fuel_efficiency' => 0,
                'average_speed' => 0,
                'routes_completed' => 0,
                'routes_assigned' => 0,
                'on_time_percentage' => 0,
                'safety_incidents' => 0,
                'traffic_violations' => 0,
                'customer_rating' => 0,
                'deliveries_completed' => 0,
                'deliveries_failed' => 0,
                'overtime_hours' => 0,
                'idle_time' => 0,
            ]
        );
        $metric->increment('routes_completed');
        $metric->total_distance += $assignment->actual_distance ?? 0;
        $metric->total_driving_time += $assignment->actual_duration ?? 0;
        $metric->fuel_efficiency = $assignment->fuel_consumed > 0 ?
            round($metric->total_distance / $assignment->fuel_consumed, 2) : $metric->fuel_efficiency;
        $metric->average_speed = $metric->total_driving_time > 0 ?
            round(($metric->total_distance / $metric->total_driving_time) * 60, 2) : $metric->average_speed;
        $metric->on_time_percentage = DriverMetric::calculateMetricsForDriver($assignment->driver_id, $assignment->assignment_date)['on_time_percentage'];
        $metric->save();

        return redirect()->route('route-assignments.show', $assignment)->with('success', 'Route completed.');
    }

    public function cancel($assignment)
    {
        $assignment = RouteAssignment::findOrFail($assignment);
        $assignment->update(['status' => 'canceled']);
        $assignment->route->update(['status' => 'canceled']);
        return redirect()->route('route-assignments.index')->with('success', 'Assignment canceled.');
    }

    public function updateLocation(Request $request, $assignment)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'odometer' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'fuel_level' => 'nullable|numeric|min:0|max:100',
            'location_name' => 'nullable|string|max:255',
        ]);

        $assignment = RouteAssignment::findOrFail($assignment);
        RouteLog::create([
            'route_id' => $assignment->route_id,
            'driver_id' => $assignment->driver_id,
            'vehicle_id' => $assignment->vehicle_id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'odometer' => $validated['odometer'],
            'speed' => $validated['speed'],
            'fuel_level' => $validated['fuel_level'],
            'location_name' => $validated['location_name'],
            'recorded_at' => now(),
        ]);

        event(new \App\Events\VehiclePositionUpdated(RouteLog::latest()->first()));
        return response()->json(['success' => true]);
    }

    public function driverTodayAssignments()
    {
        $driverId = auth()->id();
        $today = now()->toDateString();

        $assignments = RouteAssignment::where('driver_id', $driverId)
            ->whereDate('assignment_date', $today)
            ->with(['route', 'vehicle'])
            ->orderBy('scheduled_start_time')
            ->get();

        return view('driver.assignments.today', compact('assignments'));
    }

    public function driverAssignments()
    {
        $driverId = auth()->id();

        $assignments = RouteAssignment::where('driver_id', $driverId)
            ->with(['route', 'vehicle'])
            ->orderBy('assignment_date', 'desc')
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'total_assignments' => RouteAssignment::where('driver_id', $driverId)->count(),
            'this_week' => RouteAssignment::where('driver_id', $driverId)
                ->whereBetween('assignment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'completed_this_month' => RouteAssignment::where('driver_id', $driverId)
                ->where('status', 'completed')
                ->whereMonth('assignment_date', now()->month)
                ->whereYear('assignment_date', now()->year)
                ->count(),
            'upcoming' => RouteAssignment::where('driver_id', $driverId)
                ->where('assignment_date', '>=', now()->toDateString())
                ->whereIn('status', ['planned', 'assigned'])
                ->count(),
        ];

        return view('driver.assignments.index', compact('assignments', 'stats'));
    }

    public function driverAssignmentHistory()
    {
        $driverId = auth()->id();

        $assignments = RouteAssignment::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->with(['route', 'vehicle'])
            ->orderBy('assignment_date', 'desc')
            ->paginate(15);

        // Calculate performance statistics
        $completedAssignments = RouteAssignment::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->get();

        $stats = [
            'total_completed' => $completedAssignments->count(),
            'total_distance' => $completedAssignments->sum('actual_distance') ?? 0,
            'avg_completion_time' => $completedAssignments->avg('actual_duration') ?? 0,
            'on_time_percentage' => $completedAssignments->count() > 0
                ? round(($completedAssignments->where('is_on_time', true)->count() / $completedAssignments->count()) * 100, 1)
                : 0,
        ];

        return view('driver.assignments.history', compact('assignments', 'stats'));
    }

    public function driverAssignmentShow($assignment)
    {
        $assignment = RouteAssignment::where('id', $assignment)
            ->where('driver_id', auth()->id())
            ->with(['route', 'vehicle', 'checkpointVisits'])
            ->firstOrFail();

        return view('driver.assignments.show', compact('assignment'));
    }

    public function driverStartRoute(Request $request, $assignment)
    {
        $assignment = RouteAssignment::where('id', $assignment)
            ->where('driver_id', auth()->id())
            ->firstOrFail();

        if ($assignment->status !== 'planned') {
            return response()->json(['error' => 'Assignment cannot be started'], 400);
        }

        $assignment->update([
            'status' => 'in_progress',
            'actual_start_time' => now(),
        ]);

        $assignment->route->update(['status' => 'in_progress']);

        return response()->json(['success' => true, 'message' => 'Route started successfully']);
    }

    public function driverCompleteRoute(Request $request, $assignment)
    {
        $assignment = RouteAssignment::where('id', $assignment)
            ->where('driver_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'actual_distance' => 'nullable|numeric|min:0',
            'actual_duration' => 'nullable|numeric|min:0',
            'fuel_consumed' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $assignment->update([
            'status' => 'completed',
            'actual_end_time' => now(),
            'actual_distance' => $validated['actual_distance'] ?? $assignment->actual_distance,
            'actual_duration' => $validated['actual_duration'] ?? $assignment->actual_duration,
            'fuel_consumed' => $validated['fuel_consumed'] ?? $assignment->fuel_consumed,
            'notes' => $validated['notes'] ?? $assignment->notes,
        ]);

        $assignment->route->update([
            'status' => 'completed',
            'actual_distance' => $assignment->actual_distance,
            'actual_duration' => $assignment->actual_duration,
        ]);

        // Update DriverMetric
        $metric = DriverMetric::firstOrCreate(
            ['driver_id' => $assignment->driver_id, 'record_date' => $assignment->assignment_date],
            [
                'vehicle_id' => $assignment->vehicle_id,
                'total_distance' => 0,
                'total_driving_time' => 0,
                'fuel_efficiency' => 0,
                'average_speed' => 0,
                'routes_completed' => 0,
                'routes_assigned' => 0,
                'on_time_percentage' => 0,
                'safety_incidents' => 0,
                'traffic_violations' => 0,
                'customer_rating' => 0,
                'deliveries_completed' => 0,
                'deliveries_failed' => 0,
                'overtime_hours' => 0,
                'idle_time' => 0,
            ]
        );
        $metric->increment('routes_completed');
        $metric->total_distance += $assignment->actual_distance ?? 0;
        $metric->total_driving_time += $assignment->actual_duration ?? 0;
        $metric->fuel_efficiency = $assignment->fuel_consumed > 0 ?
            round($metric->total_distance / $assignment->fuel_consumed, 2) : $metric->fuel_efficiency;
        $metric->average_speed = $metric->total_driving_time > 0 ?
            round(($metric->total_distance / $metric->total_driving_time) * 60, 2) : $metric->average_speed;
        $metric->on_time_percentage = DriverMetric::calculateMetricsForDriver($assignment->driver_id, $assignment->assignment_date)['on_time_percentage'];
        $metric->save();

        return response()->json(['success' => true, 'message' => 'Route completed successfully']);
    }

    public function driverUpdateLocation(Request $request, $assignment)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'odometer' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'fuel_level' => 'nullable|numeric|min:0|max:100',
            'location_name' => 'nullable|string|max:255',
        ]);

        $assignment = RouteAssignment::where('id', $assignment)
            ->where('driver_id', auth()->id())
            ->firstOrFail();

        RouteLog::create([
            'route_id' => $assignment->route_id,
            'driver_id' => $assignment->driver_id,
            'vehicle_id' => $assignment->vehicle_id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'odometer' => $validated['odometer'],
            'speed' => $validated['speed'],
            'fuel_level' => $validated['fuel_level'],
            'location_name' => $validated['location_name'],
            'recorded_at' => now(),
        ]);

        event(new \App\Events\VehiclePositionUpdated(RouteLog::latest()->first()));
        return response()->json(['success' => true]);
    }

    public function driverCheckpointVisit(Request $request, $assignment, $checkpoint)
    {
        $assignment = RouteAssignment::where('id', $assignment)
            ->where('driver_id', auth()->id())
            ->firstOrFail();

        $checkpointVisit = \App\Models\RouteCheckpointVisit::create([
            'route_assignment_id' => $assignment->id,
            'route_checkpoint_id' => $checkpoint,
            'visited_at' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true, 'checkpoint_visit' => $checkpointVisit]);
    }
}