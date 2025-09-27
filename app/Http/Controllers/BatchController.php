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
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batches = Batch::with(['course', 'createdBy', 'updatedBy'])->orderBy('created_at', 'desc')->get();
        return view('admin.batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $batch = Batch::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'description' => $request->description,
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
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($batch);
    }

    public function destroy(Batch $batch)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if batch is being used by any converted leads
        if ($batch->convertedLeads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete batch. It is being used by existing converted leads.'
            ], 422);
        }

        $batch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Batch deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courses = \App\Models\Course::where('is_active', true)->get();
        return view('admin.batches.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Batch::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return redirect()->route('admin.batches.index')->with('message_success', 'Batch created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Batch::findOrFail($id);
        $courses = \App\Models\Course::where('is_active', true)->get();
        return view('admin.batches.edit', compact('edit_data', 'courses'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $batch = Batch::findOrFail($id);
        $batch->update([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return redirect()->route('admin.batches.index')->with('message_success', 'Batch updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $batch = Batch::findOrFail($id);
        
        // Check if batch has converted leads
        if ($batch->convertedLeads()->count() > 0) {
            return redirect()->route('admin.batches.index')->with('message_danger', 'Cannot delete batch. It has assigned converted leads.');
        }

        $batch->delete();
        return redirect()->route('admin.batches.index')->with('message_success', 'Batch deleted successfully!');
    }

    /**
     * Get batches by course ID (API endpoint)
     */
    public function getByCourse($courseId)
    {
        $batches = Batch::where('course_id', $courseId)
            ->where('is_active', true)
            ->select('id', 'title')
            ->get();

        return response()->json([
            'success' => true,
            'batches' => $batches
        ]);
    }
}
