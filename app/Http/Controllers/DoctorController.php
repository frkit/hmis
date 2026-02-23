<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return redirect()->route('users.index', ['designation' => 'doctor']);
    }

    // --- Department Management ---

    public function departments()
    {
        $departments = Department::withCount('users')->orderBy('name')->get();
        return view('doctors.departments', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|string|max:191']);
        Department::create($request->all());
        return back()->with('success', 'Department created.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate(['name' => 'required|string|max:191']);
        $department->update($request->all());
        return back()->with('success', 'Department updated.');
    }

    public function destroyDepartment(Department $department)
    {
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Cannot delete department with active staff.');
        }
        $department->delete();
        return back()->with('success', 'Department deleted.');
    }
}
