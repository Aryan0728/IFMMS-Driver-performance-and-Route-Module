@extends('layouts.app')

@section('title', 'My Performance - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Performance</h1>
    <p class="page-subtitle">Track your driving performance and statistics</p>
</div>

<!-- Performance Metrics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $metrics['total_assignments'] }}</h4>
                        <p class="mb-0">Total Assignments</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-route fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $metrics['completion_rate'] }}%</h4>
                        <p class="mb-0">Completion Rate</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $metrics['on_time_percentage'] }}%</h4>
                        <p class="mb-0">On-Time Performance</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $metrics['avg_fuel_efficiency'] ?: 'N/A' }}</h4>
                        <p class="mb-0">Fuel Efficiency (km/L)</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-gas-pump fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Performance Charts -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Performance Trends</h5>
                <div class="card-tools">
                    <form method="GET" class="d-inline">
                        <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="30" {{ $period == '30' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="60" {{ $period == '60' ? 'selected' : '' }}>Last 60 Days</option>
                            <option value="90" {{ $period == '90' ? 'selected' : '' }}>Last 90 Days</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" height="100"></canvas>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Assignments</h5>
            </div>
            <div class="card-body">
                @if($recentAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->assignment_date->format('M d') }}</td>
                                    <td>
                                        <strong>{{ $assignment->route->route_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assignment->vehicle->vehicle_number }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : ($assignment->status === 'in_progress' ? 'warning' : 'primary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignment->status === 'completed')
                                            @if($assignment->is_on_time)
                                                <span class="badge bg-success">On Time</span>
                                            @else
                                                <span class="badge bg-warning">Late</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-route fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No recent assignments</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Performance Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Performance Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-success">{{ $metrics['completed_assignments'] }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info">{{ number_format($metrics['total_distance'], 1) }} km</h4>
                        <small class="text-muted">Total Distance</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-warning">{{ number_format($metrics['total_fuel_consumed'], 1) }} L</h4>
                        <small class="text-muted">Fuel Used</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-primary">{{ $metrics['avg_duration'] ? number_format($metrics['avg_duration']) . ' min' : 'N/A' }}</h4>
                        <small class="text-muted">Avg Duration</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Assignments -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Upcoming Assignments</h5>
            </div>
            <div class="card-body">
                @if($upcomingAssignments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingAssignments as $assignment)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $assignment->route->route_name }}</h6>
                                    <p class="mb-1 text-muted">{{ $assignment->assignment_date->format('M d, Y') }}</p>
                                    <small class="text-muted">{{ $assignment->scheduled_start_time }} - {{ $assignment->scheduled_end_time }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $assignment->assignment_date->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('driver.assignments.index') }}" class="btn btn-outline-primary btn-sm">
                            View All Assignments
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-calendar-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No upcoming assignments</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }

    // Performance Chart
    var ctx = document.getElementById('performanceChart');
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }

    var chartLabels = [
        @if(isset($trends) && count($trends) > 0)
            @foreach($trends as $trend)
                '{{ $trend["month"] ?? "" }}',
            @endforeach
        @else
            'No Data'
        @endif
    ];

    var completionData = [
        @if(isset($trends) && count($trends) > 0)
            @foreach($trends as $trend)
                {{ $trend['completion_rate'] ?? 0 }},
            @endforeach
        @else
            0
        @endif
    ];

    var onTimeData = [
        @if(isset($trends) && count($trends) > 0)
            @foreach($trends as $trend)
                {{ $trend['on_time_percentage'] ?? 0 }},
            @endforeach
        @else
            0
        @endif
    ];

    var chartData = {
        labels: chartLabels,
        datasets: [{
            label: 'Completion Rate (%)',
            data: completionData,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'On-Time Performance (%)',
            data: onTimeData,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    };

    try {
        var performanceChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Performance Trends Over Time'
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating chart:', error);
    }
});
</script>
@endsection