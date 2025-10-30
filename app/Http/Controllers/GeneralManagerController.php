<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;

class GeneralManagerController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $generalManagers = User::where('role_id', 11)->with(['role'])->get();
        $roles = UserRole::all();
        
        return view('admin.general-managers.index', compact('generalManagers', 'roles'));
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

        $data = $request->only(['name', 'email', 'phone', 'code', 'ext_no', 'password']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 11; // General Manager
        $data['is_active'] = 1;

        $generalManager = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'General Manager created successfully.',
            'data' => $generalManager->load('role')
        ]);
    }

    public function show(User $generalManager)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($generalManager->load('role'));
    }

    public function destroy(User $generalManager)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        if ($generalManager->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete general manager. They have assigned leads.'
            ], 422);
        }

        $generalManager->delete();

        return response()->json([
            'success' => true,
            'message' => 'General Manager deleted successfully.'
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();
        
        return view('admin.general-managers.add', compact('country_codes'));
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
            'role_id' => 11,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.general-managers.index')->with('message_success', 'General Manager created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $generalManager = User::where('id', $id)->where('role_id', 11)->firstOrFail();
        $country_codes = get_country_code();
        
        return view('admin.general-managers.edit', compact('generalManager', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $generalManager = User::where('id', $id)->where('role_id', 11)->firstOrFail();

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

        $generalManager->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'ext_no' => $request->ext_no,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.general-managers.index')->with('message_success', 'General Manager updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $generalManager = User::where('id', $id)->where('role_id', 11)->firstOrFail();

        if ($generalManager->leads()->count() > 0) {
            return redirect()->route('admin.general-managers.index')->with('message_danger', 'Cannot delete general manager. They have assigned leads.');
        }

        $generalManager->delete();
        return redirect()->route('admin.general-managers.index')->with('message_success', 'General Manager deleted successfully!');
    }

    public function changePassword($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $generalManager = User::where('id', $id)->where('role_id', 11)->firstOrFail();
        
        return view('admin.general-managers.change-password', compact('generalManager'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $generalManager = User::where('id', $id)->where('role_id', 11)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $generalManager->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.general-managers.index')->with('message_success', 'Password updated successfully!');
    }
}


