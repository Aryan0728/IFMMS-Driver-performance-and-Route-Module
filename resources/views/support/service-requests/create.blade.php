@extends('layouts.app')

@section('title', 'Create Service Request - IFMMS')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary bg-opacity-10 me-3">
                                <i class="fas fa-plus-circle text-primary fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 fw-bold">Create Service Request</h1>
                                <p class="text-muted mb-0">Submit a new service or support request</p>
                            </div>
                        </div>
                        <a href="{{ route('support.service-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('support.service-requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Request Details</h5>
                        
                        <div class="row g-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    <option value="breakdown" {{ old('category') == 'breakdown' ? 'selected' : '' }}>
                                        🚨 Breakdown
                                    </option>
                                    <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>
                                        🔧 Maintenance
                                    </option>
                                    <option value="inspection" {{ old('category') == 'inspection' ? 'selected' : '' }}>
                                        🔍 Inspection
                                    </option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>
                                        📋 Other
                                    </option>
                                </select>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        🟢 Low - Can wait
                                    </option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                        🟡 Medium - Soon as possible
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        🟠 High - Urgent
                                    </option>
                                    <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>
                                        🔴 Critical - Immediate attention
                                    </option>
                                </select>
                                @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Vehicle -->
                            <div class="col-md-6">
                                <label class="form-label">Vehicle (Optional)</label>
                                <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                                    <option value="">No specific vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                       value="{{ old('location') }}" placeholder="Current location or address">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                                       value="{{ old('subject') }}" placeholder="Brief description of the issue" required>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Provide detailed information about your request..." required>{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Please include as much detail as possible to help us resolve your request quickly.
                                </small>
                            </div>

                            <!-- Attachments -->
                            <div class="col-12">
                                <label class="form-label">Attachments (Optional)</label>
                                <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" 
                                       multiple accept="image/*,.pdf,.doc,.docx">
                                @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    You can attach multiple files (images, PDFs, documents). Max 10MB per file.
                                </small>
                            </div>

                            <!-- GPS Coordinates (Hidden) -->
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

<script>
// Get user's location if available
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
    });
}

// Auto-save form data
let formData = {};
const form = document.querySelector('form');
const inputs = form.querySelectorAll('input, select, textarea');

// Load saved data
const savedData = localStorage.getItem('serviceRequestDraft');
if (savedData) {
    formData = JSON.parse(savedData);
    inputs.forEach(input => {
        if (formData[input.name] && input.type !== 'file') {
            input.value = formData[input.name];
        }
    });
}

// Save data on change
inputs.forEach(input => {
    input.addEventListener('change', function() {
        if (input.type !== 'file') {
            formData[input.name] = input.value;
            localStorage.setItem('serviceRequestDraft', JSON.stringify(formData));
        }
    });
});

// Clear saved data on submit
form.addEventListener('submit', function() {
    localStorage.removeItem('serviceRequestDraft');
});
</script>
@endsection