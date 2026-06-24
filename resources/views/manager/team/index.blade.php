@extends('layouts.app')

@section('title', 'My Team')
@section('page-title', 'My Team')

@section('content')
<div class="row g-3">
    <!-- Team Stats -->
    <div class="col-md-12">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Team Members</h6>
                        <h3 class="fw-bold mb-0">{{ $teamMembers->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Active Members</h6>
                        <h3 class="fw-bold mb-0 text-success">
                            {{ $teamMembers->where('status', 'active')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-info bg-opacity-10">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Departments</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $departments->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('manager.team.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Search by name, email or code" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                            <a href="{{ route('manager.team.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Team Members Table -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">
                    <i class="fas fa-users me-2 text-primary"></i>Team Members
                </h6>
            </div>
            <div class="card-body p-0">
                @if($teamMembers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamMembers as $key => $member)
                                    <tr>
                                        <td>{{ $teamMembers->firstItem() + $key }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $member->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $member->department->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $member->designation->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $member->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($member->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('manager.team.show', $member->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
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
                                Showing {{ $teamMembers->firstItem() }} to {{ $teamMembers->lastItem() }} 
                                of {{ $teamMembers->total() }} entries
                            </div>
                            <div>
                                {{ $teamMembers->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">No team members found</h5>
                        <p class="text-muted small">Try adjusting your filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-sm i {
        font-size: 32px;
    }
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