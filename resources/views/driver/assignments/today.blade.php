@extends('layouts.app')

@section('title', 'Today\'s Routes - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">Today's Routes</h1>
    <p class="page-subtitle">{{ now()->format('l, F j, Y') }}</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">My Assignments for Today</h5>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Scheduled Time</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->route->route_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->route->route_code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->vehicle->vehicle_number }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->scheduled_start_time }} - {{ $assignment->scheduled_end_time }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Duration: {{ $assignment->route->estimated_duration }} min
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'assigned' ? 'primary' : ($assignment->status === 'in_progress' ? 'warning' : ($assignment->status === 'completed' ? 'success' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignment->route->checkpoints->count() > 0)
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $assignment->completion_percentage }}%"
                                                     aria-valuenow="{{ $assignment->completion_percentage }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $assignment->completion_percentage }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $assignment->checkpointVisits->where('status', 'completed')->count() }} / {{ $assignment->route->checkpoints->count() }} checkpoints
                                            </small>
                                        @else
                                            <span class="text-muted">No checkpoints</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('driver.assignments.show', $assignment) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($assignment->status === 'assigned')
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="startRoute({{ $assignment->id }})" 
                                                        title="Start Route">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @elseif($assignment->status === 'in_progress')
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="completeRoute({{ $assignment->id }})" 
                                                        title="Complete Route">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No routes assigned for today</h5>
                        <p class="text-muted">Check back later or contact your dispatcher for assignments.</p>
                    </div>
                @endif
            </div>
        </div>
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
                        <input type="number" class="form-control" id="actual_distance" name="actual_distance" step="0.1" required>
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

<script>
let currentAssignmentId = null;

function startRoute(assignmentId) {
    if (confirm('Are you sure you want to start this route?')) {
        fetch(`/driver/assignments/${assignmentId}/start`, {
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

function completeRoute(assignmentId) {
    currentAssignmentId = assignmentId;
    const modal = new bootstrap.Modal(document.getElementById('completeRouteModal'));
    modal.show();
}

document.getElementById('completeRouteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch(`/driver/assignments/${currentAssignmentId}/complete`, {
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
</script>
@endsection