<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;

class UniversityController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $universities = University::all();
        return view('admin.universities.index', compact('universities'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_finance()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ug_amount' => 'required|numeric|min:0',
            'pg_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $university = University::create([
            'title' => $request->title,
            'description' => $request->description,
            'ug_amount' => $request->ug_amount,
            'pg_amount' => $request->pg_amount,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'University created successfully.',
            'data' => $university
        ]);
    }

    public function show(University $university)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($university);
    }

    public function update(Request $request, University $university)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_finance()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ug_amount' => 'required|numeric|min:0',
            'pg_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $university->update([
            'title' => $request->title,
            'description' => $request->description,
            'ug_amount' => $request->ug_amount,
            'pg_amount' => $request->pg_amount,
            'is_active' => $request->boolean('is_active'),
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'University updated successfully.',
                'data' => $university
            ]);
        }

        return redirect()->route('admin.universities.index')->with('message_success', 'University updated successfully!');
    }

    public function destroy(University $university)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if university is being used by any leads or converted leads
        if ($university->leads()->count() > 0 || $university->convertedLeads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete university. It is being used by existing leads or converted leads.'
            ], 422);
        }

        $university->delete();

        return response()->json([
            'success' => true,
            'message' => 'University deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.universities.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_finance()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ug_amount' => 'required|numeric|min:0',
            'pg_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $university = University::create([
            'title' => $request->title,
            'description' => $request->description,
            'ug_amount' => $request->ug_amount,
            'pg_amount' => $request->pg_amount,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'University created successfully!',
                'data' => $university
            ]);
        }

        return redirect()->route('admin.universities.index')->with('message_success', 'University created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_finance()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = University::findOrFail($id);
        return view('admin.universities.edit', compact('edit_data'));
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_finance()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $university = University::findOrFail($id);

        // Check if university is being used by any leads or converted leads
        if ($university->leads()->count() > 0 || $university->convertedLeads()->count() > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Cannot delete university. It is being used by existing leads or converted leads.'
                ], 422);
            }
            return redirect()->route('admin.universities.index')->with('message_danger', 'Cannot delete university. It is being used by existing leads or converted leads.');
        }

        $university->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'University deleted successfully.'
            ]);
        }

        return redirect()->route('admin.universities.index')->with('message_success', 'University deleted successfully!');
    }
    
    public function getUniversityData($id)
    {
        try {
            $university = University::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'university' => [
                    'id' => $university->id,
                    'title' => $university->title,
                    'ug_amount' => $university->ug_amount,
                    'pg_amount' => $university->pg_amount,
                    'is_active' => $university->is_active
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'University not found'
            ], 404);
        }
    }
}
