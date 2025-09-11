<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class CourseController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'fees' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'fees' => $request->fees,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully.',
            'data' => $course
        ]);
    }

    public function show(Course $course)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($course);
    }

    public function update(Request $request, Course $course)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'fees' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'fees' => $request->fees,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully.',
            'data' => $course
        ]);
    }

    public function destroy(Course $course)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if course is being used by any leads
        if ($course->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete course. It is being used by existing leads.'
            ], 422);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        return view('admin.courses.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'fees' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'fees' => $request->fees,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.courses.index')->with('message_success', 'Course created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $edit_data = Course::findOrFail($id);
        return view('admin.courses.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'fees' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $course = Course::findOrFail($id);
        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'fees' => $request->fees,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.courses.index')->with('message_success', 'Course updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $course = Course::findOrFail($id);
        
        // Check if course has leads
        if ($course->leads()->count() > 0) {
            return redirect()->route('admin.courses.index')->with('message_error', 'Cannot delete course. It has assigned leads.');
        }

        $course->delete();
        return redirect()->route('admin.courses.index')->with('message_success', 'Course deleted successfully!');
    }
}