<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class TeacherController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teachers = User::where('role_id', 10)->with(['role', 'team'])->get();
        
        return view('admin.teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'phone', 'code']);
        $data['role_id'] = 10; // Static role for Teacher
        $data['email'] = 'teacher_' . time() . '@example.com'; // Generate unique email
        $data['password'] = Hash::make('password123'); // Default password

        $teacher = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Teacher created successfully.',
            'teacher' => $teacher->load(['role', 'team'])
        ]);
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        // Filter only the fields we need
        $data = $request->only(['name', 'phone', 'code']);
        $data['role_id'] = 10; // Static role for Teacher
        $data['email'] = 'teacher_' . time() . '@example.com'; // Generate unique email
        $data['password'] = Hash::make('password123'); // Default password
        $data['is_active'] = true;

        $teacher = User::create($data);

        return redirect()->route('admin.teachers.index')->with('message_success', 'Teacher created successfully.');
    }

    public function show($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $teacher = User::where('role_id', 10)->with(['role', 'team'])->findOrFail($id);
        
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $teacher = User::where('role_id', 10)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Filter only the fields we need
        $data = $request->only(['name', 'phone', 'code']);

        $teacher->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully.',
            'teacher' => $teacher->load(['role', 'team'])
        ]);
    }

    public function updateForm(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teacher = User::where('role_id', 10)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        // Filter only the fields we need
        $data = $request->only(['name', 'phone', 'code']);

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')->with('message_success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $teacher = User::where('role_id', 10)->findOrFail($id);
        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $country_codes = get_country_code();

        return view('admin.teachers.add', compact('country_codes'));
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teacher = User::where('role_id', 10)->with(['role', 'team'])->findOrFail($id);
        $country_codes = get_country_code();

        return view('admin.teachers.edit', compact('teacher', 'country_codes'));
    }
}
