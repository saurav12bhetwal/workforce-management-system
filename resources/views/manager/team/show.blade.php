@extends('layouts.app')

@section('title', 'Team Member Details')
@section('page-title', 'Team Member Details')

@section('content')
<div class="row g-3">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-secondary"></i>
                </div>
                <h4>{{ $member->name }}</h4>
                <p class="text-muted">{{ $member->employee_code ?? 'N/A' }}</p>
                
                <div class="d-grid gap-2">
                    <span class="badge bg-{{ $member->status == 'active' ? 'success' : 'danger' }} p-2">
                        {{ ucfirst($member->status) }}
                    </span>
                </div>

                <hr>

                <div class="text-start">
                    <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> {{ $member->email }}</p>
                    <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> {{ $member->phone ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-building me-2"></i>Department:</strong> {{ $member->department->name ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-briefcase me-2"></i>Designation:</strong> {{ $member->designation->name ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-user-tag me-2"></i>Role:</strong> {{ $member->roles->first()?->name ?? 'No Role' }}</p>
                    <p><strong><i class="fas fa-calendar-plus me-2"></i>Joined:</strong> {{ $member->created_at->format('M d, Y') }}</p>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('manager.team.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Team
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance & Leave Summary -->
    <div class="col-md-8">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Total Attendance</h6>
                        <h2 class="fw-bold">{{ $member->attendances->count() }}</h2>
                        <small class="text-success">Days Present</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Total Leaves</h6>
                        <h2 class="fw-bold">{{ $member->leaveRequests->count() }}</h2>
                        <small class="text-warning">Leave Requests</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Recent Attendance</h6>
            </div>
            <div class="card-body p-0">
                @if($member->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($member->attendances->take(5) as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                        <td>{{ $attendance->check_in ? Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : 'N/A' }}</td>
                                        <td>{{ $attendance->check_out ? Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : 'N/A' }}</td>
                                        <td>
                                            @if($attendance->working_minutes)
                                                {{ floor($attendance->working_minutes / 60) }}h {{ $attendance->working_minutes % 60 }}m
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No attendance records found</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Leaves -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Recent Leaves</h6>
            </div>
            <div class="card-body p-0">
                @if($member->leaveRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($member->leaveRequests->take(5) as $leave)
                                    <tr>
                                        <td>{{ $leave->leave_type }}</td>
                                        <td>{{ $leave->from_date->format('M d, Y') }}</td>
                                        <td>{{ $leave->to_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $leave->status_badge_class }}">
                                                {{ $leave->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No leave records found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection