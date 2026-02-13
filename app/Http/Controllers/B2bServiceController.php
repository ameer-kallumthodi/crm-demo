<?php

namespace App\Http\Controllers;

use App\Models\B2bService;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class B2bServiceController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $b2bServices = B2bService::with(['createdBy', 'updatedBy'])->get();

        return view('admin.b2b-services.index', compact('b2bServices'));
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.b2b-services.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255|unique:b2b_services,title',
            'status' => 'required|in:active,inactive',
        ]);

        $b2bService = B2bService::create([
            'title' => $request->title,
            'status' => $request->status,
            'created_by' => AuthHelper::getCurrentUserId(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'B2B Service created successfully!',
                'data' => $b2bService
            ]);
        }

        return redirect()->route('admin.b2b-services.index')->with('message_success', 'B2B Service created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = B2bService::findOrFail($id);
        return view('admin.b2b-services.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255|unique:b2b_services,title,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        $b2bService = B2bService::findOrFail($id);
        $b2bService->update([
            'title' => $request->title,
            'status' => $request->status,
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'B2B Service updated successfully!',
                'data' => $b2bService
            ]);
        }

        return redirect()->route('admin.b2b-services.index')->with('message_success', 'B2B Service updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $b2bService = B2bService::findOrFail($id);
        $b2bService->delete();

        return redirect()->route('admin.b2b-services.index')->with('message_success', 'B2B Service deleted successfully!');
    }
}
