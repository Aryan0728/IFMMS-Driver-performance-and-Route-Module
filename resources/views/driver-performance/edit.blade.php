@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Driver Metric</h1>

    <form method="POST" action="{{ route('driver-performance.update', $driver) }}">
        @csrf
        @method('PUT')

        <a href="{{ route('driver-performance.show', $driver) }}" class="btn btn-secondary mb-3">Back to Driver Performance</a>

        <div class="form-group">
            <label for="total_distance">Total Distance (km)</label>
            <input type="number" step="0.01" name="total_distance" id="total_distance" class="form-control" value="{{ old('total_distance', $metric->total_distance) }}" required>
        </div>

        <div class="form-group">
            <label for="fuel_consumed">Fuel Consumed (L)</label>
            <input type="number" step="0.01" name="fuel_consumed" id="fuel_consumed" class="form-control" value="{{ old('fuel_consumed', $metric->fuel_consumed) }}" required>
        </div>

        <div class="form-group">
            <label for="deliveries_completed">Deliveries Completed</label>
            <input type="number" name="deliveries_completed" id="deliveries_completed" class="form-control" value="{{ old('deliveries_completed', $metric->deliveries_completed ?? 0) }}" required>
        </div>

        <div class="form-group">
            <label for="on_time_percentage">On-Time Percentage</label>
            <input type="number" name="on_time_percentage" id="on_time_percentage" class="form-control" value="{{ old('on_time_percentage', $metric->on_time_percentage ?? 0) }}" required>
        </div>

        <div class="form-group">
            <label for="safety_incidents">Safety Incidents</label>
            <input type="number" name="safety_incidents" id="safety_incidents" class="form-control" value="{{ old('safety_incidents', $metric->safety_incidents) }}" required>
        </div>

        <div class="form-group">
            <label for="traffic_violations">Traffic Violations</label>
            <input type="number" name="traffic_violations" id="traffic_violations" class="form-control" value="{{ old('traffic_violations', $metric->traffic_violations) }}" required>
        </div>

        <div class="form-group">
            <label for="customer_rating">Customer Rating</label>
            <input type="number" step="0.1" name="customer_rating" id="customer_rating" class="form-control" value="{{ old('customer_rating', $metric->customer_rating) }}" required>
        </div>

        <div class="form-group">
            <label for="overtime_hours">Overtime Hours</label>
            <input type="number" step="0.01" name="overtime_hours" id="overtime_hours" class="form-control" value="{{ old('overtime_hours', $metric->overtime_hours) }}" required>
        </div>

        <div class="form-group">
            <label for="idle_time">Idle Time (hours)</label>
            <input type="number" step="0.01" name="idle_time" id="idle_time" class="form-control" value="{{ old('idle_time', $metric->idle_time) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Metric</button>
    </form>
</div>
@endsection
