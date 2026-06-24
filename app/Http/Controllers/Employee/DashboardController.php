<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Today's attendance
        $todayAttendance = $user->attendances()
            ->whereDate('attendance_date', $today)
            ->first();

        // Today's status
        $isCheckedIn = $todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out;
        $isCheckedOut = $todayAttendance && $todayAttendance->check_out;
        
        // Working hours today
        $workingHours = '00:00:00';
        if ($todayAttendance && $todayAttendance->working_minutes) {
            $hours = floor($todayAttendance->working_minutes / 60);
            $minutes = $todayAttendance->working_minutes % 60;
            $workingHours = sprintf('%02d:%02d:00', $hours, $minutes);
        }

        // Recent attendance (last 5 days)
        $recentAttendance = $user->attendances()
            ->orderBy('attendance_date', 'desc')
            ->limit(5)
            ->get();

        // Recent leave requests (last 5)
        $recentLeaves = $user->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
// dd($user,$todayAttendance,$isCheckedIn,$isCheckedOut,$workingHours,$recentAttendance,$recentLeaves);
        return view('employee.dashboard', compact(
            'user',
            'todayAttendance',
            'isCheckedIn',
            'isCheckedOut',
            'workingHours',
            'recentAttendance',
            'recentLeaves'
        ));
    }
}