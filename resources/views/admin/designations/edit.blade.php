@extends('layouts.app')

@section('title', 'Edit Designation')
@section('page-title', 'Edit Designation')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2 text-primary"></i>Edit Designation
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.designations.update', $designation->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Designation Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $designation->name) }}" 
                               placeholder="Enter designation name" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" 
                                    {{ old('department_id', $designation->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employees Count</label>
                        <p class="form-control-static">
                            <span class="badge bg-info">{{ $designation->users()->count() }} Employees</span>
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Current Details</h6>
                        <p class="small mb-1">
                            <strong>Created:</strong> {{ $designation->created_at->format('M d, Y H:i') }}
                        </p>
                        <p class="small mb-0">
                            <strong>Department:</strong> {{ $designation->department->name ?? 'Not Assigned' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Update Designation
                </button>
                <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection