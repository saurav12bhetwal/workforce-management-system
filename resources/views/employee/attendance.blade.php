{{-- resources/views/employee/attendance.blade.php --}}
@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
<style>
    .timer-display {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 100%);
        border-radius: 20px;
        padding: 28px 20px;
        border: 1px solid #d0d9f0;
    }
    .timer-digits {
        font-size: 4.5rem;
        font-weight: 700;
        letter-spacing: 6px;
        color: #0d6efd;
        font-family: 'Courier New', monospace;
        text-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
        line-height: 1;
    }
    @media (max-width: 576px) {
        .timer-digits { font-size: 2.6rem; letter-spacing: 3px; }
    }
    .status-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .status-dot.running {
        background: #198754;
        animation: pulse 1.4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(0.8); }
    }
    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 14px 16px;
    }
</style>
@endpush

@section('content')
<div class="row g-3">

    {{-- ── Main Attendance Card ─────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4 px-4">

                {{-- Status Badge --}}
                <div class="mb-3">
                    @if($isCheckedOut)
                        <span class="badge bg-success px-4 py-2 fs-6">
                            <i class="fas fa-check-circle me-2"></i> Completed for Today
                        </span>
                    @elseif($isCheckedIn)
                        <span class="badge bg-primary px-4 py-2 fs-6">
                            <i class="fas fa-spinner fa-spin me-2"></i> Currently Working
                        </span>
                    @else
                        <span class="badge bg-secondary px-4 py-2 fs-6">
                            <i class="fas fa-clock me-2"></i> Not Checked In
                        </span>
                    @endif
                </div>

                {{-- Timer --}}
                <div class="timer-display mb-4">
                    <div class="timer-digits" id="timer">00:00:00</div>
                    <p class="text-muted small mt-2 mb-0" id="timerLabel">
                        @if($isCheckedIn)
                            <span class="status-dot running"></span> Timer Running
                        @elseif($isCheckedOut)
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Day complete — {{ $todayWorkingHours }}
                        @else
                            <i class="fas fa-hourglass-start me-1"></i> Check in to start timer
                        @endif
                    </p>
                </div>

                {{-- Check-in / Check-out info --}}
                @if($todayAttendance)
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="info-card text-start">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-sign-in-alt text-success me-1"></i> Check In
                            </small>
                            @if($todayAttendance->check_in)
                                <strong class="text-success fs-6">
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}
                                </strong>
                                <div class="text-muted small mt-1 text-truncate"
                                     title="{{ $todayAttendance->check_in_location }}">
                                    <i class="fas fa-map-pin me-1 text-danger"></i>
                                    {{ $todayAttendance->check_in_location ?? '—' }}
                                </div>
                            @else
                                <span class="text-muted">Not checked in</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-card text-start">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-sign-out-alt text-danger me-1"></i> Check Out
                            </small>
                            @if($todayAttendance->check_out)
                                <strong class="text-danger fs-6">
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}
                                </strong>
                                <div class="text-muted small mt-1 text-truncate"
                                     title="{{ $todayAttendance->check_out_location }}">
                                    <i class="fas fa-map-pin me-1 text-danger"></i>
                                    {{ $todayAttendance->check_out_location ?? '—' }}
                                </div>
                            @else
                                <span class="text-muted">{{ $isCheckedIn ? 'Not checked out yet' : '—' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Location status --}}
                <div id="locationStatus" class="mb-3 small text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    <span id="locationText">Waiting for location…</span>
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    @if(!$isCheckedOut)
                        <button id="checkInBtn"
                                class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-sm"
                                {{ $isCheckedIn ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt me-2"></i> Check In
                        </button>
                        <button id="checkOutBtn"
                                class="btn btn-danger btn-lg px-5 py-3 rounded-pill shadow-sm"
                                {{ !$isCheckedIn ? 'disabled' : '' }}>
                            <i class="fas fa-sign-out-alt me-2"></i> Check Out
                        </button>
                    @else
                        <div class="alert alert-success w-100 mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            You have completed your work day!
                            <strong>Total: {{ $todayWorkingHours }}</strong>
                        </div>
                    @endif
                </div>

                @if($isCheckedOut)
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        You cannot check in again today.
                    </p>
                @elseif($isCheckedIn)
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Click <strong>Check Out</strong> when you finish your work.
                    </p>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Monthly Summary ──────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i> Monthly Summary
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($monthlySummary as $month)
                    <div class="col-6">
                        <div class="p-2 border rounded-3 text-center">
                            <small class="text-muted d-block fw-semibold">{{ $month['month'] }}</small>
                            <div class="d-flex justify-content-center gap-2 mt-1">
                                <span class="badge bg-success">{{ $month['present_days'] }}P</span>
                                <span class="badge bg-danger">{{ $month['absent_days'] }}A</span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                {{ $month['hours'] }}h {{ $month['minutes'] }}m
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('employee.attendance.history') }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-history me-1"></i> View Full History
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Confirmation Modal ────────────────────────────────────── --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="modalTitle">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // ── State ────────────────────────────────────────────────────────────────
    let timerInterval  = null;
    let checkInAt      = null;   // ISO string from server — single source of truth
    let pendingAction  = null;   // 'checkin' | 'checkout'
    let locationCache  = null;   // { latitude, longitude } — refreshed on each action

    const modal        = new bootstrap.Modal($('#confirmModal')[0]);
    const CSRF         = $('meta[name="csrf-token"]').attr('content');

    // ── Timer ────────────────────────────────────────────────────────────────
    function startTimer(isoTimestamp) {
        checkInAt = isoTimestamp;
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(tick, 1000);
        tick(); // immediate first tick
    }

    function stopTimer(displayValue) {
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
        checkInAt = null;
        $('#timer').text(displayValue || '00:00:00');
    }

    function tick() {
        if (!checkInAt) return;
        const elapsed = Math.floor((Date.now() - new Date(checkInAt).getTime()) / 1000);
        if (elapsed < 0) return; // clock skew guard
        const h = String(Math.floor(elapsed / 3600)).padStart(2, '0');
        const m = String(Math.floor((elapsed % 3600) / 60)).padStart(2, '0');
        const s = String(elapsed % 60).padStart(2, '0');
        $('#timer').text(`${h}:${m}:${s}`);
    }

    // ── Location ─────────────────────────────────────────────────────────────
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                return reject(new Error('Geolocation not supported by your browser.'));
            }
            $('#locationText').text('Fetching location…');
            navigator.geolocation.getCurrentPosition(
                pos => {
                    locationCache = { latitude: pos.coords.latitude, longitude: pos.coords.longitude };
                    $('#locationText').html(
                        `<i class="fas fa-check-circle text-success me-1"></i>
                         Location ready (±${Math.round(pos.coords.accuracy)}m)`
                    );
                    resolve(locationCache);
                },
                err => {
                    $('#locationText').html(
                        '<i class="fas fa-exclamation-circle text-danger me-1"></i> Location access denied'
                    );
                    reject(err);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // ── Bootstrap: load status from server on page load ───────────────────────
    function loadStatus() {
        $.getJSON('{{ route("employee.attendance.status") }}', function (r) {
            if (r.is_checked_in && r.check_in_at) {
                startTimer(r.check_in_at);
                setUIState('working');
            } else if (r.is_checked_out) {
                stopTimer(r.working_hours);
                setUIState('done', r.working_hours);
            } else {
                setUIState('idle');
            }
        }).fail(function () {
            console.warn('Could not load attendance status.');
        });
    }

    function setUIState(state, workingHours) {
        if (state === 'working') {
            $('#checkInBtn').prop('disabled', true)
                .html('<i class="fas fa-sign-in-alt me-2"></i> Checked In');
            $('#checkOutBtn').prop('disabled', false)
                .html('<i class="fas fa-sign-out-alt me-2"></i> Check Out');
            $('#timerLabel').html(
                '<span class="status-dot running"></span> Timer Running'
            );
        } else if (state === 'done') {
            $('#checkInBtn').prop('disabled', true);
            $('#checkOutBtn').prop('disabled', true);
            $('#timerLabel').html(
                `<i class="fas fa-check-circle text-success me-1"></i> Day complete — ${workingHours}`
            );
        } else {
            $('#checkInBtn').prop('disabled', false)
                .html('<i class="fas fa-sign-in-alt me-2"></i> Check In');
            $('#checkOutBtn').prop('disabled', true);
            $('#timerLabel').html(
                '<i class="fas fa-hourglass-start me-1"></i> Check in to start timer'
            );
        }
    }

    // ── Button handlers ───────────────────────────────────────────────────────
    $('#checkInBtn').on('click', function () {
        if ($(this).prop('disabled')) return;
        pendingAction = 'checkin';
        $('#modalTitle').html('<i class="fas fa-sign-in-alt text-success me-2"></i> Check In');
        $('#modalBody').html(`
            <div class="text-center py-2">
                <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                <p class="fw-semibold mb-1">Ready to start your work day?</p>
                <p class="text-muted small mb-3">Your location will be captured for verification.</p>
                <div class="alert alert-info small mb-0">
                    <i class="fas fa-info-circle me-1"></i> You can only check in once per day.
                </div>
            </div>
        `);
        $('#confirmAction').removeClass('btn-danger btn-primary').addClass('btn-success').text('Yes, Check In');
        modal.show();
    });

    $('#checkOutBtn').on('click', function () {
        if ($(this).prop('disabled')) return;
        pendingAction = 'checkout';
        $('#modalTitle').html('<i class="fas fa-sign-out-alt text-danger me-2"></i> Check Out');
        $('#modalBody').html(`
            <div class="text-center py-2">
                <i class="fas fa-hourglass-end fa-3x text-warning mb-3"></i>
                <p class="fw-semibold mb-1">Ready to finish for today?</p>
                <p class="text-muted small mb-3">Your location will be captured and working hours calculated.</p>
                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Once you check out, you cannot check in again today.
                </div>
            </div>
        `);
        $('#confirmAction').removeClass('btn-success btn-primary').addClass('btn-danger').text('Yes, Check Out');
        modal.show();
    });

    $('#confirmAction').on('click', function () {
        modal.hide();
        if      (pendingAction === 'checkin')  doCheckIn();
        else if (pendingAction === 'checkout') doCheckOut();
    });

    // ── Check In ─────────────────────────────────────────────────────────────
    function doCheckIn() {
        $('#checkInBtn').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span> Checking In…');

        getLocation().then(function (loc) {
            $.post({
                url:  '{{ route("employee.attendance.check-in") }}',
                data: { latitude: loc.latitude, longitude: loc.longitude, _token: CSRF },
            })
            .done(function (res) {
                if (res.success) {
                    startTimer(res.check_in_at);
                    setUIState('working');
                    toastr.success(`Checked in at ${res.check_in_time} · ${res.location}`);
                    // Soft-reload check-in info row after 1.5 s
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .fail(function (xhr) {
                $('#checkInBtn').prop('disabled', false)
                    .html('<i class="fas fa-sign-in-alt me-2"></i> Check In');
                const msg = xhr.responseJSON?.message || 'Check-in failed. Please try again.';
                toastr.error(msg);
                if (xhr.responseJSON?.already_checked_in) {
                    setTimeout(() => window.location.reload(), 1500);
                }
            });
        }).catch(function () {
            $('#checkInBtn').prop('disabled', false)
                .html('<i class="fas fa-sign-in-alt me-2"></i> Check In');
            toastr.error('Please allow location access to check in.');
        });
    }

    // ── Check Out ────────────────────────────────────────────────────────────
    function doCheckOut() {
        $('#checkOutBtn').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span> Checking Out…');

        getLocation().then(function (loc) {
            $.post({
                url:  '{{ route("employee.attendance.check-out") }}',
                data: { latitude: loc.latitude, longitude: loc.longitude, _token: CSRF },
            })
            .done(function (res) {
                if (res.success) {
                    stopTimer(res.working_hours);
                    setUIState('done', res.working_hours);
                    toastr.success(
                        `Checked out at ${res.check_out_time} · Total: ${res.working_hours} · ${res.location}`
                    );
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .fail(function (xhr) {
                $('#checkOutBtn').prop('disabled', false)
                    .html('<i class="fas fa-sign-out-alt me-2"></i> Check Out');
                toastr.error(xhr.responseJSON?.message || 'Check-out failed. Please try again.');
            });
        }).catch(function () {
            $('#checkOutBtn').prop('disabled', false)
                .html('<i class="fas fa-sign-out-alt me-2"></i> Check Out');
            toastr.error('Please allow location access to check out.');
        });
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    loadStatus();           // sync timer with server on every page load
    getLocation().catch(() => {}); // pre-warm location so button press is instant
});
</script>
@endpush