<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ===== STATISTICS =====
        
        // Total Employees
        $totalEmployees = User::role('Employee')->count();
        
        // Total Departments
        $totalDepartments = Department::count();
        
        // Today's Attendance
        $presentToday = Attendance::whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->count();
        
        $onLeaveToday = LeaveRequest::whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where('status', 'Approved')
            ->count();
        
        $absentToday = max(0, $totalEmployees - $presentToday - $onLeaveToday);
        
        // Pending Leaves
        $pendingLeaves = LeaveRequest::where('status', 'Pending')->count();
        
        // Total Leaves This Month
        $totalLeavesThisMonth = LeaveRequest::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // ===== RECENT ACTIVITIES =====
        
        // Recent Leave Requests
        $recentLeaves = LeaveRequest::with(['user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Recent Employees
        $recentEmployees = User::role('Employee')
            ->with(['department', 'designation'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Today's Check-ins (recent)
        $todayCheckIns = Attendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->orderBy('check_in', 'desc')
            ->limit(5)
            ->get();
        
        // ===== DEPARTMENT STATISTICS =====
        $departmentStats = Department::withCount(['users' => function($query) {
                $query->role('Employee');
            }])
            ->withCount(['users as present_today_count' => function($query) use ($today) {
                $query->whereHas('attendances', function($q) use ($today) {
                    $q->whereDate('attendance_date', $today)
                      ->whereNotNull('check_in');
                });
            }])
            ->orderBy('users_count', 'desc')
            ->limit(5)
            ->get();
        
        // ===== QUICK STATS =====
        $quickStats = [
            'today_checkins' => $presentToday,
            'today_absent' => $absentToday,
            'today_on_leave' => $onLeaveToday,
            'pending_approvals' => $pendingLeaves,
            'total_employees' => $totalEmployees,
            'total_departments' => $totalDepartments,
            'this_month_leaves' => $totalLeavesThisMonth,
        ];
        
        return view('admin.dashboard', compact(
            'quickStats',
            'recentLeaves',
            'recentEmployees',
            'todayCheckIns',
            'departmentStats'
        ));
    }
}