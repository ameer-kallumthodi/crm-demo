<?php

namespace App\Http\Controllers;

use App\Models\AdmissionBatch;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class AdmissionBatchController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $admissionBatches = AdmissionBatch::with(['batch', 'createdBy', 'updatedBy'])->orderBy('created_at', 'desc')->get();
        return view('admin.admission-batches.index', compact('admissionBatches'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'batch_id' => 'required|exists:batches,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $admissionBatch = AdmissionBatch::create([
            'title' => $request->title,
            'batch_id' => $request->batch_id,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admission Batch created successfully.',
            'data' => $admissionBatch
        ]);
    }

    public function show(AdmissionBatch $admissionBatch)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($admissionBatch);
    }

    public function destroy(AdmissionBatch $admissionBatch)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        try {
            $admissionBatch->delete();
            return response()->json([
                'success' => true,
                'message' => 'Admission Batch deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admission batch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batches = Batch::where('is_active', true)->get();
        return view('admin.admission-batches.add', compact('batches'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'batch_id' => 'required|exists:batches,id',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $admissionBatch = AdmissionBatch::create([
                'title' => $request->title,
                'batch_id' => $request->batch_id,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.admission-batches.index')->with('message_success', 'Admission Batch created successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to create admission batch: ' . $e->getMessage());
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = AdmissionBatch::findOrFail($id);
        $batches = Batch::where('is_active', true)->get();
        return view('admin.admission-batches.edit', compact('edit_data', 'batches'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'batch_id' => 'required|exists:batches,id',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $admissionBatch = AdmissionBatch::findOrFail($id);
            $admissionBatch->update([
                'title' => $request->title,
                'batch_id' => $request->batch_id,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.admission-batches.index')->with('message_success', 'Admission Batch updated successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to update admission batch: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $admissionBatch = AdmissionBatch::findOrFail($id);
            $admissionBatch->delete();

            return redirect()->route('admin.admission-batches.index')->with('message_success', 'Admission Batch deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.admission-batches.index')->with('message_danger', 'Failed to delete admission batch: ' . $e->getMessage());
        }
    }

    /**
     * Get admission batches by batch for AJAX requests
     */
    public function getByBatch($batchId)
    {
        $admissionBatches = AdmissionBatch::where('batch_id', $batchId)
            ->where('is_active', 1)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return response()->json($admissionBatches);
    }
}
