@extends('layouts.app')

@section('title', 'Manage Leaves')
@section('page-title', 'Manage Leaves')

@section('content')
<div class="row g-3">
    <!-- Statistics Cards -->
    <div class="col-md-12">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Leaves</h6>
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
                <form method="GET" action="{{ route('admin.leaves.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                            <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary btn-sm">
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
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Leave Requests
                </h6>
                <span class="badge bg-primary">{{ $leaves->total() }} Records</span>
            </div>
            <div class="card-body p-0">
                @if($leaves->count() > 0)
                    <form id="bulkActionForm" action="" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>#</th>
                                        <th>Employee</th>
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
                                            <td>
                                                @if($leave->status == 'Pending')
                                                    <input type="checkbox" name="leave_ids[]" value="{{ $leave->id }}" class="leave-checkbox">
                                                @endif
                                            </td>
                                            <td>{{ $leaves->firstItem() + $key }}</td>
                                            <td>
                                                <strong>{{ $leave->user->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $leave->user->email ?? '' }}</small>
                                            </td>
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
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.leaves.show', $leave->id) }}" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($leave->status == 'Pending')
                                                        <button type="button" class="btn btn-outline-success" 
                                                                onclick="approveLeave({{ $leave->id }})" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="showRejectModal({{ $leave->id }})" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Approve Form -->
                                                <form id="approve-form-{{ $leave->id }}" 
                                                      action="{{ route('admin.leaves.approve', $leave->id) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                </form>

                                                <!-- Reject Form -->
                                                <form id="reject-form-{{ $leave->id }}" 
                                                      action="{{ route('admin.leaves.reject', $leave->id) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="rejection_reason" id="reject-reason-{{ $leave->id }}">
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    
                    <!-- Bulk Actions -->
                    <div class="p-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <button class="btn btn-sm btn-success" onclick="bulkApprove()">
                                    <i class="fas fa-check me-1"></i> Bulk Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="bulkReject()">
                                    <i class="fas fa-times me-1"></i> Bulk Reject
                                </button>
                                <span class="text-muted small ms-2" id="selectedCount">0 selected</span>
                            </div>
                            <div>
                                {{ $leaves->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">No leave requests found</h5>
                        <p class="text-muted small">Try adjusting your filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger me-2"></i>Reject Leave Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Please provide a reason for rejecting this leave request:</p>
                <textarea class="form-control" id="rejectionReason" rows="3" 
                          placeholder="Enter rejection reason..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">
                    <i class="fas fa-times me-2"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentLeaveId = null;

    // Select All
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.leave-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    // Update selected count
    document.querySelectorAll('.leave-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const count = document.querySelectorAll('.leave-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = `${count} selected`;
    }

    // Approve single leave
    function approveLeave(id) {
        if (confirm('Are you sure you want to approve this leave request?')) {
            document.getElementById('approve-form-' + id).submit();
        }
    }

    // Show reject modal
    function showRejectModal(id) {
        currentLeaveId = id;
        document.getElementById('rejectionReason').value = '';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    // Confirm reject
    document.getElementById('confirmReject')?.addEventListener('click', function() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason.');
            return;
        }
        document.getElementById('reject-reason-' + currentLeaveId).value = reason;
        document.getElementById('reject-form-' + currentLeaveId).submit();
    });

    // Bulk Approve
    function bulkApprove() {
        const selected = document.querySelectorAll('.leave-checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one leave request.');
            return;
        }
        if (confirm(`Are you sure you want to approve ${selected.length} leave request(s)?`)) {
            const form = document.getElementById('bulkActionForm');
            form.action = "{{ route('admin.leaves.bulk-approve') }}";
            form.submit();
        }
    }

    // Bulk Reject
    function bulkReject() {
        const selected = document.querySelectorAll('.leave-checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one leave request.');
            return;
        }
        const reason = prompt(`Please provide a reason for rejecting ${selected.length} leave request(s):`);
        if (reason && reason.trim()) {
            const form = document.getElementById('bulkActionForm');
            form.action = "{{ route('admin.leaves.bulk-reject') }}";
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rejection_reason';
            input.value = reason.trim();
            form.appendChild(input);
            form.submit();
        }
    }
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