@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Driver Performance Rankings</h1>
            <p class="text-muted">Comprehensive driver performance analysis and rankings</p>
        </div>
        <div>
            <a href="{{ route('driver-performance.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter"></i> Performance Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('driver-performance.rankings') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label fw-bold">Analysis Period</label>
                    <select name="period" id="period" class="form-control">
                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily Analysis</option>
                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly Analysis</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly Analysis</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="driver_id" class="form-label fw-bold">Driver Selection</label>
                    <select name="driver_id" id="driver_id" class="form-control">
                        <option value="">All Drivers</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ $driverId == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-bold">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('driver-performance.rankings') }}" class="btn btn-outline-secondary btn-sm ms-2">
                        <i class="fas fa-undo"></i> Reset Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-chart-bar"></i> Performance Metrics Overview
            </h6>
        </div>
        <div class="card-body">
            <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                <canvas id="rankingsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Rankings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-trophy"></i> Driver Performance Rankings
            </h6>
        </div>
        <div class="card-body">
            @if($performances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center"><i class="fas fa-medal"></i> Rank</th>
                                <th><i class="fas fa-user"></i> Driver Name</th>
                                <th class="text-center"><i class="fas fa-route"></i> Distance (mi)</th>
                                <th class="text-center"><i class="fas fa-map-marked-alt"></i> Routes</th>
                                <th class="text-center"><i class="fas fa-gas-pump"></i> Fuel Eff. (mpg)</th>
                                <th class="text-center"><i class="fas fa-tachometer-alt"></i> Avg Speed (mph)</th>
                                <th class="text-center"><i class="fas fa-clock"></i> On-Time %</th>
                                <th class="text-center"><i class="fas fa-shield-alt"></i> Safety Score</th>
                                <th class="text-center"><i class="fas fa-star"></i> Customer Rating</th>
                                <th class="text-center"><i class="fas fa-award"></i> Performance Score</th>
                                <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performances as $index => $performance)
                            <tr class="{{ $index < 3 ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    @if($index == 0)
                                        <span class="badge bg-gold"><i class="fas fa-crown"></i> 1st</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-silver"><i class="fas fa-medal"></i> 2nd</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-bronze"><i class="fas fa-award"></i> 3rd</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $performance->driver->user->name }}</td>
                                <td class="text-center">{{ number_format($performance->total_distance, 1) }}</td>
                                <td class="text-center">{{ $performance->total_routes }}</td>
                                <td class="text-center">{{ number_format($performance->average_fuel_efficiency, 1) }}</td>
                                <td class="text-center">{{ number_format($performance->average_speed, 1) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $performance->on_time_percentage >= 95 ? 'bg-success' : ($performance->on_time_percentage >= 85 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($performance->on_time_percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center">{{ number_format($performance->safety_score, 1) }}</td>
                                <td class="text-center">
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($performance->customer_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <small class="ms-1">{{ number_format($performance->customer_rating, 1) }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $performance->performance_score >= 90 ? 'bg-success' : ($performance->performance_score >= 75 ? 'bg-primary' : 'bg-secondary') }} fs-6">
                                        {{ number_format($performance->performance_score, 1) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('driver-performance.show', $performance->driver->id) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Performance Data Available</h5>
                    <p class="text-muted">There are no performance records for the selected period. Please adjust your filters or check back later.</p>
                </div>
            @endif
        </div>
    </div>
</div>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('rankingsChart').getContext('2d');
    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Performance Score',
                    data: chartData.performance_scores,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                },
                {
                    label: 'On-Time Percentage',
                    data: chartData.on_time_percentages,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Customer Rating',
                    data: chartData.customer_ratings,
                    backgroundColor: 'rgba(54, 185, 204, 0.8)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + (this.chart.config._config.type === 'bar' ? '' : '%');
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Driver Performance Metrics Comparison',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y;
                            if (context.datasetIndex === 1 || context.datasetIndex === 2) {
                                label += '%';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
