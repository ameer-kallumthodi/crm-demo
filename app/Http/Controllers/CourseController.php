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
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
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
            'code' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'code' => $request->code,
            'amount' => $request->amount,
            'is_active' => $request->input('is_active', 0) == 1,
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
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.courses.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'is_active' => 'boolean',
            ]);

            $course = Course::create([
                'title' => $request->title,
                'amount' => $request->amount,
                'is_active' => $request->input('is_active', 0) == 1,
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course created successfully!',
                    'data' => $course
                ]);
            }

            return redirect()->route('admin.courses.index')->with('message_success', 'Course created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('message_danger', 'Please correct the errors below.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the course. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the course. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Course::findOrFail($id);
        return view('admin.courses.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'amount' => 'required|numeric|min:0',
                'is_active' => 'boolean',
            ]);

            $course = Course::findOrFail($id);
            $course->update([
                'title' => $request->title,
                'code' => $request->code,
                'amount' => $request->amount,
                'is_active' => $request->input('is_active', 0) == 1,
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course updated successfully!',
                    'data' => $course
                ]);
            }

            return redirect()->route('admin.courses.index')->with('message_success', 'Course updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('message_danger', 'Please correct the errors below.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found.'
                ], 404);
            }
            
            return redirect()->route('admin.courses.index')->with('message_danger', 'Course not found.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the course. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the course. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $course = Course::findOrFail($id);
            
            // Check if course has leads
            if ($course->leads()->count() > 0) {
                return redirect()->route('admin.courses.index')->with('message_danger', 'Cannot delete course. It has assigned leads.');
            }

            $course->delete();
            return redirect()->route('admin.courses.index')->with('message_success', 'Course deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.courses.index')->with('message_danger', 'Course not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.courses.index')->with('message_danger', 'An error occurred while deleting the course. Please try again.');
        }
    }
}