@extends('layouts.app')

@section('title', 'Attendance Details')
@section('page-title', 'Attendance Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2 text-primary"></i>Attendance Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Employee</small>
                            <strong>{{ $attendance->user->name ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Date</small>
                            <strong>{{ $attendance->attendance_date->format('l, M d, Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Check In</small>
                            @if($attendance->check_in)
                                <strong class="text-success">
                                    {{ Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                </strong>
                                <br>
                                <small class="text-muted">{{ $attendance->check_in_location ?? 'No location' }}</small>
                            @else
                                <span class="text-muted">Not checked in</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Check Out</small>
                            @if($attendance->check_out)
                                <strong class="text-danger">
                                    {{ Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                </strong>
                                <br>
                                <small class="text-muted">{{ $attendance->check_out_location ?? 'No location' }}</small>
                            @else
                                <span class="text-muted">Not checked out</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Working Hours</small>
                            @if($attendance->working_minutes)
                                @php
                                    $hours = floor($attendance->working_minutes / 60);
                                    $minutes = $attendance->working_minutes % 60;
                                @endphp
                                <strong class="text-primary">{{ $hours }}h {{ $minutes }}m</strong>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Status</small>
                            @if($attendance->check_in)
                                <span class="badge bg-success">Present</span>
                            @else
                                <span class="badge bg-danger">Absent</span>
                            @endif
                        </div>
                    </div>
                    @if($attendance->check_in_lat && $attendance->check_in_lng)
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Location</small>
                            <a href="https://www.google.com/maps?q={{ $attendance->check_in_lat }},{{ $attendance->check_in_lng }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-map-marked-alt me-2"></i> View on Google Maps
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="border-top pt-3 mt-3">
                    <a href="{{ route('manager.attendance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection