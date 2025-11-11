<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class BatchController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batches = Batch::with(['course', 'createdBy', 'updatedBy'])->orderBy('created_at', 'desc')->get();
        return view('admin.batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->merge([
            'amount' => $request->filled('amount') ? $request->amount : null,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $batch = Batch::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'amount' => $request->input('amount'),
            'is_active' => $request->has('is_active'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Batch created successfully.',
            'data' => $batch
        ]);
    }

    public function show(Batch $batch)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($batch);
    }

    public function destroy(Batch $batch)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            if ($batch->convertedLeads()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete batch. It has assigned converted leads.'
                    ], 422);
                }
                return redirect()->route('admin.batches.index')->with('message_danger', 'Cannot delete batch. It has assigned converted leads.');
            }

            $batch->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch deleted successfully!'
                ]);
            }
            return redirect()->route('admin.batches.index')->with('message_success', 'Batch deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch not found.'
                ], 404);
            }
            return redirect()->route('admin.batches.index')->with('message_danger', 'Batch not found.');
        } catch (\Throwable $e) {
            \Log::error('[BatchController@destroy] Error deleting batch: ' . $e->getMessage(), ['batch_id' => $batch->id ?? null]);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the batch. Please try again.'
                ], 500);
            }
            return redirect()->route('admin.batches.index')->with('message_danger', 'An error occurred while deleting the batch. Please try again.');
        }
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = \App\Models\Course::where('is_active', true)->get();
        return view('admin.batches.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'amount' => $request->filled('amount') ? $request->amount : null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $batch = Batch::create([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'description' => $request->description,
                'amount' => $request->input('amount'),
                'is_active' => $request->is_active,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.batches.index')->with('message_success', 'Batch created successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to create batch: ' . $e->getMessage());
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Batch::findOrFail($id);
        $courses = \App\Models\Course::where('is_active', true)->get();
        return view('admin.batches.edit', compact('edit_data', 'courses'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'amount' => $request->filled('amount') ? $request->amount : null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $batch = Batch::findOrFail($id);
            $batch->update([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'description' => $request->description,
                'amount' => $request->input('amount'),
                'is_active' => $request->is_active,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.batches.index')->with('message_success', 'Batch updated successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to update batch: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        \Log::info('[BatchController@delete] Attempting delete', ['id' => $id]);

        try {
            $batch = Batch::findOrFail($id);

            if ($batch->convertedLeads()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete batch. It has assigned converted leads.'
                    ], 422);
                }
                return redirect()->route('admin.batches.index')->with('message_danger', 'Cannot delete batch. It has assigned converted leads.');
            }

            $batch->delete();
            \Log::info('[BatchController@delete] Batch deleted', ['id' => $id]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch deleted successfully!'
                ]);
            }
            return redirect()->route('admin.batches.index')->with('message_success', 'Batch deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch not found.'
                ], 404);
            }
            return redirect()->route('admin.batches.index')->with('message_danger', 'Batch not found.');
        } catch (\Throwable $e) {
            \Log::error('[BatchController@delete] Error deleting batch: ' . $e->getMessage(), ['id' => $id]);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the batch. Please try again.'
                ], 500);
            }
            return redirect()->route('admin.batches.index')->with('message_danger', 'An error occurred while deleting the batch. Please try again.');
        }
    }

    /**
     * Get batches by course ID (API endpoint)
     */
    public function getByCourse($courseId)
    {
        $batches = Batch::where('course_id', $courseId)
            ->where('is_active', true)
            ->select('id', 'title', 'amount')
            ->get();

        return response()->json([
            'success' => true,
            'batches' => $batches
        ]);
    }
}
