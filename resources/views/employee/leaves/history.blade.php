@extends('layouts.app')

@section('title', 'Leave History')
@section('page-title', 'Leave History')

@section('content')
<div class="row g-3">
    <!-- Statistics Cards -->
    <div class="col-md-12">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-warning bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Pending</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-success bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Approved</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['approved'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-danger bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Rejected</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $stats['rejected'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('employee.leaves.history') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Leave Type</label>
                        <select name="leave_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type }}" {{ request('leave_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
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
                            <a href="{{ route('employee.leaves.history') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Leave Table -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="mb-0">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Leave History
                </h6>
                <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i> Apply Leave
                </a>
            </div>
            <div class="card-body p-0">
                @if($leaves->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $key => $leave)
                                    <tr>
                                        <td>{{ $leaves->firstItem() + $key }}</td>
                                        <td>
                                            <span class="badge bg-{{ $leave->leave_type_badge_class }} bg-opacity-10 text-{{ $leave->leave_type_badge_class }}">
                                                {{ $leave->leave_type }}
                                            </span>
                                        </td>
                                        <td>{{ $leave->from_date->format('M d, Y') }}</td>
                                        <td>{{ $leave->to_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $leave->days_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $leave->status_badge_class }}">
                                                <i class="fas {{ $leave->status_icon }} me-1"></i>
                                                {{ $leave->status }}
                                            </span>
                                            @if($leave->status == 'Rejected' && $leave->rejection_reason)
                                                <i class="fas fa-info-circle text-muted ms-1" 
                                                   title="Reason: {{ $leave->rejection_reason }}" 
                                                   data-bs-toggle="tooltip"></i>
                                            @endif
                                        </td>
                                        <td>{{ $leave->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-muted small">
                                Showing {{ $leaves->firstItem() }} to {{ $leaves->lastItem() }} 
                                of {{ $leaves->total() }} entries
                            </div>
                            <div>
                                {{ $leaves->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">No leave records found</h5>
                        <p class="text-muted small">
                            @if(request()->has('status') || request()->has('leave_type') || 
                                request()->has('from_date') || request()->has('to_date'))
                                Try adjusting your filters
                            @else
                                You haven't applied for any leaves yet
                            @endif
                        </p>
                        <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus me-2"></i> Apply Leave
                        </a>
                        @if(request()->has('status') || request()->has('leave_type') || 
                            request()->has('from_date') || request()->has('to_date'))
                            <a href="{{ route('employee.leaves.history') }}" class="btn btn-secondary btn-sm mt-2">
                                <i class="fas fa-times me-2"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

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