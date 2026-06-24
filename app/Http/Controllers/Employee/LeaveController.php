<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Show leave page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all leaves with pagination
        $leaves = $user->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total' => $user->leaveRequests()->count(),
            'pending' => $user->leaveRequests()->pending()->count(),
            'approved' => $user->leaveRequests()->approved()->count(),
            'rejected' => $user->leaveRequests()->rejected()->count(),
        ];

        return view('employee.leaves.index', compact('leaves', 'stats'));
    }

    /**
     * Show leave application form
     */
    public function create()
    {
        $leaveTypes = LeaveRequest::TYPES;
        return view('employee.leaves.create', compact('leaveTypes'));
    }

    /**
     * Store leave request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:' . implode(',', LeaveRequest::TYPES),
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|min:10|max:500',
        ]);

        $user = Auth::user();

        // Check for overlapping leaves
        if (LeaveRequest::isOverlapping($user->id, $validated['from_date'], $validated['to_date'])) {
            return back()
                ->withInput()
                ->with('error', 'You already have a pending or approved leave request for these dates!');
        }

        LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'reason' => $validated['reason'],
            'status' => 'Pending',
        ]);

        return redirect()->route('employee.leaves.index')
                         ->with('success', '✅ Leave request submitted successfully!');
    }

    /**
     * Show leave history with filters
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->leaveRequests();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('from_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('to_date', '<=', $request->to_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => $user->leaveRequests()->count(),
            'pending' => $user->leaveRequests()->pending()->count(),
            'approved' => $user->leaveRequests()->approved()->count(),
            'rejected' => $user->leaveRequests()->rejected()->count(),
        ];

        $leaveTypes = LeaveRequest::TYPES;
        $statuses = LeaveRequest::STATUSES;

        return view('employee.leaves.history', compact('leaves', 'stats', 'leaveTypes', 'statuses'));
    }

    /**
     * Cancel a pending leave request
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $leave = $user->leaveRequests()->findOrFail($id);

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Only pending leave requests can be cancelled!');
        }

        $leave->delete();

        return back()->with('success', 'Leave request cancelled successfully!');
    }
}