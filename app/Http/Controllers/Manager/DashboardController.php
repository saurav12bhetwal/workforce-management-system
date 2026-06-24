<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = Auth::user();
        
        // Get all employees reporting to this manager
        $teamIds = $manager->subordinates()->pluck('id');
        $teamCount = $teamIds->count();

        $today = Carbon::today();

        // Today's attendance for team
        $presentToday = Attendance::whereIn('user_id', $teamIds)
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->count();

        $onLeaveToday = LeaveRequest::whereIn('user_id', $teamIds)
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where('status', 'Approved')
            ->count();

        $absentToday = max(0, $teamCount - $presentToday - $onLeaveToday);

        // Pending leave requests from team
        $pendingLeaves = LeaveRequest::whereIn('user_id', $teamIds)
            ->where('status', 'Pending')
            ->count();

        // Monthly statistics
        $monthlyStats = [
            'total_leaves' => LeaveRequest::whereIn('user_id', $teamIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'approved_leaves' => LeaveRequest::whereIn('user_id', $teamIds)
                ->where('status', 'Approved')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'rejected_leaves' => LeaveRequest::whereIn('user_id', $teamIds)
                ->where('status', 'Rejected')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Recent team members
        $recentTeamMembers = User::whereIn('id', $teamIds)
            ->with(['department', 'designation'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent leave requests from team
        $recentLeaves = LeaveRequest::whereIn('user_id', $teamIds)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Today's check-ins from team
        $todayCheckIns = Attendance::whereIn('user_id', $teamIds)
            ->with(['user'])
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->orderBy('check_in', 'desc')
            ->limit(5)
            ->get();

        // Team attendance trend (last 7 days)
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Attendance::whereIn('user_id', $teamIds)
                ->whereDate('attendance_date', $date)
                ->whereNotNull('check_in')
                ->count();
            
            $attendanceTrend[] = [
                'date' => $date->format('D'),
                'count' => $count,
            ];
        }

        // Team members with their attendance status
        $teamMembers = User::whereIn('id', $teamIds)
            ->with(['department', 'designation'])
            ->get()
            ->map(function($member) use ($today) {
                $attendance = Attendance::where('user_id', $member->id)
                    ->whereDate('attendance_date', $today)
                    ->first();
                
                $leave = LeaveRequest::where('user_id', $member->id)
                    ->whereDate('from_date', '<=', $today)
                    ->whereDate('to_date', '>=', $today)
                    ->where('status', 'Approved')
                    ->first();

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'department' => $member->department->name ?? 'N/A',
                    'designation' => $member->designation->name ?? 'N/A',
                    'status' => $leave ? 'On Leave' : ($attendance && $attendance->check_in ? 'Present' : 'Absent'),
                    'check_in' => $attendance?->check_in,
                    'check_out' => $attendance?->check_out,
                ];
            });

        return view('manager.dashboard', compact(
            'teamCount',
            'presentToday',
            'absentToday',
            'onLeaveToday',
            'pendingLeaves',
            'monthlyStats',
            'recentTeamMembers',
            'recentLeaves',
            'todayCheckIns',
            'attendanceTrend',
            'teamMembers'
        ));
    }
}