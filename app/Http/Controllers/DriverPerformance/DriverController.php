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
        $driver = Driver::with(['user', 'metrics', 'routes', 'assignments'])->findOrFail($driverId);
        $recentAssignments = $driver->assignments()->with(['route', 'vehicle'])->latest()->take(5)->get();
        $metrics = $driver->metrics()->byPeriod('weekly')->get();

        return view('driver-performance.driver', compact('driver', 'recentAssignments', 'metrics'));
    }

    public function edit($driverId)
    {
        $driver = Driver::findOrFail($driverId);
        return view('driver-performance.edit', compact('driver'));
    }

    public function update(Request $request, $driverId)
    {
        $driver = Driver::findOrFail($driverId);
        $validated = $request->validate([
            'license_number' => 'required|string|max:255',
            'license_expiry' => 'required|date|after:today',
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        $driver->update($validated);
        $driver->user->update([
            'name' => $request->input('name', $driver->user->name),
            'email' => $request->input('email', $driver->user->email),
        ]);

        return redirect()->route('driver-performance.show', $driver->id)->with('success', 'Driver updated.');
    }
}