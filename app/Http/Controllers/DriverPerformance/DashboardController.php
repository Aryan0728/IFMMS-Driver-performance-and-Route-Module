<?php
// app/Http/Controllers/DriverPerformance/DashboardController.php

namespace App\Http\Controllers\DriverPerformance;

use App\Http\Controllers\Controller;
use App\Models\DriverMetric;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Only get users with Driver role
        $drivers = User::where('role', 'Driver')
            ->with(['vehicle', 'metrics' => function($query) {
                $query->where('record_date', '>=', Carbon::now()->subDays(30));
            }])
            ->get();
            
        $overallMetrics = DriverMetric::selectRaw('
                AVG(score) as avg_score,
                SUM(miles_driven) as total_miles,
                SUM(fuel_consumed) as total_fuel,
                AVG(on_time_percentage) as on_time_avg
            ')
            ->where('record_date', '>=', Carbon::now()->subDays(30))
            ->first();
            
        return view('driver-performance.dashboard', [
            'drivers' => $drivers,
            'overallMetrics' => $overallMetrics,
            'timePeriod' => '30 Days'
        ]);
    }
}