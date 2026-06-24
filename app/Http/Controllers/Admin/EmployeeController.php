<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role (exclude admin if needed)
        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $employees = $query->with(['department', 'designation', 'manager', 'roles'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        $departments = Department::all();
        $roles = Role::all();

        return view('admin.employees.index', compact('employees', 'departments', 'roles'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $departments = Department::all();
        $designations = Designation::all();
        $managers = User::role(['Manager', 'Admin'])->get();
        $roles = Role::all();

        return view('admin.employees.create', compact('departments', 'designations', 'managers', 'roles'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'manager_id' => 'nullable|exists:users,id',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'manager_id' => $request->manager_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        // Assign role
        $user->assignRole($request->role);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee created successfully! Employee Code: ' . $user->employee_code);
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        $employee = User::with(['department', 'designation', 'manager', 'roles', 'attendances', 'leaveRequests'])
                        ->findOrFail($id);

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        $employee = User::findOrFail($id);
        $departments = Department::all();
        $designations = Designation::all();
        $managers = User::role(['Manager', 'Admin'])->where('id', '!=', $id)->get();
        $roles = Role::all();

        return view('admin.employees.edit', compact('employee', 'departments', 'designations', 'managers', 'roles'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'manager_id' => 'nullable|exists:users,id',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'manager_id' => $request->manager_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Sync role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.employees.index')
                         ->with('success', 'Employee deleted successfully!');
    }
}