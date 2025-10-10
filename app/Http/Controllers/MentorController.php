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

class MentorController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mentorUsers = User::where('role_id', 9)->with(['role'])->get();
        $roles = UserRole::all();
        
        return view('admin.mentor.index', compact('mentorUsers', 'roles'));
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
            'code' => 'nullable|string|max:10',
            'ext_no' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'ext_no', 'password']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 9; // Static role for Mentor
        $data['is_active'] = 1; // Default to active

        $mentorUser = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mentor user created successfully.',
            'data' => $mentorUser->load('role')
        ]);
    }

    public function show(User $mentorUser)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($mentorUser->load('role'));
    }

    public function destroy(User $mentorUser)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if mentor user has leads
        if ($mentorUser->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete mentor user. They have assigned leads.'
            ], 422);
        }

        $mentorUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mentor user deleted successfully.'
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();
        
        return view('admin.mentor.add', compact('country_codes'));
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
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
            'role_id' => 9, // Static role for Mentor
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.mentor.index')->with('message_success', 'Mentor user created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mentorUser = User::where('id', $id)->where('role_id', 9)->firstOrFail();
        $country_codes = get_country_code();
        
        return view('admin.mentor.edit', compact('mentorUser', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mentorUser = User::where('id', $id)->where('role_id', 9)->firstOrFail();

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

        $mentorUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.mentor.index')->with('message_success', 'Mentor user updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mentorUser = User::where('id', $id)->where('role_id', 9)->firstOrFail();

        // Check if mentor user has leads
        if ($mentorUser->leads()->count() > 0) {
            return redirect()->route('admin.mentor.index')->with('message_danger', 'Cannot delete mentor user. They have assigned leads.');
        }

        $mentorUser->delete();

        return redirect()->route('admin.mentor.index')->with('message_success', 'Mentor user deleted successfully!');
    }

    public function changePassword($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $mentorUser = User::where('id', $id)->where('role_id', 9)->firstOrFail();
        
        return view('admin.mentor.change-password', compact('mentorUser'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError);
        }

        $mentorUser = User::where('id', $id)->where('role_id', 9)->firstOrFail();
        
        $mentorUser->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.mentor.index')->with('message_success', 'Password updated successfully!');
    }
}
