{{-- resources/views/employee/attendance-history.blade.php --}}
@extends('layouts.app')

@section('title', 'Attendance History')
@section('page-title', 'Attendance History')

@push('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #6c757d;
    }
    .table td { font-size: 14px; }
</style>
@endpush

@section('content')
<div class="row g-3">

    {{-- ── Summary Cards ────────────────────────────────────── --}}
    <div class="col-12">
        <div class="row g-3">
            @foreach([
                ['label' => 'Total Days',    'value' => $summary['total_days'],   'icon' => 'fa-calendar-alt',  'color' => 'text-secondary'],
                ['label' => 'Present Days',  'value' => $summary['present_days'], 'icon' => 'fa-user-check',    'color' => 'text-success'],
                ['label' => 'Absent Days',   'value' => $summary['absent_days'],  'icon' => 'fa-user-times',    'color' => 'text-danger'],
                ['label' => 'Total Hours',   'value' => $summary['total_hours'],  'icon' => 'fa-clock',         'color' => 'text-primary'],
                ['label' => 'Avg Hrs / Day', 'value' => $summary['avg_hours'],    'icon' => 'fa-chart-line',    'color' => 'text-info'],
            ] as $card)
            <div class="col-6 col-md-4 col-lg">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center py-3">
                        <i class="fas {{ $card['icon'] }} fa-lg {{ $card['color'] }} mb-2"></i>
                        <h4 class="fw-bold mb-0 {{ $card['color'] }}">{{ $card['value'] }}</h4>
                        <small class="text-muted">{{ $card['label'] }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Filters ───────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('employee.attendance.history') }}"
                      class="row g-2 align-items-end">
                    <div class="col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label small mb-1">From Date</label>
                        <input type="date" name="from_date" class="form-control form-control-sm"
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label small mb-1">To Date</label>
                        <input type="date" name="to_date" class="form-control form-control-sm"
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-sm-6 col-md-2 col-lg-2">
                        <label class="form-label small mb-1">Month</label>
                        <select name="month" class="form-select form-select-sm">
                            <option value="">All Months</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected(request('month') == $m)>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2 col-lg-2">
                        <label class="form-label small mb-1">Year</label>
                        <select name="year" class="form-select form-select-sm">
                            <option value="">All Years</option>
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('employee.attendance.history') }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Records Table ─────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2 text-primary"></i> Attendance Records
                </h6>
                <span class="badge bg-primary rounded-pill">{{ $attendances->total() }}</span>
            </div>

            <div class="card-body p-0">
                @if($attendances->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours</th>
                                <th>Location</th>
                                <th>Map</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $i => $a)
                            <tr>
                                <td class="ps-3 text-muted small">
                                    {{ $attendances->firstItem() + $i }}
                                </td>
                                <td>
                                    <strong>{{ $a->attendance_date->format('d M Y') }}</strong>
                                </td>
                                <td class="text-muted small">
                                    {{ $a->attendance_date->format('D') }}
                                </td>
                                <td>
                                    @if($a->check_in)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            {{ \Carbon\Carbon::parse($a->check_in)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($a->check_out)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            {{ \Carbon\Carbon::parse($a->check_out)->format('h:i A') }}
                                        </span>
                                    @else
                                        @if($a->check_in)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                In Progress
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($a->working_minutes)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            {{ floor($a->working_minutes / 60) }}h {{ $a->working_minutes % 60 }}m
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($a->check_in_location)
                                        <span class="text-muted small"
                                              title="{{ $a->check_in_location }}">
                                            <i class="fas fa-map-pin text-danger me-1"></i>
                                            {{ Str::limit($a->check_in_location, 22) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($a->check_in_lat && $a->check_in_lng)
                                        <a href="https://www.google.com/maps?q={{ $a->check_in_lat }},{{ $a->check_in_lng }}"
                                           target="_blank" rel="noopener"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="View on Google Maps">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Showing {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }}
                        of {{ $attendances->total() }} records
                    </small>
                    {{ $attendances->links() }}
                </div>

                @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-2">No attendance records found.</p>
                    <a href="{{ route('employee.attendance.index') }}"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-fingerprint me-1"></i> Mark Attendance
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection