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

        return view('driver-performance.index', compact('drivers', 'performances', 'chartData', 'period', 'startDate', 'endDate'));
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
