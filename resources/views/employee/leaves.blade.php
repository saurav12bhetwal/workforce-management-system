@extends('layouts.app')

@section('title', 'My Leaves')
@section('page-title', 'My Leaves')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Leave History
                </h5>
                <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Apply Leave
                </a>
            </div>
            <div class="card-body">
                @if($allLeaves->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Leave Type</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Days</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allLeaves as $key => $leave)
                                    <tr>
                                        <td>{{ $allLeaves->firstItem() + $key }}</td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $leave->leave_type }}
                                            </span>
                                        </td>
                                        <td>{{ $leave->from_date->format('M d, Y') }}</td>
                                        <td>{{ $leave->to_date->format('M d, Y') }}</td>
                                        <td>{{ $leave->from_date->diffInDays($leave->to_date) + 1 }}</td>
                                        <td>{{ Str::limit($leave->reason, 30) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $leave->status == 'Pending' ? 'warning' : ($leave->status == 'Approved' ? 'success' : 'danger') }}">
                                                {{ $leave->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $allLeaves->firstItem() }} to {{ $allLeaves->lastItem() }} of {{ $allLeaves->total() }} entries
                        </div>
                        <div>
                            {{ $allLeaves->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No leave requests found</p>
                        <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus me-2"></i> Apply for Leave
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection