<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of all attendance records.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['user', 'user.department']);

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

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        // Filter by status (present/absent)
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
            'today_total' => Attendance::whereDate('attendance_date', $today)->count(),
            'today_present' => Attendance::whereDate('attendance_date', $today)
                ->whereNotNull('check_in')
                ->count(),
            'today_absent' => Attendance::whereDate('attendance_date', $today)
                ->whereNull('check_in')
                ->count(),
            'total_employees' => User::role('Employee')->count(),
            'total_attendance' => Attendance::count(),
            'this_month' => Attendance::whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->count(),
        ];

        // Get employees and departments for filters
        $employees = User::role('Employee')->orderBy('name')->get(['id', 'name']);
        $departments = Department::orderBy('name')->get(['id', 'name']);

        return view('admin.attendance.index', compact(
            'attendances',
            'stats',
            'employees',
            'departments'
        ));
    }

    /**
     * Show attendance details for a specific employee.
     */
    public function show($id)
    {
        $attendance = Attendance::with(['user', 'user.department'])
            ->findOrFail($id);
        
        return view('admin.attendance.show', compact('attendance'));
    }

    /**
     * Show employee attendance report.
     */
    public function employeeReport(Request $request, $userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : null;

        $query = Attendance::with('user');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        // Filter by month/year
        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('attendance_date', $request->year);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->paginate(20);

        // Summary statistics
        $summary = [
            'total_days' => $query->count(),
            'present_days' => $query->whereNotNull('check_in')->count(),
            'total_hours' => $query->sum('working_minutes'),
            'avg_hours' => $query->count() > 0 
                ? round($query->sum('working_minutes') / $query->count() / 60, 1)
                : 0,
        ];

        // Get all employees for dropdown
        $employees = User::role('Employee')->orderBy('name')->get(['id', 'name']);

        return view('admin.attendance.employee-report', compact(
            'attendances',
            'user',
            'employees',
            'summary'
        ));
    }

    /**
     * Get attendance summary for dashboard charts.
     */
    public function summary()
    {
        $today = Carbon::today();
        $weekAgo = Carbon::today()->subDays(7);

        // Last 7 days trend
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $present = Attendance::whereDate('attendance_date', $date)
                ->whereNotNull('check_in')
                ->count();
            
            $trend[] = [
                'date' => $date->format('D'),
                'present' => $present,
            ];
        }

        // Department wise attendance
        $departmentWise = Department::withCount(['users' => function($q) {
                $q->role('Employee');
            }])
            ->withCount(['users as present_today' => function($q) use ($today) {
                $q->whereHas('attendances', function($q2) use ($today) {
                    $q2->whereDate('attendance_date', $today)
                       ->whereNotNull('check_in');
                });
            }])
            ->get()
            ->map(function($dept) {
                return [
                    'name' => $dept->name,
                    'total' => $dept->users_count,
                    'present' => $dept->present_today ?? 0,
                    'percentage' => $dept->users_count > 0 
                        ? round(($dept->present_today / $dept->users_count) * 100)
                        : 0,
                ];
            });

        return response()->json([
            'trend' => $trend,
            'department_wise' => $departmentWise,
        ]);
    }

    /**
     * Export attendance report (CSV).
     */
    public function export(Request $request)
    {
        $query = Attendance::with(['user', 'user.department']);

        // Apply filters
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        // Generate CSV
        $filename = 'attendance_report_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        // Headers
        fputcsv($handle, [
            'Date',
            'Employee',
            'Department',
            'Check In',
            'Check Out',
            'Working Hours',
            'Location'
        ]);

        // Data
        foreach ($attendances as $attendance) {
            $hours = floor($attendance->working_minutes / 60);
            $minutes = $attendance->working_minutes % 60;
            $workingHours = $attendance->working_minutes 
                ? "{$hours}h {$minutes}m" 
                : 'N/A';

            fputcsv($handle, [
                $attendance->attendance_date->format('Y-m-d'),
                $attendance->user->name ?? 'N/A',
                $attendance->user->department->name ?? 'N/A',
                $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : 'N/A',
                $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : 'N/A',
                $workingHours,
                $attendance->check_in_location ?? 'N/A',
            ]);
        }

        fclose($handle);

        return response()->stream(
            function() use ($handle) {
                // Already output
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }
}