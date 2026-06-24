@extends('layouts.app')

@section('title', 'Attendance Management')
@section('page-title', 'Attendance Management')

@section('content')
<div class="row g-3">
    <!-- Statistics Cards -->
    <div class="col-md-12">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Today's Total</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['today_total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-success bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Present Today</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['today_present'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-danger bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Absent Today</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $stats['today_absent'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-info bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">This Month</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $stats['this_month'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Employee</label>
                        <select name="employee" class="form-select form-select-sm">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Department</label>
                        <select name="department" class="form-select form-select-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">From</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">To</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Button -->
    {{-- <div class="col-md-12">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.attendance.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-export me-2"></i> Export CSV
            </a>
        </div>
    </div> --}}

    <!-- Attendance Table -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2 text-primary"></i>Attendance Records
                </h6>
                <span class="badge bg-primary">{{ $attendances->total() }} Records</span>
            </div>
            <div class="card-body p-0">
                @if($attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $key => $attendance)
                                    <tr>
                                        <td>{{ $attendances->firstItem() + $key }}</td>
                                        <td>
                                            <strong>{{ $attendance->attendance_date->format('M d, Y') }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $attendance->user->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $attendance->user->email ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $attendance->user->department->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($attendance->check_in)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i>
                                                    {{ Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->check_out)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-sign-out-alt me-1"></i>
                                                    {{ Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->working_minutes)
                                                @php
                                                    $hours = floor($attendance->working_minutes / 60);
                                                    $minutes = $attendance->working_minutes % 60;
                                                @endphp
                                                <span class="badge bg-primary">
                                                    {{ $hours }}h {{ $minutes }}m
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->check_in)
                                                <span class="badge bg-success">Present</span>
                                            @else
                                                <span class="badge bg-danger">Absent</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.attendance.show', $attendance->id) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-muted small">
                                Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} 
                                of {{ $attendances->total() }} entries
                            </div>
                            <div>
                                {{ $attendances->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-4x text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">No attendance records found</h5>
                        <p class="text-muted small">Try adjusting your filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .table td {
        font-size: 13px;
        vertical-align: middle;
        padding: 12px 8px;
    }
    @media (max-width: 768px) {
        .table {
            font-size: 12px;
        }
        .table td, .table th {
            padding: 8px 4px;
        }
    }
</style>
@endpush
@endsection