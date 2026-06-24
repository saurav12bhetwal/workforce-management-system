@extends('layouts.app')

@section('title', 'Leave Details')
@section('page-title', 'Leave Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Leave Request Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Employee</small>
                            <strong>{{ $leave->user->name ?? 'N/A' }}</strong>
                            <br>
                            <small class="text-muted">{{ $leave->user->email ?? '' }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Leave Type</small>
                            <span class="badge bg-{{ $leave->leave_type_badge_class }} bg-opacity-10 text-{{ $leave->leave_type_badge_class }}">
                                {{ $leave->leave_type }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">From Date</small>
                            <strong>{{ $leave->from_date->format('l, M d, Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">To Date</small>
                            <strong>{{ $leave->to_date->format('l, M d, Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Total Days</small>
                            <strong>{{ $leave->days_count }} day(s)</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $leave->status_badge_class }}">
                                <i class="fas {{ $leave->status_icon }} me-1"></i>
                                {{ $leave->status }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Reason</small>
                            <p class="mb-0">{{ $leave->reason }}</p>
                        </div>
                    </div>
                    @if($leave->status == 'Rejected' && $leave->rejection_reason)
                    <div class="col-md-12">
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger">
                            <small class="text-danger d-block">Rejection Reason</small>
                            <p class="mb-0 text-danger">{{ $leave->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                    @if($leave->status == 'Approved' && $leave->approved_by)
                    <div class="col-md-12">
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success">
                            <small class="text-success d-block">Approved By</small>
                            <p class="mb-0 text-success">
                                {{ $leave->approver->name ?? 'N/A' }} 
                                <small class="text-muted">({{ $leave->approved_at->format('M d, Y h:i A') }})</small>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="border-top pt-3 mt-3">
                    @if($leave->status == 'Pending')
                        <button class="btn btn-success" onclick="approveLeave({{ $leave->id }})">
                            <i class="fas fa-check me-2"></i> Approve
                        </button>
                        <button class="btn btn-danger" onclick="showRejectModal({{ $leave->id }})">
                            <i class="fas fa-times me-2"></i> Reject
                        </button>
                        <form id="approve-form-{{ $leave->id }}" 
                              action="{{ route('admin.leaves.approve', $leave->id) }}" 
                              method="POST" style="display: none;">
                            @csrf
                        </form>
                        <form id="reject-form-{{ $leave->id }}" 
                              action="{{ route('admin.leaves.reject', $leave->id) }}" 
                              method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="rejection_reason" id="reject-reason-{{ $leave->id }}">
                        </form>
                    @endif
                    <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
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

    function approveLeave(id) {
        if (confirm('Are you sure you want to approve this leave request?')) {
            document.getElementById('approve-form-' + id).submit();
        }
    }

    function showRejectModal(id) {
        currentLeaveId = id;
        document.getElementById('rejectionReason').value = '';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    document.getElementById('confirmReject').addEventListener('click', function() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason.');
            return;
        }
        document.getElementById('reject-reason-' + currentLeaveId).value = reason;
        document.getElementById('reject-form-' + currentLeaveId).submit();
    });
</script>
@endpush
@endsection