<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index()
    {
        $departments = Department::orderBy('created_at', 'desc')->get();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function add()
    {
        return view('admin.departments.add');
    }

    /**
     * Store a newly created department
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:departments,title',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Department::create([
            'title' => $request->title,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:departments,title,' . $id,
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $department->update([
            'title' => $request->title,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department
     */
    public function delete($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
