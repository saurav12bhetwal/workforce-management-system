@extends('layouts.app')

@section('title', 'Manage Designations')
@section('page-title', 'Manage Designations')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="fas fa-briefcase me-2 text-primary"></i>Designation List
            </h5>
            <small class="text-muted">Manage all designations in the organization</small>
        </div>
        <a href="{{ route('admin.designations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Designation
        </a>
    </div>
    <div class="card-body">
        <!-- Search & Filter -->
        <form method="GET" action="{{ route('admin.designations.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search designations..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i> Filter
                </button>
                <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>

        <!-- Designation Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Employees</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($designations as $key => $designation)
                        <tr>
                            <td>{{ $designations->firstItem() + $key }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-2">
                                        <i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <strong>{{ $designation->name }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($designation->department)
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $designation->department->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $designation->users_count }} Employees
                                </span>
                            </td>
                            <td>{{ $designation->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.designations.edit', $designation->id) }}" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteDesignation({{ $designation->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $designation->id }}" 
                                      action="{{ route('admin.designations.destroy', $designation->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No designations found</p>
                                <a href="{{ route('admin.designations.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus me-2"></i> Add First Designation
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $designations->firstItem() }} to {{ $designations->lastItem() }} of {{ $designations->total() }} entries
            </div>
            <div>
                {{ $designations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteDesignation(id) {
        if (confirm('Are you sure you want to delete this designation?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection