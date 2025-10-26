<?php

namespace App\Http\Controllers\RouteManagement;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteLog;
use Illuminate\Http\Request;

class LiveRouteController extends Controller
{
    public function routePosition(Request $request, $routeId)
    {
        $log = RouteLog::where('route_id', $routeId)
            ->latest('recorded_at')
            ->first();

        if (!$log) {
            return response()->json([
                'lat' => -17.7134,
                'lng' => 178.0650,
                'vehicle' => null,
                'driver' => null,
            ]);
        }

        return response()->json([
            'lat' => (float) $log->latitude,
            'lng' => (float) $log->longitude,
            'vehicle' => $log->vehicle ? ($log->vehicle->make . ' ' . $log->vehicle->model) : 'Unknown',
            'driver' => $log->driver ? $log->driver->name : 'Unassigned',
        ]);
    }

    public function routeData(Request $request, $routeId)
    {
        $route = Route::with(['checkpoints', 'logs'])->findOrFail($routeId);
        return response()->json([
            'id' => $route->id,
            'name' => $route->name,
            'status' => $route->status,
            'waypoints' => $route->checkpoints->map(fn($cp) => [
                'lat' => (float) $cp->latitude,
                'lng' => (float) $cp->longitude,
                'name' => $cp->checkpoint_name,
            ])->toArray(),
            'latest_position' => $route->logs()->latest('recorded_at')->first()?->only(['latitude', 'longitude', 'recorded_at']),
        ]);
    }
}