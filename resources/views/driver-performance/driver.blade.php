@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Driver Dashboard</h1>
        <div>
            <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Performance Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Driver Performance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">View Metrics</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('driver-performance.index') }}" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-eye"></i> View Performance
                    </a>
                </div>
            </div>
        </div>

        <!-- Assignments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">View Routes</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('driver.assignments.today') }}" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-eye"></i> View Assignments
                    </a>
                </div>
            </div>
        </div>

        <!-- Incidents Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Report Incident
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Submit Report</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('driver.incidents.create') }}" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-plus"></i> Report Incident
                    </a>
                </div>
            </div>
        </div>

        <!-- Vehicle Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Vehicle
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">View Details</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('maintenance.driver.vehicle') }}" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-eye"></i> View Vehicle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Quick Actions</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <a href="{{ route('driver.assignments.index') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-route"></i> View All Assignments
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('driver.incidents.index') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-exclamation-triangle"></i> View Incidents
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('communication.index') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-comments"></i> Communications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
