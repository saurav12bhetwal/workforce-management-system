@extends('layouts.app')

@section('title', 'Manage Departments')
@section('page-title', 'Manage Departments')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="fas fa-building me-2 text-primary"></i>Department List
            </h5>
            <small class="text-muted">Manage all departments in the organization</small>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Department
        </a>
    </div>
    <div class="card-body">
        <!-- Search -->
        <form method="GET" action="{{ route('admin.departments.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search departments..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Department Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        <th>Employees</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $key => $department)
                        <tr>
                            <td>{{ $departments->firstItem() + $key }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                    <strong>{{ $department->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $department->users_count }} Employees
                                </span>
                            </td>
                            <td>{{ $department->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.departments.edit', $department->id) }}" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteDepartment({{ $department->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $department->id }}" 
                                      action="{{ route('admin.departments.destroy', $department->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No departments found</p>
                                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus me-2"></i> Add First Department
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
                Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} entries
            </div>
            <div>
                {{ $departments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteDepartment(id) {
        if (confirm('Are you sure you want to delete this department?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection