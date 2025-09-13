<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuthHelper;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role_id', 2)->with('role')->get();
        return view('admin.admins.index', compact('admins'));
    }

    public function ajax_add()
    {
        return view('admin.admins.add-modal');
    }

    public function ajax_edit($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admins.edit-modal', compact('admin'));
    }

    public function submit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => 2, // Always set to admin role
                'is_active' => true,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully!',
                'data' => $admin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the admin. Please try again.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $admin = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => $request->has('is_active'),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully!',
                'data' => $admin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the admin. Please try again.'
            ], 500);
        }
    }

    public function delete($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admins.delete-modal', compact('admin'));
    }

    public function destroy($id)
    {
        try {
            $admin = User::findOrFail($id);
            $admin->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Admin deleted successfully!'
                ]);
            }
            
            return redirect()->route('admin.admins.index')->with('message_success', 'Admin deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the admin. Please try again.'
                ], 500);
            }
            
            return redirect()->route('admin.admins.index')->with('message_danger', 'An error occurred while deleting the admin. Please try again.');
        }
    }

    public function changePassword($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admins.change-password-modal', compact('admin'));
    }

    public function updatePassword(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = User::findOrFail($id);
            $admin->update([
                'password' => Hash::make($request->password),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the password. Please try again.'
            ], 500);
        }
    }
}
