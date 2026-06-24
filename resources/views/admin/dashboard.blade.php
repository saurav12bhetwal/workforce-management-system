<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<!-- Quick Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-primary ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Total Employees</h6>
                        <h3 class="fw-bold mb-0">{{ $quickStats['total_employees'] }}</h3>
                    </div>
                    <div class="bg-primary  p-3 rounded-3">
                        <i class="fas fa-users fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-success ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Present Today</h6>
                        <h3 class="fw-bold mb-0 ">{{ $quickStats['today_checkins'] }}</h3>
                    </div>
                    <div class="bg-success  p-3 rounded-3">
                        <i class="fas fa-check-circle fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-warning ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">On Leave</h6>
                        <h3 class="fw-bold mb-0 ">{{ $quickStats['today_on_leave'] }}</h3>
                    </div>
                    <div class="bg-warning  p-3 rounded-3">
                        <i class="fas fa-clock fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-danger ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Absent Today</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $quickStats['today_absent'] }}</h3>
                    </div>
                    <div class="bg-danger  p-3 rounded-3">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-info ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Departments</h6>
                        <h3 class="fw-bold mb-0 ">{{ $quickStats['total_departments'] }}</h3>
                    </div>
                    <div class="bg-info  p-3 rounded-3">
                        <i class="fas fa-building fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-purple ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Pending Leaves</h6>
                        <h3 class="fw-bold mb-0 ">{{ $quickStats['pending_approvals'] }}</h3>
                    </div>
                    <div class="bg-purple  p-3 rounded-3">
                        <i class="fas fa-clipboard-list fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-secondary ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Leaves This Month</h6>
                        <h3 class="fw-bold mb-0">{{ $quickStats['this_month_leaves'] }}</h3>
                    </div>
                    <div class="bg-secondary  p-3 rounded-3">
                        <i class="fas fa-calendar-alt fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-teal ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class=" mb-1">Attendance Rate</h6>
                        <h3 class="fw-bold mb-0">
                            @php
                                $rate = $quickStats['total_employees'] > 0 
                                    ? round(($quickStats['today_checkins'] / $quickStats['total_employees']) * 100) 
                                    : 0;
                            @endphp
                            {{ $rate }}%
                        </h3>
                    </div>
                    <div class="bg-teal  p-3 rounded-3">
                        <i class="fas fa-chart-pie fa-2x "></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><i class="fas fa-building me-2 "></i>Department Overview</h6>
            </div>
            <div class="card-body p-0">
                @if($departmentStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Department</th>
                                    <th>Total Employees</th>
                                    <th>Present Today</th>
                                    <th>Attendance %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentStats as $dept)
                                    @php
                                        $presentCount = $dept->present_today_count ?? 0;
                                        $totalCount = $dept->users_count ?? 0;
                                        $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $dept->name }}</strong></td>
                                        <td>{{ $totalCount }}</td>
                                        <td>{{ $presentCount }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-75" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $percentage }}%;" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <span class="small">{{ $percentage }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class=" mb-0">No departments found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Row -->
<div class="row g-3">
    <!-- Recent Leaves -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2 "></i>Recent Leave Requests</h6>
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentLeaves->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentLeaves as $leave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $leave->user->name ?? 'N/A' }}</strong>
                                    <span class="badge bg-secondary   ms-2">{{ $leave->leave_type }}</span>
                                    <br>
                                    <small class="">
                                        {{ $leave->from_date->format('M d') }} - {{ $leave->to_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $leave->status_badge_class }}">
                                    {{ $leave->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x  mb-2"></i>
                        <p class=" mb-0">No recent leave requests</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Check-ins -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-fingerprint me-2 "></i>Today's Recent Check-ins</h6>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($todayCheckIns->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todayCheckIns as $attendance)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $attendance->user->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $attendance->check_in ? Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : 'N/A' }}
                                    </small>
                                </div>
                                <span class="badge bg-success">Checked In</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-fingerprint fa-3x  mb-2"></i>
                        <p class=" mb-0">No check-ins yet today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Employees -->
<div class="row g-3 mt-2">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-user-plus me-2 "></i>New Employees</h6>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentEmployees->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentEmployees as $employee)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    <span class="badge bg-primary   ms-2">{{ $employee->department->name ?? 'N/A' }}</span>
                                    <br>
                                    <small class="">
                                        <i class="fas fa-envelope me-1"></i> {{ $employee->email }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-plus fa-3x  mb-2"></i>
                        <p class=" mb-0">No new employees</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    . { color: #6f42c1; }
    .bg-purple { background-color: #6f42c1; }
    .text-teal { color: #20c997; }
    .bg-teal { background-color: #20c997; }
    . { opacity: 0.1; }
    . { opacity: 0.25; }
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 12px 20px;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease-in-out;
    }
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }
    }
</style>
@endpush
@endsection