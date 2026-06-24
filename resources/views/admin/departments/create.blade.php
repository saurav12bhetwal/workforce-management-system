@extends('layouts.app')

@section('title', 'Add New Department')
@section('page-title', 'Add New Department')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2 text-primary"></i>Create New Department
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.departments.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Enter department name" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-top pt-3 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Create Department
                </button>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection