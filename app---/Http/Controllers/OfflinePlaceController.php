<?php

namespace App\Http\Controllers;

use App\Models\OfflinePlace;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class OfflinePlaceController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $offlinePlaces = OfflinePlace::all();
        return view('admin.offline-places.index', compact('offlinePlaces'));
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.offline-places.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            $offlinePlace = OfflinePlace::create([
                'name' => $request->name,
                'is_active' => $request->boolean('is_active'),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Offline place created successfully!',
                    'data' => $offlinePlace
                ]);
            }

            return redirect()->route('admin.offline-places.index')->with('message_success', 'Offline place created successfully!');
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
                    'message' => 'An error occurred while creating the offline place. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while creating the offline place. Please try again.');
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = OfflinePlace::findOrFail($id);
        return view('admin.offline-places.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            $offlinePlace = OfflinePlace::findOrFail($id);
            $offlinePlace->update([
                'name' => $request->name,
                'is_active' => $request->boolean('is_active'),
            ]);

            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Offline place updated successfully!',
                    'data' => $offlinePlace
                ]);
            }

            return redirect()->route('admin.offline-places.index')->with('message_success', 'Offline place updated successfully!');
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
                    'message' => 'Offline place not found.'
                ], 404);
            }
            
            return redirect()->route('admin.offline-places.index')->with('message_danger', 'Offline place not found.');
        } catch (\Exception $e) {
            // For AJAX requests, return JSON response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the offline place. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('message_danger', 'An error occurred while updating the offline place. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            $offlinePlace = OfflinePlace::findOrFail($id);
            $offlinePlace->delete();
            return redirect()->route('admin.offline-places.index')->with('message_success', 'Offline place deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.offline-places.index')->with('message_danger', 'Offline place not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.offline-places.index')->with('message_danger', 'An error occurred while deleting the offline place. Please try again.');
        }
    }
}
