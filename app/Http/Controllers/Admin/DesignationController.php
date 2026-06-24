<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Department;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of designations.
     */
    public function index(Request $request)
    {
        $query = Designation::with('department');

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $designations = $query->withCount('users')
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);

        $departments = Department::all();

        return view('admin.designations.index', compact('designations', 'departments'));
    }

    /**
     * Show the form for creating a new designation.
     */
    public function create()
    {
        $departments = Department::all();
        return view('admin.designations.create', compact('departments'));
    }

    /**
     * Store a newly created designation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        Designation::create([
            'name' => $request->name,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('admin.designations.index')
                         ->with('success', 'Designation created successfully!');
    }

    /**
     * Show the form for editing the specified designation.
     */
    public function edit($id)
    {
        $designation = Designation::findOrFail($id);
        $departments = Department::all();
        return view('admin.designations.edit', compact('designation', 'departments'));
    }

    /**
     * Update the specified designation.
     */
    public function update(Request $request, $id)
    {
        $designation = Designation::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $id,
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $designation->update([
            'name' => $request->name,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('admin.designations.index')
                         ->with('success', 'Designation updated successfully!');
    }

    /**
     * Remove the specified designation.
     */
    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);
        
        // Check if designation has employees
        if ($designation->users()->count() > 0) {
            return back()->with('error', 'Cannot delete designation with assigned employees!');
        }

        $designation->delete();

        return redirect()->route('admin.designations.index')
                         ->with('success', 'Designation deleted successfully!');
    }

    /**
     * Get designations by department (for AJAX)
     */
    public function getByDepartment($departmentId)
    {
        $designations = Designation::where('department_id', $departmentId)
                                   ->orderBy('name')
                                   ->get(['id', 'name']);
        
        return response()->json($designations);
    }
}