@extends('layouts.app')

@section('title', 'Add New Employee')
@section('page-title', 'Add New Employee')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent">
        <h5 class="mb-0">
            <i class="fas fa-user-plus me-2 text-primary"></i>Create New Employee
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.employees.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Personal Information -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimum 8 characters</small>
                        @error('password')
                            <span class="text-danger small d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Work Information -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Work Information</h6>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                id="department_id" name="department_id">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="designation_id" class="form-label">Designation</label>
                        <select class="form-select @error('designation_id') is-invalid @enderror" 
                                id="designation_id" name="designation_id">
                            <option value="">Select Designation</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                                    {{ $designation->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('designation_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Reporting Manager</label>
                        <select class="form-select @error('manager_id') is-invalid @enderror" 
                                id="manager_id" name="manager_id">
                            <option value="">Select Manager</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }} ({{ $manager->employee_code ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-top pt-3 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Create Employee
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
<script>
    $(document).ready(function() {
        // When department changes, load designations
        $('#department_id').on('change', function() {
            var departmentId = $(this).val();
            var designationSelect = $('#designation_id');
            
            // Clear current options
            designationSelect.empty();
            designationSelect.append('<option value="">Select Designation</option>');
            
            if (departmentId) {
                // Show loading
                designationSelect.append('<option value="" disabled>Loading...</option>');
                
                // AJAX request
                $.ajax({
                    url: '/admin/designations/by-department/' + departmentId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        designationSelect.find('option:disabled').remove();
                        $.each(data, function(key, value) {
                            designationSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function() {
                        designationSelect.find('option:disabled').remove();
                        designationSelect.append('<option value="" disabled>Error loading designations</option>');
                    }
                });
            }
        });

        // Trigger change on page load if department is selected
        @if(old('department_id', $employee->department_id ?? null))
            $('#department_id').trigger('change');
        @endif
    });
</script>
@endpush
@endsection