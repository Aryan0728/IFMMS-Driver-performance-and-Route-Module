@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-route text-primary"></i> Edit Route: {{ $route->route_name }}
        </h1>
        <a href="{{ route('route-management.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Routes
        </a>
    </div>

    <!-- Card -->
    <div class="card border-left-primary shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-map-marked-alt"></i> Route Details & Waypoints
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('route-management.update', $route) }}">
                @csrf
                @method('PUT')

                <!-- Route Name & Driver -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag text-primary"></i> Route Name *
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   required 
                                   value="{{ old('name', $route->route_name) }}" 
                                   placeholder="e.g., Downtown Delivery Route">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="assigned_driver_id" class="form-label">
                                <i class="fas fa-user text-primary"></i> Driver *
                            </label>
                            <select class="form-control @error('assigned_driver_id') is-invalid @enderror" 
                                    id="assigned_driver_id" 
                                    name="assigned_driver_id" required>
                                <option value="">Select a Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" 
                                        {{ old('assigned_driver_id', $route->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->license_number ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_driver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Vehicle & Status -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle_id" class="form-label">
                                <i class="fas fa-truck text-primary"></i> Vehicle *
                            </label>
                            <select class="form-control @error('vehicle_id') is-invalid @enderror" 
                                    id="vehicle_id" name="vehicle_id" required>
                                <option value="">Select a Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" 
                                        {{ old('vehicle_id', $route->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->license_plate }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle text-primary"></i> Status
                            </label>
                            <select class="form-control" id="status" name="status">
                                <option value="planned" {{ old('status', $route->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="in_progress" {{ old('status', $route->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $route->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="delayed" {{ old('status', $route->status) == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                <option value="canceled" {{ old('status', $route->status) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Time Fields -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_time" class="form-label">
                                <i class="fas fa-clock text-primary"></i> Start Time *
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" name="start_time" 
                                   required 
                                   value="{{ old('start_time', $route->start_time ? $route->start_time->format('Y-m-d\TH:i') : '') }}">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estimated_end_time" class="form-label">
                                <i class="fas fa-clock text-primary"></i> Estimated End Time *
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('estimated_end_time') is-invalid @enderror" 
                                   id="estimated_end_time" 
                                   name="estimated_end_time" required 
                                   value="{{ old('estimated_end_time', $route->estimated_end_time ? $route->estimated_end_time->format('Y-m-d\TH:i') : '') }}">
                            @error('estimated_end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Distance & Duration -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estimated_distance" class="form-label">
                                <i class="fas fa-road text-primary"></i> Estimated Distance (miles) *
                            </label>
                            <input type="number" 
                                   class="form-control @error('estimated_distance') is-invalid @enderror" 
                                   id="estimated_distance" name="estimated_distance" 
                                   step="0.1" min="0" required 
                                   value="{{ old('estimated_distance', $route->estimated_distance) }}" 
                                   placeholder="e.g., 45.5">
                            @error('estimated_distance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estimated_duration" class="form-label">
                                <i class="fas fa-hourglass-half text-primary"></i> Estimated Duration (minutes) *
                            </label>
                            <input type="number" 
                                   class="form-control @error('estimated_duration') is-invalid @enderror" 
                                   id="estimated_duration" 
                                   name="estimated_duration" min="1" required 
                                   value="{{ old('estimated_duration', $route->estimated_duration) }}" 
                                   placeholder="e.g., 120">
                            @error('estimated_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Waypoints Map -->
                <div class="form-group">
                    <label for="waypoints" class="form-label">
                        <i class="fas fa-map-marker-alt text-primary"></i> Route Waypoints (Click on Map to Add)
                    </label>
                    <div id="waypoints-map" 
                         style="height: 400px; width: 100%; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 0.375rem;">
                    </div>
                    <textarea class="form-control @error('optimized_waypoints') is-invalid @enderror" 
                              id="waypoints" 
                              name="optimized_waypoints" rows="6" required readonly 
                              placeholder='Waypoints will appear here as JSON'>
                        {{ old('optimized_waypoints', $route->optimized_waypoints ?? '') }}
                    </textarea>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Click on the map to add waypoints. Drag markers to adjust. Waypoints will be saved in JSON format.
                    </small>
                    @error('optimized_waypoints')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label for="notes" class="form-label">
                        <i class="fas fa-sticky-note text-primary"></i> Notes
                    </label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                              placeholder="Any additional notes about this route...">{{ old('notes', $route->notes) }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save"></i> Update Route
                    </button>
                    <a href="{{ route('route-management.show', $route) }}" class="btn btn-secondary btn-lg shadow-sm">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add basic validation for end time to be after start time
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('estimated_end_time');

            startTimeInput.addEventListener('change', function() {
                endTimeInput.min = this.value;
            });

            // Map-based waypoint selection
            var map = L.map('waypoints-map').setView([-17.7134, 178.0650], 7); // Centered on Fiji
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var waypoints = [];
            var markers = [];

            // Load existing waypoints if available
            const existingWaypoints = @json($route->optimized_waypoints ?? []);
            if (existingWaypoints && Array.isArray(existingWaypoints)) {
                waypoints = existingWaypoints;
                waypoints.forEach(function(wp) {
                    addWaypoint(wp.lat, wp.lng, wp.name);
                });
            }

            function updateWaypointsTextarea() {
                document.getElementById('waypoints').value = JSON.stringify(waypoints, null, 2);
            }

            function addWaypoint(lat, lng, name) {
                var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                var wpName = name || 'Waypoint ' + (waypoints.length + 1);
                waypoints.push({ name: wpName, lat: lat, lng: lng });
                markers.push(marker);

                marker.bindPopup(wpName).openPopup();

                marker.on('dragend', function(e) {
                    var pos = e.target.getLatLng();
                    var i = markers.indexOf(marker);
                    if (i !== -1) {
                        waypoints[i].lat = pos.lat;
                        waypoints[i].lng = pos.lng;
                        updateWaypointsTextarea();
                    }
                });

                updateWaypointsTextarea();
            }

            map.on('click', function(e) {
                addWaypoint(e.latlng.lat, e.latlng.lng);
            });
        });
    </script>
@endpush
@endsection
