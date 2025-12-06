<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;

class HODController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $hodUsers = User::where('role_id', 14)->with(['role'])->get();
        $roles = UserRole::all();
        
        return view('admin.hod.index', compact('hodUsers', 'roles'));
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
            'password' => 'required|string|min:8',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'password']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 14; // Static role for HOD
        $data['is_active'] = 1; // Default to active

        $hodUser = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'HOD user created successfully.',
            'data' => $hodUser->load('role')
        ]);
    }

    public function show(User $hodUser)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($hodUser->load('role'));
    }

    public function destroy(User $hodUser)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if HOD user has leads
        if ($hodUser->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete HOD user. They have assigned leads.'
            ], 422);
        }

        $hodUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'HOD user deleted successfully.'
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();
        
        return view('admin.hod.add', compact('country_codes'));
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
            'password' => 'required|string|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'password' => Hash::make($request->password),
            'role_id' => 14, // Static role for HOD
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'HOD user created successfully!',
                'data' => $user
            ]);
        }

        return redirect()->route('admin.hod.index')->with('message_success', 'HOD user created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $hodUser = User::where('id', $id)->where('role_id', 14)->firstOrFail();
        $country_codes = get_country_code();
        
        return view('admin.hod.edit', compact('hodUser', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $hodUser = User::where('id', $id)->where('role_id', 14)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $hodUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'HOD user updated successfully!',
                'data' => $hodUser
            ]);
        }

        return redirect()->route('admin.hod.index')->with('message_success', 'HOD user updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $hodUser = User::where('id', $id)->where('role_id', 14)->firstOrFail();

        // Check if HOD user has leads
        if ($hodUser->leads()->count() > 0) {
            return redirect()->route('admin.hod.index')->with('message_danger', 'Cannot delete HOD user. They have assigned leads.');
        }

        $hodUser->delete();

        return redirect()->route('admin.hod.index')->with('message_success', 'HOD user deleted successfully!');
    }
}

