<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $departments = Department::withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department created successfully!');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
        ]);

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        
        // Check if department has employees
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Cannot delete department with assigned employees!');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department deleted successfully!');
    }
}