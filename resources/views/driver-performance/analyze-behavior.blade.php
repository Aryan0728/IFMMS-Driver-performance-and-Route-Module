@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Behavior Analysis: {{ $driver->name }}</h1>
        <a href="{{ route('driver-performance.show', $driver->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Driver Details
        </a>
    </div>

    <!-- Behavior Analysis Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Safety Incidents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $metrics->sum('hard_brakes') + $metrics->sum('rapid_accelerations') + $metrics->sum('speeding_incidents') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Speed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics->avg('average_speed'), 1) }} mph
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tachometer-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Fuel Efficiency
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics->avg('fuel_efficiency'), 1) }} mpg
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                On-Time Performance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics->avg('on_time_percentage'), 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics Table -->
    @if($metrics && $metrics->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Monthly Behavior Metrics</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Miles Driven</th>
                                <th>Fuel Consumed</th>
                                <th>Average Speed</th>
                                <th>Hard Brakes</th>
                                <th>Rapid Accelerations</th>
                                <th>Speeding Incidents</th>
                                <th>On-Time %</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metrics as $metric)
                            <tr>
                                <td>{{ $metric->record_date->format('M Y') }}</td>
                                <td>{{ number_format($metric->miles_driven, 1) }}</td>
                                <td>{{ number_format($metric->fuel_consumed, 1) }}</td>
                                <td>{{ number_format($metric->average_speed, 1) }}</td>
                                <td>{{ $metric->hard_brakes }}</td>
                                <td>{{ $metric->rapid_accelerations }}</td>
                                <td>{{ $metric->speeding_incidents }}</td>
                                <td>{{ number_format($metric->on_time_percentage, 1) }}%</td>
                                <td>{{ $metric->score }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <p class="mb-0">No behavior metrics available for analysis.</p>
            </div>
        </div>
    @endif

    <!-- Charts Section -->
    @if($metrics && $metrics->count() > 0)
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">Performance Score Trend</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="scoreTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-danger text-white">
                        <h6 class="m-0 font-weight-bold">Safety Incidents Trend</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="safetyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recommendations -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning text-white">
            <h6 class="m-0 font-weight-bold">Recommendations</h6>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @if($metrics->avg('hard_brakes') > 5)
                    <li class="list-group-item">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        High number of hard brakes detected. Consider defensive driving training.
                    </li>
                @endif
                @if($metrics->avg('rapid_accelerations') > 5)
                    <li class="list-group-item">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        Frequent rapid accelerations. Focus on smoother driving habits.
                    </li>
                @endif
                @if($metrics->avg('speeding_incidents') > 2)
                    <li class="list-group-item">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        Speeding incidents above threshold. Review speed management.
                    </li>
                @endif
                @if($metrics->avg('fuel_efficiency') < 20)
                    <li class="list-group-item">
                        <i class="fas fa-info-circle text-info"></i>
                        Fuel efficiency could be improved. Consider eco-driving techniques.
                    </li>
                @endif
                @if($metrics->avg('on_time_percentage') < 80)
                    <li class="list-group-item">
                        <i class="fas fa-clock text-warning"></i>
                        On-time performance needs improvement. Review route planning and time management.
                    </li>
                @endif
                @if($metrics->count() == 0)
                    <li class="list-group-item">
                        <i class="fas fa-info-circle text-secondary"></i>
                        No data available for recommendations.
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare chart data
    const metrics = @json($metrics->take(10)->values());
    const dates = metrics.map(m => new Date(m.record_date).toLocaleDateString());
    const scores = metrics.map(m => m.score);
    const onTimeRates = metrics.map(m => m.on_time_percentage);
    const safetyIncidents = metrics.map(m => m.hard_brakes + m.rapid_accelerations + m.speeding_incidents);

    // Score Trend Chart
    const scoreCtx = document.getElementById('scoreTrendChart').getContext('2d');
    new Chart(scoreCtx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Performance Score',
                data: scores,
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100
                }
            }
        }
    });

    // Safety Trend Chart
    const safetyCtx = document.getElementById('safetyTrendChart').getContext('2d');
    new Chart(safetyCtx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Safety Incidents',
                data: safetyIncidents,
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
