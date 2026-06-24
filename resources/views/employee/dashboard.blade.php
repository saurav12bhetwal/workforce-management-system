@extends('layouts.app')

@section('title', 'Employee Dashboard')
@section('page-title', 'Employee Dashboard')

@section('content')
<div class="row g-3">
    <!-- Welcome Card -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body">
                <h4 class="mb-1">Welcome back, {{ Auth::user()->name }}!</h4>
                <p class="mb-0 opacity-75">{{ Carbon\Carbon::now()->format('l, F d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Today's Status -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3"><i class="fas fa-clock me-1"></i>Today's Status</h6>
                @if($isCheckedIn)
                    <div class="bg-success bg-opacity-10 p-4 rounded-3 text-center">
                        <i class="fas fa-check-circle text-success fa-3x mb-2"></i>
                        <h5 class="text-success">Checked In</h5>
                        <p class="text-muted mb-0">Working hours: {{ $workingHours }}</p>
                    </div>
                @elseif($isCheckedOut)
                    <div class="bg-info bg-opacity-10 p-4 rounded-3 text-center">
                        <i class="fas fa-check-double text-info fa-3x mb-2"></i>
                        <h5 class="text-info">Checked Out</h5>
                        <p class="text-muted mb-0">Hours worked: {{ $workingHours }}</p>
                    </div>
                @else
                    <div class="bg-secondary bg-opacity-10 p-4 rounded-3 text-center">
                        <i class="fas fa-hourglass-start text-secondary fa-3x mb-2"></i>
                        <h5 class="text-secondary">Not Checked In</h5>
                        <p class="text-muted mb-0">Please check in to start your day</p>
                    </div>
                @endif
                <div class="mt-3 text-center">
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-fingerprint me-1"></i> Mark Attendance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3"><i class="fas fa-bolt me-1"></i>Quick Actions</h6>
                <div class="d-grid gap-3">
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-fingerprint me-2"></i> Mark Attendance
                    </a>
                    <a href="{{ route('employee.leaves.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus-circle me-2"></i> Apply for Leave
                    </a>
                    <a href="{{ route('employee.leaves.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-clipboard-list me-2"></i> View My Leaves
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Attendance</h6>
                <a href="{{ route('employee.attendance.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentAttendance->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentAttendance as $att)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $att->attendance_date->format('M d, Y') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        In: {{ $att->check_in ? Carbon\Carbon::parse($att->check_in)->format('h:i A') : 'N/A' }}
                                        | Out: {{ $att->check_out ? Carbon\Carbon::parse($att->check_out)->format('h:i A') : 'N/A' }}
                                    </small>
                                </div>
                                @if($att->working_minutes)
                                    <span class="badge bg-success">
                                        {{ floor($att->working_minutes / 60) }}h {{ $att->working_minutes % 60 }}m
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No attendance records found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Leaves -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Recent Leave Requests</h6>
                <a href="{{ route('employee.leaves.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentLeaves->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentLeaves as $leave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $leave->leave_type }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $leave->from_date->format('M d') }} - {{ $leave->to_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $leave->status == 'Pending' ? 'warning' : ($leave->status == 'Approved' ? 'success' : 'danger') }}">
                                    {{ $leave->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No leave requests found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection