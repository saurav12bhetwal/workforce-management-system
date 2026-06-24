<!-- resources/views/manager/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Manager Dashboard')

@section('content')
<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
            <div class="card-body">
                <h6 class="text-muted mb-1">Team Members</h6>
                <h3 class="fw-bold mb-0">{{ $teamCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-success bg-opacity-10">
            <div class="card-body">
                <h6 class="text-muted mb-1">Present Today</h6>
                <h3 class="fw-bold mb-0 text-success">{{ $presentToday }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-warning bg-opacity-10">
            <div class="card-body">
                <h6 class="text-muted mb-1">On Leave</h6>
                <h3 class="fw-bold mb-0 text-warning">{{ $onLeaveToday }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 bg-danger bg-opacity-10">
            <div class="card-body">
                <h6 class="text-muted mb-1">Absent</h6>
                <h3 class="fw-bold mb-0 text-danger">{{ $absentToday }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pending Approvals</h6>
                <h2 class="fw-bold text-warning">{{ $pendingLeaves }}</h2>
                <small>Leave requests waiting for your action</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-2">This Month's Leaves</h6>
                <div class="d-flex gap-3">
                    <div>
                        <span class="badge bg-success">Approved</span>
                        <strong>{{ $monthlyStats['approved_leaves'] }}</strong>
                    </div>
                    <div>
                        <span class="badge bg-danger">Rejected</span>
                        <strong>{{ $monthlyStats['rejected_leaves'] }}</strong>
                    </div>
                    <div>
                        <span class="badge bg-secondary">Total</span>
                        <strong>{{ $monthlyStats['total_leaves'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-2">Attendance Rate</h6>
                @php
                    $rate = $teamCount > 0 ? round(($presentToday / $teamCount) * 100) : 0;
                @endphp
                <h2 class="fw-bold text-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}">
                    {{ $rate }}%
                </h2>
                <small>Team attendance today</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Trend Chart -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Team Attendance Trend (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Team Members Status -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Team Members - Today's Status</h6>
                <a href="{{ route('manager.team.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Member</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamMembers as $member)
                                <tr>
                                    <td><strong>{{ $member['name'] }}</strong></td>
                                    <td>{{ $member['department'] }}</td>
                                    <td>{{ $member['designation'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $member['status'] == 'Present' ? 'success' : ($member['status'] == 'On Leave' ? 'warning' : 'danger') }}">
                                            {{ $member['status'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($member['check_in'])
                                            {{ Carbon\Carbon::parse($member['check_in'])->format('h:i A') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member['check_out'])
                                            {{ Carbon\Carbon::parse($member['check_out'])->format('h:i A') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <p class="text-muted mb-0">No team members found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row g-3">
    <!-- Recent Leaves -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Recent Leave Requests</h6>
                <a href="{{ route('manager.leaves.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentLeaves->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentLeaves as $leave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $leave->user->name ?? 'N/A' }}</strong>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">{{ $leave->leave_type }}</span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $leave->from_date->format('M d') }} - {{ $leave->to_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $leave->status_badge_class }}">
                                    {{ $leave->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No recent leave requests</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Today's Check-ins -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-fingerprint me-2 text-primary"></i>Today's Check-ins</h6>
                <a href="{{ route('manager.attendance.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($todayCheckIns->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todayCheckIns as $attendance)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $attendance->user->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                    </small>
                                </div>
                                <span class="badge bg-success">Checked In</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No check-ins yet today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Trend Chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($attendanceTrend, 'date')) !!},
                datasets: [{
                    label: 'Team Members Present',
                    data: {!! json_encode(array_column($attendanceTrend, 'count')) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 12px 20px;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }
    }
</style>
@endpush
@endsection