<?php

namespace App\Http\Controllers\DriverPerformance;

use App\Http\Controllers\Controller;
use App\Models\DriverPerformance;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'weekly');
        $startDate = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));
        $driverId = $request->input('driver_id');

        // Show only the logged-in driver's performance if they are a driver
        if (auth()->user()->role === 'Driver') {
            $drivers = User::where('id', auth()->id())->with(['metrics' => function($query) {
                $query->where('record_date', '>=', now()->subDays(30));
            }, 'vehicle'])->get();
        } else {
            $drivers = User::where('role', 'driver')->with(['metrics' => function($query) {
                $query->where('record_date', '>=', now()->subDays(30));
            }, 'vehicle'])->get();
        }

        $overallMetrics = [
            'avg_score' => $drivers->flatMap->metrics->avg('score') ?? 0,
            'on_time_avg' => $drivers->flatMap->metrics->avg('on_time_percentage') ?? 0,
            'total_miles' => $drivers->flatMap->metrics->sum('miles_driven') ?? 0,
            'total_fuel' => $drivers->flatMap->metrics->sum('fuel_consumed') ?? 0,
        ];

        $timePeriod = '30 days';

        return view('driver-performance.index', compact('drivers', 'overallMetrics', 'timePeriod'));
    }

    public function rankings(Request $request)
    {
        $period = $request->input('period', 'weekly');
        $startDate = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));
        $driverId = $request->input('driver_id');

        $drivers = User::where('role', 'driver')->get();

        $performances = DriverPerformance::with('driver')
            ->when($driverId, fn($q) => $q->where('driver_id', $driverId))
            ->when(auth()->user()->role === 'Driver', fn($q) => $q->where('driver_id', auth()->id()))
            ->where('period_type', $period)
            ->where('period_start', $startDate)
            ->orderBy('performance_score', 'desc')
            ->get();

        $chartData = [
            'labels' => $performances->pluck('driver.name')->toArray(),
            'performance_scores' => $performances->pluck('performance_score')->toArray(),
            'on_time_percentages' => $performances->pluck('on_time_percentage')->toArray(),
            'customer_ratings' => $performances->pluck('customer_rating')->toArray(),
        ];

        return view('driver-performance.rankings', compact('drivers', 'performances', 'chartData', 'period', 'startDate', 'endDate', 'driverId'));
    }
}
