@extends('layouts.app')

@section('title', 'My Leaves')
@section('page-title', 'My Leaves')

@section('content')
<div class="row g-3">
    <!-- Statistics Cards -->
    <div class="col-md-12">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Leaves</h6>
                                <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-25 p-3 rounded-3">
                                <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-warning bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Pending</h6>
                                <h3 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-25 p-3 rounded-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-success bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Approved</h6>
                                <h3 class="fw-bold mb-0 text-success">{{ $stats['approved'] }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-25 p-3 rounded-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-danger bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Rejected</h6>
                                <h3 class="fw-bold mb-0 text-danger">{{ $stats['rejected'] }}</h3>
                            </div>
                            <div class="bg-danger bg-opacity-25 p-3 rounded-3">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave List -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="mb-0">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Leave Requests
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
                                    <th>Actions</th>
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
                                        <td>
                                            @if($leave->status == 'Pending')
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="cancelLeave({{ $leave->id }})"
                                                        title="Cancel this request">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <form id="cancel-form-{{ $leave->id }}" 
                                                      action="{{ route('employee.leaves.cancel', $leave->id) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-muted small">
                                Showing {{ $leaves->firstItem() }} to {{ $leaves->lastItem() }} 
                                of {{ $leaves->total() }} entries
                            </div>
                            <div>
                                {{ $leaves->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">No leave requests yet</h5>
                        <p class="text-muted small">Apply for your first leave request</p>
                        <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus me-2"></i> Apply Leave
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function cancelLeave(id) {
        if (confirm('Are you sure you want to cancel this leave request?')) {
            document.getElementById('cancel-form-' + id).submit();
        }
    }

    // Initialize tooltips
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