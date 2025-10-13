<?php

namespace App\Http\Controllers;

use App\Models\SubCourse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;

class SubCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subCourses = SubCourse::with('course')->orderBy('course_id')->orderBy('title')->get();
        $courses = Course::where('is_active', true)->orderBy('title')->get();

        return view('admin.sub-courses.index', compact('subCourses', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::where('is_active', true)->orderBy('title')->get();
        return view('admin.sub-courses.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        // Normalize checkbox to boolean for validation
        $request->merge([
            'is_active' => $request->boolean('is_active'),
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        SubCourse::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.sub-courses.index')->with('message_success', 'Sub Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subCourse = SubCourse::with('course')->findOrFail($id);
        return view('admin.sub-courses.show', compact('subCourse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function ajax_edit(string $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subCourse = SubCourse::findOrFail($id);
        $courses = Course::where('is_active', true)->orderBy('title')->get();
        
        return view('admin.sub-courses.edit', compact('subCourse', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateForm(Request $request, string $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subCourse = SubCourse::findOrFail($id);

        // Normalize checkbox to boolean for validation
        $request->merge([
            'is_active' => $request->boolean('is_active'),
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $subCourse->update([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.sub-courses.index')->with('message_success', 'Sub Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $subCourse = SubCourse::findOrFail($id);
        
        // Check if sub course is being used by any converted leads
        if ($subCourse->convertedLeads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete sub course. It is being used by converted leads.'
            ], 422);
        }

        $subCourse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub Course deleted successfully.'
        ]);
    }
}
