<?php

namespace App\Http\Controllers;

use App\Models\UniversityCourse;
use App\Models\University;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class UniversityCourseController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $universityCourses = UniversityCourse::with('university')->get();
        return view('admin.university-courses.index', compact('universityCourses'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'course_type' => 'nullable|string|in:UG,PG',
            'is_active' => 'nullable|boolean',
        ]);

        $universityCourse = UniversityCourse::create([
            'university_id' => $request->university_id,
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
            'course_type' => $request->course_type,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'University Course created successfully.',
            'data' => $universityCourse->load('university')
        ]);
    }

    public function show(UniversityCourse $universityCourse)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($universityCourse->load('university'));
    }

    public function destroy(UniversityCourse $universityCourse)
    {
        if (!RoleHelper::is_admin_or_super_admin()  && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if university course is being used by any leads
        if ($universityCourse->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete university course. It is being used by existing leads.'
            ], 422);
        }

        $universityCourse->delete();

        return response()->json([
            'success' => true,
            'message' => 'University Course deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $universities = University::active()->get();
        return view('admin.university-courses.add', compact('universities'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'university_id' => 'required|exists:universities,id',
                'title' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'amount' => 'required|numeric|min:0',
                'hod_number' => 'nullable|string|max:20',
                'description' => 'nullable|string',
                'duration_months' => 'nullable|integer|min:1',
                'course_type' => 'nullable|string|in:UG,PG',
                'is_active' => 'nullable|boolean',
            ]);

            $universityCourse = UniversityCourse::create([
                'university_id' => $request->university_id,
                'title' => $request->title,
                'code' => $request->code,
                'amount' => $request->amount,
                'hod_number' => $request->hod_number,
                'description' => $request->description,
                'duration_months' => $request->duration_months,
                'course_type' => $request->course_type,
                'is_active' => $request->boolean('is_active'),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'University Course created successfully!',
                    'data' => $universityCourse->load('university')
                ]);
            }

            return redirect()->route('admin.university-courses.index')->with('message_success', 'University Course created successfully!');
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
                    'message' => 'An error occurred while creating the university course. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the university course. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = UniversityCourse::with('university')->findOrFail($id);
        $universities = University::active()->get();
        return view('admin.university-courses.edit', compact('edit_data', 'universities'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'university_id' => 'required|exists:universities,id',
                'title' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'amount' => 'required|numeric|min:0',
                'hod_number' => 'nullable|string|max:20',
                'description' => 'nullable|string',
                'duration_months' => 'nullable|integer|min:1',
                'course_type' => 'nullable|string|in:UG,PG',
                'is_active' => 'nullable|boolean',
            ]);

            $universityCourse = UniversityCourse::findOrFail($id);
            $universityCourse->update([
                'university_id' => $request->university_id,
                'title' => $request->title,
                'amount' => $request->amount,
                'description' => $request->description,
                'course_type' => $request->course_type,
                'is_active' => $request->boolean('is_active'),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'University Course updated successfully!',
                    'data' => $universityCourse->load('university')
                ]);
            }

            return redirect()->route('admin.university-courses.index')->with('message_success', 'University Course updated successfully!');
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
                    'message' => 'University Course not found.'
                ], 404);
            }
            
            return redirect()->route('admin.university-courses.index')->with('message_danger', 'University Course not found.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the university course. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the university course. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $universityCourse = UniversityCourse::findOrFail($id);
            
            // Check if university course has leads
            if ($universityCourse->leads()->count() > 0) {
                return redirect()->route('admin.university-courses.index')->with('message_danger', 'Cannot delete university course. It has assigned leads.');
            }

            $universityCourse->delete();
            return redirect()->route('admin.university-courses.index')->with('message_success', 'University Course deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.university-courses.index')->with('message_danger', 'University Course not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.university-courses.index')->with('message_danger', 'An error occurred while deleting the university course. Please try again.');
        }
    }

    /**
     * Get university courses by university ID for AJAX requests
     */
    public function getByUniversity($universityId)
    {
        $universityCourses = UniversityCourse::where('university_id', $universityId)
            ->where('is_active', true)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return response()->json($universityCourses);
    }
}
