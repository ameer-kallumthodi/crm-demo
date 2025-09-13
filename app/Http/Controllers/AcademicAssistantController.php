<?php

namespace App\Http\Controllers;

use App\Models\AcademicAssistant;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicAssistantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicAssistants = AcademicAssistant::with(['createdBy', 'updatedBy'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.academic-assistants.index', compact('academicAssistants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $country_codes = get_country_code();
        return view('admin.academic-assistants.create', compact('country_codes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:academic_assistants,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['created_by'] = AuthHelper::getCurrentUserId();
        $data['updated_by'] = AuthHelper::getCurrentUserId();

        AcademicAssistant::create($data);

        return redirect()->route('academic-assistants.index')
            ->with('message_success', 'Academic Assistant created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicAssistant $academicAssistant)
    {
        $academicAssistant->load(['createdBy', 'updatedBy']);
        return view('admin.academic-assistants.show', compact('academicAssistant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicAssistant $academicAssistant)
    {
        $country_codes = get_country_code();
        return view('admin.academic-assistants.edit', compact('academicAssistant', 'country_codes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicAssistant $academicAssistant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:academic_assistants,email,' . $academicAssistant->id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['updated_by'] = AuthHelper::getCurrentUserId();

        $academicAssistant->update($data);

        return redirect()->route('academic-assistants.index')
            ->with('message_success', 'Academic Assistant updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicAssistant $academicAssistant)
    {
        try {
            $academicAssistant->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Academic Assistant deleted successfully!'
                ]);
            }
            
            return redirect()->route('academic-assistants.index')
                ->with('message_success', 'Academic Assistant deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the academic assistant. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('message_danger', 'An error occurred while deleting the academic assistant. Please try again.');
        }
    }
}
