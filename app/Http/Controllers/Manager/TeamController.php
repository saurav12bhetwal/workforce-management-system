<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display team members.
     */
    public function index(Request $request)
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $query = User::whereIn('id', $teamIds)
            ->with(['department', 'designation']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $teamMembers = $query->orderBy('name')->paginate(15);

        // Get departments for filter
        $departments = $manager->subordinates()
            ->with('department')
            ->get()
            ->pluck('department')
            ->filter()
            ->unique('id')
            ->values();

        return view('manager.team.index', compact('teamMembers', 'departments'));
    }

    /**
     * Show team member details.
     */
    public function show($id)
    {
        $manager = Auth::user();
        $teamIds = $manager->subordinates()->pluck('id');

        $member = User::whereIn('id', $teamIds)
            ->with(['department', 'designation', 'attendances', 'leaveRequests'])
            ->findOrFail($id);

        return view('manager.team.show', compact('member'));
    }
}