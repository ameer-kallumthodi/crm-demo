<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class LeadSourceController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $leadSources = LeadSource::all();
        return view('admin.lead-sources.index', compact('leadSources'));
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
            'sort_order' => 'integer|min:0',
        ]);

        $leadSource = LeadSource::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead Source created successfully.',
            'data' => $leadSource
        ]);
    }

    public function show(LeadSource $leadSource)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($leadSource);
    }

    public function update(Request $request, LeadSource $leadSource)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $leadSource->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead Source updated successfully.',
            'data' => $leadSource
        ]);
    }

    public function destroy(LeadSource $leadSource)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if lead source is being used by any leads
        if ($leadSource->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete lead source. It is being used by existing leads.'
            ], 422);
        }

        $leadSource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead Source deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        return view('admin.lead-sources.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        LeadSource::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.lead-sources.index')->with('message_success', 'Lead Source created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $edit_data = LeadSource::findOrFail($id);
        return view('admin.lead-sources.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $leadSource = LeadSource::findOrFail($id);
        $leadSource->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.lead-sources.index')->with('message_success', 'Lead Source updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $leadSource = LeadSource::findOrFail($id);
        
        // Check if lead source has leads
        if ($leadSource->leads()->count() > 0) {
            return redirect()->route('admin.lead-sources.index')->with('message_error', 'Cannot delete lead source. It has assigned leads.');
        }

        $leadSource->delete();
        return redirect()->route('admin.lead-sources.index')->with('message_success', 'Lead Source deleted successfully!');
    }
}