<?php

namespace App\Http\Controllers;

use App\Models\RegistrationLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class RegistrationLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $registrationLinks = RegistrationLink::orderBy('created_at', 'desc')->get();
        return view('admin.registration-links.index', compact('registrationLinks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255|unique:registration_links,title',
        ]);

        $registrationLink = RegistrationLink::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registration Link created successfully.',
            'data' => $registrationLink
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(RegistrationLink $registrationLink)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($registrationLink);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegistrationLink $registrationLink)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255|unique:registration_links,title,' . $registrationLink->id,
        ]);

        $registrationLink->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registration Link updated successfully.',
            'data' => $registrationLink
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        try {
            $registrationLink = RegistrationLink::findOrFail($id);
            $registrationLink->delete();
            return response()->json([
                'success' => true,
                'message' => 'Registration Link deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete registration link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show add modal
     */
    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.registration-links.add');
    }

    /**
     * Submit add form
     */
    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255|unique:registration_links,title',
            ]);

            $registrationLink = RegistrationLink::create([
                'title' => $request->title,
            ]);

            return redirect()->route('admin.registration-links.index')->with('message_success', 'Registration Link created successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to create registration link: ' . $e->getMessage());
        }
    }

    /**
     * Show edit modal
     */
    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = RegistrationLink::findOrFail($id);
        return view('admin.registration-links.edit', compact('edit_data'));
    }

    /**
     * Update registration link
     */
    public function update_registration_link(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255|unique:registration_links,title,' . $id,
            ]);

            $registrationLink = RegistrationLink::findOrFail($id);
            $registrationLink->update([
                'title' => $request->title,
            ]);

            return redirect()->route('admin.registration-links.index')->with('message_success', 'Registration Link updated successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to update registration link: ' . $e->getMessage());
        }
    }

    /**
     * Delete registration link (for modal)
     */
    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        try {
            $registrationLink = RegistrationLink::findOrFail($id);
            $registrationLink->delete();
            return response()->json([
                'success' => true,
                'message' => 'Registration Link deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete registration link: ' . $e->getMessage()
            ], 500);
        }
    }
}
