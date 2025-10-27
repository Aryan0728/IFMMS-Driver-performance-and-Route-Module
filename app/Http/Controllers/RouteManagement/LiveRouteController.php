<?php
// app/Http/Controllers/RouteManagement/LiveRouteController.php

namespace App\Http\Controllers\RouteManagement;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteLog;
use Illuminate\Http\Request;

class LiveRouteController extends Controller
{
    public function updatePosition(Request $request, Route $route)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'odometer' => 'required|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'fuel_level' => 'nullable|numeric|between:0,100'
        ]);
        
        $log = new RouteLog($validated);
        $log->recorded_at = now();
        $route->logs()->save($log);
        
        // Update route status if needed
        if ($route->status === 'planned') {
            $route->status = 'in_progress';
            $route->save();
        }
        
        return response()->json(['status' => 'success', 'message' => 'Position updated']);
    }
    
    public function getRouteData(Route $route)
    {
        $route->load(['logs' => function($query) {
            $query->orderBy('recorded_at', 'asc');
        }]);
        
        return response()->json([
            'route' => $route,
            'waypoints' => $route->optimized_waypoints,
            'logs' => $route->logs
        ]);
    }
}