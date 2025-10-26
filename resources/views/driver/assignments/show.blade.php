@extends('layouts.app')

@section('title', 'Assignment Details - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">Assignment Details</h1>
    <p class="page-subtitle">{{ $assignment->route->route_name }} - {{ $assignment->assignment_date->format('M d, Y') }}</p>
</div>

<div class="row">
    <!-- Assignment Overview -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Route Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Route Details</h6>
                        <p><strong>Route Name:</strong> {{ $assignment->route->route_name }}</p>
                        <p><strong>Route Code:</strong> {{ $assignment->route->route_code }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($assignment->route->route_type) }}</p>
                        <p><strong>Priority:</strong> {{ ucfirst($assignment->route->priority) }}</p>
                        <p><strong>Distance:</strong> {{ number_format($assignment->route->total_distance, 1) }} km</p>
                        <p><strong>Estimated Duration:</strong> {{ $assignment->route->estimated_duration }} minutes</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Assignment Details</h6>
                        <p><strong>Date:</strong> {{ $assignment->assignment_date->format('l, F j, Y') }}</p>
                        <p><strong>Scheduled Time:</strong> {{ $assignment->scheduled_start_time }} - {{ $assignment->scheduled_end_time }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $assignment->status === 'assigned' ? 'primary' : ($assignment->status === 'in_progress' ? 'warning' : ($assignment->status === 'completed' ? 'success' : 'secondary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                            </span>
                        </p>
                        @if($assignment->actual_start_time)
                            <p><strong>Actual Start:</strong> {{ $assignment->actual_start_time }}</p>
                        @endif
                        @if($assignment->actual_end_time)
                            <p><strong>Actual End:</strong> {{ $assignment->actual_end_time }}</p>
                        @endif
                        @if($assignment->actual_distance)
                            <p><strong>Actual Distance:</strong> {{ number_format($assignment->actual_distance, 1) }} km</p>
                        @endif
                        @if($assignment->fuel_consumed)
                            <p><strong>Fuel Consumed:</strong> {{ number_format($assignment->fuel_consumed, 1) }} liters</p>
                        @endif
                    </div>
                </div>
                
                @if($assignment->route->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p>{{ $assignment->route->description }}</p>
                    </div>
                @endif
                
                @if($assignment->route->special_instructions)
                    <div class="mt-3">
                        <h6>Special Instructions</h6>
                        <div class="alert alert-info">
                            {{ $assignment->route->special_instructions }}
                        </div>
                    </div>
                @endif
                
                @if($assignment->notes)
                    <div class="mt-3">
                        <h6>Notes</h6>
                        <p>{{ $assignment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Checkpoints -->
        @if($assignment->route->checkpoints->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Checkpoints</h5>
                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $assignment->completion_percentage }}%"
                         aria-valuenow="{{ $assignment->completion_percentage }}" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted">
                    {{ $assignment->checkpointVisits->where('status', 'completed')->count() }} of {{ $assignment->route->checkpoints->count() }} completed
                </small>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($assignment->route->checkpoints as $checkpoint)
                        @php
                            $visit = $assignment->checkpointVisits->where('checkpoint_id', $checkpoint->id)->first();
                        @endphp
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $visit && $visit->status === 'completed' ? 'success' : 'secondary' }} me-2">
                                            {{ $checkpoint->sequence_order }}
                                        </span>
                                        <div>
                                            <h6 class="mb-1">{{ $checkpoint->checkpoint_name }}</h6>
                                            <p class="mb-1 text-muted">{{ $checkpoint->address }}</p>
                                            <small class="text-muted">
                                                Type: {{ ucfirst(str_replace('_', ' ', $checkpoint->checkpoint_type)) }} | 
                                                Est. Duration: {{ $checkpoint->estimated_duration }} min
                                            </small>
                                        </div>
                                    </div>
                                    
                                    @if($checkpoint->special_instructions)
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="fas fa-info-circle"></i> {{ $checkpoint->special_instructions }}
                                            </small>
                                        </div>
                                    @endif
                                    
                                    @if($visit && $visit->visited_at)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle"></i> Visited at {{ $visit->visited_at->format('H:i') }}
                                            </small>
                                            @if($visit->notes)
                                                <br>
                                                <small class="text-muted">Note: {{ $visit->notes }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                @if($assignment->status === 'in_progress' && (!$visit || $visit->status !== 'completed'))
                                    <button class="btn btn-sm btn-success" 
                                            onclick="markCheckpointVisited({{ $checkpoint->id }})"
                                            title="Mark as Visited">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Vehicle Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Vehicle Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Vehicle Number:</strong> {{ $assignment->vehicle->vehicle_number }}</p>
                <p><strong>Make & Model:</strong> {{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</p>
                <p><strong>Year:</strong> {{ $assignment->vehicle->year }}</p>
                <p><strong>License Plate:</strong> {{ $assignment->vehicle->license_plate }}</p>
                <p><strong>Fuel Type:</strong> {{ ucfirst($assignment->vehicle->fuel_type) }}</p>
                <p><strong>Current Mileage:</strong> {{ number_format($assignment->vehicle->mileage) }} km</p>
            </div>
        </div>

        <!-- Actions -->
        @if($assignment->status === 'assigned' || $assignment->status === 'in_progress')
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Actions</h5>
            </div>
            <div class="card-body">
                @if($assignment->status === 'assigned')
                    <button class="btn btn-success w-100 mb-2" onclick="startRoute()">
                        <i class="fas fa-play"></i> Start Route
                    </button>
                @elseif($assignment->status === 'in_progress')
                    <button class="btn btn-warning w-100 mb-2" onclick="completeRoute()">
                        <i class="fas fa-check"></i> Complete Route
                    </button>
                @endif
                
                <a href="{{ route('driver.assignments.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Back to Schedule
                </a>
            </div>
        </div>
        @endif

        <!-- Performance Summary -->
        @if($assignment->status === 'completed')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Performance Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-{{ $assignment->is_on_time ? 'success' : 'warning' }}">
                            {{ $assignment->is_on_time ? 'On Time' : 'Late' }}
                        </h4>
                        <small class="text-muted">Delivery Status</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ $assignment->completion_percentage }}%</h4>
                        <small class="text-muted">Checkpoints</small>
                    </div>
                </div>
                
                @if($assignment->fuel_efficiency)
                    <hr>
                    <div class="text-center">
                        <h5 class="text-primary">{{ $assignment->fuel_efficiency }} km/L</h5>
                        <small class="text-muted">Fuel Efficiency</small>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Complete Route Modal -->
<div class="modal fade" id="completeRouteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Route</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeRouteForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="actual_distance" class="form-label">Actual Distance (km)</label>
                        <input type="number" class="form-control" id="actual_distance" name="actual_distance" 
                               step="0.1" value="{{ $assignment->route->total_distance }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="fuel_consumed" class="form-label">Fuel Consumed (liters)</label>
                        <input type="number" class="form-control" id="fuel_consumed" name="fuel_consumed" step="0.1" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Route</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark Checkpoint Modal -->
<div class="modal fade" id="checkpointModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Checkpoint as Visited</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkpointForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="checkpoint_notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="checkpoint_notes" name="notes" rows="3" 
                                  placeholder="Any observations or issues at this checkpoint..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Visited</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentCheckpointId = null;

function startRoute() {
    if (confirm('Are you sure you want to start this route?')) {
        fetch(`/driver/assignments/{{ $assignment->id }}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Route started successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while starting the route.');
        });
    }
}

function completeRoute() {
    const modal = new bootstrap.Modal(document.getElementById('completeRouteModal'));
    modal.show();
}

function markCheckpointVisited(checkpointId) {
    currentCheckpointId = checkpointId;
    const modal = new bootstrap.Modal(document.getElementById('checkpointModal'));
    modal.show();
}

document.getElementById('completeRouteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch(`/driver/assignments/{{ $assignment->id }}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Route completed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while completing the route.');
    });
});

document.getElementById('checkpointForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch(`/driver/assignments/{{ $assignment->id }}/checkpoint/${currentCheckpointId}/visit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Checkpoint marked as visited!');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while marking the checkpoint.');
    });
});
</script>
@endsection