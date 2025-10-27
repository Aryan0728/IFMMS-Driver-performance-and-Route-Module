<?php
// app/Http/Controllers/DriverPerformance/DriverController.php

namespace App\Http\Controllers\DriverPerformance;

use App\Http\Controllers\Controller;
use App\Models\DriverMetric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\DriverAnalyticsService;

class DriverController extends Controller
{
    protected $analyticsService;

    public function __construct(DriverAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function show(User $driver)
    {
        // Ensure the user is a driver
        if (!$driver->isDriver()) {
            abort(404, 'Driver not found');
        }

        $metrics = $driver->metrics()
            ->with('vehicle')
            ->orderBy('record_date', 'asc')
            ->paginate(10);
            
        $performanceTrend = $driver->metrics()
            ->selectRaw('record_date, AVG(score) as avg_score')
            ->where('record_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('record_date')
            ->orderBy('record_date')
            ->get();

        // Generate training recommendations based on latest metric
        $latestMetric = $driver->metrics()->orderBy('record_date', 'desc')->first();
        $recommendations = $this->generateTrainingRecommendations($latestMetric);

        return view('driver-performance.driver', [
            'driver' => $driver,
            'metrics' => $metrics,
            'performanceTrend' => $performanceTrend,
            'recommendations' => $recommendations
        ]);
    }

    public function edit(User $driver, DriverMetric $metric)
    {
        if (!$driver->isDriver()) {
            abort(404, 'Driver not found');
        }

        return view('driver-performance.edit', [
            'driver' => $driver,
            'metric' => $metric
        ]);
    }

    public function update(Request $request, User $driver, DriverMetric $metric)
    {
        $validated = $request->validate([
            'miles_driven' => 'required|numeric|min:0',
            'fuel_consumed' => 'required|numeric|min:0',
            'deliveries_completed' => 'required|integer|min:0',
            'on_time_percentage' => 'required|integer|min:0|max:100',
            'hard_brakes' => 'required|integer|min:0',
            'rapid_accelerations' => 'required|integer|min:0',
            'speeding_incidents' => 'required|integer|min:0',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $metric->update($validated);

        return redirect()->route('driver-performance.show', $driver)
            ->with('success', 'Driver metric updated successfully.');
    }

    public function analyzeBehavior(User $driver)
    {
        if (!$driver->isDriver()) {
            abort(404, 'Driver not found');
        }

        // Analyze behavior for yesterday (or last day with logs)
        $date = now()->subDay();
        $metric = $this->analyticsService->analyzeDriverBehavior($driver->id, $date);

        if ($metric) {
            return redirect()->route('driver-performance.show', $driver)
                ->with('success', 'Behavior analysis completed for ' . $date);
        } else {
            return redirect()->route('driver-performance.show', $driver)
                ->with('info', 'No route logs found for analysis on ' . $date);
        }
    }

    protected function generateTrainingRecommendations(?DriverMetric $metric)
    {
        if (!$metric) {
            return [];
        }

        $recommendations = [];

        if ($metric->score < 70) {
            $recommendations[] = 'Consider refresher training on safe driving practices.';
        }
        if ($metric->hard_brakes > 5) {
            $recommendations[] = 'Training on smooth braking techniques is recommended.';
        }
        if ($metric->rapid_accelerations > 5) {
            $recommendations[] = 'Training on gradual acceleration is recommended.';
        }
        if ($metric->speeding_incidents > 3) {
            $recommendations[] = 'Speed management training is strongly recommended.';
        }
        if ($metric->on_time_percentage < 80) {
            $recommendations[] = 'Time management and route planning training suggested.';
        }
        if ($metric->fuel_consumed > 0 && $metric->miles_driven > 0) {
            $mpg = $metric->miles_driven / $metric->fuel_consumed;
            if ($mpg < 15) {
                $recommendations[] = 'Fuel efficiency training recommended to improve MPG.';
            }
        }

        return $recommendations;
    }
}
