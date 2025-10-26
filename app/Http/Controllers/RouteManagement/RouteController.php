<?php

namespace App\Http\Controllers\RouteManagement;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RoutesExport;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::with(['driver', 'vehicle']);
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($driver_id = $request->input('driver_id')) {
            $query->where('assigned_driver_id', $driver_id);
        }
        if ($date_from = $request->input('date_from')) {
            $query->whereDate('start_time', '>=', $date_from);
        }
        if ($date_to = $request->input('date_to')) {
            $query->whereDate('start_time', '<=', $date_to);
        }

        $routes = $query->paginate(10);
        $drivers = User::whereHas('roles', fn($q) => $q->where('name', 'Driver'))->get();
        return view('route-management.index', compact('routes', 'drivers'));
    }

    public function create()
    {
        $drivers = User::whereHas('roles', fn($q) => $q->where('name', 'Driver'))->get();
        $vehicles = Vehicle::all();
        return view('route-management.create', compact('drivers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'start_time' => 'required|date',
            'estimated_end_time' => 'required|date|after:start_time',
            'status' => 'required|in:planned,in_progress,completed,delayed,canceled',
            'estimated_distance' => 'nullable|numeric|min:0',
            'estimated_duration' => 'nullable|numeric|min:0',
            'optimized_waypoints' => 'nullable|array',
            'optimized_waypoints.*.lat' => 'required_with:optimized_waypoints|numeric|between:-90,90',
            'optimized_waypoints.*.lng' => 'required_with:optimized_waypoints|numeric|between:-180,180',
            'notes' => 'nullable|string',
        ]);

        $route = Route::create($validated);
        if ($request->has('checkpoints')) {
            foreach ($request->input('checkpoints', []) as $index => $checkpoint) {
                RouteCheckpoint::create([
                    'route_id' => $route->id,
                    'checkpoint_name' => $checkpoint['name'] ?? 'Checkpoint ' . ($index + 1),
                    'address' => $checkpoint['address'] ?? null,
                    'latitude' => $checkpoint['lat'],
                    'longitude' => $checkpoint['lng'],
                    'sequence_order' => $index + 1,
                    'checkpoint_type' => $checkpoint['type'] ?? 'waypoint',
                    'is_mandatory' => $checkpoint['is_mandatory'] ?? true,
                ]);
            }
        }

        return redirect()->route('route-management.index')->with('success', 'Route created.');
    }

    public function show($route)
    {
        $route = Route::with(['driver', 'vehicle', 'checkpoints', 'logs'])->findOrFail($route);
        return view('route-management.show', compact('route'));
    }

    public function edit($route)
    {
        $route = Route::with('checkpoints')->findOrFail($route);
        $drivers = User::whereHas('roles', fn($q) => $q->where('name', 'Driver'))->get();
        $vehicles = Vehicle::all();
        return view('route-management.edit', compact('route', 'drivers', 'vehicles'));
    }

    public function update(Request $request, $route)
    {
        $route = Route::findOrFail($route);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'start_time' => 'required|date',
            'estimated_end_time' => 'required|date|after:start_time',
            'status' => 'required|in:planned,in_progress,completed,delayed,canceled',
            'estimated_distance' => 'nullable|numeric|min:0',
            'estimated_duration' => 'nullable|numeric|min:0',
            'optimized_waypoints' => 'nullable|array',
            'optimized_waypoints.*.lat' => 'required_with:optimized_waypoints|numeric|between:-90,90',
            'optimized_waypoints.*.lng' => 'required_with:optimized_waypoints|numeric|between:-180,180',
            'notes' => 'nullable|string',
        ]);

        $route->update($validated);
        if ($request->has('checkpoints')) {
            $route->checkpoints()->delete();
            foreach ($request->input('checkpoints', []) as $index => $checkpoint) {
                RouteCheckpoint::create([
                    'route_id' => $route->id,
                    'checkpoint_name' => $checkpoint['name'] ?? 'Checkpoint ' . ($index + 1),
                    'address' => $checkpoint['address'] ?? null,
                    'latitude' => $checkpoint['lat'],
                    'longitude' => $checkpoint['lng'],
                    'sequence_order' => $index + 1,
                    'checkpoint_type' => $checkpoint['type'] ?? 'waypoint',
                    'is_mandatory' => $checkpoint['is_mandatory'] ?? true,
                ]);
            }
        }

        return redirect()->route('route-management.show', $route)->with('success', 'Route updated.');
    }

    public function destroy($route)
    {
        Route::findOrFail($route)->delete();
        return redirect()->route('route-management.index')->with('success', 'Route deleted.');
    }

    public function vehicles()
    {
        $logs = RouteLog::whereIn('route_id', Route::active()->pluck('id'))
            ->latest('recorded_at')
            ->get()
            ->unique('vehicle_id')
            ->map(function ($log) {
                return [
                    'id' => $log->vehicle_id,
                    'name' => $log->vehicle ? ($log->vehicle->make . ' ' . $log->vehicle->model) : 'Unknown',
                    'driver' => $log->driver ? $log->driver->name : 'Unassigned',
                    'lat' => (float) ($log->latitude ?? -17.7134),
                    'lng' => (float) ($log->longitude ?? 178.0650),
                ];
            });

        return response()->json($logs);
    }

    public function activeRoutes()
    {
        $routes = Route::active()->with(['driver', 'checkpoints'])->get()->map(function ($route) {
            return [
                'id' => $route->id,
                'name' => $route->name,
                'status' => $route->status,
                'optimized_waypoints' => $route->checkpoints->map(fn($cp) => [
                    'lat' => (float) $cp->latitude,
                    'lng' => (float) $cp->longitude,
                ])->toArray(),
            ];
        });

        return response()->json($routes);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'driver_ids' => 'nullable|array',
            'driver_ids.*' => 'exists:users,id',
            'status' => 'nullable|in:planned,in_progress,completed,delayed,canceled',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $routes = Route::query()
            ->with(['driver', 'vehicle'])
            ->when($validated['driver_ids'], fn($q) => $q->whereIn('assigned_driver_id', $validated['driver_ids']))
            ->when($validated['status'], fn($q) => $q->where('status', $validated['status']))
            ->when($validated['date_from'], fn($q) => $q->whereDate('start_time', '>=', $validated['date_from']))
            ->when($validated['date_to'], fn($q) => $q->whereDate('start_time', '<=', $validated['date_to']))
            ->get();

        switch ($validated['format']) {
            case 'csv':
                return $this->generateCsvReport($routes);
            case 'excel':
                return Excel::download(new RoutesExport($routes), 'routes_' . now()->format('Y-m-d') . '.xlsx');
            case 'pdf':
                $pdf = Pdf::loadView('route-management.pdf-report', ['routes' => $routes]);
                return $pdf->download('routes_' . now()->format('Y-m-d') . '.pdf');
        }
    }

    protected function generateCsvReport($routes)
    {
        $filename = 'routes_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Route Name', 'Driver', 'Vehicle', 'Status', 'Start Time', 'Distance (miles)', 'Duration (min)']);

        foreach ($routes as $route) {
            fputcsv($handle, [
                $route->name,
                $route->driver ? $route->driver->name : 'Unassigned',
                $route->vehicle ? ($route->vehicle->make . ' ' . $route->vehicle->model) : 'Unassigned',
                $route->status,
                $route->start_time ? $route->start_time->format('Y-m-d H:i') : 'N/A',
                $route->actual_distance ?? $route->estimated_distance,
                $route->actual_duration ?? $route->estimated_duration,
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function () use ($handle) {}, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}