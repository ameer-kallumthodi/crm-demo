<?php

namespace App\Http\Controllers;

use App\Models\LeadStatus;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class LeadStatusController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $leadStatuses = LeadStatus::all();
        return view('admin.lead-statuses.index', compact('leadStatuses'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $leadStatus = LeadStatus::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead Status created successfully.',
            'data' => $leadStatus
        ]);
    }

    public function show(LeadStatus $leadStatus)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($leadStatus);
    }



    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.lead-statuses.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $leadStatus = LeadStatus::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead Status created successfully!',
                'data' => $leadStatus
            ]);
        }

        return redirect()->route('admin.lead-statuses.index')->with('message_success', 'Lead Status created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = LeadStatus::findOrFail($id);
        return view('admin.lead-statuses.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $leadStatus = LeadStatus::findOrFail($id);
        $leadStatus->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead Status updated successfully!',
                'data' => $leadStatus
            ]);
        }

        return redirect()->route('admin.lead-statuses.index')->with('message_success', 'Lead Status updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $leadStatus = LeadStatus::findOrFail($id);
        
        // Check if lead status has leads
        if ($leadStatus->leads()->count() > 0) {
            return redirect()->route('admin.lead-statuses.index')->with('message_danger', 'Cannot delete lead status. It has assigned leads.');
        }

        $leadStatus->delete();
        return redirect()->route('admin.lead-statuses.index')->with('message_success', 'Lead Status deleted successfully!');
    }
}