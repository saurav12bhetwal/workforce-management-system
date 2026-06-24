<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display team attendance.
     */
    public function index(Request $request)
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $query = Attendance::whereIn('user_id', $teamIds)
            ->with(['user', 'user.department']);

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        // Filter by employee
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'present') {
                $query->whereNotNull('check_in');
            } elseif ($request->status == 'absent') {
                $query->whereNull('check_in');
            }
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics
        $today = Carbon::today();
        $stats = [
            'today_total' => Attendance::whereIn('user_id', $teamIds)
                ->whereDate('attendance_date', $today)
                ->count(),
            'today_present' => Attendance::whereIn('user_id', $teamIds)
                ->whereDate('attendance_date', $today)
                ->whereNotNull('check_in')
                ->count(),
            'today_absent' => Attendance::whereIn('user_id', $teamIds)
                ->whereDate('attendance_date', $today)
                ->whereNull('check_in')
                ->count(),
            'total_attendance' => Attendance::whereIn('user_id', $teamIds)->count(),
            'this_month' => Attendance::whereIn('user_id', $teamIds)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->count(),
        ];

        // Get team members for filter
        $teamMembers = User::whereIn('id', $teamIds)->orderBy('name')->get(['id', 'name']);

        return view('manager.attendance.index', compact(
            'attendances',
            'stats',
            'teamMembers'
        ));
    }

    /**
     * Show attendance details.
     */
    public function show($id)
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $attendance = Attendance::whereIn('user_id', $teamIds)
            ->with(['user', 'user.department'])
            ->findOrFail($id);

        return view('manager.attendance.show', compact('attendance'));
    }

    /**
     * Get team attendance summary.
     */
    public function summary()
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $today = Carbon::today();
        $weekAgo = Carbon::today()->subDays(7);

        // Last 7 days trend
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $present = Attendance::whereIn('user_id', $teamIds)
                ->whereDate('attendance_date', $date)
                ->whereNotNull('check_in')
                ->count();
            
            $trend[] = [
                'date' => $date->format('D'),
                'present' => $present,
            ];
        }

        return response()->json([
            'trend' => $trend,
        ]);
    }
}