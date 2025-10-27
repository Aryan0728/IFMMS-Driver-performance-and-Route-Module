@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Driver Metric</h1>

    <form method="POST" action="{{ route('driver-performance.update', [$driver, $metric]) }}">
        @csrf
        @method('PUT')

        <a href="{{ route('driver-performance.show', $driver) }}" class="btn btn-secondary mb-3">Back to Driver Performance</a>

        <div class="form-group">
            <label for="miles_driven">Miles Driven</label>
            <input type="number" step="0.01" name="miles_driven" id="miles_driven" class="form-control" value="{{ old('miles_driven', $metric->miles_driven) }}" required>
        </div>

        <div class="form-group">
            <label for="fuel_consumed">Fuel Consumed (gal)</label>
            <input type="number" step="0.01" name="fuel_consumed" id="fuel_consumed" class="form-control" value="{{ old('fuel_consumed', $metric->fuel_consumed) }}" required>
        </div>

        <div class="form-group">
            <label for="deliveries_completed">Deliveries Completed</label>
            <input type="number" name="deliveries_completed" id="deliveries_completed" class="form-control" value="{{ old('deliveries_completed', $metric->deliveries_completed) }}" required>
        </div>

        <div class="form-group">
            <label for="on_time_percentage">On-Time Percentage</label>
            <input type="number" name="on_time_percentage" id="on_time_percentage" class="form-control" value="{{ old('on_time_percentage', $metric->on_time_percentage) }}" required>
        </div>

        <div class="form-group">
            <label for="hard_brakes">Hard Brakes</label>
            <input type="number" name="hard_brakes" id="hard_brakes" class="form-control" value="{{ old('hard_brakes', $metric->hard_brakes) }}" required>
        </div>

        <div class="form-group">
            <label for="rapid_accelerations">Rapid Accelerations</label>
            <input type="number" name="rapid_accelerations" id="rapid_accelerations" class="form-control" value="{{ old('rapid_accelerations', $metric->rapid_accelerations) }}" required>
        </div>

        <div class="form-group">
            <label for="speeding_incidents">Speeding Incidents</label>
            <input type="number" name="speeding_incidents" id="speeding_incidents" class="form-control" value="{{ old('speeding_incidents', $metric->speeding_incidents) }}" required>
        </div>

        <div class="form-group">
            <label for="score">Performance Score</label>
            <input type="number" step="0.01" name="score" id="score" class="form-control" value="{{ old('score', $metric->score) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Metric</button>
    </form>
</div>
@endsection
