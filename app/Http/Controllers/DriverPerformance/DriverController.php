<?php

namespace App\Http\Controllers\DriverPerformance;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverMetric;
use App\Models\RouteAssignment;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show($driverId)
    {
        $driver = \App\Models\User::where('role', 'Driver')->findOrFail($driverId);
        $recentAssignments = \App\Models\RouteAssignment::where('driver_id', $driverId)->with(['route', 'vehicle'])->latest()->take(5)->get();
        $metrics = \App\Models\DriverMetric::where('driver_id', $driverId)->byPeriod('weekly')->get();

        return view('driver-performance.show', compact('driver', 'recentAssignments', 'metrics'));
    }

    public function edit($driverId)
    {
        $driver = \App\Models\User::where('role', 'Driver')->findOrFail($driverId);
        $metric = \App\Models\DriverMetric::where('driver_id', $driverId)->latest()->first();

        if (!$metric) {
            $metric = \App\Models\DriverMetric::create([
                'driver_id' => $driverId,
                'record_date' => now(),
                'miles_driven' => 0,
                'fuel_consumed' => 0,
                'deliveries_completed' => 0,
                'on_time_percentage' => 0,
                'hard_brakes' => 0,
                'rapid_accelerations' => 0,
                'speeding_incidents' => 0,
                'score' => 0,
            ]);
        }

        return view('driver-performance.edit', compact('driver', 'metric'));
    }

    public function update(Request $request, $driverId)
    {
        $validated = $request->validate([
            'miles_driven' => 'required|numeric|min:0',
            'fuel_consumed' => 'required|numeric|min:0',
            'deliveries_completed' => 'required|integer|min:0',
            'on_time_percentage' => 'required|numeric|min:0|max:100',
            'hard_brakes' => 'required|integer|min:0',
            'rapid_accelerations' => 'required|integer|min:0',
            'speeding_incidents' => 'required|integer|min:0',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $metric = \App\Models\DriverMetric::where('driver_id', $driverId)->latest()->first();

        if ($metric) {
            $metric->update($validated);
        } else {
            \App\Models\DriverMetric::create([
                'driver_id' => $driverId,
                'record_date' => now(),
                'miles_driven' => $validated['miles_driven'] ?? 0,
                'fuel_consumed' => $validated['fuel_consumed'] ?? 0,
                'deliveries_completed' => $validated['deliveries_completed'] ?? 0,
                'on_time_percentage' => $validated['on_time_percentage'] ?? 0,
                'hard_brakes' => $validated['hard_brakes'] ?? 0,
                'rapid_accelerations' => $validated['rapid_accelerations'] ?? 0,
                'speeding_incidents' => $validated['speeding_incidents'] ?? 0,
                'score' => $validated['score'] ?? 0,
            ]);
        }

        return redirect()->route('driver-performance.show', $driverId)->with('success', 'Driver metric updated.');
    }

    public function analyzeBehavior($driverId)
    {
        $driver = \App\Models\User::where('role', 'Driver')->findOrFail($driverId);
        $metrics = \App\Models\DriverMetric::where('driver_id', $driverId)->byPeriod('monthly')->get();

        return view('driver-performance.analyze-behavior', compact('driver', 'metrics'));
    }
}