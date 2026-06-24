<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        // Check if already checked in today
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($attendance && $attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today'
            ], 400);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => $today
            ],
            [
                'check_in' => now(),
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
            ]
        );

        // Store timer start in session
        session(['attendance_timer_start' => now()->timestamp]);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully',
            'data' => $attendance,
            'timer_start' => now()->timestamp
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active check-in found'
            ], 400);
        }

        // Calculate working minutes
        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = now();
        $workingMinutes = $checkIn->diffInMinutes($checkOut);

        $attendance->update([
            'check_out' => $checkOut,
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
            'working_minutes' => $workingMinutes
        ]);

        // Clear timer session
        session()->forget('attendance_timer_start');

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully',
            'data' => $attendance,
            'working_hours' => gmdate('H:i:s', $workingMinutes * 60)
        ]);
    }

    public function status()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $timerStart = session('attendance_timer_start');

        return response()->json([
            'is_checked_in' => $attendance && $attendance->check_in && !$attendance->check_out,
            'check_in_time' => $attendance?->check_in,
            'check_out_time' => $attendance?->check_out,
            'working_minutes' => $attendance?->working_minutes ?? 0,
            'timer_start' => $timerStart,
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return response()->json($attendances);
    }
}