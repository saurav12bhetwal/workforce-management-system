@extends('layouts.app')

@section('title', 'Manage Employees')
@section('page-title', 'Manage Employees')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="fas fa-users me-2 text-primary"></i>Employee List
            </h5>
            <small class="text-muted">Manage all employees in the system</small>
        </div>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Employee
        </a>
    </div>
    <div class="card-body">
        <!-- Search & Filter -->
        <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email or code" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i> Filter
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Clear
                </a>
            </div>
        </form>

        <!-- Employee Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Role</th>
                        <th>Manager</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $key => $employee)
                        <tr>
                            <td>{{ $employees->firstItem() + $key }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $employee->employee_code ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                    </div>
                                    <strong>{{ $employee->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $employee->department->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $employee->roles->first()?->name == 'Admin' ? 'danger' : ($employee->roles->first()?->name == 'Manager' ? 'warning' : 'secondary') }}">
                                    {{ $employee->roles->first()?->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td>{{ $employee->manager->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.employees.show', $employee->id) }}" 
                                       class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteEmployee({{ $employee->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Form -->
                                <form id="delete-form-{{ $employee->id }}" 
                                      action="{{ route('admin.employees.destroy', $employee->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No employees found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} entries
            </div>
            <div>
                {{ $employees->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteEmployee(id) {
        if (confirm('Are you sure you want to delete this employee?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush

@push('styles')
<style>
    .avatar-sm i {
        font-size: 32px;
    }
</style>
@endpush
@endsection