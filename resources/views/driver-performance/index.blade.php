@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Driver Performance Dashboard</h1>
        <a href="{{ route('route-management.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Routes
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Filter Performance</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('driver-performance.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label">Period</label>
                    <select name="period" id="period" class="form-control">
                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="driver_id" class="form-label">Driver</label>
                    <select name="driver_id" id="driver_id" class="form-control">
                        <option value="">All Drivers</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Apply</button>
                    <a href="{{ route('driver-performance.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    @if(isset($chartData) && $chartData['labels'])
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Performance Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Driver Performance</h6>
        </div>
        <div class="card-body">
            @if(isset($performances) && $performances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Total Distance (miles)</th>
                                <th>Total Routes</th>
                                <th>Fuel Efficiency (mpg)</th>
                                <th>Average Speed (mph)</th>
                                <th>On-Time %</th>
                                <th>Safety Score</th>
                                <th>Customer Rating</th>
                                <th>Performance Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performances as $performance)
                            <tr>
                                <td>{{ $performance->driver->name }}</td>
                                <td>{{ number_format($performance->total_distance, 1) }}</td>
                                <td>{{ $performance->total_routes }}</td>
                                <td>{{ number_format($performance->average_fuel_efficiency, 1) }}</td>
                                <td>{{ number_format($performance->average_speed, 1) }}</td>
                                <td>{{ number_format($performance->on_time_percentage, 1) }}%</td>
                                <td>{{ number_format($performance->safety_score, 1) }}</td>
                                <td>{{ number_format($performance->customer_rating, 1) }}</td>
                                <td>{{ number_format($performance->performance_score, 1) }}</td>
                                <td>
                                    <a href="{{ route('driver-performance.show', $performance->driver->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-4x text-gray-300 mb-3"></i>
                    <h5>No performance data available</h5>
                    <p class="text-muted">Create a new route to assign to drivers and generate performance metrics.</p>
                    <a href="{{ route('route-management.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Route
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>




@endsection