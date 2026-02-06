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

class AcademicAssistantController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $academicAssistants = User::where('role_id', 5)->with(['role'])->get();
        $roles = UserRole::all();
        
        return view('admin.academic-assistants.index', compact('academicAssistants', 'roles'));
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
            'designation' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'designation', 'password']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 5; // Static role for Academic Assistant
        $data['is_active'] = 1; // Default to active

        $academicAssistant = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Academic Assistant created successfully.',
            'data' => $academicAssistant->load('role')
        ]);
    }

    public function show(User $academicAssistant)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($academicAssistant->load('role'));
    }

    public function destroy(User $academicAssistant)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if academic assistant has leads
        if ($academicAssistant->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete academic assistant. They have assigned leads.'
            ], 422);
        }

        $academicAssistant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Academic Assistant deleted successfully.'
        ]);
    }

    public function ajax_add(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();
        
        return view('admin.academic-assistants.add', compact('country_codes'));
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
            'designation' => 'required|string|max:255',
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
            'designation' => $request->designation,
            'password' => Hash::make($request->password),
            'role_id' => 5, // Static role for Academic Assistant
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.academic-assistants.index')->with('message_success', 'Academic Assistant created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $academicAssistant = User::where('id', $id)->where('role_id', 5)->firstOrFail();
        $country_codes = get_country_code();
        
        return view('admin.academic-assistants.edit', compact('academicAssistant', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $academicAssistant = User::where('id', $id)->where('role_id', 5)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'designation' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $academicAssistant->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'designation' => $request->designation,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.academic-assistants.index')->with('message_success', 'Academic Assistant updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $academicAssistant = User::where('id', $id)->where('role_id', 5)->firstOrFail();

        // Check if academic assistant has leads
        if ($academicAssistant->leads()->count() > 0) {
            return redirect()->route('admin.academic-assistants.index')->with('message_danger', 'Cannot delete academic assistant. They have assigned leads.');
        }

        $academicAssistant->delete();

        return redirect()->route('admin.academic-assistants.index')->with('message_success', 'Academic Assistant deleted successfully!');
    }

    public function changePassword($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $academicAssistant = User::where('id', $id)->where('role_id', 5)->firstOrFail();
        
        return view('admin.academic-assistants.change-password', compact('academicAssistant'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $academicAssistant = User::where('id', $id)->where('role_id', 5)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $academicAssistant->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.academic-assistants.index')->with('message_success', 'Password updated successfully!');
    }

    /**
     * Get all academic assistants for AJAX requests
     */
    public function getAll()
    {
        $academicAssistants = User::where('role_id', 5)
            ->where('is_active', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($academicAssistants);
    }
}
