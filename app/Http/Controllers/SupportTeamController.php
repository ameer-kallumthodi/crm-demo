<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class SupportTeamController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportTeamUsers = User::where('role_id', 8)->with(['role'])->get();
        $roles = UserRole::all();
        
        return view('admin.support-team.index', compact('supportTeamUsers', 'roles'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'ext_no', 'password']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 8; // Static role for Support Team
        $data['is_active'] = 1; // Default to active

        $supportTeamUser = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Support Team user created successfully.',
            'data' => $supportTeamUser->load('role')
        ]);
    }

    public function show(User $supportTeamUser)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($supportTeamUser->load('role'));
    }

    public function destroy(User $supportTeamUser)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if support team user has leads
        if ($supportTeamUser->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete support team user. They have assigned leads.'
            ], 422);
        }

        $supportTeamUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Support Team user deleted successfully.'
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();
        
        return view('admin.support-team.add', compact('country_codes'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'password' => Hash::make($request->password),
            'role_id' => 8, // Static role for Support Team
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.support-team.index')->with('message_success', 'Support Team user created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportTeamUser = User::where('id', $id)->where('role_id', 8)->firstOrFail();
        $country_codes = get_country_code();
        
        return view('admin.support-team.edit', compact('supportTeamUser', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportTeamUser = User::where('id', $id)->where('role_id', 8)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $supportTeamUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.support-team.index')->with('message_success', 'Support Team user updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportTeamUser = User::where('id', $id)->where('role_id', 8)->firstOrFail();

        // Check if support team user has leads
        if ($supportTeamUser->leads()->count() > 0) {
            return redirect()->route('admin.support-team.index')->with('message_danger', 'Cannot delete support team user. They have assigned leads.');
        }

        $supportTeamUser->delete();

        return redirect()->route('admin.support-team.index')->with('message_success', 'Support Team user deleted successfully!');
    }

    public function changePassword($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportTeamUser = User::where('id', $id)->where('role_id', 8)->firstOrFail();
        
        return view('admin.support-team.change-password', compact('supportTeamUser'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError);
        }

        $supportTeamUser = User::where('id', $id)->where('role_id', 8)->firstOrFail();
        
        $supportTeamUser->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.support-team.index')->with('message_success', 'Password updated successfully!');
    }
}
