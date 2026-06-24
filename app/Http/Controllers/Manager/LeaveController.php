<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display team leave requests.
     */
    public function index(Request $request)
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $query = LeaveRequest::whereIn('user_id', $teamIds)
            ->with(['user']);

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
            'total' => LeaveRequest::whereIn('user_id', $teamIds)->count(),
            'pending' => LeaveRequest::whereIn('user_id', $teamIds)->where('status', 'Pending')->count(),
            'approved' => LeaveRequest::whereIn('user_id', $teamIds)->where('status', 'Approved')->count(),
            'rejected' => LeaveRequest::whereIn('user_id', $teamIds)->where('status', 'Rejected')->count(),
        ];

        // Get team members for filter
        $teamMembers = User::whereIn('id', $teamIds)->orderBy('name')->get(['id', 'name']);
        $leaveTypes = LeaveRequest::TYPES;
        $statuses = LeaveRequest::STATUSES;

        return view('manager.leaves.index', compact(
            'leaves',
            'stats',
            'teamMembers',
            'leaveTypes',
            'statuses'
        ));
    }

    /**
     * Approve a leave request.
     */
    public function approve($id)
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $leave = LeaveRequest::whereIn('user_id', $teamIds)->findOrFail($id);

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

        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $leave = LeaveRequest::whereIn('user_id', $teamIds)->findOrFail($id);

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
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $leave = LeaveRequest::whereIn('user_id', $teamIds)
            ->with(['user', 'approver'])
            ->findOrFail($id);

        return view('manager.leaves.show', compact('leave'));
    }
}