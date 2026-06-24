@extends('layouts.app')

@section('title', 'Add New Designation')
@section('page-title', 'Add New Designation')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2 text-primary"></i>Create New Designation
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.designations.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Designation Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Enter designation name (e.g., Senior Developer)" required>
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
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select the department this designation belongs to</small>
                        @error('department_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Information</h6>
                        <p class="small text-muted mb-1">
                            <i class="fas fa-check-circle text-success me-1"></i> 
                            Designations are job titles/positions in the organization
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-check-circle text-success me-1"></i> 
                            Each designation belongs to a specific department
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Create Designation
                </button>
                <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection