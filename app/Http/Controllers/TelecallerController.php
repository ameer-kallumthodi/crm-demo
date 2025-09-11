<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class TelecallerController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $telecallers = User::where('role_id', 3)->with(['role', 'team'])->get();
        $roles = UserRole::all();
        $teams = Team::all();
        
        return view('admin.telecallers.index', compact('telecallers', 'roles', 'teams'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:user_roles,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $telecaller = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'team_id' => $request->team_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Telecaller created successfully.',
            'data' => $telecaller->load('role', 'team')
        ]);
    }

    public function show(User $telecaller)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($telecaller->load('role', 'team'));
    }

    public function update(Request $request, User $telecaller)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $telecaller->id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:user_roles,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'role_id' => $request->role_id,
            'team_id' => $request->team_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $telecaller->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Telecaller updated successfully.',
            'data' => $telecaller->load('role', 'team')
        ]);
    }

    public function destroy(User $telecaller)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if telecaller has leads
        if ($telecaller->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete telecaller. They have assigned leads.'
            ], 422);
        }

        $telecaller->delete();

        return response()->json([
            'success' => true,
            'message' => 'Telecaller deleted successfully.'
        ]);
    }

    public function changePassword(Request $request, User $telecaller)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $telecaller->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $roles = UserRole::all();
        $teams = Team::all();
        return view('admin.telecallers.add', compact('roles', 'teams'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:user_roles,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'team_id' => $request->team_id,
        ]);

        return redirect()->route('admin.telecallers.index')->with('message_success', 'Telecaller created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $edit_data = User::findOrFail($id);
        $roles = UserRole::all();
        $teams = Team::all();
        return view('admin.telecallers.edit', compact('edit_data', 'roles', 'teams'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:user_roles,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $telecaller = User::findOrFail($id);
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'role_id' => $request->role_id,
            'team_id' => $request->team_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $telecaller->update($updateData);

        return redirect()->route('admin.telecallers.index')->with('message_success', 'Telecaller updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $telecaller = User::findOrFail($id);
        
        // Check if telecaller has leads
        if ($telecaller->leads()->count() > 0) {
            return redirect()->route('admin.telecallers.index')->with('message_error', 'Cannot delete telecaller. They have assigned leads.');
        }

        $telecaller->delete();
        return redirect()->route('admin.telecallers.index')->with('message_success', 'Telecaller deleted successfully!');
    }

    public function changePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $edit_data = User::findOrFail($id);
        return view('admin.telecallers.change-password', compact('edit_data'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $telecaller = User::findOrFail($id);
        $telecaller->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.telecallers.index')->with('message_success', 'Password changed successfully!');
    }
}