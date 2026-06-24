@extends('layouts.app')

@section('title', 'Apply Leave')
@section('page-title', 'Apply Leave')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>Apply for Leave
                </h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('employee.leaves.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="leave_type" class="form-label fw-semibold">
                            Leave Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('leave_type') is-invalid @enderror" 
                                id="leave_type" name="leave_type" required>
                            <option value="">Select Leave Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type }}" {{ old('leave_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="from_date" class="form-label fw-semibold">
                                    From Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('from_date') is-invalid @enderror" 
                                       id="from_date" name="from_date" value="{{ old('from_date') }}" required>
                                @error('from_date')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="to_date" class="form-label fw-semibold">
                                    To Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('to_date') is-invalid @enderror" 
                                       id="to_date" name="to_date" value="{{ old('to_date') }}" required>
                                @error('to_date')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="daysCountDisplay" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-calculator me-2"></i>
                            Total days: <strong id="daysCount">0</strong> day(s)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">
                            Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" 
                                  id="reason" name="reason" rows="4" 
                                  placeholder="Please provide a detailed reason for your leave request" 
                                  required>{{ old('reason') }}</textarea>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-info-circle me-1"></i> Minimum 10 characters
                        </div>
                        @error('reason')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3">
                        <h6 class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Important Information</h6>
                        <ul class="small text-muted mb-0">
                            <li>Leave requests are subject to approval by your manager</li>
                            <li>You can cancel pending leave requests</li>
                            <li>Check for overlapping leaves before applying</li>
                        </ul>
                    </div>

                    <div class="border-top pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Submit Request
                        </button>
                        <a href="{{ route('employee.leaves.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fromDate = document.getElementById('from_date');
        const toDate = document.getElementById('to_date');
        const daysCountDisplay = document.getElementById('daysCountDisplay');
        const daysCount = document.getElementById('daysCount');

        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        fromDate.min = today;
        toDate.min = today;

        // Update days count
        function updateDaysCount() {
            if (fromDate.value && toDate.value) {
                const from = new Date(fromDate.value);
                const to = new Date(toDate.value);
                
                if (to >= from) {
                    const diffTime = Math.abs(to - from);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    daysCount.textContent = diffDays;
                    daysCountDisplay.style.display = 'block';
                    return;
                }
            }
            daysCountDisplay.style.display = 'none';
        }

        fromDate.addEventListener('change', function() {
            toDate.min = this.value;
            if (toDate.value < this.value) {
                toDate.value = this.value;
            }
            updateDaysCount();
        });

        toDate.addEventListener('change', updateDaysCount);
    });
</script>
@endpush

@push('styles')
<style>
    .form-label {
        font-size: 14px;
        color: #1a1a2e;
    }
    textarea {
        resize: vertical;
    }
</style>
@endpush
@endsection