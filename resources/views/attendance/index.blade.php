@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Attendance</div>
                <div class="card-body text-center">
                    <!-- Timer Display -->
                    <div id="timer" class="display-1 mb-4">
                        00:00:00
                    </div>
                    
                    <!-- Location Status -->
                    <div id="location-status" class="mb-3">
                        <span class="badge bg-secondary">Fetching location...</span>
                    </div>

                    <!-- Check-in/Check-out Buttons -->
                    <button id="checkInBtn" class="btn btn-success btn-lg me-2">
                        <i class="fas fa-sign-in-alt"></i> Check In
                    </button>
                    <button id="checkOutBtn" class="btn btn-danger btn-lg" disabled>
                        <i class="fas fa-sign-out-alt"></i> Check Out
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let timerInterval = null;
    let timerStart = localStorage.getItem('timer_start');
    
    // Get user location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    localStorage.setItem('latitude', position.coords.latitude);
                    localStorage.setItem('longitude', position.coords.longitude);
                    $('#location-status').html('<span class="badge bg-success">Location captured</span>');
                },
                function(error) {
                    $('#location-status').html('<span class="badge bg-danger">Location access denied</span>');
                }
            );
        }
    }

    // Update timer
    function updateTimer() {
        if (timerStart) {
            const elapsed = Math.floor((Date.now() - parseInt(timerStart)) / 1000);
            const hours = String(Math.floor(elapsed / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((elapsed % 3600) / 60)).padStart(2, '0');
            const seconds = String(elapsed % 60).padStart(2, '0');
            $('#timer').text(`${hours}:${minutes}:${seconds}`);
        }
    }

    // Check attendance status
    function checkStatus() {
        $.get('/api/attendance/status', function(response) {
            if (response.is_checked_in) {
                $('#checkInBtn').prop('disabled', true);
                $('#checkOutBtn').prop('disabled', false);
                timerStart = response.timer_start;
                localStorage.setItem('timer_start', timerStart);
                if (!timerInterval) {
                    timerInterval = setInterval(updateTimer, 1000);
                }
            } else {
                $('#checkInBtn').prop('disabled', false);
                $('#checkOutBtn').prop('disabled', true);
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
                localStorage.removeItem('timer_start');
                $('#timer').text('00:00:00');
            }
        });
    }

    // Check In
    $('#checkInBtn').click(function() {
        getLocation();
        const lat = localStorage.getItem('latitude');
        const lng = localStorage.getItem('longitude');
        
        if (!lat || !lng) {
            alert('Please allow location access');
            return;
        }

        $.post('/api/attendance/check-in', {
            latitude: lat,
            longitude: lng,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                timerStart = response.timer_start;
                localStorage.setItem('timer_start', timerStart);
                $('#checkInBtn').prop('disabled', true);
                $('#checkOutBtn').prop('disabled', false);
                timerInterval = setInterval(updateTimer, 1000);
                toastr.success('Checked in successfully');
            }
        });
    });

    // Check Out
    $('#checkOutBtn').click(function() {
        getLocation();
        const lat = localStorage.getItem('latitude');
        const lng = localStorage.getItem('longitude');

        $.post('/api/attendance/check-out', {
            latitude: lat,
            longitude: lng,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                $('#checkInBtn').prop('disabled', false);
                $('#checkOutBtn').prop('disabled', true);
                clearInterval(timerInterval);
                timerInterval = null;
                localStorage.removeItem('timer_start');
                $('#timer').text('00:00:00');
                toastr.success(`Checked out. Working hours: ${response.working_hours}`);
            }
        });
    });

    // Initialize
    checkStatus();
    if (timerStart) {
        timerInterval = setInterval(updateTimer, 1000);
    }
});
</script>
@endsection