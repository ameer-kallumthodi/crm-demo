<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class SubjectController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $subjects = Subject::with(['course', 'createdBy'])->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'is_active' => 'boolean',
        ]);

        $subject = Subject::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'is_active' => $request->input('is_active', 0) == 1,
            'created_by' => \App\Helpers\AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully.',
            'data' => $subject
        ]);
    }

    public function show(Subject $subject)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($subject->load(['course', 'createdBy', 'updatedBy']));
    }

    public function destroy(Subject $subject)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if subject has converted leads
        if ($subject->convertedLeads()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete subject. It has assigned converted leads.'
            ], 422);
        }

        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = Course::active()->get();
        return view('admin.subjects.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'course_id' => 'required|exists:courses,id',
                'is_active' => 'boolean',
            ]);

            $subject = Subject::create([
                'title' => $request->title,
                'description' => $request->description,
                'course_id' => $request->course_id,
                'is_active' => $request->input('is_active', 0) == 1,
                'created_by' => \App\Helpers\AuthHelper::getCurrentUserId(),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject created successfully!',
                    'data' => $subject
                ]);
            }

            return redirect()->route('admin.subjects.index')->with('message_success', 'Subject created successfully!');
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
                    'message' => 'An error occurred while creating the subject. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the subject. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Subject::findOrFail($id);
        $courses = Course::active()->get();
        return view('admin.subjects.edit', compact('edit_data', 'courses'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'course_id' => 'required|exists:courses,id',
                'is_active' => 'boolean',
            ]);

            $subject = Subject::findOrFail($id);
            $subject->update([
                'title' => $request->title,
                'description' => $request->description,
                'course_id' => $request->course_id,
                'is_active' => $request->input('is_active', 0) == 1,
                'updated_by' => \App\Helpers\AuthHelper::getCurrentUserId(),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject updated successfully!',
                    'data' => $subject
                ]);
            }

            return redirect()->route('admin.subjects.index')->with('message_success', 'Subject updated successfully!');
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
                    'message' => 'Subject not found.'
                ], 404);
            }
            
            return redirect()->route('admin.subjects.index')->with('message_danger', 'Subject not found.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the subject. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the subject. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $subject = Subject::findOrFail($id);
            
            // Check if subject has converted leads
            if ($subject->convertedLeads()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete subject. It has assigned converted leads.'
                    ], 422);
                }
                return redirect()->route('admin.subjects.index')->with('message_danger', 'Cannot delete subject. It has assigned converted leads.');
            }

            $subject->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject deleted successfully!'
                ]);
            }
            
            return redirect()->route('admin.subjects.index')->with('message_success', 'Subject deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found.'
                ], 404);
            }
            return redirect()->route('admin.subjects.index')->with('message_danger', 'Subject not found.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the subject. Please try again.'
                ], 500);
            }
            return redirect()->route('admin.subjects.index')->with('message_danger', 'An error occurred while deleting the subject. Please try again.');
        }
    }

    /**
     * Get subjects by course for AJAX requests
     */
    public function getByCourse($courseId)
    {
        $subjects = Subject::where('course_id', $courseId)
            ->where('is_active', 1)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return response()->json($subjects);
    }
}
