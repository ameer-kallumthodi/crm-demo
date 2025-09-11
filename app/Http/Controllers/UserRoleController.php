<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class UserRoleController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $userRoles = UserRole::orderBy('title')->get();
        return view('admin.user-roles.index', compact('userRoles'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255|unique:user_roles,title',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $userRole = UserRole::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Role created successfully.',
            'data' => $userRole
        ]);
    }

    public function show(UserRole $userRole)
    {
        if (!RoleHelper::is_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($userRole);
    }

    public function update(Request $request, UserRole $userRole)
    {
        if (!RoleHelper::is_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255|unique:user_roles,title,' . $userRole->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $userRole->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Role updated successfully.',
            'data' => $userRole
        ]);
    }

    public function destroy(UserRole $userRole)
    {
        if (!RoleHelper::is_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if role is being used by any users
        if ($userRole->users()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete role. It is being used by existing users.'
            ], 422);
        }

        $userRole->delete();

        return response()->json([
            'success' => true,
            'message' => 'User Role deleted successfully.'
        ]);
    }
}