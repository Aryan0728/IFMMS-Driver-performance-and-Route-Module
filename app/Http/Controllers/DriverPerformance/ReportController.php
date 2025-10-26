<?php

namespace App\Http\Controllers\DriverPerformance;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverPerformance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DriverPerformanceExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'weekly');
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());
        $driverId = $request->input('driver_id');

        $query = DriverPerformance::with('driver.user')
            ->when($driverId, fn($q) => $q->where('driver_id', $driverId))
            ->ranked($period, $startDate, $endDate === $startDate ? null : $endDate);

        $performances = $query->get();
        $drivers = Driver::active()->with('user')->get();

        return view('driver-performance.rankings', compact('performances', 'drivers', 'period', 'startDate', 'endDate', 'driverId'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'period' => 'required|in:daily,weekly,monthly,custom',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'driver_ids' => 'nullable|array',
            'driver_ids.*' => 'exists:drivers,user_id',
        ]);

        $performances = DriverPerformance::with('driver.user')
            ->when($validated['driver_ids'], fn($q) => $q->whereIn('driver_id', $validated['driver_ids']))
            ->ranked($validated['period'], $validated['start_date'], $validated['end_date'])
            ->get();

        switch ($validated['format']) {
            case 'csv':
                return $this->generateCsvReport($performances);
            case 'excel':
                return Excel::download(new DriverPerformanceExport($performances), 'driver_performance_' . now()->format('Y-m-d') . '.xlsx');
            case 'pdf':
                $pdf = Pdf::loadView('driver-performance.pdf-report', ['performances' => $performances]);
                return $pdf->download('driver_performance_' . now()->format('Y-m-d') . '.pdf');
        }
    }

    protected function generateCsvReport($performances)
    {
        $filename = 'driver_performance_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Driver Name',
            'Period',
            'Total Distance (miles)',
            'Total Routes',
            'Fuel Efficiency (mpg)',
            'Average Speed (mph)',
            'On-Time %',
            'Safety Score',
            'Customer Rating',
            'Performance Score',
        ]);

        foreach ($performances as $performance) {
            fputcsv($handle, [
                $performance->driver->user->name,
                $performance->period_type . ' (' . $performance->period_start->format('Y-m-d') . ' to ' . ($performance->period_end ? $performance->period_end->format('Y-m-d') : $performance->period_start->format('Y-m-d')) . ')',
                $performance->total_distance,
                $performance->total_routes,
                $performance->average_fuel_efficiency,
                $performance->average_speed,
                $performance->on_time_percentage,
                $performance->safety_score,
                $performance->customer_rating,
                $performance->performance_score,
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function () use ($handle) {}, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}