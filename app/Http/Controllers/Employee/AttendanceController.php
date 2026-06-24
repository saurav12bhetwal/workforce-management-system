<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show the attendance page.
     */
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $isCheckedIn  = $todayAttendance?->check_in && !$todayAttendance?->check_out;
        $isCheckedOut = (bool) $todayAttendance?->check_out;

        $todayWorkingHours = '00:00:00';
        if ($todayAttendance) {
            if ($todayAttendance->check_out && $todayAttendance->working_minutes !== null) {
                $todayWorkingHours = $this->formatMinutes($todayAttendance->working_minutes);
            } elseif ($todayAttendance->check_in && !$todayAttendance->check_out) {
                $elapsed = Carbon::parse($todayAttendance->check_in)->diffInMinutes(now());
                $todayWorkingHours = $this->formatMinutes($elapsed);
            }
        }

        $monthlySummary = $this->getMonthlySummary($user);

        return view('employee.attendance', compact(
            'todayAttendance',
            'isCheckedIn',
            'isCheckedOut',
            'todayWorkingHours',
            'monthlySummary'
        ));
    }

    /**
     * Check in — capture location, store check_in timestamp.
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user  = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($attendance?->check_in) {
            return response()->json([
                'success'            => false,
                'message'            => 'You have already checked in today.',
                'already_checked_in' => true,
            ], 409);
        }

        $locationName = LocationHelper::getAddress($request->latitude, $request->longitude);
        $checkInTime  = now();

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'attendance_date' => $today],
            [
                'check_in'          => $checkInTime,
                'check_in_lat'      => $request->latitude,
                'check_in_lng'      => $request->longitude,
                'check_in_location' => $locationName,
                'working_minutes'   => null,
            ]
        );

        return response()->json([
            'success'       => true,
            'message'       => 'Checked in successfully!',
            // ISO-8601 timestamp: JS will compute elapsed time from this
            'check_in_at'   => $checkInTime->toIso8601String(),
            'check_in_time' => $checkInTime->format('h:i A'),
            'location'      => $locationName,
        ]);
    }

    /**
     * Check out — capture location, calculate working minutes.
     */
    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user  = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            $alreadyOut = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->whereNotNull('check_out')
                ->exists();

            return response()->json([
                'success' => false,
                'message' => $alreadyOut
                    ? 'You have already checked out today.'
                    : 'No active check-in found. Please check in first.',
            ], 409);
        }

        $checkOut       = now();
        $workingMinutes = (int) Carbon::parse($attendance->check_in)->diffInMinutes($checkOut);
        $locationName   = LocationHelper::getAddress($request->latitude, $request->longitude);

        $attendance->update([
            'check_out'          => $checkOut,
            'check_out_lat'      => $request->latitude,
            'check_out_lng'      => $request->longitude,
            'check_out_location' => $locationName,
            'working_minutes'    => $workingMinutes,
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Checked out successfully!',
            'working_hours'  => $this->formatMinutes($workingMinutes),
            'check_out_time' => $checkOut->format('h:i A'),
            'location'       => $locationName,
        ]);
    }

    /**
     * Return current attendance status.
     * The timer anchor is always the DB check_in timestamp — never session/localStorage.
     */
    public function status(): JsonResponse
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $isCheckedIn  = $attendance?->check_in && !$attendance?->check_out;
        $isCheckedOut = (bool) $attendance?->check_out;

        return response()->json([
            'is_checked_in'   => (bool) $isCheckedIn,
            'is_checked_out'  => $isCheckedOut,
            'check_in_at'     => $attendance?->check_in
                ? Carbon::parse($attendance->check_in)->toIso8601String()
                : null,
            'check_in_time'   => $attendance?->check_in
                ? Carbon::parse($attendance->check_in)->format('h:i A')
                : null,
            'check_out_time'  => $attendance?->check_out
                ? Carbon::parse($attendance->check_out)->format('h:i A')
                : null,
            'working_minutes' => $attendance?->working_minutes ?? 0,
            'working_hours'   => $attendance?->working_minutes
                ? $this->formatMinutes($attendance->working_minutes)
                : '00:00:00',
        ]);
    }

    /**
     * Attendance history with filters.
     */
    public function history(Request $request)
    {
        $user  = Auth::user();

        $query = Attendance::where('user_id', $user->id);

        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }
        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('attendance_date', $request->year);
        }

        $attendances = (clone $query)
            ->orderBy('attendance_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Summary respects current filters
        $filteredRows  = (clone $query)->get(['check_in', 'working_minutes']);
        $totalMinutes  = (int) $filteredRows->sum('working_minutes');
        $presentDays   = $filteredRows->whereNotNull('check_in')->count();
        $totalDays     = $filteredRows->count();
        $avgMinutes    = $presentDays > 0 ? (int) ($totalMinutes / $presentDays) : 0;

        $summary = [
            'total_days'   => $totalDays,
            'present_days' => $presentDays,
            'absent_days'  => $totalDays - $presentDays,
            'total_hours'  => $this->formatMinutes($totalMinutes),
            'avg_hours'    => $this->formatMinutes($avgMinutes),
        ];

        return view('employee.attendance-history', compact('attendances', 'summary'));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d:00', floor($minutes / 60), $minutes % 60);
    }

    private function getMonthlySummary($user): array
    {
        $data = [];

        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);

            $rows = Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', $month->month)
                ->whereYear('attendance_date', $month->year)
                ->get(['check_in', 'working_minutes']);

            $totalMinutes = (int) $rows->sum('working_minutes');
            $presentDays  = $rows->whereNotNull('check_in')->count();
            $totalDays    = $rows->count();

            $data[] = [
                'month'        => $month->format('M Y'),
                'total_days'   => $totalDays,
                'present_days' => $presentDays,
                'absent_days'  => $totalDays - $presentDays,
                'hours'        => floor($totalMinutes / 60),
                'minutes'      => $totalMinutes % 60,
            ];
        }

        return $data;
    }
}