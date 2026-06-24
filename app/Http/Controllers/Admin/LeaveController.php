<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of all leave requests.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['user', 'approver']);

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

        // Filter by employee
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'Pending')->count(),
            'approved' => LeaveRequest::where('status', 'Approved')->count(),
            'rejected' => LeaveRequest::where('status', 'Rejected')->count(),
        ];

        // Get all employees for filter
        $employees = User::role('Employee')->orderBy('name')->get(['id', 'name']);
        $leaveTypes = LeaveRequest::TYPES;
        $statuses = LeaveRequest::STATUSES;

        return view('admin.leaves.index', compact(
            'leaves', 
            'stats', 
            'employees', 
            'leaveTypes', 
            'statuses'
        ));
    }

    /**
     * Approve a leave request.
     */
    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This leave request has already been processed.');
        }

        $leave->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', '✅ Leave request approved successfully!');
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status !== 'Pending') {
            return back()->with('error', 'This leave request has already been processed.');
        }

        $leave->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', '✅ Leave request rejected successfully!');
    }

    /**
     * Show leave details.
     */
    public function show($id)
    {
        $leave = LeaveRequest::with(['user', 'approver'])->findOrFail($id);
        return view('admin.leaves.show', compact('leave'));
    }

    /**
     * Bulk approve multiple leaves.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'leave_ids' => 'required|array',
            'leave_ids.*' => 'exists:leave_requests,id',
        ]);

        $count = LeaveRequest::whereIn('id', $request->leave_ids)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return back()->with('success', "✅ {$count} leave request(s) approved successfully!");
    }

    /**
     * Bulk reject multiple leaves.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'leave_ids' => 'required|array',
            'leave_ids.*' => 'exists:leave_requests,id',
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $count = LeaveRequest::whereIn('id', $request->leave_ids)
            ->where('status', 'Pending')
            ->update([
                'status' => 'Rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

        return back()->with('success', "✅ {$count} leave request(s) rejected successfully!");
    }
}