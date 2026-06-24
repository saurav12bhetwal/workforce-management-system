@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-secondary"></i>
                </div>
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->employee_code ?? 'N/A' }}</p>
                
                <hr>
                
                <div class="text-start">
                    <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> {{ $user->email }}</p>
                    <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-building me-2"></i>Department:</strong> {{ $user->department->name ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-briefcase me-2"></i>Designation:</strong> {{ $user->designation->name ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-user-tag me-2"></i>Role:</strong> {{ $user->roles->first()?->name ?? 'No Role' }}</p>
                    <p><strong><i class="fas fa-user me-2"></i>Manager:</strong> {{ $user->manager->name ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-calendar-plus me-2"></i>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Profile
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Leave blank to keep current">
                        <small class="text-muted">Minimum 8 characters. Leave blank to keep current password.</small>
                        @error('password')
                            <span class="text-danger small d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection