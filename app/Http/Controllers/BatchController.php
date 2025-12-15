<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Log;

class BatchController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batches = Batch::with(['course', 'createdBy', 'updatedBy', 'postponeBatch'])->orderBy('created_at', 'desc')->get();
        return view('admin.batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->merge([
            'amount' => $request->filled('amount') ? $request->amount : null,
            'sslc_amount' => $request->filled('sslc_amount') ? $request->sslc_amount : null,
            'plustwo_amount' => $request->filled('plustwo_amount') ? $request->plustwo_amount : null,
        ]);

        $rules = [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'sslc_amount' => 'nullable|numeric|min:0',
            'plustwo_amount' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];

        $request->validate($rules);

        $batch = Batch::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'amount' => $request->input('amount'),
            'sslc_amount' => $request->input('sslc_amount'),
            'plustwo_amount' => $request->input('plustwo_amount'),
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
            Log::error('[BatchController@destroy] Error deleting batch: ' . $e->getMessage(), ['batch_id' => $batch->id ?? null]);
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
                'sslc_amount' => $request->filled('sslc_amount') ? $request->sslc_amount : null,
                'plustwo_amount' => $request->filled('plustwo_amount') ? $request->plustwo_amount : null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $rules = [
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'sslc_amount' => 'nullable|numeric|min:0',
                'plustwo_amount' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ];

            $request->validate($rules);

            $batch = Batch::create([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'description' => $request->description,
                'amount' => $request->input('amount'),
                'sslc_amount' => $request->input('sslc_amount'),
                'plustwo_amount' => $request->input('plustwo_amount'),
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
                'sslc_amount' => $request->filled('sslc_amount') ? $request->sslc_amount : null,
                'plustwo_amount' => $request->filled('plustwo_amount') ? $request->plustwo_amount : null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $rules = [
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'description' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'sslc_amount' => 'nullable|numeric|min:0',
                'plustwo_amount' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ];

            if ((int) $request->course_id === 16) {
                $rules['sslc_amount'] = 'required|numeric|min:0';
                $rules['plustwo_amount'] = 'required|numeric|min:0';
            }

            $request->validate($rules);

            $batch = Batch::findOrFail($id);
            
            // Prepare update data
            $updateData = [
                'title' => $request->title,
                'course_id' => $request->course_id,
                'description' => $request->description,
                'amount' => $request->input('amount'),
                'sslc_amount' => $request->input('sslc_amount'),
                'plustwo_amount' => $request->input('plustwo_amount'),
                'is_active' => $request->is_active,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ];
            
            // If status is being changed to Inactive (0), clear all postpone fields
            if ($request->is_active == 0) {
                $updateData['postpone_batch_id'] = null;
                $updateData['postpone_start_date'] = null;
                $updateData['postpone_end_date'] = null;
                $updateData['batch_postpone_amount'] = null;
                $updateData['is_postpone_active'] = 0;
            }
            
            $batch->update($updateData);

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

        Log::info('[BatchController@delete] Attempting delete', ['id' => $id]);

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
            Log::info('[BatchController@delete] Batch deleted', ['id' => $id]);

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
            Log::error('[BatchController@delete] Error deleting batch: ' . $e->getMessage(), ['id' => $id]);
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

    /**
     * Show postpone modal for a batch
     */
    public function ajax_postpone($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batch = Batch::with(['course', 'postponeBatch'])->findOrFail($id);
        
        // Get batches under the same course (excluding the current batch)
        $postponeBatches = Batch::where('course_id', $batch->course_id)
            ->where('id', '!=', $batch->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.batches.postpone', compact('batch', 'postponeBatches'));
    }

    /**
     * Store postpone information for a batch
     */
    public function postpone_submit(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            $request->merge([
                'batch_postpone_amount' => $request->filled('batch_postpone_amount') ? $request->batch_postpone_amount : null,
                'set_inactive' => $request->has('set_inactive') && $request->set_inactive == 1 ? 1 : 0,
            ]);

            // If setting to inactive, clear all postpone fields
            if ($request->set_inactive == 1) {
                $request->merge([
                    'postpone_batch_id' => null,
                    'postpone_start_date' => null,
                    'postpone_end_date' => null,
                    'batch_postpone_amount' => null,
                ]);
            }

            // Validate based on whether inactive is being set
            if ($request->set_inactive == 1) {
                // If inactive, postpone fields are not required
                $request->validate([
                    'set_inactive' => 'nullable|boolean',
                    'postpone_batch_id' => 'nullable|exists:batches,id',
                    'postpone_start_date' => 'nullable|date',
                    'postpone_end_date' => 'nullable|date',
                    'batch_postpone_amount' => 'nullable|numeric|min:0',
                ]);
            } else {
                // If active, postpone fields are required
                $request->validate([
                    'set_inactive' => 'nullable|boolean',
                    'postpone_batch_id' => 'required|exists:batches,id',
                    'postpone_start_date' => 'required|date',
                    'postpone_end_date' => 'required|date|after_or_equal:postpone_start_date',
                    'batch_postpone_amount' => 'nullable|numeric|min:0',
                ]);
            }

            $batch = Batch::findOrFail($id);
            
            // Prepare update data
            $updateData = [
                'updated_by' => AuthHelper::getCurrentUserId(),
            ];
            
            if ($request->set_inactive == 1) {
                // Clear all postpone fields when postponed status is inactive
                $updateData['postpone_batch_id'] = null;
                $updateData['postpone_start_date'] = null;
                $updateData['postpone_end_date'] = null;
                $updateData['batch_postpone_amount'] = null;
                $updateData['is_postpone_active'] = 0;
            } else {
                // Set postpone fields when postponed status is active
                $updateData['postpone_batch_id'] = $request->postpone_batch_id;
                $updateData['postpone_start_date'] = $request->postpone_start_date;
                $updateData['postpone_end_date'] = $request->postpone_end_date;
                $updateData['batch_postpone_amount'] = $request->input('batch_postpone_amount');
                $updateData['is_postpone_active'] = 1;
            }
            
            $batch->update($updateData);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch postponed successfully!'
                ]);
            }
            return redirect()->route('admin.batches.index')->with('message_success', 'Batch postponed successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            Log::error('[BatchController@postpone_submit] Error postponing batch: ' . $e->getMessage(), ['batch_id' => $id]);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while postponing the batch. Please try again.'
                ], 500);
            }
            return back()->withInput()->with('message_danger', 'An error occurred while postponing the batch. Please try again.');
        }
    }
}
